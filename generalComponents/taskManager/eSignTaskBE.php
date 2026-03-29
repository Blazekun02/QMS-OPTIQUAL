<?php
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$policyID = $data['policyID'];
$password = $data['password']; 
$accID = $_SESSION['accID'];

// 1. Verify the User's Password
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

// 2. Generate the Cryptographic E-Signature (The "Barcode")
$signatureData = $accID . "-" . $policyID . "-" . microtime(true);
$digitalSignatureHash = hash('sha256', $signatureData);

// ✨ 3. THE FIX: Determine the NEXT Status dynamically!
$statusQuery = $conn->prepare("SELECT policyStatusID FROM policytbl WHERE policyID = ?");
$statusQuery->bind_param("i", $policyID);
$statusQuery->execute();
$statusResult = $statusQuery->get_result();

$currentStatus = 3; // Fallback
if ($statusResult->num_rows > 0) {
    $currentStatus = $statusResult->fetch_assoc()['policyStatusID'];
}
$statusQuery->close();

// If it is currently Pending(1) or Reviewed(2), signing it makes it Verified(3)
// If it is already Verified(3), signing it makes it Approved(4)!
$newStatus = 3; 
if ($currentStatus == 1 || $currentStatus == 2) {
    $newStatus = 3; 
} else if ($currentStatus == 3) {
    $newStatus = 4; 
}

// 4. Complete the Task & Update Policy Status
$updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = ? WHERE policyID = ?");
$updatePolicy->bind_param("ii", $newStatus, $policyID);

if ($updatePolicy->execute()) {
    
    // Mark the task as completed by deleting the assignment
    $completeTask = $conn->prepare("DELETE FROM tasktbl WHERE policyAssigned = ? AND assignedTo = ?");
    $completeTask->bind_param("ii", $policyID, $accID);
    $completeTask->execute();
    
    echo json_encode([
        'success' => true, 
        'signatureHash' => $digitalSignatureHash,
        'message' => 'Document successfully signed and verified!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update document status.']);
}

$conn->close();
?>