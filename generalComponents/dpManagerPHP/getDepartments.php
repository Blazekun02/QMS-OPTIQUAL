<?php
header('Content-Type: application/json');
require_once '../../connect.php';

// Check if dptParentID column exists
$checkColumn = $conn->query("SHOW COLUMNS FROM dorgtbl LIKE 'dptParentID'");
$hasParentColumn = $checkColumn->num_rows > 0;

if ($hasParentColumn) {
    // Hierarchical structure 
    $result = $conn->query("SELECT dptID, dptName, dptParentID FROM dorgtbl ORDER BY dptParentID IS NULL DESC, dptParentID ASC, dptID ASC");
} else {
    // Flat structure 
    $result = $conn->query("SELECT dptID, dptName FROM dorgtbl ORDER BY dptID ASC");
}

$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}

// ✨ NEW: Fetch all assigned employees and their details
$empResult = $conn->query("
    SELECT e.dptID, e.accID, e.departmentRole, a.fullName, a.email 
    FROM empperdeptbl e 
    JOIN accdatatbl a ON e.accID = a.accID
");

$employees = [];
if ($empResult && $empResult->num_rows > 0) {
    while ($empRow = $empResult->fetch_assoc()) {
        $employees[] = $empRow;
    }
}

// Send BOTH folders and employees back to JavaScript
echo json_encode([
    'success' => true, 
    'departments' => $departments, 
    'hasHierarchy' => $hasParentColumn,
    'employees' => $employees
]);

$conn->close();


?>