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

// ── Insert into policytbl ─────────────────────────────────────
$stmt = $conn->prepare("
    INSERT INTO policytbl
        (title, contentPath, requestChangeContentPath, policyAuthor, originalPolicyID, policyStatusID)
    VALUES (?, ?, ?, ?, ?, 1)
");
$stmt->bind_param("sssii", $policyTitle, $relativePath, $relativeLogPath, $accID, $originalPolicyID);

if (!$stmt->execute()) {
    echo "❌ Database Error (policytbl): " . $stmt->error;
    exit;
}
$newPolicyID = $conn->insert_id;
$stmt->close();

// ── Insert into tasktbl (puts it in QAD's workspace inbox) ───
$taskStmt = $conn->prepare("INSERT INTO tasktbl (policyAssigned, assignedBy) VALUES (?, ?)");
$taskStmt->bind_param("ii", $newPolicyID, $accID);
$taskStmt->execute();
$taskStmt->close();

// ── If revision → insert into revisionhistorytbl ───────────────
if ($isRevision && $originalPolicyID) {

    // Calculate next version number
    $verStmt = $conn->prepare("
        SELECT versionNo FROM revisionhistorytbl
        WHERE originalPolicyID = ?
        ORDER BY revisionID DESC LIMIT 1
    ");
    $verStmt->bind_param("i", $originalPolicyID);
    $verStmt->execute();
    $verRow = $verStmt->get_result()->fetch_assoc();
    $verStmt->close();

    if ($verRow) {
        $lastVersion = $verRow['versionNo'];
    } else {
        // This is the first revision, so start at version 1.0
        $lastVersion = '1.0';
    }

    // Bump version
    $parts = explode('.', $lastVersion);
    $major = (int)($parts[0] ?? 1);
    $minor = (int)($parts[1] ?? 0);
    $newVersion = ($revisionType === 'major')
        ? ($major + 1) . '.0'
        : $major . '.' . ($minor + 1);

    // ✨ FIXED: Now pointing to your actual revisionhistorytbl and correct columns
    $revStmt = $conn->prepare("
        INSERT INTO revisionhistorytbl
            (originalPolicyID, currentPolicyID, versionNo, revisionType, revisionFormPath, changeDescription, revisedBy, dateRevised)
        VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())
    ");
    
    $revStmt->bind_param("iissssi",
        $originalPolicyID,
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