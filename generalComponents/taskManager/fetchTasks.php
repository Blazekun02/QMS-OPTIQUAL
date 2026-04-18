<?php
if(!session_id()){ session_start(); }

require_once __DIR__ . '/../../filepaths.php';
require_once BASE_DIR . '/connect.php';

header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

try {
    if (!isset($_SESSION['accID'])) {
        echo json_encode(['error' => 'Session expired']);
        exit;
    }

    $accID = $_SESSION['accID']; 
    
    $roleStmt = $conn->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
    $roleStmt->bind_param("i", $accID);
    $roleStmt->execute();
    $userRole = $roleStmt->get_result()->fetch_assoc()['roleID'] ?? 0;
    $roleStmt->close();

    $actionRequired = [];
    $mySubmissions = [];

    // ==========================================
    // 1. FETCH "ACTION REQUIRED" (Tasks to do)
    // ==========================================
    $selectCore = "
        SELECT 
            p.policyID, p.title AS policyTitle, a.fullName AS author, 
            p.dateSubmitted, p.versionNo AS version, ps.policyStatusName AS status, p.policyStatusID AS statusCode,
            rev.fullName AS reviewerName, ver.fullName AS verifierName, app.fullName AS approverName,
            p.contentPath AS pdfPath
        FROM policytbl p
        LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
        LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
        LEFT JOIN accdatatbl rev ON p.reviewedBy = rev.accID
        LEFT JOIN accdatatbl ver ON p.policyVerifier = ver.accID
        LEFT JOIN accdatatbl app ON p.policyApprover = app.accID
    ";

    if ($userRole == 2) {
        $query = $selectCore . " WHERE (p.policyStatusID = 2 AND p.policyVerifier IS NULL) OR p.policyStatusID IN (3, 4) ORDER BY p.dateSubmitted ASC";
        $res = $conn->query($query);
        if ($res) {
            while($row = $res->fetch_assoc()){ $actionRequired[] = $row; }
        }
    } else {
        $stmt = $conn->prepare($selectCore . "
            JOIN tasktbl t ON p.policyID = t.policyAssigned
            WHERE t.assignedTo = ? ORDER BY t.dateCreated");
        $stmt->bind_param("i", $accID);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()){ $actionRequired[] = $row; }
        $stmt->close();
    }

    // ==========================================
    // 2. FETCH "MY SUBMISSIONS" (Track progress)
    // ==========================================
    // ✨ THE FIX: Uses your exact database columns (content and remarksOn)
    $trackStmt = $conn->prepare("
        SELECT 
            p.policyID, p.title AS policyTitle, p.dateSubmitted, 
            ps.policyStatusName AS status, p.policyStatusID AS statusCode, p.contentPath AS pdfPath,
            (SELECT content FROM feedbacktbl f WHERE f.remarksOn = p.policyID ORDER BY f.feedbackID DESC LIMIT 1) AS activeFeedback
        FROM policytbl p
        LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
        WHERE p.policyAuthor = ?
        ORDER BY p.dateSubmitted DESC
    ");
    $trackStmt->bind_param("i", $accID);
    $trackStmt->execute();
    $trackRes = $trackStmt->get_result();
    while($trackRow = $trackRes->fetch_assoc()){ 
        $mySubmissions[] = $trackRow; 
    }
    $trackStmt->close();

    echo json_encode([
        'actionRequired' => $actionRequired,
        'mySubmissions' => $mySubmissions
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>