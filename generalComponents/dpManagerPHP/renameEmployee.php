<?php
// Prevent any HTML or error text from breaking the JSON response
ob_start();
header('Content-Type: application/json');
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['accID']) && isset($data['dptID']) && isset($data['newRole'])) {
    $stmt = $conn->prepare("UPDATE empperdeptbl SET departmentRole = ? WHERE accID = ? AND dptID = ?");
    
    if (!$stmt) {
        $response = ['success' => false, 'message' => 'Database prepare failed: ' . $conn->error];
    } else {
        $stmt->bind_param("sii", $data['newRole'], $data['accID'], $data['dptID']);
        if ($stmt->execute()) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'Database execute failed: ' . $stmt->error];
        }
        $stmt->close();
    }
} else {
    $response = ['success' => false, 'message' => 'Missing data. Ensure all fields are filled.'];
}

mysqli_close($conn);
$jsonOutput = json_encode($response);
ob_end_clean();
echo $jsonOutput;
?>