<?php
header('Content-Type: application/json');
ob_start();
session_start();
include '../../connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['dptName']) || trim($data['dptName']) === '') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing department name.']);
    exit;
}

if (!isset($data['dptParentID'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing Parent ID! Please hard-refresh your browser (Ctrl+F5) to reload the folder IDs.']);
    exit;
}

$dptName = trim($data['dptName']);
$dptParentID = $data['dptParentID'];
try {
    // Check if dptParentID column exists and handle accordingly
    $checkColumn = $conn->query("SHOW COLUMNS FROM dorgtbl LIKE 'dptParentID'");
    if ($checkColumn->num_rows > 0) {
        // Column exists, use it
        $stmt = $conn->prepare("INSERT INTO dorgtbl (dptName, dptParentID) VALUES (?, ?)");
        if (!$stmt) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("si", $dptName, $dptParentID);
    } else {
        // Column doesn't exist, just insert name
        $stmt = $conn->prepare("INSERT INTO dorgtbl (dptName) VALUES (?)");
        if (!$stmt) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("s", $dptName);
    }
    
    if ($stmt->execute()) {
        $response = ['success' => true, 'departmentId' => $stmt->insert_id];
    } else {
        $response = ['success' => false, 'message' => 'Database error: ' . $stmt->error];
    }

    $stmt->close();
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    exit;
}
mysqli_close($conn);

ob_end_clean();
echo json_encode($response);

?>