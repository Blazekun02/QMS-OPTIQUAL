<?php
session_start();
include '../connect.php'; // Adjust this path if connect.php is located elsewhere

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $policyTitle = isset($input['policyTitle']) ? trim($input['policyTitle']) : '';
    $isRevision = isset($input['isRevision']) ? $input['isRevision'] : false;
    $originalPolicyID = isset($input['originalPolicyID']) ? $input['originalPolicyID'] : null;

    if (empty($policyTitle)) {
        echo json_encode(['error' => 'Policy title is empty.']);
        exit;
    }

    if ($isRevision && !empty($originalPolicyID)) {
        // Exclude the original policy from the check, allowing the same title for a revision
        $stmt = $conn->prepare("SELECT policyID FROM policytbl WHERE title = ? AND policyID != ?");
        if ($stmt) {
            $stmt->bind_param("si", $policyTitle, $originalPolicyID);
            $stmt->execute();
            $stmt->store_result();
            
            echo json_encode(['exists' => $stmt->num_rows > 0]);
            $stmt->close();
        } else {
            echo json_encode(['error' => 'Database query failed.']);
        }
    } else {
        // Search the database for any policy bearing this title
        $stmt = $conn->prepare("SELECT policyID FROM policytbl WHERE title = ?");
        if ($stmt) {
            $stmt->bind_param("s", $policyTitle);
            $stmt->execute();
            $stmt->store_result();
            
            echo json_encode(['exists' => $stmt->num_rows > 0]);
            $stmt->close();
        } else {
            echo json_encode(['error' => 'Database query failed.']);
        }
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>