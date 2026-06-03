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

// 2. Fetch all active policies
// ✨ THE FIX: Tightly restrict to IN (4, 5) so we completely hide Archived (Status 6) documents!
    $queryPol = "SELECT policyID, title, categoryID FROM policytbl WHERE policyStatusID IN (4, 5)";
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