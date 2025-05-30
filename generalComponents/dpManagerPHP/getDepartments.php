<?php
header('Content-Type: application/json');
require_once '../../connect.php';

$result = $conn->query("SELECT dptID, dptName FROM dorgtbl ORDER BY dptID ASC");
$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}
echo json_encode(['success' => true, 'departments' => $departments]);
$conn->close();
?>