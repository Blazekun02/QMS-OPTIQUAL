<?php
include '../../connect.php';
header('Content-Type: application/json');

// Get parameters
$action = $_GET['action'] ?? 'summary'; // 'summary' for dashboard, 'details' for table
$type = $_GET['type'] ?? 'active';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? date('Y');

if ($action === 'summary') {
    // --- 1. KPI & Chart Data ---
    // This query gets the count for ALL statuses in one go for efficiency
    $status_query = "SELECT policyStatusID, COUNT(*) as cnt FROM policytbl GROUP BY policyStatusID";
    $status_results = mysqli_query($conn, $status_query);
    
    // Initialize arrays to hold the data for the dashboard components
    $kpiCounts = [
        'active' => 0, 
        'pending' => 0, 
        'rejected' => 0,
        'speed' => 0,    // Placeholder for average completion time
        'expiring' => 0  // Placeholder for policies expiring soon
    ];
    $statusCounts = [
        'approved' => 0,     // Status 4 & 5
        'under_review' => 0, // Status 2, 3
        'draft' => 0,        // Status 1
        'archived' => 0      // Status 6 (Rejected/Archived fallback)
    ];
    
    // Loop through the query results and populate our data arrays
    while($row = mysqli_fetch_assoc($status_results)) {
        // For KPI boxes
        if (in_array($row['policyStatusID'], [4, 5])) $kpiCounts['active'] += $row['cnt'];
        if (in_array($row['policyStatusID'], [1, 2, 3])) $kpiCounts['pending'] += $row['cnt'];
        if ($row['policyStatusID'] == 6) $kpiCounts['rejected'] += $row['cnt'];

        // For Status Donut Chart
        if (in_array($row['policyStatusID'], [4, 5])) $statusCounts['approved'] += $row['cnt'];
        if (in_array($row['policyStatusID'], [2, 3])) $statusCounts['under_review'] += $row['cnt'];
        if ($row['policyStatusID'] == 1) $statusCounts['draft'] = $row['cnt'];
        if ($row['policyStatusID'] == 6) $statusCounts['archived'] = $row['cnt'];
    }

    // --- Assemble the final response to match the new dashboard's expectations ---
    $response = [
        'kpiData' => $kpiCounts,
        'workflowData' => [5, 8, 4, 2], // Placeholder data for workflow chart
        'statusData' => array_values($statusCounts),
        'accessData' => [rand(10,50), rand(20,60), rand(30,70), rand(25,65), rand(40,80), rand(15,40), rand(5,20)] // Placeholder data for access chart
    ];
    
    echo json_encode($response);

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
} else {
    // 2. Fetch detailed breakdown for the table
    if ($type === 'pending') {
        $statusId = $_GET['statusId'] ?? '';
        // ✨ THE FIX: Query specific to Pending Tasks showing the policy name, current task, and days assigned
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
        // ✨ NEW: Fetch specific details for Rejected policies including the latest feedback
        $query = "SELECT p.policyID, p.title, a.fullName as authorName, 
                         p.dateSubmitted as submissionDate, 
                         p.dateRejection as rejectionDate,
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
            $query .= " AND MONTH(p.dateSubmitted) = ?";
            $params[] = $month;
            $types .= 'i';
        }
        if (!empty($year)) {
            $query .= " AND YEAR(p.dateSubmitted) = ?";
            $params[] = $year;
            $types .= 'i';
        }
        $query .= " ORDER BY p.dateSubmitted DESC";
    } else {
        // ✨ Map to arrays so we can catch 4 and 5 as Active.
        $statusMap = ['active' => [4, 5]];
        $statusIDs = $statusMap[$type] ?? [4, 5];
        
        $placeholders = implode(',', array_fill(0, count($statusIDs), '?'));
        $query = "SELECT c.categoryName, COUNT(p.policyID) as total
                  FROM categorytbl c 
                  LEFT JOIN policytbl p ON c.categoryID = p.categoryID AND p.policyStatusID IN ($placeholders)";

        $params = $statusIDs;
        $types = str_repeat('i', count($statusIDs));

        if (!empty($month)) {
            $query .= " AND MONTH(p.dateUploaded) = ?";
            $params[] = $month;
            $types .= 'i';
        }
        if (!empty($year)) {
            $query .= " AND YEAR(p.dateUploaded) = ?";
            $params[] = $year;
            $types .= 'i';
        }
        $query .= " GROUP BY c.categoryID, c.categoryName ORDER BY c.categoryName ASC";
    }

    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
    }

    $data = [];
    if ($result) while($row = $result->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
}
?>