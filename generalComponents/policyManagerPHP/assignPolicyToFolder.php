<?php
ob_start();
session_start();
include __DIR__ . '/../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!array_key_exists('categoryID', $data) || !isset($data['policyID'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing folder or policy ID']);
    exit;
}

$categoryID = $data['categoryID']; 
$policyID = $data['policyID'];
$currentUserID = isset($_SESSION['accID']) ? $_SESSION['accID'] : 0;

// Get the policy title
$titleQuery = $conn->prepare("SELECT title FROM policytbl WHERE policyID = ?");
$titleQuery->bind_param("i", $policyID);
$titleQuery->execute();
$titleResult = $titleQuery->get_result();
$policyTitle = $titleResult->num_rows > 0 ? $titleResult->fetch_assoc()['title'] : "A document";
$titleQuery->close();

if ($categoryID === null) {
    $stmt = $conn->prepare("UPDATE policytbl SET categoryID = NULL, policyStatusID = 5 WHERE policyID = ? AND policyStatusID = 4");
    $stmt->bind_param("i", $policyID);
} else {
    $stmt = $conn->prepare("UPDATE policytbl SET categoryID = ?, policyStatusID = 5 WHERE policyID = ? AND policyStatusID = 4");
    $stmt->bind_param("ii", $categoryID, $policyID);
}

if ($stmt->execute()) {
    // ✨ NOTIFICATION SYSTEM
    $safeTitle = substr($policyTitle, 0, 25);
    $message = "New document added: " . $safeTitle;

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
mysqli_close($conn);
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>