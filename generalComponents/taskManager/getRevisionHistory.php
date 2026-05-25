<?php
// Prevent ugly PHP errors from breaking our JSON response
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

// ✨ BULLETPROOF QUERY: Only grabs the exact columns the frontend table actually needs!
$sql = "
    SELECT
        rv.revisionID,
        rv.versionNo,
        rv.revisionFormPath,
        rv.changeDescription,
        rv.dateRevised AS dateSubmitted,
        
        orig.title AS originalTitle,
        
        rev.title AS revisedTitle,
        rev.contentPath AS revisedFilePath,
        
        ps.policyStatusName AS revisionStatus,
        
        sub.fullName AS submittedBy

    FROM revisionhistorytbl rv
    JOIN policytbl orig ON rv.originalPolicyID = orig.policyID
    JOIN policytbl rev ON rv.currentPolicyID = rev.policyID
    LEFT JOIN policystatus ps ON rev.policyStatusID = ps.policyStatusID
    LEFT JOIN accdatatbl sub ON rv.revisedBy = sub.accID
";

$params = [];
$types  = '';

// Filter logic if needed later
if ($filterPolicyID) {
    $sql   .= " WHERE rv.originalPolicyID = ? OR rv.currentPolicyID = ?";
    $params = [$filterPolicyID, $filterPolicyID];
    $types  = 'ii';
}

$sql .= " ORDER BY rv.originalPolicyID ASC, rv.revisionID ASC";

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) { 
        ob_end_clean(); 
        echo json_encode(['success'=>false, 'message'=>'Prepare failed: '.$conn->error]); 
        exit; 
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query($sql);
    if (!$res) { 
        ob_end_clean(); 
        echo json_encode(['success'=>false, 'message'=>'Query failed: '.$conn->error]); 
        exit; 
    }
}

$revisions = [];
while ($row = $res->fetch_assoc()) {
    $revisions[] = $row;
}

ob_end_clean();
echo json_encode(['success' => true, 'revisions' => $revisions], JSON_INVALID_UTF8_IGNORE);
?>