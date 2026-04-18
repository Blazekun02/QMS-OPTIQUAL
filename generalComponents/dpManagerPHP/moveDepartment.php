<?php
// moveDepartment.php
ob_start();
header('Content-Type: application/json');
session_start();
require_once '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['departmentId']) && array_key_exists('newParentId', $data)) {
    $departmentId = intval($data['departmentId']);
    $newParentId = $data['newParentId']; // Can be a number or null

    // Prevent a folder from being dropped into itself
    if ($departmentId === $newParentId) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Cannot move a folder into itself.']);
        exit;
    }

    // Prepare the SQL based on whether it's moving to the root or into another folder
    if ($newParentId === null) {
        $stmt = $conn->prepare("UPDATE dorgtbl SET dptParentID = NULL WHERE dptID = ?");
        $stmt->bind_param("i", $departmentId);
    } else {
        $stmt = $conn->prepare("UPDATE dorgtbl SET dptParentID = ? WHERE dptID = ?");
        $stmt->bind_param("ii", $newParentId, $departmentId);
    }

    if ($stmt->execute()) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'message' => 'Database error: ' . $stmt->error];
    }
    
    $stmt->close();
} else {
    $response = ['success' => false, 'message' => 'Missing parameters'];
}

$conn->close();
ob_end_clean();
echo json_encode($response);
?>