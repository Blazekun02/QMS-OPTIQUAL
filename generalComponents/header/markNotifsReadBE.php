<?php
ob_start();
session_start();
include '../../connect.php'; 

// Catch the JSON sent from JavaScript
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['accID']) || !isset($data['notifID'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing session or ID.']);
    exit;
}

$accID = $_SESSION['accID'];
$notifID = $data['notifID'];

// ✨ UPGRADED: Now it only updates the exact Notification ID that was clicked!
$stmt = $conn->prepare("UPDATE notiftbl SET notifStatus = 1 WHERE receivedBy = ? AND notifID = ?");
if ($stmt) {
    $stmt->bind_param("ii", $accID, $notifID);
    $stmt->execute();
    $stmt->close();
    
    $response = ['success' => true];
} else {
    $response = ['success' => false, 'message' => 'Database error: ' . $conn->error];
}

ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>