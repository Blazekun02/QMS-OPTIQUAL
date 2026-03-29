<?php
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$policyID = $data['policyID'];
$assigneeID = $data['assigneeID'];
$roleType = $data['roleType']; // "Verifier" or "Approver"
$assignedBy = $_SESSION['accID'];

// Determine task type and new policy status based on your QMS rules
// *You may need to adjust these integer IDs to match your tasktypetbl and policystatustbl!*
if ($roleType === 'Verifier') {
    $taskTypeID = 2; // Assuming 2 = 'For Verification/Review'
    $newPolicyStatusID = 2; 
} else {
    $taskTypeID = 3; // Assuming 3 = 'For Approval'
    $newPolicyStatusID = 3; 
}

// 1. Assign the task to the user
$stmt = $conn->prepare("INSERT INTO tasktbl (policyAssigned, assignedTo, assignedBy, taskTypeID) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $policyID, $assigneeID, $assignedBy, $taskTypeID);

if ($stmt->execute()) {
    // 2. Update the Policy's main status to reflect the workflow advancement
    $updateStmt = $conn->prepare("UPDATE policytbl SET policyStatusID = ? WHERE policyID = ?");
    $updateStmt->bind_param("ii", $newPolicyStatusID, $policyID);
    
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Task assigned, but failed to update policy status.']);
    }
    $updateStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>