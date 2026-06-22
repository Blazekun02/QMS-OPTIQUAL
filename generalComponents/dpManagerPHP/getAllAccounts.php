<?php
session_start();
error_reporting(0);
header('Content-Type: application/json');

$response = [
    'success' => false, 
    'employees' => [], 
    'message' => 'Unknown error.'
];

try {
    require_once '../../connect.php';

    if (!$conn) throw new Exception("Database connection failed.");

    // Fetch accounts excluding Admin (roleID = 1) and users already assigned to a department
    $empQuery = "
        SELECT accID, fullName, email 
        FROM accdatatbl 
        WHERE roleID != 1 
          AND accID NOT IN (SELECT accID FROM empperdeptbl WHERE accID IS NOT NULL)
        ORDER BY fullName ASC
    ";
    $empResult = $conn->query($empQuery);

    if ($empResult) {
        while ($row = $empResult->fetch_assoc()) {
            $response['employees'][] = $row;
        }
        $response['success'] = true;
    } else {
        throw new Exception("Database Error: " . $conn->error);
    }

    $conn->close();

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>
