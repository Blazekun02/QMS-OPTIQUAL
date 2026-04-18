<?php
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['accIDs']) || !is_array($data['accIDs'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No users selected']);
    exit;
}

$accIDs = $data['accIDs'];
$defaultRole = 4; // 4 = Staff (Returning them to standard users)

// Create placeholders for the SQL IN clause (?, ?, ?)
$placeholders = implode(',', array_fill(0, count($accIDs), '?'));
$types = str_repeat('i', count($accIDs));

$stmt = $conn->prepare("UPDATE accdatatbl SET roleID = ? WHERE accID IN ($placeholders)");

// Bind the default role first, then all the account IDs
$stmt->bind_param("i" . $types, $defaultRole, ...$accIDs);

if ($stmt->execute()) {
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