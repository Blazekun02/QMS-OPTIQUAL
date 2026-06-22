<?php
session_start();
include '../../connect.php';

header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Securely extract and cast parameters
$month = isset($_GET['month']) && is_numeric($_GET['month']) && $_GET['month'] > 0 ? (int)$_GET['month'] : 0;
$year = isset($_GET['year']) && is_numeric($_GET['year']) && $_GET['year'] > 0 ? (int)$_GET['year'] : 0;
$parentID = isset($_GET['parentID']) && is_numeric($_GET['parentID']) ? (int)$_GET['parentID'] : null;

$dateCondition = "";

if ($month > 0) {
    $dateCondition .= " AND MONTH(COALESCE(p.dateUploaded, p.dateSubmitted)) = $month";
}
if ($year > 0) {
    $dateCondition .= " AND YEAR(COALESCE(p.dateUploaded, p.dateSubmitted)) = $year";
}

if ($parentID === null) {
    // Top Level: Departments
    $query = "
        SELECT 
            c.categoryID AS categoryID,
            c.categoryName AS categoryName,
            SUM(CASE WHEN p.policyStatusID IN (4, 5) THEN 1 ELSE 0 END) AS policiesApproved,
            COUNT(p.policyID) AS totalPoliciesSubmitted
        FROM 
            categorytbl c
        LEFT JOIN 
            policytbl p ON c.categoryID = p.categoryID $dateCondition
        WHERE 
            c.parentCategoryID IS NULL OR c.categoryID IN (SELECT DISTINCT categoryID FROM policytbl WHERE categoryID IS NOT NULL)
        GROUP BY 
            c.categoryID, c.categoryName
        ORDER BY 
            totalPoliciesSubmitted DESC
    ";
} else {
    // Drill Down: Sub-departments
    $query = "
        SELECT 
            c.categoryID AS categoryID,
            c.categoryName AS categoryName,
            SUM(CASE WHEN p.policyStatusID IN (4, 5) THEN 1 ELSE 0 END) AS policiesApproved,
            COUNT(p.policyID) AS totalPoliciesSubmitted
        FROM 
            categorytbl c
        LEFT JOIN 
            policytbl p ON c.categoryID = p.categoryID $dateCondition
        WHERE 
            c.parentCategoryID = $parentID
        GROUP BY 
            c.categoryID, c.categoryName
        ORDER BY 
            totalPoliciesSubmitted DESC
    ";
}

$result = $conn->query($query);
$labels = [];
$activeData = [];
$totalData = [];
$categoryIds = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['categoryName'];
        $activeData[] = (int)$row['policiesApproved'];
        $totalData[] = (int)$row['totalPoliciesSubmitted'];
        $categoryIds[] = $row['categoryID'];
    }
}

echo json_encode([
    'success' => true,
    'labels' => $labels,
    'active' => $activeData,
    'total' => $totalData,
    'categoryIds' => $categoryIds,
    'parentID' => $parentID
]);
?>