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

// 1. Insert the task into the Verifier's/Approver's inbox
$stmt = $conn->prepare("INSERT INTO tasktbl (policyAssigned, assignedTo, assignedBy, taskTypeID) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $policyID, $assigneeID, $assignedBy, $taskTypeID);

if ($stmt->execute()) {
    
    // Do NOT set the completed verifier/approver here.
    // The assigned task is tracked in tasktbl, while the actual signer is stored when they complete verification or approval.

    // ✨ NEW: Fetch the exact Policy Title from the database
    $policyTitle = "a document"; // Fallback
    $titleStmt = $conn->prepare("SELECT title FROM policytbl WHERE policyID = ?");
    $titleStmt->bind_param("i", $policyID);
    $titleStmt->execute();
    $titleResult = $titleStmt->get_result();
    if ($titleResult->num_rows > 0) {
        $policyTitle = $titleResult->fetch_assoc()['title'];
    }
    $titleStmt->close();

    // Fetch the Assignee's full name from the database
    $nameStmt = $conn->prepare("SELECT fullName FROM accdatatbl WHERE accID = ?");
    $nameStmt->bind_param("i", $assigneeID);
    $nameStmt->execute();
    $nameResult = $nameStmt->get_result();
    $assigneeName = "User ID " . $assigneeID; // Fallback
    if ($nameResult->num_rows > 0) {
        $assigneeName = $nameResult->fetch_assoc()['fullName'];
    }
    $nameStmt->close();

    // 2. Insert notification for the RECEIVER (The Employee)
    // ✨ UPGRADED: Now includes the Role AND the Policy Title!
    $notifMessage = "You have been assigned as a " . $roleType . " for the document: '" . $policyTitle . "'.";
    $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus) VALUES (?, ?, 0)");
    if ($notifStmt) {
        $notifStmt->bind_param("is", $assigneeID, $notifMessage);
        $notifStmt->execute();
        $notifStmt->close();
    }

    // 3. Insert a confirmation notification for the SENDER (The QAD)
    // ✨ UPGRADED: Now includes the Policy Title here too!
    $qadReceiptMessage = "You successfully assigned " . $assigneeName . " to the document: '" . $policyTitle . "'.";
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