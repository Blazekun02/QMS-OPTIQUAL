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
    // 1. Get info about the feedback being replied to (the parent)
    $query = "SELECT f.remarksBy, f.remarksOn, f.sessionID, p.title 
              FROM feedbacktbl f 
              JOIN policytbl p ON f.remarksOn = p.policyID 
              WHERE f.feedbackID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $feedbackID);
    $stmt->execute();
    $parentFeedback = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$parentFeedback) {
        throw new Exception("Parent feedback not found.");
    }

    $policyID = $parentFeedback['remarksOn'];
    $policyTitle = $parentFeedback['title'];
    $recipientID = $parentFeedback['remarksBy'];

    // Determine the sessionID for the conversation thread.
    // If the parent has a sessionID, use it. Otherwise, use the parent's own feedbackID.
    $sessionID = ($parentFeedback['sessionID'] != 0) ? $parentFeedback['sessionID'] : $feedbackID;

    // 2. Insert the new reply as a new record in feedbacktbl
    // We'll use fbType=1 for 'general' feedback/reply
    $stmt = $conn->prepare("INSERT INTO feedbacktbl (remarksOn, remarksBy, content, fbType, sessionID, dateSubmitted) VALUES (?, ?, ?, 1, ?, NOW())");
    $stmt->bind_param("iisis", $policyID, $repliedBy, $replyContent, $sessionID);
    $stmt->execute();
    $stmt->close();

    // 3. Notify the user who is being replied to
    if ($recipientID != $repliedBy) {
        // Check if the reply is to a rejection feedback to customize the message
        $parentFbTypeQuery = "SELECT fbType FROM feedbacktbl WHERE feedbackID = ?";
        $stmt = $conn->prepare($parentFbTypeQuery);
        $stmt->bind_param("i", $feedbackID);
        $stmt->execute();
        $parentFbType = $stmt->get_result()->fetch_assoc()['fbType'];
        $stmt->close();

        if ($parentFbType == 2) { // fbType 2 is 'rejection'
            $notifMessage = "Your rejection feedback on '{$policyTitle}' has a new reply.";
        } else {
            $notifMessage = "Your feedback on '{$policyTitle}' has a new reply.";
        }
            $stmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus, dateTimeSent) VALUES (?, ?, 0, NOW())");
        $stmt->bind_param("is", $recipientID, $notifMessage);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Reply submitted successfully.']);

} catch (Exception $e) {
    $conn->rollback();
    emitFatalJson($e->getMessage(), 'submitFeedbackReply.php');
}

$conn->close();
?>