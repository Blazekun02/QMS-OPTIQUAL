<?php
if (!session_id()) session_start();
include '../connect.php'; 

$clientHash = $_GET['hash'] ?? '';

// Global System State Fingerprint
// This query calculates a mathematical checksum of the critical workflow tables to detect ANY changes instantly.
// (e.g., if a policy is signed, the policyStatusID sum changes. If a task completes, taskStatus changes.)
$sql = "SELECT 
    (SELECT IFNULL(SUM(policyStatusID), 0) + COUNT(*) FROM policytbl) as polState,
    (SELECT IFNULL(SUM(taskStatus), 0) + COUNT(*) FROM tasktbl) as taskState,
    (SELECT IFNULL(MAX(notifID), 0) FROM notiftbl) as notifState,
    (SELECT IFNULL(MAX(categoryID), 0) FROM categorytbl) as catState
";

$res = $conn->query($sql);
if ($res) {
    $row = $res->fetch_assoc();
    
    // Generate a unique fingerprint of the current state of the database
    $serverHash = md5($row['polState'] . '_' . $row['taskState'] . '_' . $row['notifState'] . '_' . $row['catState']);
    
    $hasUpdates = false;
    if ($clientHash !== '' && $clientHash !== $serverHash) {
        $hasUpdates = true;
    }
    
    echo json_encode([
        'hasUpdates' => $hasUpdates,
        'hash' => $serverHash
    ]);
} else {
    echo json_encode(['hasUpdates' => false, 'error' => $conn->error]);
}
$conn->close();
?>