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
    // First check if there are any foreign key constraints that would prevent deletion
    $checkConstraints = $conn->query("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                                      FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                                      WHERE REFERENCED_TABLE_NAME = 'dorgtbl' AND REFERENCED_COLUMN_NAME = 'dptID'");
    
    $hasConstraints = $checkConstraints->num_rows > 0;
    
    // Try to delete the department
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
        // Check the error code
        $errorCode = $conn->errno;
        if ($errorCode == 1451) {
            // Foreign key constraint violation
            $response = ['success' => false, 'message' => 'Cannot delete: This department is referenced by other records. Remove dependencies first.'];
        } else {
            $response = ['success' => false, 'message' => 'Database error: ' . $stmt->error . ' (Code: ' . $errorCode . ')'];
        }
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