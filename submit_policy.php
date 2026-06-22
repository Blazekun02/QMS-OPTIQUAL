<?php
/**
 * submit_policy.php — hardened for network-mounted (Samba) deployment
 *
 * Changes vs. original:
 *  - ob_start() at the very top so a stray warning/notice can never
 *    cause "headers already sent" -> the redirect always works.
 *  - A dedicated error log (php_errors_submit_policy.log) so fatals are
 *    visible to you without exposing details to the end user.
 *  - Every $conn->prepare() call is checked. On localhost a prepare()
 *    almost never fails, but on a freshly migrated schema a single typo'd
 *    column/table name returns false, and calling ->bind_param() on
 *    false is a fatal error -> exactly the "DB write happened, then 500"
 *    symptom you're seeing, since the failing prepare is *after* the
 *    main policytbl insert.
 *  - mkdir() / move_uploaded_file() failures are now actually checked
 *    and reported instead of silently continuing.
 *  - Business logic (versioning, lineage walk, task/notification loop,
 *    revision history) is UNCHANGED.
 */

// ── Error visibility (logs only, never shown to the browser) ─────────
ini_set('display_errors', '0');           // never leak errors to users
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php_errors_submit_policy.log');
error_reporting(E_ALL);

// Catch true fatal errors (the kind that bypass try/catch) and log them
// in a readable way, then fail cleanly instead of a blank 500.
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("FATAL in submit_policy.php: {$err['message']} in {$err['file']} on line {$err['line']}");
        if (ob_get_level()) { ob_end_clean(); }
        if (!headers_sent()) {
            http_response_code(500);
        }
        echo "<script>alert('A server error occurred while finishing your submission. Please check with your administrator (see php_errors_submit_policy.log).'); history.back();</script>";
    }
});

// Start output buffering immediately so nothing can be flushed to the
// browser before our header() redirect at the bottom.
ob_start();

if (!session_id()) session_start();
include '../connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $conn->close();
    exit;
}

$policyTitle      = trim($_POST['policyTitle'] ?? '');
$accID            = (int)($_SESSION['accID'] ?? 0);
$isRevision       = isset($_POST['isRevision']);
$originalPolicyID = ($isRevision && !empty($_POST['originalPolicyID']))
                    ? (int)$_POST['originalPolicyID'] : null;
$revisionType     = in_array($_POST['revisionType'] ?? '', ['minor','major'])
                    ? $_POST['revisionType'] : 'minor';
$changesDesc      = trim($_POST['changesDescription'] ?? '');

$file    = $_FILES['policyFile']    ?? null;
$logFile = $_FILES['changeLogFile'] ?? null;

// ── Version Calculation Helper ────────────────────────────────
function calculateNextVersion(array $historicalVersions, string $revisionType = 'minor'): string {
    $maxMajor = 1;
    $maxMinor = 0;

    foreach ($historicalVersions as $version) {
        $cleanVer = str_ireplace('v', '', trim((string)$version));
        if ($cleanVer === '') continue;

        $parts = explode('.', $cleanVer);

        $currMajor = (int)($parts[0] ?? 1);
        $currMinor = isset($parts[1]) ? (int)$parts[1] : 0;

        if ($currMajor > $maxMajor) {
            $maxMajor = $currMajor;
            $maxMinor = $currMinor;
        } elseif ($currMajor === $maxMajor && $currMinor > $maxMinor) {
            $maxMinor = $currMinor;
        }
    }

    if (strtolower($revisionType) === 'major') {
        return ($maxMajor + 1) . '.0';
    } else {
        return $maxMajor . '.' . ($maxMinor + 1);
    }
}

/**
 * Wrapper around $conn->prepare() that logs and stops execution cleanly
 * if the prepare fails, instead of letting a fatal error happen later
 * when ->bind_param() is called on `false`.
 */
function safePrepare(mysqli $conn, string $sql, string $context) {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("prepare() failed [$context]: " . $conn->error . " | SQL: $sql");
        if (ob_get_level()) { ob_end_clean(); }
        echo "<script>alert('A database error occurred ($context). Please contact your administrator.'); history.back();</script>";
        $conn->close();
        exit;
    }
    return $stmt;
}

