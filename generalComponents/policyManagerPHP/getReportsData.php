<?php
include '../../connect.php';
header('Content-Type: application/json');

// Get parameters
$action = $_GET['action'] ?? 'summary'; // 'summary' for dashboard, 'details' for table
$type = $_GET['type'] ?? 'active';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

if ($action === 'summary') {
    // --- 1. KPI & Chart Data ---
    $query = "SELECT policyStatusID, COUNT(*) as cnt 
              FROM policytbl p 
              WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($month)) {
        $query .= " AND MONTH(COALESCE(p.dateRejection, p.dateUploaded, p.dateSubmitted)) = ?";
        $params[] = $month;
        $types .= 'i';
    }
    if (!empty($year)) {
        $query .= " AND YEAR(COALESCE(p.dateRejection, p.dateUploaded, p.dateSubmitted)) = ?";
        $params[] = $year;
        $types .= 'i';
    }
    
    $query .= " GROUP BY policyStatusID";

    // Initialize arrays to hold the data for the dashboard components
    $kpiCounts = [
        'active' => 0, 
        'pending' => 0, 
        'rejected' => 0,
        'speed' => 0,    
        'expiring' => 0,
        'feedbacks' => 0
    ];
    $statusCounts = [
        'approved' => 0,     
        'under_review' => 0, 
        'draft' => 0,        
        'archived' => 0      
    ];

    // Fetch Feedback KPI Count (Only General Feedbacks for valid policies)
    $fbQuery = "SELECT COUNT(f.feedbackID) as cnt FROM feedbacktbl f JOIN policytbl p ON f.remarksOn = p.policyID WHERE f.fbType = 1";
    $fbParams = [];
    $fbTypes = "";
    if (!empty($month)) {
        $fbQuery .= " AND MONTH(f.dateSubmitted) = ?";
        $fbParams[] = $month;
        $fbTypes .= 'i';
    }
    if (!empty($year)) {
        $fbQuery .= " AND YEAR(f.dateSubmitted) = ?";
        $fbParams[] = $year;
        $fbTypes .= 'i';
    }
    if (!empty($fbParams)) {
        $stmtFb = $conn->prepare($fbQuery);
        $stmtFb->bind_param($fbTypes, ...$fbParams);
        $stmtFb->execute();
        $resFb = $stmtFb->get_result();
        if ($rowFb = $resFb->fetch_assoc()) {
            $kpiCounts['feedbacks'] = (int)$rowFb['cnt'];
        }
        $stmtFb->close();
    } else {
        $resFb = $conn->query($fbQuery);
        if ($resFb && $rowFb = $resFb->fetch_assoc()) {
            $kpiCounts['feedbacks'] = (int)$rowFb['cnt'];
        }
    }

    // ✨ THE FIX: Standardize result loop processing to prevent object mismatch crashes
    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            processSummaryRow($row, $kpiCounts, $statusCounts);
        }
        $stmt->close();
    } else {
        $res = mysqli_query($conn, $query);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                processSummaryRow($row, $kpiCounts, $statusCounts);
            }
        }
    }

    // --- Assemble the final response ---
    $response = [
        'kpiData' => $kpiCounts,
        'workflowData' => [5, 8, 4, 2], 
        'statusData' => array_values($statusCounts),
        'accessData' => [rand(10,50), rand(20,60), rand(30,70), rand(25,65), rand(40,80), rand(15,40), rand(5,20)] 
    ];
    
    echo json_encode($response);
    exit;
} else if ($action === 'pendingChart') {
    $query = "SELECT 
                 CASE 
                     WHEN p.policyStatusID = 1 THEN 'To be Reviewed'
                     WHEN p.policyStatusID = 2 THEN 'To be Verified'
                     WHEN p.policyStatusID = 3 THEN 'To be Approved'
                     WHEN p.policyStatusID = 4 THEN 'To be Uploaded'
                     ELSE 'Pending'
                 END as taskName,
                 p.policyStatusID,
                 COUNT(*) as total
              FROM policytbl p
              WHERE p.policyStatusID IN (1, 2, 3)";
    $params = [];
    $types = "";

    if (!empty($month)) {
        $query .= " AND MONTH(p.dateSubmitted) = ?";
        $params[] = $month;
        $types .= 'i';
    }
    if (!empty($year)) {
        $query .= " AND YEAR(p.dateSubmitted) = ?";
        $params[] = $year;
        $types .= 'i';
    }
    $query .= " GROUP BY p.policyStatusID, taskName";

    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
    }

    $labels = [];
    $data = [];
    $statusIds = [];
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $labels[] = $row['taskName'];
            $data[] = (int)$row['total'];
            $statusIds[] = $row['policyStatusID'];
        }
    }
    echo json_encode(['success' => true, 'labels' => $labels, 'data' => $data, 'statusIds' => $statusIds]);
    exit;
} else if ($action === 'rejected_reason') {
    $policyId = isset($_GET['policyID']) ? intval($_GET['policyID']) : 0;
    $response = ['success' => false, 'title' => '', 'reason' => ''];
    
    if ($policyId > 0) {
        $query = "SELECT p.title, f.content as reason
                  FROM policytbl p
                  LEFT JOIN (
                      SELECT remarksOn, MAX(feedbackID) as max_id
                      FROM feedbacktbl
                      GROUP BY remarksOn
                  ) latest_fb ON p.policyID = latest_fb.remarksOn
                  LEFT JOIN feedbacktbl f ON latest_fb.max_id = f.feedbackID
                  WHERE p.policyID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $policyId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $response['success'] = true;
            $response['title'] = $row['title'];
            $response['reason'] = $row['reason'];
        }
        $stmt->close();
    }
    echo json_encode($response);
    exit;
} else if ($action === 'feedback_details') {
    $policyId = isset($_GET['policyID']) ? intval($_GET['policyID']) : 0;
    
    $response = ['success' => false, 'feedbacks' => [], 'policyTitle' => ''];
    
    if ($policyId > 0) {
        $query = "SELECT 
                    sender.fullName AS senderName,
                    author.fullName AS authorName,
                    DATE_FORMAT(f.dateSubmitted, '%b %d, %Y') AS dateSent,
                    f.content AS content,
                    p.title AS policyTitle,
                    CASE WHEN f.fbType = 2 THEN 'rejection' ELSE 'general' END as type
                  FROM feedbacktbl f
                  JOIN policytbl p ON f.remarksOn = p.policyID
                  JOIN accdatatbl sender ON f.remarksBy = sender.accID
                  JOIN accdatatbl author ON p.policyAuthor = author.accID
                  WHERE (p.policyID = ? OR p.originalPolicyID = ?)";
                  
        $params = [$policyId, $policyId];
        $types = "ii";

        if (!empty($month)) {
            $query .= " AND MONTH(f.dateSubmitted) = ?";
            $params[] = $month;
            $types .= 'i';
        }
        if (!empty($year)) {
            $query .= " AND YEAR(f.dateSubmitted) = ?";
            $params[] = $year;
            $types .= 'i';
        }

        $query .= " ORDER BY f.feedbackID DESC";
                  
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            if (empty($response['policyTitle'])) $response['policyTitle'] = $row['policyTitle'];
            unset($row['policyTitle']);
            $response['feedbacks'][] = $row;
        }
        $response['success'] = true;
        $stmt->close();
    }
    echo json_encode($response);
    exit;
} else {
    // 2. Fetch detailed breakdown for the table
    if ($type === 'pending') {
        $statusId = $_GET['statusId'] ?? '';
        $query = "SELECT p.title, 
                         CASE 
                             WHEN p.policyStatusID = 1 THEN 'To be Reviewed'
                             WHEN p.policyStatusID = 2 THEN 'To be Verified'
                             WHEN p.policyStatusID = 3 THEN 'To be Approved'
                             WHEN p.policyStatusID = 4 THEN 'To be Uploaded'
                             ELSE 'Pending'
                         END as taskName,
                         COALESCE(DATEDIFF(NOW(), p.dateSubmitted), 0) as daysAssigned
                  FROM policytbl p
                  WHERE p.policyStatusID IN (1, 2, 3)";
        $params = [];
        $types = "";

        if (!empty($statusId)) {
            $query .= " AND p.policyStatusID = ?";
            $params[] = $statusId;
            $types .= 'i';
        }
        if (!empty($month)) {
            $query .= " AND MONTH(p.dateSubmitted) = ?";
            $params[] = $month;
            $types .= 'i';
        }
        if (!empty($year)) {
            $query .= " AND YEAR(p.dateSubmitted) = ?";
            $params[] = $year;
            $types .= 'i';
        }
        $query .= " ORDER BY daysAssigned DESC";
    } else if ($type === 'rejected') {
        // ✨ THE FIX: We fallback to dateSubmitted here as well so old rejections match the card counts!
        $query = "SELECT p.policyID, p.title, a.fullName as authorName, 
                         DATE_FORMAT(p.dateSubmitted, '%b %d, %Y') as submissionDate, 
                         DATE_FORMAT(COALESCE(p.dateRejection, p.dateSubmitted), '%b %d, %Y') as rejectionDate,
                         f.content as reason
                  FROM policytbl p
                  LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
                  LEFT JOIN (
                      SELECT remarksOn, MAX(feedbackID) as max_id
                      FROM feedbacktbl
                      GROUP BY remarksOn
                  ) latest_fb ON p.policyID = latest_fb.remarksOn
                  LEFT JOIN feedbacktbl f ON latest_fb.max_id = f.feedbackID
                  WHERE p.policyStatusID = 6";
        $params = [];
        $types = "";

        if (!empty($month)) {
            $query .= " AND MONTH(COALESCE(p.dateRejection, p.dateSubmitted)) = ?";
            $params[] = $month;
            $types .= 'i';
        }
        if (!empty($year)) {
            $query .= " AND YEAR(COALESCE(p.dateRejection, p.dateSubmitted)) = ?";
            $params[] = $year;
            $types .= 'i';
        }
        $query .= " ORDER BY p.dateSubmitted DESC";
    } else if ($type === 'feedbacks') {
        $query = "SELECT COALESCE(p.originalPolicyID, p.policyID) AS policyId, 
                         MAX(p.title) AS policyTitle, 
                         SUM(CASE WHEN f.fbType = 1 THEN 1 ELSE 0 END) AS feedbackCount,
                         SUM(CASE WHEN f.fbType = 2 THEN 1 ELSE 0 END) AS rejectionFeedbackCount
                  FROM policytbl p 
                  JOIN feedbacktbl f ON p.policyID = f.remarksOn
                  WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($month)) {
            $query .= " AND MONTH(f.dateSubmitted) = ?";
            $params[] = $month;
            $types .= 'i';
        }
        if (!empty($year)) {
            $query .= " AND YEAR(f.dateSubmitted) = ?";
            $params[] = $year;
            $types .= 'i';
        }

        $query .= " GROUP BY COALESCE(p.originalPolicyID, p.policyID) 
                  ORDER BY COUNT(f.feedbackID) DESC";
    } else {
        $statusMap = ['active' => [4, 5]];
        $statusIDs = $statusMap[$type] ?? [4, 5];
        
        $placeholders = implode(',', array_fill(0, count($statusIDs), '?'));
        $query = "SELECT c.categoryName, COUNT(p.policyID) as total
                  FROM categorytbl c 
                  LEFT JOIN policytbl p ON c.categoryID = p.categoryID AND p.policyStatusID IN ($placeholders)";

        $params = $statusIDs;
        $types = str_repeat('i', count($statusIDs));

        if (!empty($month)) {
            $query .= " AND MONTH(COALESCE(p.dateUploaded, p.dateSubmitted)) = ?";
            $params[] = $month;
            $types .= 'i';
        }
        if (!empty($year)) {
            $query .= " AND YEAR(COALESCE(p.dateUploaded, p.dateSubmitted)) = ?";
            $params[] = $year;
            $types .= 'i';
        }
        $query .= " GROUP BY c.categoryID, c.categoryName ORDER BY c.categoryName ASC";
    }

    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        // ✨ Prevent PHP Fatal Errors from outputting HTML and breaking the JS JSON parser
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Query prepare failed: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
        // ✨ Prevent PHP Fatal Errors from outputting HTML and breaking the JS JSON parser
        if (!$result) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $conn->error]);
            exit;
        }
    }

    $data = [];
    if ($result) while($row = $result->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
}

// ✨ HELPER FUNCTION: Placed outside or inside to handle tracking logic safely
function processSummaryRow($row, &$kpiCounts, &$statusCounts) {
    $statusID = (int)$row['policyStatusID'];
    $count = (int)$row['cnt'];

    // For Top Summary Boxes
    if (in_array($statusID, [4, 5])) $kpiCounts['active'] += $count;
    if (in_array($statusID, [1, 2, 3])) $kpiCounts['pending'] += $count;
    if ($statusID === 6) $kpiCounts['rejected'] += $count;

    // For Status Donut Chart
    if (in_array($statusID, [4, 5])) $statusCounts['approved'] += $count;
    if (in_array($statusID, [2, 3])) $statusCounts['under_review'] += $count;
    if ($statusID === 1) $statusCounts['draft'] += $count;
    if ($statusID === 6) $statusCounts['archived'] += $count;
}
?>
