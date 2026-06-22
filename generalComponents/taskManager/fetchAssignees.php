<?php
session_start();
error_reporting(0); // Suppress errors from breaking the JSON output
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

    // Capture the target policyID if passed by the workspace assignment modal
    $policyID = isset($_GET['policyID']) ? (int)$_GET['policyID'] : 0;
    $authorID = 0;

    // If a valid policyID is provided, find the author's account ID so we can exclude them
    if ($policyID > 0) {
        $authorQuery = $conn->prepare("SELECT policyAuthor FROM policytbl WHERE policyID = ?");
        if ($authorQuery) {
            $authorQuery->bind_param("i", $policyID);
            $authorQuery->execute();
            $authorResult = $authorQuery->get_result();
            if ($row = $authorResult->fetch_assoc()) {
                $authorID = (int)$row['policyAuthor'];
            }
            $authorQuery->close();
        }
    }

    // 1. Fetch Structural Folders
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

    // 2. Fetch Employees assigned to folders
    $empQuery = "
        SELECT a.accID, a.fullName, a.email, ed.dptID 
        FROM accdatatbl a
        JOIN empperdeptbl ed ON a.accID = ed.accID
        ORDER BY a.fullName ASC
    ";
    $empResult = $conn->query($empQuery);

    if ($empResult) {
        while ($row = $empResult->fetch_assoc()) {
            $row['isAuthor'] = ($authorID > 0 && (int)$row['accID'] === $authorID) ? true : false;
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