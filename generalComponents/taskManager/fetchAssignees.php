<?php
session_start();
include '../../connect.php';

// ✨ FIX: Updated the table name to match your database exactly (empperdeptbl)
$query = "
    SELECT DISTINCT a.accID, a.fullName, a.email 
    FROM accdatatbl a
    JOIN empperdeptbl da ON a.accID = da.accID
    ORDER BY a.fullName ASC
";

$result = $conn->query($query);
$employees = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    echo json_encode(['success' => true, 'employees' => $employees]);
} else {
    // Fallback: If no one is in a department yet, just fetch all active accounts
    $fallbackQuery = "SELECT accID, fullName, email FROM accdatatbl ORDER BY fullName ASC";
    $fallbackResult = $conn->query($fallbackQuery);
    while ($row = $fallbackResult->fetch_assoc()) {
        $employees[] = $row;
    }
    echo json_encode(['success' => true, 'employees' => $employees]);
}
?>