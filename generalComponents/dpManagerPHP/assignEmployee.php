<?php
// Start output buffering to trap any weird PHP warnings
ob_start(); 
header('Content-Type: application/json');
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['accID']) || !isset($data['dptID']) || !isset($data['departmentRole'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing account, department, or role data']);
    exit;
}

$accID = $data['accID'];
$dptID = $data['dptID'];
$role = $data['departmentRole'];

try {
    $stmt = $conn->prepare("INSERT INTO empperdeptbl (accID, dptID, departmentRole) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $accID, $dptID, $role);

    if ($stmt->execute()) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'message' => $stmt->error];
    }
    
    $stmt->close();
} catch (Exception $e) {
    // 🐛 CRITICAL FIX: Catch the SQL error instead of crashing PHP!
    
    // Check if it is a duplicate entry error (MySQL Error Code 1062)
    if ($e->getCode() == 1062) {
        $response = ['success' => false, 'message' => 'This employee is already assigned to this department or role!'];
    } else {
        // Catch any other weird SQL errors
        $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

mysqli_close($conn);

// Clear the buffer of any HTML warnings and strictly output the JSON
ob_end_clean();
echo json_encode($response);
?>