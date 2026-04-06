<?php
session_start();
error_reporting(0); // Suppress ugly HTML errors from breaking the JSON
header('Content-Type: application/json');

$response = [
    'success' => false, 
    'departments' => [], 
    'employees' => [], 
    'message' => 'Unknown error.'
];

try {
    require_once '../../connect.php';

    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

    // ✨ FIX: Updated to your exact table name: "dorgtbl"
    $dptQuery = "SELECT * FROM dorgtbl ORDER BY dptName ASC";
    $dptResult = $conn->query($dptQuery);
    
    if ($dptResult && $dptResult->num_rows > 0) {
        while ($row = $dptResult->fetch_assoc()) {
            $response['departments'][] = [
                'dptID' => $row['dptID'],
                'dptName' => $row['dptName'],
                'dptParentID' => $row['dptParentID']
            ];
        }
    } elseif ($conn->error) {
        // If there's still a typo, catch it!
        throw new Exception("Database Error: " . $conn->error);
    }

    // 2. Fetch Employees assigned to those folders
    $empQuery = "
        SELECT a.accID, a.fullName, a.email, ed.dptID 
        FROM accdatatbl a
        JOIN empperdeptbl ed ON a.accID = ed.accID
        ORDER BY a.fullName ASC
    ";
    $empResult = $conn->query($empQuery);

    if ($empResult && $empResult->num_rows > 0) {
        while ($row = $empResult->fetch_assoc()) {
            $response['employees'][] = $row;
        }
        $response['success'] = true;
    } else {
        // ULTIMATE FALLBACK: If folder mapping is empty, just grab all users so the UI isn't blank
        $fallback = $conn->query("SELECT accID, fullName, email, NULL as dptID FROM accdatatbl ORDER BY fullName ASC");
        if ($fallback && $fallback->num_rows > 0) {
            while ($row = $fallback->fetch_assoc()) {
                $response['employees'][] = $row;
            }
            $response['success'] = true;
        } else {
            throw new Exception("Could not fetch any employees.");
        }
    }

    $conn->close();

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>