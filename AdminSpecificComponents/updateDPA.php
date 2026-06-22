<?php
session_start();
include '../connect.php';
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
    exit; // Stop script execution after sending the error
}

// Optional: Add role check to ensure only admins can perform this action
if (!isset($_SESSION['roleID']) || $_SESSION['roleID'] != 1) { // Assuming 1 is the admin roleID
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: You do not have permission to perform this action.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['dpaContent']) || empty(trim($data['dpaContent']))) {
    echo json_encode(['success' => false, 'message' => 'DPA content cannot be empty.']);
    exit;
}

$newContent = $data['dpaContent'];

$conn->begin_transaction();

try {
    // 1. Get the latest version number
    $versionQuery = "SELECT MAX(dpaVersion) as maxVersion FROM dpatbl";
    $versionResult = $conn->query($versionQuery);
    $lastVersion = 0;
    if ($versionRow = $versionResult->fetch_assoc()) {
        $lastVersion = (int)$versionRow['maxVersion'];
    }
    $newVersion = $lastVersion + 1;

    // 2. Insert the new DPA content with the new version
    $stmt = $conn->prepare("INSERT INTO dpatbl (dpaContents, dateUploaded, dpaVersion) VALUES (?, NOW(), ?)");
    $stmt->bind_param("si", $newContent, $newVersion);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Data Privacy Agreement updated successfully.']);

} catch (Exception $e) {
    $conn->rollback();
    emitFatalJson($e->getMessage(), 'updateDPA.php');
}

$conn->close();
?>