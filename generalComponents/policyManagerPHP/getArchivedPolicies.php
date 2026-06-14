<?php
if (!session_id()) session_start();
include '../../connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['accID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$roleID = $_SESSION['roleID'] ?? null;
if ($roleID === null) {
    $roleStmt = $conn->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
    if ($roleStmt) {
        $roleStmt->bind_param("i", $_SESSION['accID']);
        $roleStmt->execute();
        $roleResult = $roleStmt->get_result();
        if ($roleResult && $roleResult->num_rows > 0) {
            $roleRow = $roleResult->fetch_assoc();
            $roleID = (int)$roleRow['roleID'];
            $_SESSION['roleID'] = $roleID;
        }
        $roleStmt->close();
    }
}

if ($roleID !== 2 && $roleID !== 3) {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

$archives = [];

// policyStatusID = 7 represents Archived/Replaced documents
$sql = "
    SELECT 
        p.policyID, 
        p.title, 
        p.contentPath, 
        p.versionNo, 
        p.requestChangeContentPath AS revisionFormPath,
        IFNULL(p.dateUploaded, p.dateSubmitted) as dateUploaded, 
        a.fullName AS authorName, 
        COALESCE(p.originalPolicyID, p.policyID) AS rootPolicyID,
        IFNULL(root.title, p.title) AS rootTitle
    FROM policytbl p
    LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
    LEFT JOIN policytbl root ON root.policyID = COALESCE(p.originalPolicyID, p.policyID)
    WHERE p.policyStatusID = 7
    ORDER BY rootTitle ASC,
             CAST(SUBSTRING_INDEX(p.versionNo, '.', 1) AS UNSIGNED) ASC,
             CAST(SUBSTRING_INDEX(p.versionNo, '.', -1) AS UNSIGNED) ASC,
             p.dateUploaded DESC,
             p.policyID DESC
";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['dateUploaded'] = $row['dateUploaded'] ?? 'N/A';
        $row['rootPolicyID'] = (int)$row['rootPolicyID'];
        $row['rootTitle'] = $row['rootTitle'] ?: $row['title'];
        $archives[] = $row;
    }
    echo json_encode(['success' => true, 'archives' => $archives]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database query failed: ' . $conn->error]);
}

$conn->close();
?>