<?php
header('Content-Type: application/json');
session_start();  
require_once '../../connect.php';
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['departmentId'])) {
    echo json_encode(['success' => false, 'message' => 'No department ID provided']);
    exit;
}

$departmentId = intval($data['departmentId']);

$stmt = $conn->prepare("DELETE FROM dorgtbl WHERE dptID = ?");
$stmt->bind_param('i', $departmentId);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete department']);
}
$stmt->close();
$conn->close();
?>