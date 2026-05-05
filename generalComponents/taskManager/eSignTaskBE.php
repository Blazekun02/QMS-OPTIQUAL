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

// ✨ 4. THE 4-STEP WORKFLOW LOGIC (Now with Name Tracking!)
if ($currentStatus == 1) {
    $newStatus = 2; // QA Staff signs -> becomes 2 (Reviewed)
    $updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = ?, reviewedBy = ? WHERE policyID = ?");
    $updatePolicy->bind_param("iii", $newStatus, $accID, $policyID);
} else if ($currentStatus == 2) {
    $newStatus = 3; // Verifier signs -> becomes 3 (Verified)
    $updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = ?, verifiedBy = ? WHERE policyID = ?");
    $updatePolicy->bind_param("iii", $newStatus, $accID, $policyID);
} else if ($currentStatus == 3) {
    $newStatus = 4; // Approver signs -> becomes 4 (Approved)
    $updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = ?, approvedBy = ? WHERE policyID = ?");
    $updatePolicy->bind_param("iii", $newStatus, $accID, $policyID);
} else {
    // Fallback
    $newStatus = $currentStatus;
    $updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = ? WHERE policyID = ?");
    $updatePolicy->bind_param("ii", $newStatus, $policyID);
}

if ($updatePolicy->execute()) {
    // Clear from tasktbl so it leaves the inbox
    $completeTask = $conn->prepare("DELETE FROM tasktbl WHERE policyAssigned = ? AND assignedTo = ?");
    $completeTask->bind_param("ii", $policyID, $accID);
    $completeTask->execute();
    
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