<?php
include '../../connect.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['departmentId'], $data['newDepartmentName'])) {
    $id = intval($data['departmentId']);
    $newName = trim($data['newDepartmentName']);
    if ($id && $newName) {
        $stmt = $conn->prepare("UPDATE departmenttbl SET dptName=? WHERE dptID=?");
        $stmt->bind_param("si", $newName, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'DB error']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
}
$conn->close();
?>