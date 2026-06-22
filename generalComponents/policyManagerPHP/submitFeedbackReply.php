<?php
session_start();
include '../../connect.php';

header('Content-Type: application/json');

function emitFatalJson(string $message, string $source) {
    if (ob_get_level()) { ob_end_clean(); }
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
    }
    echo json_encode([
        'success'      => false,
        'message'      => 'Server error',
        'debug_source' => $source,
        'debug_error'  => $message,
    ]);
}

if (!isset($_SESSION['accID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['feedbackID']) || !isset($data['replyContent'])) {
    echo json_encode(['success' => false, 'message' => 'Missing feedback ID or reply content.']);
    exit;
}

$feedbackID = $data['feedbackID'];
$replyContent = $data['replyContent'];
$repliedBy = $_SESSION['accID'];

$conn->begin_transaction();

try {
    // 1. Update the feedback record with the reply
    $stmt = $conn->prepare("UPDATE feedbacktbl SET replyContent = ?, repliedBy = ?, dateReplied = NOW() WHERE feedbackID = ?");
    $stmt->bind_param("sii", $replyContent, $repliedBy, $feedbackID);
    $stmt->execute();
    $stmt->close();

    // 2. Notify the original feedback submitter
    $query = "SELECT f.remarksBy, p.title 
              FROM feedbacktbl f 
              JOIN policytbl p ON f.remarksOn = p.policyID 
              WHERE f.feedbackID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $feedbackID);
    $stmt->execute();
    $result = $stmt->get_result();
    $feedbackInfo = $result->fetch_assoc();
    $stmt->close();

    if ($feedbackInfo) {
        $originalSubmitterID = $feedbackInfo['remarksBy'];
        $policyTitle = $feedbackInfo['title'];

        if ($originalSubmitterID != $repliedBy) {
            $notifMessage = "Your feedback on '{$policyTitle}' has been addressed.";
            $stmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus, dateTimeSent) VALUES (?, ?, 0, NOW())");
            $stmt->bind_param("is", $originalSubmitterID, $notifMessage);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Reply submitted successfully.']);

} catch (Exception $e) {
    $conn->rollback();
    emitFatalJson($e->getMessage(), 'submitFeedbackReply.php');
}

$conn->close();
?>