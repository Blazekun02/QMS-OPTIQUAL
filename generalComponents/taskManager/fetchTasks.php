<?php
if(!session_id()){
    session_start();
}

require_once __DIR__ . '/../../filepaths.php';
require_once BASE_DIR . '/connect.php';

// ✨ Anti-Cache Headers
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

try {
    if (!isset($_SESSION['accID'])) {
        echo json_encode(['message' => 'Session expired']);
        exit;
    }

    $accID = $_SESSION['accID']; 
    $tasks = [];
    
    $roleStmt = $conn->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
    $roleStmt->bind_param("i", $accID);
    $roleStmt->execute();
    $result = $roleStmt->get_result();
    
    $userRole = 0;
    if ($row = $result->fetch_assoc()) {
        $userRole = $row['roleID'];
    }
    $roleStmt->close();

    // ✨ The core SELECT statement that grabs the reviewer, verifier, and approver names!
    $selectCore = "
        SELECT 
            p.policyID, p.title AS policyTitle, a.fullName AS author, 
            p.dateSubmitted, p.versionNo AS version, ps.policyStatusName AS status, p.contentPath AS pdfPath,
            rev.fullName AS reviewerName,
            ver.fullName AS verifierName,
            app.fullName AS approverName
    ";

    // QAD (Role 2)
    if ($userRole == 2) {
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
        if($res) { while($row = $res->fetch_assoc()) { $tasks[] = $row; } }
    } 
    // QA STAFF (Role 3)
    else if ($userRole == 3) {
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
        if($res) { while($row = $res->fetch_assoc()) { $tasks[] = $row; } }
    }
    // REGULAR STAFF / ASSIGNEES
    else {
        $stmt = $conn->prepare("
            SELECT
                p.policyID, p.title AS policyTitle, a.fullName AS author,
                t.dateCreated AS dateSubmitted, p.versionNo AS version, tt.taskTypeName AS status, p.contentPath AS pdfPath,
                rev.fullName AS reviewerName,
                ver.fullName AS verifierName,
                app.fullName AS approverName
            FROM tasktbl t
            LEFT JOIN policytbl p ON t.policyAssigned = p.policyID
            LEFT JOIN tasktypetbl tt ON t.taskTypeID = tt.tasktypeID
            LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
            LEFT JOIN accdatatbl rev ON p.reviewedBy = rev.accID
            LEFT JOIN accdatatbl ver ON p.verifiedBy = ver.accID
            LEFT JOIN accdatatbl app ON p.approvedBy = app.accID
            WHERE t.assignedTo = ?
            ORDER BY t.dateCreated
        ");
        $stmt->bind_param("i", $accID);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()){ $tasks[] = $row; }
        $stmt->close();
    }

    if (count($tasks) > 0) {
        echo json_encode($tasks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['message' => 'No tasks found']);
    }
    
} catch (Exception $e) { 
     echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>