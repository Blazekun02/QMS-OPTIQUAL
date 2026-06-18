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
$maxDepth = 20; // Prevent infinite loop in case of circular references
$currentDepth = 0;
while ($currentDepth < $maxDepth) {
    $stmt = $conn->prepare("SELECT originalPolicyID FROM policytbl WHERE policyID = ?");
    $stmt->bind_param("i", $rootID);
    $stmt->execute();
    $res = $stmt->get_result();
    $foundParent = false;
    if ($row = $res->fetch_assoc()) {
        if (!empty($row['originalPolicyID'])) {
            $rootID = (int)$row['originalPolicyID'];
            $foundParent = true;
        }
    }
    $stmt->close();
    if (!$foundParent) break;
    $currentDepth++;
}

// Collect all IDs in the lineage to support legacy nested revisions
$lineage = [$rootID];
$currentIndex = 0;
while ($currentIndex < count($lineage)) {
    $currentID = $lineage[$currentIndex];
    $stmt = $conn->prepare("SELECT policyID FROM policytbl WHERE originalPolicyID = ?");
    $stmt->bind_param("i", $currentID);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        if (!in_array($row['policyID'], $lineage)) {
            $lineage[] = (int)$row['policyID'];
        }
    }
    $stmt->close();
    $currentIndex++;
}

// Fetch the original and all its approved/published revisions
$placeholders = implode(',', array_fill(0, count($lineage), '?'));
$sql = "
    SELECT 
        p.policyID, 
        p.title, 
        COALESCE(r.versionNo, p.versionNo) AS rawVersion,
        p.dateUploaded AS datePublished, 
        a.fullName AS authorName, 
        ap.fullName AS approverName,
        p.contentPath,
        p.requestChangeContentPath AS revisionFormPath
    FROM policytbl p
    LEFT JOIN revisionhistorytbl r ON r.currentPolicyID = p.policyID
    LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
    LEFT JOIN accdatatbl ap ON p.policyApprover = ap.accID
    WHERE p.policyID IN ($placeholders)
      AND p.policyStatusID >= 4
    ORDER BY 
      CAST(SUBSTRING_INDEX(COALESCE(r.versionNo, p.versionNo), '.', 1) AS UNSIGNED) ASC,
      CASE WHEN INSTR(COALESCE(r.versionNo, p.versionNo), '.') > 0 
           THEN CAST(SUBSTRING_INDEX(COALESCE(r.versionNo, p.versionNo), '.', -1) AS UNSIGNED) 
           ELSE 0 END ASC,
      p.dateUploaded ASC
";

$stmt2 = $conn->prepare($sql);
$types = str_repeat('i', count($lineage));
$stmt2->bind_param($types, ...$lineage);
$stmt2->execute();
$result = $stmt2->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $ver = $row['rawVersion'];
    // Normalize integer-only versions (e.g. '1' -> '1.0')
    if ($ver !== null && strpos($ver, '.') === false) {
        $ver = $ver . '.0';
    }
    $history[] = [
        'policyID' => $row['policyID'],
        'title' => $row['title'],
        'versionNo' => $ver ? 'v'.$ver : 'Original',
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