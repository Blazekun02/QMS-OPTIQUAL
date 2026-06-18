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

// Get current policy status and author
$query = $conn->prepare("SELECT policyStatusID, policyAuthor, title FROM policytbl WHERE policyID = ?");
$query->bind_param("i", $policyID);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Policy not found']);
    exit;
}

$policyData = $result->fetch_assoc();
$currentStatus = (int)$policyData['policyStatusID'];
$policyAuthorID = $policyData['policyAuthor'];
$policyTitle = $policyData['title'];
$query->close();

// ✨ THE ACTUAL FIX: The query to move the policy into the folder!
$stmt = $conn->prepare("UPDATE policytbl SET categoryID = ? WHERE policyID = ?");
$stmt->bind_param("ii", $categoryID, $policyID);

if ($stmt->execute()) {
    // If it was just "Approved" (Status 4), mark as Published (5), clear tasks, and notify
    if ($currentStatus === 4 || $currentStatus === 5) {
        $updateStatus = $conn->prepare("UPDATE policytbl SET policyStatusID = 5, dateUploaded = COALESCE(dateUploaded, NOW()) WHERE policyID = ?");
        $updateStatus->bind_param("i", $policyID);
        $updateStatus->execute();
        $updateStatus->close();
        
        $deleteTask = $conn->prepare("DELETE FROM tasktbl WHERE policyAssigned = ?");
        $deleteTask->bind_param("i", $policyID);
        $deleteTask->execute();
        $deleteTask->close();
        
        if ($currentStatus === 4) { // Only notify if it wasn't already published
            $notifMessage = "Your policy '" . substr($policyTitle, 0, 50) . (strlen($policyTitle) > 50 ? '...' : '') . "' has been published!";
            $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus, dateTimeSent) VALUES (?, ?, 0, NOW())");
            $notifStmt->bind_param("is", $policyAuthorID, $notifMessage);
            $notifStmt->execute();
            $notifStmt->close();
        }
    }

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