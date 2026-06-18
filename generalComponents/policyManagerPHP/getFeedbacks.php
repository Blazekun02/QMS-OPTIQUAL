<?php
session_start();
include '../../connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['accID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$accID = $_SESSION['accID'];

$roleID = 0; // Default to a non-privileged role

// Safely get user role
$roleStmt = $conn->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
if ($roleStmt) {
    $roleStmt->bind_param("i", $accID);
    $roleStmt->execute();
    $roleResult = $roleStmt->get_result();
    if ($roleRow = $roleResult->fetch_assoc()) {
        $roleID = (int)$roleRow['roleID'];
    }
    $roleStmt->close();
}

try {
    $baseQuery = "SELECT f.*, p.title as policyTitle, a.fullName as submittedBy, r.fullName as replierName
                  FROM feedbacktbl f 
                  JOIN policytbl p ON f.remarksOn = p.policyID 
                  JOIN accdatatbl a ON f.remarksBy = a.accID 
                  LEFT JOIN accdatatbl r ON f.repliedBy = r.accID";

    if ($roleID == 2 || $roleID == 3) {
        // Fetch ALL feedbacks
        $query = $baseQuery . " ORDER BY f.feedbackID DESC";
        $stmt = $conn->prepare($query);
    } else {
        // Staff see feedback on policies they authored OR feedback they submitted
        $query = $baseQuery . " WHERE p.policyAuthor = ? OR f.remarksBy = ? ORDER BY f.feedbackID DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $accID, $accID);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $feedbacks = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['dateSubmitted'])) {
            $row['dateSubmitted'] = date('M d, Y', strtotime($row['dateSubmitted']));
        }
        $feedbacks[] = $row;
    }

    echo json_encode(['success' => true, 'feedbacks' => $feedbacks]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>