<?php
header('Content-Type: application/json');
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['departmentId'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing department ID']);
    exit;
}

$departmentId = $data['departmentId'];

try {
    // ✨ STEP 1: Safely remove all employees assigned to this specific folder
    $delEmp = $conn->prepare("DELETE FROM empperdeptbl WHERE dptID = ?");
    $delEmp->bind_param("i", $departmentId);
    $delEmp->execute();
    $delEmp->close();

    // ✨ STEP 2: Evacuate Sub-Folders! Move any child folders to the Root level
    $moveChildren = $conn->prepare("UPDATE dorgtbl SET dptParentID = NULL WHERE dptParentID = ?");
    $moveChildren->bind_param("i", $departmentId);
    $moveChildren->execute();
    $moveChildren->close();

    // ✨ STEP 3: Now that the folder is empty, safely delete it!
    $stmt = $conn->prepare("DELETE FROM dorgtbl WHERE dptID = ?");
    if (!$stmt) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("i", $departmentId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'Department not found or already deleted.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Database error: ' . $stmt->error];
    }

    $stmt->close();
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    exit;
}

mysqli_close($conn);

ob_end_clean();
echo json_encode($response);
?>