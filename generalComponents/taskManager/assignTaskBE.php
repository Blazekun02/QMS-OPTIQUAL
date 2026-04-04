<?php
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$policyID = $data['policyID'];
$assigneeID = $data['assigneeID'];
$roleType = $data['roleType']; 
$assignedBy = $_SESSION['accID'];

// Task Type: 2 = Verification/Review, 3 = Approval
$taskTypeID = ($roleType === 'Verifier') ? 2 : 3;

// ONLY insert the task. Do NOT change the policy status here! 
$stmt = $conn->prepare("INSERT INTO tasktbl (policyAssigned, assignedTo, assignedBy, taskTypeID) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $policyID, $assigneeID, $assignedBy, $taskTypeID);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>