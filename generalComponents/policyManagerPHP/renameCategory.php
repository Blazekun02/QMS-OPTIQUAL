<?php
ob_start(); 
session_start();
include '../../connect.php'; 

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['categoryID']) || !isset($data['newName'])) {
    ob_end_clean(); 
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing ID or Name']);
    exit;
}

$categoryID = $data['categoryID'];
$newName = $data['newName'];

// Update the folder name in the database
$stmt = $conn->prepare("UPDATE categorytbl SET categoryName = ? WHERE categoryID = ?");
$stmt->bind_param("si", $newName, $categoryID);

if ($stmt->execute()) {
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