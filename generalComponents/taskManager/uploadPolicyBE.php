<?php
ob_start();
session_start();
include '../../connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['policyID'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing policy ID.']);
    exit;
}

$policyID = $data['policyID'];
$currentUserID = $_SESSION['accID'] ?? null; // The QAD performing the upload

if (!$currentUserID) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

try {
    // 1. Get the policy author's ID and title
    $getAuthorStmt = $conn->prepare("SELECT policyAuthor, title FROM policytbl WHERE policyID = ?");
    $getAuthorStmt->bind_param("i", $policyID);
    $getAuthorStmt->execute();
    $authorResult = $getAuthorStmt->get_result();

    if ($authorResult->num_rows === 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Policy not found.']);
        exit;
    }

    $policyData = $authorResult->fetch_assoc();
    $policyAuthorID = $policyData['policyAuthor'];
    $policyTitle = $policyData['title'];
    $getAuthorStmt->close();

    // 2. Update policy status to 'Published' (Status 5) and set the upload timestamp & category.
    $categoryID = isset($data['categoryID']) && $data['categoryID'] !== 'NULL' ? (int)$data['categoryID'] : null;
    $newStatusID = 5; 
    
    if ($categoryID === null) {
        $updatePolicyStmt = $conn->prepare("UPDATE policytbl SET policyStatusID = ?, dateUploaded = NOW(), categoryID = NULL WHERE policyID = ?");
        $updatePolicyStmt->bind_param("ii", $newStatusID, $policyID);
    } else {
        $updatePolicyStmt = $conn->prepare("UPDATE policytbl SET policyStatusID = ?, dateUploaded = NOW(), categoryID = ? WHERE policyID = ?");
        $updatePolicyStmt->bind_param("iii", $newStatusID, $categoryID, $policyID);
    }

    if (!$updatePolicyStmt->execute()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to update policy status: ' . $updatePolicyStmt->error]);
        exit;
    }
    $updatePolicyStmt->close();

    // 3. Delete any remaining tasks for this policy from tasktbl
    $deleteTaskStmt = $conn->prepare("DELETE FROM tasktbl WHERE policyAssigned = ?");
    $deleteTaskStmt->bind_param("i", $policyID);

    if (!$deleteTaskStmt->execute()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to delete task entries: ' . $deleteTaskStmt->error]);
        exit;
    }
    $deleteTaskStmt->close();

    // 4. Send notification to the author that their policy has been published
    $notifMessage = "Your policy '" . substr($policyTitle, 0, 50) . (strlen($policyTitle) > 50 ? '...' : '') . "' has been published!";
    $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus, dateTimeSent) VALUES (?, ?, 0, NOW())");
    $notifStmt->bind_param("is", $policyAuthorID, $notifMessage);
    $notifStmt->execute();
    $notifStmt->close();

    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Policy successfully uploaded and author\'s task removed.']);

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>