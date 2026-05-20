<?php
ob_start();
session_start();
include __DIR__ . '/../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['policyID'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing policy ID']);
    exit;
}

$policyID = $data['policyID'];
$currentUserID = isset($_SESSION['accID']) ? $_SESSION['accID'] : 0;

$infoQuery = $conn->prepare("
    SELECT p.title, c.categoryName 
    FROM policytbl p 
    LEFT JOIN categorytbl c ON p.categoryID = c.categoryID 
    WHERE p.policyID = ?
");
$infoQuery->bind_param("i", $policyID);
$infoQuery->execute();
$infoResult = $infoQuery->get_result();

$policyTitle = "Document";
$folderName = "a folder";

if ($infoResult->num_rows > 0) {
    $row = $infoResult->fetch_assoc();
    if (!empty($row['title'])) $policyTitle = $row['title'];
    if (!empty($row['categoryName'])) $folderName = $row['categoryName'];
}
$infoQuery->close();

$stmt = $conn->prepare("UPDATE policytbl SET categoryID = NULL, policyStatusID = 4 WHERE policyID = ?");
$stmt->bind_param("i", $policyID);

if ($stmt->execute()) {
    // ✨ NOTIFICATION SYSTEM
    $shortTitle = substr($policyTitle, 0, 20);
    $message = "Removed: " . $shortTitle . " from folder";

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