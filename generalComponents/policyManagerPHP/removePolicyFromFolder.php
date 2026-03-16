<?php
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['policyID'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing policy ID']);
    exit;
}

$policyID = $data['policyID'];

// Remove from folder (set categoryID to NULL) and revert status back to 4 (Approved)
$stmt = $conn->prepare("UPDATE policytbl SET categoryID = NULL, policyStatusID = 4 WHERE policyID = ?");
$stmt->bind_param("i", $policyID);

if ($stmt->execute()) {
    $response = ['success' => true];
} else {
    $response = ['success' => false, 'message' => $stmt->error];
}

$stmt->close();
mysqli_close($conn);

ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>