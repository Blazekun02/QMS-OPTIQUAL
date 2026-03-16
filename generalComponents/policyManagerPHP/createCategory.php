<?php
// 1. Trap any accidental HTML or echoes (like the alert from connect.php)
ob_start(); 

session_start();
include '../../connect.php'; // Make sure this path correctly points to your connect.php

// Get the JSON sent by JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['categoryName'])) {
    ob_end_clean(); // Empty the trap
    header('Content-Type: application/json');
    // FIXED: Changed json_stringify to json_encode
    echo json_encode(['success' => false, 'message' => 'Missing category name']);
    exit;
}

$categoryName = $data['categoryName'];
$parentCategoryID = (isset($data['parentCategoryID']) && $data['parentCategoryID'] !== null) ? $data['parentCategoryID'] : null;

// Prepare the SQL statement
if ($parentCategoryID === null) {
    // It is a Parent Folder
    $stmt = $conn->prepare("INSERT INTO categorytbl (categoryName, parentCategoryID) VALUES (?, NULL)");
    $stmt->bind_param("s", $categoryName);
} else {
    // It is a Child Folder
    $stmt = $conn->prepare("INSERT INTO categorytbl (categoryName, parentCategoryID) VALUES (?, ?)");
    $stmt->bind_param("si", $categoryName, $parentCategoryID);
}

// Execute the query
if ($stmt->execute()) {
    $response = [
        'success' => true, 
        'categoryID' => $stmt->insert_id
    ];
} else {
    $response = ['success' => false, 'message' => $stmt->error];
}

$stmt->close();
$conn->close();

// 2. Wipe away any accidental HTML/Alerts trapped earlier
ob_end_clean(); 

// 3. Send pure, clean JSON back to JavaScript
header('Content-Type: application/json');
// FIXED: Changed json_stringify to json_encode
echo json_encode($response);
?>