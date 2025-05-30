<?php

//Localhost Database Connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "qms";

//Airhub Database Connection
// $host = "airhub-soe.apc.edu.ph";
// $dbUsername = "jdrizal";
// $dbPassword = "SOETiny1!";
// $dbname = "qms";

$conn = new mysqli($host,  $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";

?>