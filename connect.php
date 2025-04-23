<?php

$host = "localhost";
$port = "3306";
$dbUsername = "root";
$dbPassword = "";
$dbname = "qms";

$conn = new mysqli($host,  $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";

?>