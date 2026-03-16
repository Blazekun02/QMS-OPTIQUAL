<?php
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['categoryID']) || !isset($data['policyID'])) {
    $response = ['success' => false, 'message' => 'Missing folder or policy ID'];
} else {
    $categoryID = $data['categoryID'];
    $policyID = $data['policyID'];

    // Link it to the folder AND change status to 5 (Uploaded)
    $stmt = $conn->prepare("UPDATE policytbl SET categoryID = ?, policyStatusID = 5 WHERE policyID = ? AND policyStatusID = 4");
    $stmt->bind_param("ii", $categoryID, $policyID);

    if ($stmt->execute()) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'message' => $stmt->error];
    }
    $stmt->close();
}

mysqli_close($conn);
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>