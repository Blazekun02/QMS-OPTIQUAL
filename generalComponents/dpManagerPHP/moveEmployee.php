<?php
ob_start();
header('Content-Type: application/json');
session_start();
require_once '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

// Create a variable to hold the response instead of echoing it immediately
$response = [];

if (isset($data['accID']) && isset($data['oldDptID']) && isset($data['newDptID'])) {
    $accID = intval($data['accID']);
    $oldDptID = intval($data['oldDptID']);
    $newDptID = $data['newDptID'];

    // Prevent dropping a user into the background (they must be in a folder)
    if ($newDptID === 'root' || $newDptID === null) {
        $response = ['success' => false, 'message' => 'Employees must be dropped inside a folder.'];
    } else if ($oldDptID == $newDptID) {
        $response = ['success' => false, 'message' => 'Employee is already in this folder.'];
    } else {
        // Update the employee's folder assignment
        $stmt = $conn->prepare("UPDATE empperdeptbl SET dptID = ? WHERE accID = ? AND dptID = ?");
        $stmt->bind_param("iii", $newDptID, $accID, $oldDptID);

        if ($stmt->execute()) {
            $response = ['success' => true];
        } else {
            // If they drag the user into a folder they are ALREADY assigned to (duplicate role)
            if ($conn->errno == 1062) {
                $response = ['success' => false, 'message' => 'This user already has a role in that folder!'];
            } else {
                $response = ['success' => false, 'message' => 'Database error: ' . $stmt->error];
            }
        }
        $stmt->close();
    }
} else {
    $response = ['success' => false, 'message' => 'Missing parameters.'];
}

$conn->close();

// ✨ THE FIX: Clean the buffer FIRST, and THEN echo the response!
ob_end_clean();
echo json_encode($response);

?>