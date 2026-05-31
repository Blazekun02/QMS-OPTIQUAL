<?php
ob_start();
session_start();
include '../../connect.php';

header('Content-Type: application/json');

$policyID = isset($_GET['policyID']) ? intval($_GET['policyID']) : 0;

if ($policyID <= 0) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid Policy ID']);
    exit;
}

// Find the root originalPolicyID for the family lineage
$rootID = $policyID;
$stmt = $conn->prepare("SELECT originalPolicyID FROM policytbl WHERE policyID = ?");
$stmt->bind_param("i", $policyID);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    if (!empty($row['originalPolicyID'])) {
        $rootID = $row['originalPolicyID'];
    }
}
$stmt->close();

// Fetch the original and all its approved/published revisions
$sql = "
    SELECT 
        p.policyID, 
        p.title, 
        p.versionNo, 
        p.dateUploaded AS datePublished, 
        a.fullName AS authorName, 
        ap.fullName AS approverName,
        p.contentPath,
        p.requestChangeContentPath AS revisionFormPath
    FROM policytbl p
    LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
    LEFT JOIN accdatatbl ap ON p.policyApprover = ap.accID
    WHERE (p.policyID = ? OR p.originalPolicyID = ?)
      AND p.policyStatusID >= 4
    ORDER BY p.versionNo ASC, p.dateUploaded ASC
";

$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("ii", $rootID, $rootID);
$stmt2->execute();
$result = $stmt2->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = [
        'policyID' => $row['policyID'],
        'title' => $row['title'],
        'versionNo' => $row['versionNo'] ? 'v'.$row['versionNo'] : 'Original',
        'datePublished' => $row['datePublished'] ? date('M d, Y', strtotime($row['datePublished'])) : 'N/A',
        'authorName' => $row['authorName'] ?: 'Unknown',
        'approverName' => $row['approverName'] ?: 'Unknown',
        'contentPath' => $row['contentPath'],
        'revisionFormPath' => $row['revisionFormPath'] ?? null
    ];
}
$stmt2->close();

ob_end_clean();
echo json_encode(['success' => true, 'history' => $history]);
?>