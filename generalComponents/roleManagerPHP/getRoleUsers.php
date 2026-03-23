<?php
ob_start();
session_start();
include '../../connect.php';

// 1. Fetch CURRENT Team Members (Assuming roleID = 3 is the QA Team)
$queryTeam = "SELECT accID, fullName, email FROM accdatatbl WHERE roleID = 3";
$resultTeam = mysqli_query($conn, $queryTeam);
$teamMembers = [];
if ($resultTeam) {
    while ($row = mysqli_fetch_assoc($resultTeam)) {
        if (empty($row['fullName'])) $row['fullName'] = $row['email']; 
        $teamMembers[] = $row;
    }
}

// 2. Fetch AVAILABLE Employees (People who are NOT on the team yet)
$queryAvail = "SELECT accID, fullName, email FROM accdatatbl WHERE roleID != 3";
$resultAvail = mysqli_query($conn, $queryAvail);
$availableUsers = [];
if ($resultAvail) {
    while ($row = mysqli_fetch_assoc($resultAvail)) {
        if (empty($row['fullName'])) $row['fullName'] = $row['email']; 
        $availableUsers[] = $row;
    }
}

mysqli_close($conn);

ob_end_clean();
header('Content-Type: application/json');
echo json_encode(['success' => true, 'teamMembers' => $teamMembers, 'availableUsers' => $availableUsers]);
?>