// ── Basic validation ──────────────────────────────────────────
if (!$policyTitle || !$file || $file['error'] !== 0) {
    if (ob_get_level()) { ob_end_clean(); }
    echo "<script>alert('Missing title or file. Please try again.'); history.back();</script>";
    exit;
}

// ── Directories (now actually checked, not silently swallowed) ──────
$targetDir = __DIR__ . "/../files/";
$logDir    = __DIR__ . "/../files/logs/";
$archiveDir = __DIR__ . "/../files/archives/";

if (!file_exists($targetDir)) {
    if (!@mkdir($targetDir, 0777, true) && !file_exists($targetDir)) {
        error_log("mkdir() failed for targetDir: $targetDir — check Samba mount permissions for the web server user.");
        if (ob_get_level()) { ob_end_clean(); }
        echo "<script>alert('Server cannot create the files directory. Check Samba folder permissions.'); history.back();</script>";
        exit;
    }
}
if ($isRevision && !file_exists($logDir)) {
    if (!@mkdir($logDir, 0777, true) && !file_exists($logDir)) {
        error_log("mkdir() failed for logDir: $logDir — check Samba mount permissions for the web server user.");
        // Not fatal on its own — log upload will just be skipped below.
    }
}
if (!file_exists($archiveDir)) {
    if (!@mkdir($archiveDir, 0777, true) && !file_exists($archiveDir)) {
        error_log("mkdir() failed for archiveDir: $archiveDir — check Samba mount permissions for the web server user.");
        // This is not fatal for a new submission, but will prevent archiving.
    }
}

if (!is_writable($targetDir)) {
    error_log("targetDir exists but is NOT WRITABLE by the web server user: $targetDir");
    if (ob_get_level()) { ob_end_clean(); }
    echo "<script>alert('Files directory is not writable. Check Samba folder permissions for the web server account.'); history.back();</script>";
    exit;
}

// ── Upload main policy PDF ────────────────────────────────────
$fileName       = time() . '_' . preg_replace('/\s+/', '_', basename($file['name']));
$targetFilePath = $targetDir . $fileName;
$relativePath   = "/qms_optiqual/files/" . $fileName;

if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
    error_log("move_uploaded_file() failed: tmp={$file['tmp_name']} -> target=$targetFilePath");
    if (ob_get_level()) { ob_end_clean(); }
    echo "<script>alert('Error uploading policy file. Check Samba folder permissions.'); history.back();</script>";
    exit;
}

// ── Upload revision form PDF (if revision) ────────────────────
$relativeLogPath = null;
if ($isRevision && $logFile && $logFile['error'] === 0) {
    $logName         = "LOG_" . time() . "_" . preg_replace('/\s+/', '_', basename($logFile['name']));
    $relativeLogPath = "/qms_optiqual/files/logs/" . $logName;
    if (!move_uploaded_file($logFile['tmp_name'], $logDir . $logName)) {
        error_log("move_uploaded_file() failed for change-log file: $logDir$logName");
        $relativeLogPath = null;
    }
}

// ── Determine ultimate root and version number ───────────────
$ultimateRootID = $originalPolicyID;
if ($ultimateRootID) {
    $maxDepth = 20;
    $currentDepth = 0;
    while ($currentDepth < $maxDepth) {
        $stmtRoot = safePrepare($conn, "SELECT originalPolicyID FROM policytbl WHERE policyID = ?", "root-lineage lookup");
        $stmtRoot->bind_param("i", $ultimateRootID);
        $stmtRoot->execute();
        $resRoot = $stmtRoot->get_result();
        $foundParent = false;
        if ($rowRoot = $resRoot->fetch_assoc()) {
            if (!empty($rowRoot['originalPolicyID'])) {
                $ultimateRootID = (int)$rowRoot['originalPolicyID'];
                $foundParent = true;
            }
        }
        $stmtRoot->close();
        if (!$foundParent) break;
        $currentDepth++;
    }
}

