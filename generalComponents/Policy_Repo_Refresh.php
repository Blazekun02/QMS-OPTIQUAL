<?php
// $host = "localhost";
// $port = "3306";
// $dbUsername = "root";
// $dbPassword = "";
// $dbname = "qms";

$host = "localhost";
$dbUsername = "jdrizal";
$dbPassword = "SOETiny1!";
$dbname = "qms";

// ✨ FIX: Renamed variable to $refresh_conn so it doesn't destroy the main page's $conn!
$refresh_conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if (!$refresh_conn->connect_error) {
    $sql = "SELECT MAX(dateSubmitted) AS last_update FROM policytbl";
    $result = mysqli_query($refresh_conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        // echo json_encode(['last_update' => $row['last_update']]);
    } else {
        echo json_encode(['error' => 'Failed to fetch updates']);
    }

    $refresh_conn->close();
}