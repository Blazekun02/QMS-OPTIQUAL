<?php
session_start();
include '../../connect.php';

header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['policyId']) || !isset($data['content'])) {
    echo json_encode(['success' => false, 'message' => 'Missing policy ID or feedback content.']);
    exit;
}

$policyId = $data['policyId'];
$content = $data['content'];
$remarksBy = $_SESSION['accID'] ?? null;

if (!$remarksBy) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$conn->begin_transaction();

try {
    // 1. Insert the feedback
    // 1. Insert the feedback
    $feedbackTypeId = 2; // ✨ FIX: Explicitly define this!
    $stmt = $conn->prepare("INSERT INTO feedbacktbl (remarksOn, remarksBy, content, fbType) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("iis", $policyId, $remarksBy, $content); // Only 3 parameters now
    $stmt->execute();

    // 2. Notify the Author
    $stmt = $conn->prepare("SELECT policyAuthor, title FROM policytbl WHERE policyID = ?");
    $stmt->bind_param("i", $policyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $policy = $result->fetch_assoc();
    $stmt->close();

    if ($policy) {
        $notifMessage = "New feedback on your policy '{$policy['title']}': {$content}";
        $stmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus) VALUES (?, ?, 0)");
        $stmt->bind_param("is", $policy['policyAuthor'], $notifMessage);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Feedback submitted.']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>