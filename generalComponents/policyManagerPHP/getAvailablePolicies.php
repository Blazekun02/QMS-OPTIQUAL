<?php
ob_start();
session_start();
include '../../connect.php';

// ✨ THE FIX: Use IN (4, 5) so it doesn't accidentally catch Rejected (6) or Archived (7)!
    $query = "SELECT policyID, title FROM policytbl WHERE policyStatusID IN (4, 5) AND categoryID IS NULL";
$result = mysqli_query($conn, $query);

$policies = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $policies[] = $row;
    }
    $response = ['success' => true, 'policies' => $policies];
} else {
    $response = ['success' => false, 'message' => mysqli_error($conn)];
}

mysqli_close($conn);
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>