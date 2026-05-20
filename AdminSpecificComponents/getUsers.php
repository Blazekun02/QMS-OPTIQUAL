<?php
// Enable error reporting to catch hidden issues
error_reporting(E_ALL);
ini_set('display_errors', 0); // Kept at 0 so raw HTML errors don't break the JSON format

header('Content-Type: application/json');

// Include database connection
require_once "../connect.php";

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Fetch users from the database
$sql = "SELECT accID, fullName, email, roleID FROM accdatatbl";
$result = $conn->query($sql);

$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(["success" => true, "users" => $users]);
} else {
    // Output the specific MySQL error for debugging
    echo json_encode(["success" => false, "message" => "Database query failed: " . $conn->error]);
}

$conn->close();
?>