$newVersion = null;
if ($isRevision && $ultimateRootID) {
    $lineage = [$ultimateRootID];
    for ($i = 0; $i < count($lineage); $i++) {
        $currentID = $lineage[$i];
        $childStmt = safePrepare($conn, "SELECT policyID FROM policytbl WHERE originalPolicyID = ?", "lineage children lookup");
        $childStmt->bind_param("i", $currentID);
        $childStmt->execute();
        $childRes = $childStmt->get_result();
        while ($childRow = $childRes->fetch_assoc()) {
            $childID = (int)$childRow['policyID'];
            if (!in_array($childID, $lineage, true)) {
                $lineage[] = $childID;
            }
        }
        $childStmt->close();
    }

    $placeholders = implode(',', array_fill(0, count($lineage), '?'));
    $sql = "SELECT COALESCE(r.versionNo, p.versionNo) as versionNo
            FROM policytbl p
            LEFT JOIN revisionhistorytbl r ON r.currentPolicyID = p.policyID
            WHERE p.policyID IN ($placeholders)
              AND COALESCE(r.versionNo, p.versionNo) IS NOT NULL AND COALESCE(r.versionNo, p.versionNo) != ''";
    $verStmt = safePrepare($conn, $sql, "version history lookup");
    $types = str_repeat('i', count($lineage));
    $verStmt->bind_param($types, ...$lineage);
    $verStmt->execute();
    $verRes = $verStmt->get_result();

    $historicalVersions = [];
    while ($verRow = $verRes->fetch_assoc()) {
        $historicalVersions[] = $verRow['versionNo'];
    }
    $verStmt->close();

    $newVersion = calculateNextVersion($historicalVersions, $revisionType);
} else if (!$isRevision) {
    $newVersion = '1.0';
}

