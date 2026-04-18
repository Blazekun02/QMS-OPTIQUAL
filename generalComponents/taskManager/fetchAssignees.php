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

    // 1. Fetch Folders
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
        throw new Exception("Database Error: " . $conn->error);
    }

    // ✨ THE FIX: Fetch Employees assigned to folders (STRICT JOIN, NO FALLBACK)
    $empQuery = "
        SELECT a.accID, a.fullName, a.email, ed.dptID 
        FROM accdatatbl a
        JOIN empperdeptbl ed ON a.accID = ed.accID
        ORDER BY a.fullName ASC
    ";
    $empResult = $conn->query($empQuery);

    if ($empResult) {
        // Even if there are 0 rows, we return success so the UI can show "No users found"
        while ($row = $empResult->fetch_assoc()) {
            $response['employees'][] = $row;
        }
        $response['success'] = true;
    } else {
        throw new Exception("Database Error: " . $conn->error);
    }

    $conn->close();

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>