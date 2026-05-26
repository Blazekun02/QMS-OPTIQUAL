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
                CASE WHEN p.policyStatusID >= 2 THEN r.fullName ELSE NULL END AS reviewerName,
                CASE WHEN p.policyStatusID >= 3 THEN v.fullName ELSE NULL END AS verifierName,
                CASE WHEN p.policyStatusID >= 4 THEN ap.fullName ELSE NULL END AS approverName,
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
            if      ($statusID == 2) $whereClauses[] = "p.policyStatusID = 2";
            elseif  ($statusID == 3) $whereClauses[] = "p.policyStatusID = 3";
            elseif  ($statusID == 4) $whereClauses[] = "p.policyStatusID = 4 AND p.categoryID IS NULL";
            else                     $whereClauses[] = "p.policyStatusID = 1";
        } else {
            // Default: all active policies the QAD still needs to act on
            // ✨ FIX: Removed '1' so QAD ignores Pending policies (leaving them for QAP)
            $whereClauses[] = "p.policyStatusID IN (2, 3, 4)";
            
            // Exclude approved policies already filed into a folder
            $whereClauses[] = "NOT (p.policyStatusID = 4 AND p.categoryID IS NOT NULL)";
        }

    } else {
        // -----------------------------------------------------------------
        // REGULAR USERS (QAP, Staff, Verifier, Approver):
        // Tasks are explicitly assigned via tasktbl.
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
                t.dateCreated    AS taskDate,
                t.taskStatus,
                t.assignedBy,
                p.contentPath    AS pdfPath,
                p.requestChangeContentPath,
                CASE WHEN p.policyStatusID >= 2 THEN r.fullName ELSE NULL END AS reviewerName,
                CASE WHEN p.policyStatusID >= 3 THEN v.fullName ELSE NULL END AS verifierName,
                CASE WHEN p.policyStatusID >= 4 THEN ap.fullName ELSE NULL END AS approverName,
                p.originalPolicyID,
                (SELECT contentPath FROM policytbl WHERE policyID = p.originalPolicyID) AS originalFilePath
            FROM tasktbl t
            JOIN policytbl p     ON t.policyAssigned = p.policyID
            LEFT JOIN accdatatbl a   ON p.policyAuthor   = a.accID
            LEFT JOIN accdatatbl r   ON p.policyReviewer = r.accID
            LEFT JOIN accdatatbl v   ON p.policyVerifier = v.accID
            LEFT JOIN accdatatbl ap  ON p.policyApprover = ap.accID
            LEFT JOIN policystatus ps ON p.policyStatusID = ps.policyStatusID
        ";

        $dateColumn   = "t.dateCreated";
        $whereClauses = ["t.assignedTo = ?", "t.taskStatus = 0"];
        $params       = [$accID];
        $types        = "i";

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
        while ($row = $res->fetch_assoc()) { $actionRequired[] = $row; }
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

    echo json_encode([
        'actionRequired' => $actionRequired,
        'mySubmissions'  => $mySubmissions,
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>