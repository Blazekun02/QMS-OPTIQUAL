<?php
ob_start();
session_start();
// ✨ THE FIX: Jump back TWO folders to reach the root connect.php
include '../../connect.php'; 

if (!isset($_SESSION['accID'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'No session found.']);
    exit;
}

$accID = $_SESSION['accID'];

$stmt = $conn->prepare("UPDATE notiftbl SET notifStatus = 1 WHERE receivedBy = ? AND notifStatus = 0");
if ($stmt) {
    $stmt->bind_param("i", $accID);
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