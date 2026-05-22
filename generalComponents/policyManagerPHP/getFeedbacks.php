<?php
session_start();
include '../../connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['accID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$accID = $_SESSION['accID'];

// Check user role: 2=QAD, 3=QAP (can see all), otherwise only see own
$stmt = $conn->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
$stmt->bind_param("i", $accID);
$stmt->execute();
$roleResult = $stmt->get_result()->fetch_assoc();
$roleID = $roleResult['roleID'];

try {
    if ($roleID == 2 || $roleID == 3) {
        // Fetch ALL feedbacks
        $query = "SELECT f.*, p.title as policyTitle, a.fullName as submittedBy 
                  FROM feedbacktbl f 
                  JOIN policytbl p ON f.remarksOn = p.policyID 
                  JOIN accdatatbl a ON f.remarksBy = a.accID 
                  ORDER BY f.feedbackID DESC";
        $stmt = $conn->prepare($query);
    } else {
        // Fetch ONLY feedback for policies authored by this staff
        $query = "SELECT f.*, p.title as policyTitle, a.fullName as submittedBy 
                  FROM feedbacktbl f 
                  JOIN policytbl p ON f.remarksOn = p.policyID 
                  JOIN accdatatbl a ON f.remarksBy = a.accID 
                  WHERE p.policyAuthor = ? 
                  ORDER BY f.feedbackID DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $accID);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $feedbacks = [];
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }

    echo json_encode(['success' => true, 'feedbacks' => $feedbacks]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>