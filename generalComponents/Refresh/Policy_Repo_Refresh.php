<?php
$host = "localhost";
$port = "3306";
$dbUsername = "root";
$dbPassword = "";
$dbname = "qms";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT MAX(dateSubmitted) AS last_update FROM policytbl";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    // echo json_encode(['last_update' => $row['last_update']]);
} else {
    echo json_encode(['error' => 'Failed to fetch updates']);
}

$conn->close();
?>

