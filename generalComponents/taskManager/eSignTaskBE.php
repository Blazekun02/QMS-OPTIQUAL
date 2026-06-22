<?php
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$policyID = $data['policyID'];
$password = $data['password']; 
$accID = $_SESSION['accID'];

// 1. Verify Password
$stmt = $conn->prepare("SELECT password FROM accdatatbl WHERE accID = ?");
$stmt->bind_param("i", $accID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $dbPassword = $row['password'];
    
    $isPasswordCorrect = false;
    if (password_verify($password, $dbPassword)) {
        $isPasswordCorrect = true; 
    } else if ($password === $dbPassword) {
        $isPasswordCorrect = true; 
    }

    if (!$isPasswordCorrect) {
        echo json_encode(['success' => false, 'message' => 'Incorrect password. Signature failed.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}
$stmt->close();

// 2. Generate Cryptographic Hash
$signatureData = $accID . "-" . $policyID . "-" . microtime(true);
$digitalSignatureHash = hash('sha256', $signatureData);

// 3. Determine NEXT Status
$statusQuery = $conn->prepare("SELECT policyStatusID FROM policytbl WHERE policyID = ?");
$statusQuery->bind_param("i", $policyID);
$statusQuery->execute();
$statusResult = $statusQuery->get_result();

$currentStatus = 1; 
if ($statusResult->num_rows > 0) {
    $currentStatus = $statusResult->fetch_assoc()['policyStatusID'];
}
$statusQuery->close();

// ✨ Verify Authorization to Prevent Race Conditions
$isAuthorized = false;

// Check if they are explicitly assigned in tasktbl
$taskCheck = $conn->prepare("SELECT taskID FROM tasktbl WHERE policyAssigned = ? AND assignedTo = ?");
$taskCheck->bind_param("ii", $policyID, $accID);
$taskCheck->execute();
if ($taskCheck->get_result()->num_rows > 0) {
    $isAuthorized = true;
}
$taskCheck->close();

// If not in tasktbl, check roles for implicit authorization
if (!$isAuthorized) {
    $roleQuery = $conn->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
    $roleQuery->bind_param("i", $accID);
    $roleQuery->execute();
    $roleRes = $roleQuery->get_result();
    if ($roleRes->num_rows > 0) {
        $userRole = $roleRes->fetch_assoc()['roleID'];
        // QAP (Role 3) can universally review Status 1 policies
        if ($userRole == 3 && $currentStatus == 1) {
            $isAuthorized = true;
        }
        // QAD (Role 2) can universally approve Status 3 policies
        else if ($userRole == 2 && $currentStatus == 3) {
            $isAuthorized = true;
        }
    }
    $roleQuery->close();
}

if (!$isAuthorized) {
    echo json_encode(['success' => false, 'message' => 'Task is no longer available or you are not authorized to sign this document at its current stage.']);
    exit;
}

// ✨ 4. THE 4-STEP WORKFLOW LOGIC (Now with Name Tracking AND Date Stamping!)
if ($currentStatus == 1) {
    $newStatus = 2; // QA Staff signs -> becomes 2 (Reviewed)
    // ✨ ADDED: dateReviewed = NOW()
    $updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = ?, policyReviewer = ?, dateReviewed = NOW() WHERE policyID = ?");
    $updatePolicy->bind_param("iii", $newStatus, $accID, $policyID);

} else if ($currentStatus == 2) {
    $newStatus = 3; // Verifier signs -> becomes 3 (Verified)
    // ✨ ADDED: dateVerified = NOW()
    $updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = ?, policyVerifier = ?, dateVerified = NOW() WHERE policyID = ?");
    $updatePolicy->bind_param("iii", $newStatus, $accID, $policyID);

} else if ($currentStatus == 3) {
    $newStatus = 4; // Approver signs -> becomes 4 (Approved)
    // ✨ ADDED: dateApproved = NOW()
    $updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = ?, policyApprover = ?, dateApproved = NOW() WHERE policyID = ?");
    $updatePolicy->bind_param("iii", $newStatus, $accID, $policyID);

} else {
    // Fallback
    $newStatus = $currentStatus;
    $updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = ? WHERE policyID = ?");
    $updatePolicy->bind_param("ii", $newStatus, $policyID);
}
if ($updatePolicy->execute()) {
    // Clear from tasktbl so it leaves the inbox
    $completeTask = $conn->prepare("DELETE FROM tasktbl WHERE policyAssigned = ?");
    $completeTask->bind_param("i", $policyID);
    $completeTask->execute();
    
    // ====================================================================
    // ✨ REVISION REPLACEMENT LOGIC ✨
    // ====================================================================
    if ($newStatus == 4) { // Only when fully approved
        $revQuery = $conn->prepare("SELECT originalPolicyID FROM policytbl WHERE policyID = ?");
        if ($revQuery) {
            $revQuery->bind_param("i", $policyID);
            $revQuery->execute();
            $revResult = $revQuery->get_result();
            if ($revResult->num_rows > 0) {
                $revRow = $revResult->fetch_assoc();
                if (!empty($revRow['originalPolicyID'])) {
                    $origID = $revRow['originalPolicyID'];
                    
                    // 1. Inherit the folder (categoryID) of the original policy family
                    $catQuery = $conn->prepare("SELECT categoryID FROM policytbl WHERE (policyID = ? OR originalPolicyID = ?) AND categoryID IS NOT NULL ORDER BY policyID DESC LIMIT 1");
                    if ($catQuery) {
                        $catQuery->bind_param("ii", $origID, $origID);
                        $catQuery->execute();
                        $catResult = $catQuery->get_result();
                        if ($catResult->num_rows > 0) {
                            $catRow = $catResult->fetch_assoc();
                            $origCatID = $catRow['categoryID'];
                            
                            if ($origCatID !== null) {
                                $updateCat = $conn->prepare("UPDATE policytbl SET categoryID = ?, policyStatusID = 5, dateUploaded = NOW() WHERE policyID = ?");
                                $updateCat->bind_param("ii", $origCatID, $policyID);
                                $updateCat->execute();
                                $updateCat->close();
                            } else {
                                $updateCat = $conn->prepare("UPDATE policytbl SET categoryID = NULL, policyStatusID = 5, dateUploaded = NOW() WHERE policyID = ?");
                                $updateCat->bind_param("i", $policyID);
                                $updateCat->execute();
                                $updateCat->close();
                            }
                        } else {
                            $updateCat = $conn->prepare("UPDATE policytbl SET categoryID = NULL, policyStatusID = 5, dateUploaded = NOW() WHERE policyID = ?");
                            $updateCat->bind_param("i", $policyID);
                            $updateCat->execute();
                            $updateCat->close();
                        }
                        $catQuery->close();
                    }
                    
                    // 2. ✨ Archive ALL older policies in this family (use Status 7 = Archived) AND move their files.
                    $archiveDir = __DIR__ . '/../../files/archives/';
                    $targetDir = __DIR__ . '/../../files/';
                    if (!file_exists($archiveDir)) {
                        @mkdir($archiveDir, 0777, true);
                    }

                    $oldPoliciesQuery = $conn->prepare("SELECT policyID, contentPath FROM policytbl WHERE (policyID = ? OR originalPolicyID = ?) AND policyID != ? AND policyStatusID IN (4, 5)");
                    if ($oldPoliciesQuery) {
                        $oldPoliciesQuery->bind_param("iii", $origID, $origID, $policyID);
                        $oldPoliciesQuery->execute();
                        $oldPoliciesResult = $oldPoliciesQuery->get_result();

                        while ($oldPolicy = $oldPoliciesResult->fetch_assoc()) {
                            $oldPolicyID = $oldPolicy['policyID'];
                            $oldRelativePath = $oldPolicy['contentPath'];

                            if ($oldRelativePath && strpos($oldRelativePath, 'archives') === false && is_writable($archiveDir)) {
                                $oldFileName = basename($oldRelativePath);
                                $oldFullPath = $targetDir . $oldFileName;
                                $newArchiveFullPath = $archiveDir . $oldFileName;
                                $newArchiveRelativePath = '/qms_optiqual/files/archives/' . $oldFileName;

                                if (file_exists($oldFullPath) && !file_exists($newArchiveFullPath)) {
                                    if (rename($oldFullPath, $newArchiveFullPath)) {
                                        // Now update the DB record for this specific old policy
                                        $archiveStmt = $conn->prepare("UPDATE policytbl SET policyStatusID = 7, contentPath = ? WHERE policyID = ?");
                                        $archiveStmt->bind_param("si", $newArchiveRelativePath, $oldPolicyID);
                                        $archiveStmt->execute();
                                        $archiveStmt->close();
                                    }
                                }
                            }
                        }
                        $oldPoliciesQuery->close();
                    }
                }
            }
            $revQuery->close();
        }
    }

    // ====================================================================
    // ✨ UNIVERSAL STATUS NOTIFICATION BLOCK ✨
    // ====================================================================
    
    // 1. Figure out what action just happened based on the new status
    $actionWord = "Reviewed"; 
    if ($newStatus == 3) $actionWord = "Verified";
    if ($newStatus == 4) $actionWord = "Approved";
    
    // 2. Fetch the original Author's ID and the Policy Title
    $authorQuery = $conn->prepare("SELECT policyAuthor, title FROM policytbl WHERE policyID = ?");
    $authorQuery->bind_param("i", $policyID); 
    $authorQuery->execute();
    $authorResult = $authorQuery->get_result();
    
    if ($authorResult->num_rows > 0) {
        $row = $authorResult->fetch_assoc();
        $authorID = $row['policyAuthor'];
        
        // Only send a notification if the author isn't the one who just clicked sign
        if ($authorID != $accID) {
            $shortTitle = substr($row['title'], 0, 20); 
            $message = "Your policy '" . $shortTitle . "...' was " . $actionWord . "!";
            
            // Send the notification directly to the Author
            $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus, dateTimeSent) VALUES (?, ?, 0, NOW())");
            $notifStmt->bind_param("is", $authorID, $message);
            $notifStmt->execute();
            $notifStmt->close();
        }
    }
    $authorQuery->close();
    // ====================================================================
    
    echo json_encode([
        'success' => true, 
        'signatureHash' => $digitalSignatureHash,
        'message' => 'Document successfully signed and advanced!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update document status.']);
}
$conn->close();
?>