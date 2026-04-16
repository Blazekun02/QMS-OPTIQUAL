<?php
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['policyId']) || !array_key_exists('newFolderId', $data)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing data inputs.']);
    exit;
}

$policyID = $data['policyId'];
$newFolderID = $data['newFolderId'];

if ($newFolderID === null) {
    // Move policy to Root
    $stmt = $conn->prepare("UPDATE policytbl SET categoryID = NULL WHERE policyID = ?");
    $stmt->bind_param("i", $policyID);
} else {
    // Move policy inside a folder
    $stmt = $conn->prepare("UPDATE policytbl SET categoryID = ? WHERE policyID = ?");
    $stmt->bind_param("ii", $newFolderID, $policyID);
}

if ($stmt->execute()) {
    $response = ['success' => true];
} else {
    $response = ['success' => false, 'message' => $stmt->error];
}

$stmt->close();
$conn->close();

ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>