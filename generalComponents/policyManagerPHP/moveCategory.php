<?php
ob_start();
session_start();
include __DIR__ . '/../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['categoryID']) || !array_key_exists('newParentID', $data)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing data inputs.']);
    exit;
}

$categoryID = $data['categoryID'];
$newParentID = $data['newParentID'];
$currentUserID = isset($_SESSION['accID']) ? $_SESSION['accID'] : 0;

$nameQuery = $conn->prepare("SELECT categoryName FROM categorytbl WHERE categoryID = ?");
$nameQuery->bind_param("i", $categoryID);
$nameQuery->execute();
$nameResult = $nameQuery->get_result();
$folderName = $nameResult->num_rows > 0 ? $nameResult->fetch_assoc()['categoryName'] : "A folder";
$nameQuery->close();

if ($newParentID === null) {
    $stmt = $conn->prepare("UPDATE categorytbl SET parentCategoryID = NULL WHERE categoryID = ?");
    $stmt->bind_param("i", $categoryID);
} else {
    if ($categoryID == $newParentID) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Cannot move a folder into itself.']);
        exit;
    }
    $stmt = $conn->prepare("UPDATE categorytbl SET parentCategoryID = ? WHERE categoryID = ?");
    $stmt->bind_param("ii", $newParentID, $categoryID);
}

if ($stmt->execute()) {
    // ✨ NOTIFICATION SYSTEM
    $shortFolder = substr($folderName, 0, 25);
    $message = "Folder moved: " . $shortFolder;

    // ✨ THE FIX: Search the main accounts table for Directors (Role 2) and QA Staff (Role 3)
    $notifyQuery = "SELECT accID FROM accdatatbl WHERE roleID IN (2, 3) AND accID != ?";
    $notifyStmt = $conn->prepare($notifyQuery);
    $notifyStmt->bind_param("i", $currentUserID);
    $notifyStmt->execute();
    $notifyResult = $notifyStmt->get_result();

    if ($notifyResult->num_rows > 0) {
        $notifStmt = $conn->prepare("INSERT INTO notiftbl (receivedBy, message, notifStatus, dateTimeSent) VALUES (?, ?, 0, NOW())");
        while ($row = $notifyResult->fetch_assoc()) {
            $notifStmt->bind_param("is", $row['accID'], $message);
            $notifStmt->execute();
        }
        $notifStmt->close();
    }
    $notifyStmt->close();

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