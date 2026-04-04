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
            p.dateSubmitted, p.versionNo AS version, ps.policyStatusName AS status, p.contentPath AS pdfPath,
            rev.fullName AS reviewerName, ver.fullName AS verifierName, app.fullName AS approverName
    ";

    if ($userRole == 2) { 
        // QAD sees Reviewed, Verified, Approved
        $query = $selectCore . "
            FROM policytbl p
            LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
            LEFT JOIN accdatatbl rev ON p.reviewedBy = rev.accID
            LEFT JOIN accdatatbl ver ON p.verifiedBy = ver.accID
            LEFT JOIN accdatatbl app ON p.approvedBy = app.accID
            LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
            WHERE p.policyStatusID IN (2, 3, 4)
        ";
        $res = $conn->query($query);
        if($res) { while($row = $res->fetch_assoc()) { $actionRequired[] = $row; } }
    } else if ($userRole == 3) {
        // QA Staff sees Pending
        $query = $selectCore . "
            FROM policytbl p
            LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
            LEFT JOIN accdatatbl rev ON p.reviewedBy = rev.accID
            LEFT JOIN accdatatbl ver ON p.verifiedBy = ver.accID
            LEFT JOIN accdatatbl app ON p.approvedBy = app.accID
            LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
            WHERE p.policyStatusID = 1
        ";
        $res = $conn->query($query);
        if($res) { while($row = $res->fetch_assoc()) { $actionRequired[] = $row; } }
    } else {
        // Regular Staff
        $stmt = $conn->prepare("
            SELECT p.policyID, p.title AS policyTitle, a.fullName AS author, t.dateCreated AS dateSubmitted, p.versionNo AS version, tt.taskTypeName AS status, p.contentPath AS pdfPath, rev.fullName AS reviewerName, ver.fullName AS verifierName, app.fullName AS approverName 
            FROM tasktbl t 
            LEFT JOIN policytbl p ON t.policyAssigned = p.policyID 
            LEFT JOIN tasktypetbl tt ON t.taskTypeID = tt.tasktypeID 
            LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID 
            LEFT JOIN accdatatbl rev ON p.reviewedBy = rev.accID 
            LEFT JOIN accdatatbl ver ON p.verifiedBy = ver.accID 
            LEFT JOIN accdatatbl app ON p.approvedBy = app.accID 
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
    $trackStmt = $conn->prepare("
        SELECT 
            p.policyID, p.title AS policyTitle, p.dateSubmitted, 
            ps.policyStatusName AS status, p.policyStatusID AS statusCode, p.contentPath AS pdfPath
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

    // RETURN BOTH ARRAYS IN ONE JSON PACKAGE!
    echo json_encode([
        'actionRequired' => $actionRequired,
        'mySubmissions' => $mySubmissions
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) { 
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>