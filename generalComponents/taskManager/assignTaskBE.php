<?php
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['policyID']) || !isset($data['assigneeID'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing data inputs.']);
    exit;
}

$policyID = $data['policyID'];
$assigneeID = $data['assigneeID'];
$roleType = $data['roleType']; 
$assignedBy = $_SESSION['accID'];

// Task Type: 2 = Verification/Review, 3 = Approval
$taskTypeID = ($roleType === 'Verifier') ? 2 : 3;

// 1. Insert the task into the Verifier's inbox
$stmt = $conn->prepare("INSERT INTO tasktbl (policyAssigned, assignedTo, assignedBy, taskTypeID) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $policyID, $assigneeID, $assignedBy, $taskTypeID);

if ($stmt->execute()) {
    
    // ✨ THE FIX: Link this assignee to the policy so the QAD's fetchTasks.php knows it has been handled!
    if ($roleType === 'Verifier') {
        $updatePolicy = $conn->prepare("UPDATE policytbl SET policyVerifier = ? WHERE policyID = ?");
        $updatePolicy->bind_param("ii", $assigneeID, $policyID);
        $updatePolicy->execute();
        $updatePolicy->close();
    } else {
        $updatePolicy = $conn->prepare("UPDATE policytbl SET policyApprover = ? WHERE policyID = ?");
        $updatePolicy->bind_param("ii", $assigneeID, $policyID);
        $updatePolicy->execute();
        $updatePolicy->close();
    }

    // 2. Insert notification
    $notifMessage = "You have been assigned a new task as a " . $roleType . ".";
    
    // ✨ THE FIX: Changed to 'receivedBy'
    $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus) VALUES (?, ?, 0)");
    if ($notifStmt) {
        $notifStmt->bind_param("is", $assigneeID, $notifMessage);
        $notifStmt->execute();
        $notifStmt->close();
    }

    $response = ['success' => true];
} else {
    $response = ['success' => false, 'message' => $stmt->error];
}
$stmt->close();

// Safely output pure JSON
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>