<?php
ob_start();
session_start();
include '../../connect.php';

// 1. Fetch all folders
$queryCat = "SELECT categoryID, categoryName, parentCategoryID FROM categorytbl ORDER BY parentCategoryID IS NOT NULL, parentCategoryID ASC";
$resultCat = mysqli_query($conn, $queryCat);
$categories = [];
if ($resultCat) {
    while ($row = mysqli_fetch_assoc($resultCat)) {
        $categories[] = $row;
    }
}

// 2. Fetch all Uploaded policies that are inside a folder
// ✨ THE FIX: Changed policyStatusID from 5 to 4 so it matches your "Approved" status!
$queryPol = "SELECT policyID, title, categoryID FROM policytbl WHERE categoryID IS NOT NULL AND policyStatusID = 4";
$resultPol = mysqli_query($conn, $queryPol);
$policies = [];
if ($resultPol) {
    while ($row = mysqli_fetch_assoc($resultPol)) {
        $policies[] = $row;
    }
}

mysqli_close($conn);

ob_end_clean();
header('Content-Type: application/json');
echo json_encode(['success' => true, 'categories' => $categories, 'policies' => $policies]);
?>