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
    
    // Link this assignee to the policy so the QAD's fetchTasks.php knows it has been handled
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

    // ✨ NEW: Fetch the Assignee's full name from the database!
    $nameStmt = $conn->prepare("SELECT fullName FROM accdatatbl WHERE accID = ?");
    $nameStmt->bind_param("i", $assigneeID);
    $nameStmt->execute();
    $nameResult = $nameStmt->get_result();
    $assigneeName = "User ID " . $assigneeID; // Fallback just in case
    if ($nameResult->num_rows > 0) {
        $assigneeName = $nameResult->fetch_assoc()['fullName'];
    }
    $nameStmt->close();

    // 2. Insert notification for the RECEIVER (The Employee)
    $notifMessage = "You have been assigned a new task as a " . $roleType . ".";
    $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus) VALUES (?, ?, 0)");
    if ($notifStmt) {
        $notifStmt->bind_param("is", $assigneeID, $notifMessage);
        $notifStmt->execute();
        $notifStmt->close();
    }

    // 3. Insert a confirmation notification for the SENDER (The QAD)
    // ✨ FIX: It now uses the assignee's name!
    $qadReceiptMessage = "You successfully assigned a task to " . $assigneeName . ".";
    $receiptStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus) VALUES (?, ?, 0)");
    if ($receiptStmt) {
        $receiptStmt->bind_param("is", $assignedBy, $qadReceiptMessage);
        $receiptStmt->execute();
        $receiptStmt->close();
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