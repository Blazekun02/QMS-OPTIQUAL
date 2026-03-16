<?php
ob_start(); 
session_start();
include '../../connect.php'; 

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['categoryID'])) {
    ob_end_clean(); 
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing category ID']);
    exit;
}

$categoryID = $data['categoryID'];

// Delete the folder from the database
$stmt = $conn->prepare("DELETE FROM categorytbl WHERE categoryID = ?");
$stmt->bind_param("i", $categoryID);

if ($stmt->execute()) {
    $response = ['success' => true];
} else {
    // If this fails, it usually means the folder has child folders or policies inside it!
    $response = ['success' => false, 'message' => 'Cannot delete folder. It may contain child folders or policies.'];
}

$stmt->close();
$conn->close();

ob_end_clean(); 
header('Content-Type: application/json');
echo json_encode($response);
?>