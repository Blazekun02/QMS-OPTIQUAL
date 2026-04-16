<?php
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['categoryID']) || !array_key_exists('newParentID', $data)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing data inputs.']);
    exit;
}

$categoryID = $data['categoryID'];
$newParentID = $data['newParentID'];

if ($newParentID === null) {
    // Move folder to Root
    $stmt = $conn->prepare("UPDATE categorytbl SET parentCategoryID = NULL WHERE categoryID = ?");
    $stmt->bind_param("i", $categoryID);
} else {
    // Prevent a folder from being dragged into itself
    if ($categoryID == $newParentID) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Cannot move a folder into itself.']);
        exit;
    }
    // Move folder inside another folder
    $stmt = $conn->prepare("UPDATE categorytbl SET parentCategoryID = ? WHERE categoryID = ?");
    $stmt->bind_param("ii", $newParentID, $categoryID);
}

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