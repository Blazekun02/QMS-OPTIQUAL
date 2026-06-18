<?php
if (!session_id()) session_start();
include '../connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $conn->close();
    exit;
}

$policyTitle      = trim($_POST['policyTitle'] ?? '');
$accID            = (int)$_SESSION['accID'];
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
        // Strip any accidental 'v' prefix and split the string
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

// ── Basic validation ──────────────────────────────────────────
if (!$policyTitle || !$file || $file['error'] !== 0) {
    echo "<script>alert('Missing title or file. Please try again.'); history.back();</script>";
    exit;
}

// ── Directories ───────────────────────────────────────────────
$targetDir = __DIR__ . "/../files/";
$logDir    = __DIR__ . "/../files/logs/";
if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
if ($isRevision && !file_exists($logDir)) mkdir($logDir, 0777, true);

// ── Upload main policy PDF ────────────────────────────────────
$fileName       = time() . '_' . preg_replace('/\s+/', '_', basename($file['name']));
$targetFilePath = $targetDir . $fileName;
$relativePath   = "/qms_optiqual/files/" . $fileName;

if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
    echo "<script>alert('Error uploading policy file. Check folder permissions.'); history.back();</script>";
    exit;
}

// ── Upload revision form PDF (if revision) ────────────────────
$relativeLogPath = null;
if ($isRevision && $logFile && $logFile['error'] === 0) {
    $logName         = "LOG_" . time() . "_" . preg_replace('/\s+/', '_', basename($logFile['name']));
    $relativeLogPath = "/qms_optiqual/files/logs/" . $logName;
    if (!move_uploaded_file($logFile['tmp_name'], $logDir . $logName)) {
        $relativeLogPath = null; // non-fatal
    }
}

// ── Determine ultimate root and version number ───────────────
$ultimateRootID = $originalPolicyID;
if ($ultimateRootID) {
    $maxDepth = 20; // Prevent infinite loops from circular references
    $currentDepth = 0;
    while ($currentDepth < $maxDepth) {
        $stmtRoot = $conn->prepare("SELECT originalPolicyID FROM policytbl WHERE policyID = ?");
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
        $childStmt = $conn->prepare("SELECT policyID FROM policytbl WHERE originalPolicyID = ?");
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
    // Extract all versions for this lineage and parse them dynamically in PHP 
    // to completely avoid MySQL string-casting limitations.
    // ✨ FIXED: Also check revisionhistorytbl in case legacy policies only stored versions there!
    $sql = "SELECT COALESCE(r.versionNo, p.versionNo) as versionNo 
            FROM policytbl p
            LEFT JOIN revisionhistorytbl r ON r.currentPolicyID = p.policyID
            WHERE p.policyID IN ($placeholders) 
              AND COALESCE(r.versionNo, p.versionNo) IS NOT NULL AND COALESCE(r.versionNo, p.versionNo) != ''";
    $verStmt = $conn->prepare($sql);
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
    // New original policy starts at version 1.0
    $newVersion = '1.0';
}

// ── Insert into policytbl ─────────────────────────────────────
$stmt = $conn->prepare("
    INSERT INTO policytbl
        (title, contentPath, requestChangeContentPath, policyAuthor, originalPolicyID, policyStatusID, versionNo)
    VALUES (?, ?, ?, ?, ?, 1, ?)
");
$stmt->bind_param("sssiis", $policyTitle, $relativePath, $relativeLogPath, $accID, $ultimateRootID, $newVersion);

if (!$stmt->execute()) {
    echo "❌ Database Error (policytbl): " . $stmt->error;
    exit;
}
$newPolicyID = $conn->insert_id;
$stmt->close();

// ── Assign task and send notifications to ALL Quality Assurance Personnel (Role ID 3) ───
$qapQuery = $conn->query("SELECT accID FROM accdatatbl WHERE roleID = 3");
if ($qapQuery && $qapQuery->num_rows > 0) {
    $taskStmt = $conn->prepare("INSERT INTO tasktbl (policyAssigned, assignedTo, assignedBy, taskTypeID, taskStatus) VALUES (?, ?, ?, 2, 0)");
    $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus) VALUES (?, ?, 0)");
    
    $notifMsg = "A new policy is pending your review: " . $policyTitle;
    
    while ($qapRow = $qapQuery->fetch_assoc()) {
        $qapID = (int)$qapRow['accID'];
        
        if ($taskStmt) {
            $taskStmt->bind_param("iii", $newPolicyID, $qapID, $accID);
            $taskStmt->execute();
        }
        
        if ($notifStmt) {
            $notifStmt->bind_param("is", $qapID, $notifMsg);
            $notifStmt->execute();
        }
    }
    
    if ($taskStmt) $taskStmt->close();
    if ($notifStmt) $notifStmt->close();
}

// ── If revision → insert into revisionhistorytbl ───────────────
if ($isRevision && $ultimateRootID) {
    // ✨ FIXED: Now pointing to your actual revisionhistorytbl and correct columns
    $revStmt = $conn->prepare("
        INSERT INTO revisionhistorytbl
            (originalPolicyID, currentPolicyID, versionNo, revisionType, revisionFormPath, changeDescription, revisedBy, dateRevised)
        VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())
    ");
    
    $revStmt->bind_param("iissssi",
        $ultimateRootID,
        $newPolicyID,
        $newVersion,
        $revisionType,
        $relativeLogPath,
        $changesDesc,
        $accID
    );
    $revStmt->execute();
    $revStmt->close();
}



// ── Fetch true roleID before closing connection ───────────────
    $roleID = 4; // Default to staff
    if (isset($_SESSION['roleID'])) {
        $roleID = $_SESSION['roleID'];
    } else {
        $roleStmt = $conn->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
        $roleStmt->bind_param("i", $accID);
        $roleStmt->execute();
        $roleRow = $roleStmt->get_result()->fetch_assoc();
        if ($roleRow) {
            $roleID = $roleRow['roleID'];
            $_SESSION['roleID'] = $roleID; // Fix the missing session variable
        }
        $roleStmt->close();
    }

    $conn->close();

    // ── Redirect to appropriate dashboard ─────────────────────────
    switch ($roleID) {
        case 2:  header("Location: /qms_optiqual/QADSpecificComponents/QADMain/QAD-POV.php"); break;
        case 3:  header("Location: /qms_optiqual/QAPSpecificComponents/QAPMain/QAP-POV.php"); break;
        default: header("Location: /qms_optiqual/staffSpecificComponents/staffMain/staff-POV.php");
    }
    exit;

    $conn->close();
?>