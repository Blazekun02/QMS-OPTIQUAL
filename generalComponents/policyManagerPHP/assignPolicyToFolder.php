<?php
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

// ✨ FIX: Use array_key_exists for categoryID because it is allowed to be 'null'
if (!array_key_exists('categoryID', $data) || !isset($data['policyID'])) {
    $response = ['success' => false, 'message' => 'Missing folder or policy ID'];
} else {
    $categoryID = $data['categoryID']; // This could be a number OR null
    $policyID = $data['policyID'];

    // ✨ FIX: Safely route the SQL depending on if it goes in a folder or the root!
    if ($categoryID === null) {
        // Upload to Root (No folder)
        $stmt = $conn->prepare("UPDATE policytbl SET categoryID = NULL, policyStatusID = 5 WHERE policyID = ? AND policyStatusID = 4");
        $stmt->bind_param("i", $policyID);
    } else {
        // Upload inside a specific folder
        $stmt = $conn->prepare("UPDATE policytbl SET categoryID = ?, policyStatusID = 5 WHERE policyID = ? AND policyStatusID = 4");
        $stmt->bind_param("ii", $categoryID, $policyID);
    }

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