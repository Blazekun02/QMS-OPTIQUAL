<?php
ob_start(); 
session_start();
include __DIR__ . '/../../connect.php'; 

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['categoryID']) || !isset($data['newName'])) {
    ob_end_clean(); 
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing ID or Name']);
    exit;
}

$categoryID = $data['categoryID'];
$newName = $data['newName'];
$currentUserID = isset($_SESSION['accID']) ? $_SESSION['accID'] : 0;

$nameQuery = $conn->prepare("SELECT categoryName FROM categorytbl WHERE categoryID = ?");
$nameQuery->bind_param("i", $categoryID);
$nameQuery->execute();
$nameResult = $nameQuery->get_result();
$oldName = $nameResult->num_rows > 0 ? $nameResult->fetch_assoc()['categoryName'] : "A folder";
$nameQuery->close();

$stmt = $conn->prepare("UPDATE categorytbl SET categoryName = ? WHERE categoryID = ?");
$stmt->bind_param("si", $newName, $categoryID);

if ($stmt->execute()) {
    // ✨ NOTIFICATION SYSTEM
    $shortOld = substr($oldName, 0, 15);
    $shortNew = substr($newName, 0, 15);
    $message = "Renamed folder: '" . $shortOld . "' to '" . $shortNew . "'";

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