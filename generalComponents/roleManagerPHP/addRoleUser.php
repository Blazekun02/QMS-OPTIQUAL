<?php
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['accID'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing account ID']);
    exit;
}

$accID = $data['accID'];

// Change their role to 3 (QA Team Member)
$stmt = $conn->prepare("UPDATE accdatatbl SET roleID = 3 WHERE accID = ?");
$stmt->bind_param("i", $accID);

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