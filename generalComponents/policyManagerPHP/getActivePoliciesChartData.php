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
    $dateCondition .= " AND MONTH(p.dateUploaded) = $month";
}
if ($year > 0) {
    $dateCondition .= " AND YEAR(p.dateUploaded) = $year";
}

if ($parentID === null) {
    // Top Level: Parent Folders (includes policies nested in child folders) + Main Repository
    $query = "
        SELECT 
            c.categoryID,
            c.categoryName, 
            (
                SELECT COUNT(p.policyID)
                FROM policytbl p
                LEFT JOIN categorytbl child ON p.categoryID = child.categoryID
                WHERE (p.categoryID = c.categoryID OR child.parentCategoryID = c.categoryID)
                AND p.policyStatusID IN (4, 5)
                $dateCondition
            ) as activePolicies
        FROM categorytbl c
        WHERE c.parentCategoryID IS NULL

        UNION ALL

        SELECT 
            NULL as categoryID,
            'Main Repository' as categoryName, 
            COUNT(p.policyID) as activePolicies
        FROM policytbl p
        WHERE p.categoryID IS NULL 
          AND p.policyStatusID IN (4, 5) 
          $dateCondition
    ";
} else {
    // Drill Down: Child Folders of the specific Parent + Direct Policies
    $query = "
        SELECT 
            c.categoryID,
            c.categoryName, 
            COUNT(p.policyID) as activePolicies
        FROM categorytbl c
        LEFT JOIN policytbl p 
            ON c.categoryID = p.categoryID 
            AND p.policyStatusID IN (4, 5) 
            $dateCondition
        WHERE c.parentCategoryID = $parentID
        GROUP BY c.categoryID, c.categoryName

        UNION ALL

        SELECT 
            NULL as categoryID,
            'Directly in Folder' as categoryName, 
            COUNT(p.policyID) as activePolicies
        FROM policytbl p
        WHERE p.categoryID = $parentID
          AND p.policyStatusID IN (4, 5) 
          $dateCondition
    ";
}

$result = $conn->query($query);
$labels = [];
$activeData = [];
$categoryIds = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['categoryName'];
        $activeData[] = (int)$row['activePolicies'];
        $categoryIds[] = $row['categoryID'];
    }
}

echo json_encode([
    'success' => true,
    'labels' => $labels,
    'active' => $activeData,
    'categoryIds' => $categoryIds,
    'parentID' => $parentID
]);
?>