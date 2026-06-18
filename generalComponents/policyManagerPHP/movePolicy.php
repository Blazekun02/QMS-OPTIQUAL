<?php
ob_start();
session_start();
include __DIR__ . '/../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['policyId']) || !array_key_exists('newFolderId', $data)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing data inputs.']);
    exit;
}

$policyID = $data['policyId'];
$newFolderID = $data['newFolderId'];
$currentUserID = isset($_SESSION['accID']) ? $_SESSION['accID'] : 0;

$titleQuery = $conn->prepare("SELECT title, policyStatusID, policyAuthor FROM policytbl WHERE policyID = ?");
$titleQuery->bind_param("i", $policyID);
$titleQuery->execute();
$titleResult = $titleQuery->get_result();

$policyTitle = "A document";
$currentStatus = 0;
$policyAuthorID = null;
if ($titleResult->num_rows > 0) {
    $row = $titleResult->fetch_assoc();
    $policyTitle = $row['title'];
    $currentStatus = (int)$row['policyStatusID'];
    $policyAuthorID = $row['policyAuthor'];
}
$titleQuery->close();

if ($newFolderID === null) {
    $stmt = $conn->prepare("UPDATE policytbl SET categoryID = NULL WHERE policyID = ?");
    $stmt->bind_param("i", $policyID);
} else {
    $stmt = $conn->prepare("UPDATE policytbl SET categoryID = ? WHERE policyID = ?");
    $stmt->bind_param("ii", $newFolderID, $policyID);
}

if ($stmt->execute()) {
    // If it was just "Approved" (Status 4), mark as Published (5), clear tasks
    if ($currentStatus === 4 || $currentStatus === 5) {
        $updateStatus = $conn->prepare("UPDATE policytbl SET policyStatusID = 5, dateUploaded = COALESCE(dateUploaded, NOW()) WHERE policyID = ?");
        $updateStatus->bind_param("i", $policyID);
        $updateStatus->execute();
        $updateStatus->close();
        
        $deleteTask = $conn->prepare("DELETE FROM tasktbl WHERE policyAssigned = ?");
        $deleteTask->bind_param("i", $policyID);
        $deleteTask->execute();
        $deleteTask->close();
        
        if ($currentStatus === 4) { // Only notify if it wasn't already published
            $notifMessage = "Your policy '" . substr($policyTitle, 0, 50) . (strlen($policyTitle) > 50 ? '...' : '') . "' has been published!";
            $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus, dateTimeSent) VALUES (?, ?, 0, NOW())");
            $notifStmt->bind_param("is", $policyAuthorID, $notifMessage);
            $notifStmt->execute();
            $notifStmt->close();
        }
    }

    // ✨ NOTIFICATION SYSTEM
    $safeTitle = substr($policyTitle, 0, 25);
    $message = "Document moved: " . $safeTitle;

    // ✨ THE FIX: Search the main accounts table for Directors (Role 2) and QA Staff (Role 3)
    $notifyQuery = "SELECT accID FROM accdatatbl WHERE roleID IN (2, 3) AND accID != ?";
    $notifyStmt = $conn->prepare($notifyQuery);
    $notifyStmt->bind_param("i", $currentUserID);
    $notifyStmt->execute();
    $notifyResult = $notifyStmt->get_result();

    if ($notifyResult->num_rows > 0) {
        $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus, dateTimeSent) VALUES (?, ?, 0, NOW())");
        while ($row = $notifyResult->fetch_assoc()) {
            $notifStmt->bind_param("is", $row['accID'], $message);
            $notifStmt->execute();
        }
        $notifStmt->close();
    }
    $notifyStmt->close();

    $response = ['success' => true];
} else {
    $response = ['success' => false, 'message' => $stmt->error];
}

$stmt->close();
$conn->close();
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>