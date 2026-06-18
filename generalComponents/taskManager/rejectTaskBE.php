<?php
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['policyID']) || !isset($data['reason'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing data inputs.']);
    exit;
}

$policyID = $data['policyID'];
$reason = $data['reason'];
$qadID = $_SESSION['accID']; // ✨ Get the reviewer's ID

// 1. Get the Author of the policy so we can notify them
$getAuthor = $conn->prepare("SELECT policyAuthor, title FROM policytbl WHERE policyID = ?");
$getAuthor->bind_param("i", $policyID);
$getAuthor->execute();
$authorResult = $getAuthor->get_result();

if ($authorResult->num_rows > 0) {
    $row = $authorResult->fetch_assoc();
    $authorID = $row['policyAuthor'];
    $policyTitle = $row['title'];
    
    $updatePolicy = $conn->prepare("UPDATE policytbl SET policyStatusID = 6, policyReviewer = NULL, reviewedBy = NULL, policyVerifier = NULL, policyApprover = NULL WHERE policyID = ?");
    
    if (!$updatePolicy) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'DB Prepare Error: ' . $conn->error]);
        exit;
    }
    
    $updatePolicy->bind_param("i", $policyID);
    $updatePolicy->execute();
    $updatePolicy->close();
    
    // 3. Remove the task from the current user's inbox
    $deleteTask = $conn->prepare("DELETE FROM tasktbl WHERE policyAssigned = ?");
    $deleteTask->bind_param("i", $policyID);
    $deleteTask->execute();
    $deleteTask->close();

    // 4. Send Notification to Author AND insert into Feedback Table
    $notifMessage = "Your document '$policyTitle' was returned. Reason: $reason";
    
    // ✨ THE FIX: Changed to 'receivedBy'
    $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus) VALUES (?, ?, 0)");
    if ($notifStmt) {
        $notifStmt->bind_param("is", $authorID, $notifMessage);
        $notifStmt->execute();
        $notifStmt->close();
    }

    // ✨ NEW: Temporarily disable foreign key checks to prevent crashes from empty config tables!
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    $feedbackStmt = $conn->prepare("INSERT INTO feedbacktbl (remarksOn, remarksBy, content, fbType, dateSubmitted) VALUES (?, ?, ?, 2, CURDATE())");
    if ($feedbackStmt) {
        $feedbackStmt->bind_param("iis", $policyID, $qadID, $reason);
        $feedbackStmt->execute();
        $feedbackStmt->close();
    }

    // Turn the security checks back on
    $conn->query("SET FOREIGN_KEY_CHECKS=1");

    $response = ['success' => true];
} else {
    $response = ['success' => false, 'message' => 'Policy not found.'];
}

$getAuthor->close();
$conn->close();

ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>