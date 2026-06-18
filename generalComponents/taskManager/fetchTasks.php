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

    $sort = $_GET['sort'] ?? 'date_desc';

    $actionRequired = [];
    $mySubmissions  = [];
    $finishedTasks  = [];

    // ==========================================
    // 1. FETCH "ACTION REQUIRED" (Tasks to do)
    // ==========================================
    $selectCore = "
        SELECT 
            p.policyID, p.title AS policyTitle, a.fullName AS author, 
            p.dateSubmitted, p.versionNo AS version, ps.policyStatusName AS status, p.policyStatusID AS statusCode,
            rev.fullName AS reviewerName, ver.fullName AS verifierName, app.fullName AS approverName, p.contentPath AS pdfPath
            " . ($userRole != 2 ? ", t.dateCreated" : ", p.dateSubmitted AS dateCreated") . "
        FROM policytbl p
        LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
        LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
        LEFT JOIN accdatatbl rev ON p.reviewedBy = rev.accID
        LEFT JOIN accdatatbl ver ON p.policyVerifier = ver.accID
        LEFT JOIN accdatatbl app ON p.policyApprover = app.accID
    ";
    

    $whereClauses = [];
    $params = [];
    $types = "";
    $orderBy = "";

    if ($userRole == 2) {
        // -----------------------------------------------------------------
        // QAD: Does NOT depend on tasktbl at all.
        // QAD sees every policy that is in-progress (status 1-4) and
        // has not yet been filed into a folder after approval.
        // -----------------------------------------------------------------
        $selectCore = "
            SELECT
                p.policyID,
                p.title          AS policyTitle,
                a.fullName       AS author,
                p.dateSubmitted,
                ps.policyStatusName AS status,
                p.policyStatusID AS statusCode,
                NULL             AS taskID,
                NULL             AS taskDate,
                NULL             AS taskStatus,
                NULL             AS assignedBy,
                p.contentPath    AS pdfPath,
                p.requestChangeContentPath,
                r.fullName       AS reviewerName,
                v.fullName       AS verifierName,
                ap.fullName      AS approverName,
                p.originalPolicyID,
                (SELECT contentPath FROM policytbl WHERE policyID = p.originalPolicyID) AS originalFilePath
            FROM policytbl p
            LEFT JOIN accdatatbl a   ON p.policyAuthor   = a.accID
            LEFT JOIN accdatatbl r   ON p.policyReviewer = r.accID
            LEFT JOIN accdatatbl v   ON p.policyVerifier = v.accID
            LEFT JOIN accdatatbl ap  ON p.policyApprover = ap.accID
            LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
        ";

        $dateColumn   = "p.dateSubmitted";
        $whereClauses = [];
        $params       = [];
        $types        = "";

        // Handle status-specific filter
        if (strpos($sort, 'status_') === 0) {
            $statusID = (int) substr($sort, 7);
            $whereClauses[] = "p.policyStatusID = " . $statusID;
            if ($statusID == 2) {
                $whereClauses[] = "p.policyVerifier IS NULL";
            } elseif ($statusID == 3) {
                $whereClauses[] = "p.policyApprover IS NULL";
            }
        } else {
            // Default: all active policies the QAD still needs to act on.
            $whereClauses[] = "
                (p.policyStatusID = 1) OR
                (p.policyStatusID = 2 AND p.policyVerifier IS NULL) OR
                (p.policyStatusID = 3 AND p.policyApprover IS NULL) OR
                (p.policyStatusID = 4) OR
                (p.policyStatusID = 5 AND p.dateUploaded IS NULL)
            ";
        }

    } else {
        // -----------------------------------------------------------------
        // REGULAR USERS (QAP, Staff, Verifier, Approver):
        // Tasks are explicitly assigned via tasktbl.
        // QAP (Role 1) also automatically sees all Pending (Status 1) policies.
        // -----------------------------------------------------------------
        $selectCore = "
            SELECT
                p.policyID,
                p.title          AS policyTitle,
                a.fullName       AS author,
                p.dateSubmitted,
                ps.policyStatusName AS status,
                p.policyStatusID AS statusCode,
                t.taskID,
                COALESCE(t.dateCreated, p.dateSubmitted) AS taskDate,
                t.taskStatus,
                t.assignedBy,
                p.contentPath    AS pdfPath,
                p.requestChangeContentPath,
                r.fullName       AS reviewerName,
                v.fullName       AS verifierName,
                ap.fullName      AS approverName,
                p.originalPolicyID,
                (SELECT contentPath FROM policytbl WHERE policyID = p.originalPolicyID) AS originalFilePath
            FROM policytbl p
            LEFT JOIN tasktbl t      ON t.policyAssigned = p.policyID AND t.assignedTo = ? AND t.taskStatus = 0
            LEFT JOIN accdatatbl a   ON p.policyAuthor   = a.accID
            LEFT JOIN accdatatbl r   ON p.policyReviewer = r.accID
            LEFT JOIN accdatatbl v   ON p.policyVerifier = v.accID
            LEFT JOIN accdatatbl ap  ON p.policyApprover = ap.accID
            LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
        ";

        $dateColumn   = "COALESCE(t.dateCreated, p.dateSubmitted)";
        $params       = [$accID];
        $types        = "i";

        if ($userRole == 3) {
            // QAP sees all Pending (1) tasks + explicitly assigned tasks
            $whereClauses = ["(p.policyStatusID = 1 OR t.taskID IS NOT NULL)"];
        } else {
            // Others only see explicitly assigned tasks
            $whereClauses = ["t.taskID IS NOT NULL"];
        }

        // Handle status-specific filter
        if (strpos($sort, 'status_') === 0) {
            $statusID       = (int) substr($sort, 7);
            $whereClauses[] = "p.policyStatusID = ?";
            $params[]       = $statusID;
            $types         .= "i";
        }
    }

    // Date sorting (applies to both roles; status_ sort still sorts by date)
    switch ($sort) {
        case 'date_asc':
            $orderBy = " ORDER BY $dateColumn ASC";
            break;
        case 'date_desc':
        default:
            $orderBy = " ORDER BY $dateColumn DESC";
            break;
    }

    $whereSQL = !empty($whereClauses)
        ? " WHERE " . implode(" AND ", $whereClauses)
        : "";

    $query = $selectCore . $whereSQL . $orderBy;

    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        if (!$stmt) { throw new Exception("Prepare failed: " . $conn->error); }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $conn->query($query);
        if (!$res) { throw new Exception("Query failed: " . $conn->error); }
    }

    if ($res) {
        while ($row = $res->fetch_assoc()) { 
            $dateForNewCheck = $row['taskDate'] ?? $row['dateSubmitted'];
            $row['isNewTask'] = (strtotime($dateForNewCheck) >= strtotime('-2 days'));
            $actionRequired[] = $row; 
        }
    }
    if (isset($stmt)) { $stmt->close(); }

    // =====================================================================
    // 2. FETCH "MY SUBMISSIONS" (Track progress — same for all roles)
    // =====================================================================
    $trackStmt = $conn->prepare("
        SELECT
            p.policyID,
            p.title          AS policyTitle,
            p.dateSubmitted,
            ps.policyStatusName AS status,
            p.policyStatusID AS statusCode,
            p.contentPath    AS pdfPath,
            (SELECT content FROM feedbacktbl f
             WHERE f.remarksOn = p.policyID
             ORDER BY f.feedbackID DESC LIMIT 1) AS activeFeedback
        FROM policytbl p
        LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
        WHERE p.policyAuthor = ?
        ORDER BY p.dateSubmitted DESC
    ");
    $trackStmt->bind_param("i", $accID);
    $trackStmt->execute();
    $trackRes = $trackStmt->get_result();
    while ($trackRow = $trackRes->fetch_assoc()) { $mySubmissions[] = $trackRow; }
    $trackStmt->close();

    // =====================================================================
    // 3. FETCH "FINISHED TASKS" (Completed / archived items)
    // =====================================================================
    if ($userRole == 2) {
        $historyStmt = $conn->prepare("SELECT p.policyID,
                p.title AS policyTitle,
                a.fullName AS author,
                p.dateSubmitted,
                p.dateUploaded,
                p.dateReviewed,
                p.dateVerified,
                p.dateApproved,
                ps.policyStatusName AS status,
                p.policyStatusID AS statusCode,
                p.contentPath AS pdfPath
            FROM policytbl p
            LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
            LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
            WHERE p.policyStatusID IN (6, 7) OR (p.policyStatusID = 5 AND p.dateUploaded IS NOT NULL)
            ORDER BY COALESCE(p.dateUploaded, p.dateApproved, p.dateVerified, p.dateReviewed, p.dateSubmitted) DESC");
    } else {
        $historyStmt = $conn->prepare("SELECT p.policyID,
                p.title AS policyTitle,
                a.fullName AS author,
                p.dateSubmitted,
                p.dateUploaded,
                p.dateReviewed,
                p.dateVerified,
                p.dateApproved,
                ps.policyStatusName AS status,
                p.policyStatusID AS statusCode,
                p.contentPath AS pdfPath
            FROM policytbl p
            LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
            LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
            WHERE p.policyStatusID IN (5, 6, 7)
              AND (p.policyAuthor = ? OR p.policyReviewer = ? OR p.policyVerifier = ? OR p.policyApprover = ?)
            ORDER BY COALESCE(p.dateUploaded, p.dateApproved, p.dateVerified, p.dateReviewed, p.dateSubmitted) DESC");
        $historyStmt->bind_param("iiii", $accID, $accID, $accID, $accID);
    }
    if ($historyStmt) {
        $historyStmt->execute();
        $historyRes = $historyStmt->get_result();
        while ($historyRow = $historyRes->fetch_assoc()) { $finishedTasks[] = $historyRow; }
        $historyStmt->close();
    }

    echo json_encode([
        'actionRequired' => $actionRequired,
        'mySubmissions'  => $mySubmissions,
        'finishedTasks'  => $finishedTasks,
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>