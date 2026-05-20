<?php
// Prevent any HTML errors from breaking the JSON
ob_start();
session_start();
include __DIR__ . '/../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

// Catch the IDs (checking both camelCase and PascalCase just in case your JS uses either)
$policyID = $data['policyID'] ?? $data['policyId'] ?? null;
$categoryID = $data['categoryID'] ?? $data['folderId'] ?? null;

if (!$policyID || !$categoryID) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing Policy ID or Folder ID']);
    exit;
}

// ✨ THE ACTUAL FIX: The query to move the policy into the folder!
$stmt = $conn->prepare("UPDATE policytbl SET categoryID = ? WHERE policyID = ?");
$stmt->bind_param("ii", $categoryID, $policyID);

if ($stmt->execute()) {
    $response = ['success' => true];
} else {
    $response = ['success' => false, 'message' => 'Database error: ' . $stmt->error];
}

$stmt->close();
$conn->close();

// Send the proper JSON success response back to JavaScript
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>