// ── Insert into policytbl ─────────────────────────────────────
$stmt = safePrepare($conn, "
    INSERT INTO policytbl
        (title, contentPath, requestChangeContentPath, policyAuthor, originalPolicyID, policyStatusID, versionNo)
    VALUES (?, ?, ?, ?, ?, 1, ?)
", "policytbl insert");
$stmt->bind_param("sssiis", $policyTitle, $relativePath, $relativeLogPath, $accID, $ultimateRootID, $newVersion);

if (!$stmt->execute()) {
    error_log("policytbl insert execute() failed: " . $stmt->error);
    if (ob_get_level()) { ob_end_clean(); }
    echo "❌ Database Error (policytbl): " . htmlspecialchars($stmt->error);
    exit;
}
$newPolicyID = $conn->insert_id;
$stmt->close();

// ── Assign task and send notifications safely ─────────────────
$qapQuery = $conn->query("SELECT accID FROM accdatatbl WHERE roleID = 3");
if ($qapQuery === false) {
    error_log("accdatatbl query failed: " . $conn->error);
} elseif ($qapQuery->num_rows > 0) {
    $taskStmt  = safePrepare($conn, "INSERT INTO tasktbl (policyAssigned, assignedTo, assignedBy, taskTypeID, taskStatus) VALUES (?, ?, ?, 2, 0)", "tasktbl insert");
    $notifStmt = safePrepare($conn, "INSERT INTO notiftbl (receivedBy, message, notifStatus) VALUES (?, ?, 0)", "notiftbl insert");

    $notifMsg = "A new policy is pending your review: " . $policyTitle;

    while ($qapRow = $qapQuery->fetch_assoc()) {
        $qapID = (int)$qapRow['accID'];

        $taskStmt->bind_param("iii", $newPolicyID, $qapID, $accID);
        if (!$taskStmt->execute()) {
            error_log("tasktbl insert execute() failed: " . $taskStmt->error);
        }

        $notifStmt->bind_param("is", $qapID, $notifMsg);
        if (!$notifStmt->execute()) {
            error_log("notiftbl insert execute() failed: " . $notifStmt->error);
        }
    }

    $taskStmt->close();
    $notifStmt->close();
}

// ── If revision → insert into revisionhistorytbl ───────────────
if ($isRevision && $ultimateRootID) {
    $revStmt = safePrepare($conn, "
        INSERT INTO revisionhistorytbl
            (originalPolicyID, currentPolicyID, versionNo, revisionType, revisionFormPath, changeDescription, revisedBy, dateRevised)
        VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())
    ", "revisionhistorytbl insert");

    $revStmt->bind_param("iissssi",
        $ultimateRootID,
        $newPolicyID,
        $newVersion,
        $revisionType,
        $relativeLogPath,
        $changesDesc,
        $accID
    );
    if (!$revStmt->execute()) {
        error_log("revisionhistorytbl insert execute() failed: " . $revStmt->error);
    }
    $revStmt->close();

    // ✨ FIX: Archive the old policy that was just revised.
    // We use originalPolicyID which is the direct parent of the new revision.
    if ($originalPolicyID && is_writable($archiveDir)) {
        // 1. Get the file path of the policy being replaced.
        $oldPathStmt = safePrepare($conn, "SELECT contentPath FROM policytbl WHERE policyID = ?", "get old policy path");
        $oldPathStmt->bind_param("i", $originalPolicyID);
        $oldPathStmt->execute();
        $oldPathResult = $oldPathStmt->get_result();
        if ($oldPolicy = $oldPathResult->fetch_assoc()) {
            $oldRelativePath = $oldPolicy['contentPath'];
            if ($oldRelativePath && strpos($oldRelativePath, 'archives') === false) {
                $oldFileName = basename($oldRelativePath);
                $oldFullPath = $targetDir . $oldFileName;
                $newArchiveFullPath = $archiveDir . $oldFileName;
                $newArchiveRelativePath = '/qms_optiqual/files/archives/' . $oldFileName;

                // 2. Move the physical file to the archives directory.
                if (file_exists($oldFullPath) && !file_exists($newArchiveFullPath)) {
                    if (rename($oldFullPath, $newArchiveFullPath)) {
                        // 3. Update the database record to point to the new path and set status to archived.
                        $archiveStmt = safePrepare($conn, "UPDATE policytbl SET policyStatusID = 7, contentPath = ? WHERE policyID = ?", "archive old policy");
                        $archiveStmt->bind_param("si", $newArchiveRelativePath, $originalPolicyID);
                        if (!$archiveStmt->execute()) {
                            error_log("Archiving old policy ID {$originalPolicyID} failed: " . $archiveStmt->error);
                        }
                        $archiveStmt->close();
                    } else {
                        error_log("rename() failed to move old policy file: from '{$oldFullPath}' to '{$newArchiveFullPath}'");
                    }
                }
            }
        }
        $oldPathStmt->close();
    }

}

// ── Fetch true roleID before closing connection ───────────────
$roleID = 4;
if (isset($_SESSION['roleID'])) {
    $roleID = $_SESSION['roleID'];
} else {
    $roleStmt = safePrepare($conn, "SELECT roleID FROM accdatatbl WHERE accID = ?", "roleID lookup");
    $roleStmt->bind_param("i", $accID);
    $roleStmt->execute();
    $roleRow = $roleStmt->get_result()->fetch_assoc();
    if ($roleRow) {
        $roleID = $roleRow['roleID'];
        $_SESSION['roleID'] = $roleID;
    }
    $roleStmt->close();
}

$conn->close();

// ── Redirect safely ─────────────────────────────────────────
// Discard any buffered output (stray notices/whitespace) so header()
// is guaranteed to still be able to send the Location header.
if (ob_get_level()) {
    ob_end_clean();
}

switch ($roleID) {
    case 2:
        header("Location: /qms_optiqual/QADSpecificComponents/QADMain/QAD-POV.php");
        break;
    case 3:
        header("Location: /qms_optiqual/QAPSpecificComponents/QAPMain/QAP-POV.php");
        break;
    default:
        header("Location: /qms_optiqual/staffSpecificComponents/staffMain/staff-POV.php");
}
exit;