<?php

// Localhost Database Connection
// $host = "localhost";
// $dbUsername = "root";
// $dbPassword = "";
// $dbname = "qms";

// //Airhub Database Connection
$host = "localhost";
$dbUsername = "jdrizal";
$dbPassword = "SOETiny1!";
$dbname = "qms";

try {
    $conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database Connection Exception: " . $e->getMessage());
}

// echo "Connected successfully";