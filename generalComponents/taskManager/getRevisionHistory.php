<?php
// Place this at: generalComponents/revisionHistory/getRevisionHistory.php
ob_start();
if (!session_id()) session_start();
require_once __DIR__ . '/../../connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['accID'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$filterPolicyID = isset($_GET['policyID']) ? (int)$_GET['policyID'] : null;

$sql = "
    SELECT
        rv.revisionID,
        rv.originalPolicyID,
        rv.revisedPolicyID,
        rv.versionNo,
        rv.revisionType,
        rv.revisionFormPath,
        rv.changesDescription,
        rv.dateSubmitted,

        orig.title          AS originalTitle,
        orig.versionNo      AS originalVersion,
        orig.contentPath    AS originalFilePath,

        rev.title           AS revisedTitle,
        rev.contentPath     AS revisedFilePath,
        rev.policyStatusID  AS revisionStatusID,
        ps.policyStatusName AS revisionStatus,
        rev.dateReviewed,
        rev.dateVerified,
        rev.dateApproved,

        sub.fullName        AS submittedBy,
        reviewer.fullName   AS reviewedBy,
        verifier.fullName   AS verifiedBy,
        approver.fullName   AS approvedBy

    FROM  policyrevisiontbl rv
    JOIN  policytbl  orig     ON rv.originalPolicyID = orig.policyID
    JOIN  policytbl  rev      ON rv.revisedPolicyID  = rev.policyID
    LEFT JOIN policystatus ps ON rev.policyStatusID  = ps.policyStatusID
    LEFT JOIN accdatatbl sub      ON rv.submittedBy      = sub.accID
    LEFT JOIN accdatatbl reviewer ON rev.policyReviewer  = reviewer.accID
    LEFT JOIN accdatatbl verifier ON rev.policyVerifier  = verifier.accID
    LEFT JOIN accdatatbl approver ON rev.policyApprover  = approver.accID
";

$params = [];
$types  = '';

if ($filterPolicyID) {
    $sql   .= " WHERE rv.originalPolicyID = ? OR rv.revisedPolicyID = ?";
    $params = [$filterPolicyID, $filterPolicyID];
    $types  = 'ii';
}

$sql .= " ORDER BY rv.originalPolicyID ASC, rv.revisionID ASC";

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>$conn->error]); exit; }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
} else {
    $res = $conn->query($sql);
    if (!$res) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>$conn->error]); exit; }
}

$rows = [];
while ($row = $res->fetch_assoc()) $rows[] = $row;

ob_end_clean();
echo json_encode(['success' => true, 'revisions' => $rows]);