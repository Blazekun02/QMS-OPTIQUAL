<?php
header('Content-Type: application/json');
session_start();
require_once '../../connect.php';

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Validate the input
if (isset($data['departmentId'], $data['newDepartmentName'])) {
    $departmentId = intval($data['departmentId']);
    $newDepartmentName = trim($data['newDepartmentName']);

    if ($departmentId && $newDepartmentName) {
        // Prepare the SQL query to update the department name
        $stmt = $conn->prepare("UPDATE dorgtbl SET dptName = ? WHERE dptID = ?");
        $stmt->bind_param("si", $newDepartmentName, $departmentId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
}

$conn->close();
?>