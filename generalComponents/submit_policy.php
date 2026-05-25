<?php
if (!session_id()) session_start();
include '../connect.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $policyTitle = $_POST['policyTitle'];
        $file = $_FILES['policyFile'];
        $accID  = $_SESSION['accID'];

        // ✨ FIX 1: Point the physical upload to the correct "files" folder
        // Using ../ goes up one level from generalComponents to qms_optiqual
        $targetDir = "../files/"; 
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $maxFileSize = 10 * 1024 * 1024; // 10 MB in bytes

        if ($file["size"] > $maxFileSize) {
            echo "<script>alert('File size exceeds the 10MB limit.');</script>";
            exit();
        }

        // ✨ FIX 2: Clean the filename to prevent spaces from breaking the URL
        $fileName = basename($file["name"]);
        $cleanFileName = str_replace(' ', '_', $fileName); // Replaces spaces with underscores
        
        // The physical path where the file is moved on the server
        $targetFilePath = $targetDir . $cleanFileName;
        
        // The URL path saved to the database for the iframe to read
        $relativePath = "/qms_optiqual/files/" . $cleanFileName;
        
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Allowed file types
        $allowedTypes = array('pdf', 'doc', 'docx', 'txt');

        
        if (in_array($fileType, $allowedTypes)) {
            // Upload Files to Server
            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                
                // Explicitly set categoryID to NULL for new submissions
                $stmt = $conn->prepare("INSERT INTO policytbl (title, contentPath, policyAuthor, categoryID) VALUES (?, ?, ?, NULL)");
                $stmt->bind_param("ssi", $policyTitle, $relativePath, $accID);
                
                if ($stmt->execute()) {
                    // Get the last inserted policyID
                    $policyID = $conn->insert_id;

                    // Insert into tasktbl
                    $taskStmt = $conn->prepare("INSERT INTO tasktbl (policyAssigned, assignedBy) VALUES (?, ?)");
                    $taskStmt->bind_param("ii", $policyID, $accID); 
                    
                    if ($taskStmt->execute()) {
                        // Redirect after successful insertion
                        header("Location: /qms_optiqual/QADSpecificComponents/QADMain/QAD-POV.php");
                        exit(); 
                    } else {
                        echo "❌ Error saving to tasktbl: " . $taskStmt->error;
                    }
                    $taskStmt->close();

                } else {
                    echo "❌ Error saving to policytbl: " . $stmt->error;
                }
                $stmt->close();
                
            } else {
                echo "<script>alert('Error moving uploaded file. Check folder permissions.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only PDF, DOC, DOCX, and TXT are allowed.');</script>";
        }
    }
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

// ── If revision → insert into policyrevisiontbl ───────────────
if ($isRevision && $originalPolicyID) {

    // Calculate next version number
    $verStmt = $conn->prepare("
        SELECT versionNo FROM policyrevisiontbl
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
        // First revision — base off original policy versionNo
        $origStmt = $conn->prepare("SELECT versionNo FROM policytbl WHERE policyID = ?");
        $origStmt->bind_param("i", $originalPolicyID);
        $origStmt->execute();
        $origRow  = $origStmt->get_result()->fetch_assoc();
        $origStmt->close();
        $lastVersion = $origRow['versionNo'] ?? '1.0';
    }

    // Bump version
    $parts = explode('.', $lastVersion);
    $major = (int)($parts[0] ?? 1);
    $minor = (int)($parts[1] ?? 0);
    $newVersion = ($revisionType === 'major')
        ? ($major + 1) . '.0'
        : $major . '.' . ($minor + 1);

    // Insert revision record
    $revStmt = $conn->prepare("
        INSERT INTO policyrevisiontbl
            (originalPolicyID, revisedPolicyID, versionNo, revisionType, revisionFormPath, changesDescription, submittedBy)
        VALUES (?, ?, ?, ?, ?, ?, ?)
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

    // Stamp the new policy row's versionNo
    $updStmt = $conn->prepare("UPDATE policytbl SET versionNo = ? WHERE policyID = ?");
    $updStmt->bind_param("si", $newVersion, $newPolicyID);
    $updStmt->execute();
    $updStmt->close();
}

$conn->close();

// Redirect to appropriate dashboard
$roleID = $_SESSION['roleID'] ?? 4;
switch ($roleID) {
    case 2:  header("Location: /qms_optiqual/QADSpecificComponents/QADMain/QAD-POV.php"); break;
    case 3:  header("Location: /qms_optiqual/QAPSpecificComponents/QAPMain/QAP-POV.php"); break;
    default: header("Location: /qms_optiqual/staffSpecificComponents/staffMain/staff-POV.php");
}
exit;