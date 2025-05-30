<?php
header('Content-Type: application/json');
session_start();
require_once '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$departmentName = trim($data['departmentName'] ?? '');



if ($departmentName === '') {
    echo json_encode(['success' => false, 'message' => 'Department name is required.']);
    exit;
}


$stmt = $conn->prepare("INSERT INTO dorgtbl (dptName) VALUES (?)");
if ($stmt) {
    $stmt->bind_param("s", $departmentName);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
}
$conn->close();
?>