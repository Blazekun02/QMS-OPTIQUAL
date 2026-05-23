<?php
include '../../connect.php';
header('Content-Type: application/json');

// Get parameters
$action = $_GET['action'] ?? 'summary'; // 'summary' for boxes, 'details' for table
$type = $_GET['type'] ?? 'active';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? date('Y');

if ($action === 'summary') {
    // 1. Fetch counts for all 3 boxes
    // Assuming status IDs: 4=Active, 1=Pending, 5=Rejected (Adjust these IDs to match your DB!)
    $counts = [
        'active' => 0,
        'pending' => 0,
        'rejected' => 0
    ];
    
    $results = mysqli_query($conn, "SELECT policyStatusID, COUNT(*) as cnt FROM policytbl GROUP BY policyStatusID");
    while($row = mysqli_fetch_assoc($results)) {
        if ($row['policyStatusID'] == 4) $counts['active'] = $row['cnt'];
        if ($row['policyStatusID'] == 1) $counts['pending'] = $row['cnt'];
        if ($row['policyStatusID'] == 5) $counts['rejected'] = $row['cnt'];
    }
    echo json_encode($counts);

} else {
    // 2. Fetch detailed breakdown for the table
    $statusMap = ['active' => 4, 'pending' => 1, 'rejected' => 5];
    $statusID = $statusMap[$type] ?? 4;
    
    $query = "SELECT c.categoryName, COUNT(p.policyID) as total 
              FROM categorytbl c 
              LEFT JOIN policytbl p ON c.categoryID = p.categoryID 
              WHERE p.policyStatusID = '$statusID'";

    if($month) $query .= " AND MONTH(p.dateUploaded) = '$month'";
    $query .= " AND YEAR(p.dateUploaded) = '$year' GROUP BY c.categoryID";

    $result = mysqli_query($conn, $query);
    $data = [];
    while($row = mysqli_fetch_assoc($result)) $data[] = $row;
    echo json_encode($data);
}
?>