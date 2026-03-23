<?php

//start session
if(!session_id()){
    session_start();
}

//Include filepaths
require_once __DIR__ . '/../../filepaths.php';

//Include set message
require_once genMsg_dir . '/setMessage.php';


//Include database connection
require_once BASE_DIR . '/connect.php';

try {
    if (!isset($_SESSION['accID'])) {
        setMessage("Session expired. Please log in again.", "error");
        header("Location: /qms_optiqual/staffSpecificComponents/staffMain/staff-POV.php");
        exit;
    }

    $accID = $_SESSION['accID']; 
    
    // We need to know if this user is the QA Director (Assuming roleID 2 is QA Director)
    // First, let's quickly grab their role
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $roleStmt = $pdo->prepare("SELECT roleID FROM accdatatbl WHERE accID = ?");
    $roleStmt->execute([$accID]);
    $userRole = $roleStmt->fetchColumn();

    $tasks = [];

    // IF USER IS QA DIRECTOR (roleID == 2)
    if ($userRole == 2) {
        // Fetch ALL Pending Policies (policyStatusID = 1) AND tasks specifically assigned to them
        $stmt = $pdo->prepare("
            SELECT 
                p.policyID,
                p.title AS policyTitle, 
                a.fullName AS author, 
                p.dateSubmitted, 
                p.versionNo AS version, 
                'Pending' AS status, 
                p.contentPath AS pdfPath
            FROM policytbl p
            LEFT JOIN accdatatbl a ON p.policyAuthor = a.accID
            WHERE p.policyStatusID = 1
        ");
        
        // Execute the query for Pending policies
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    // IF USER IS NOT QA DIRECTOR
    else {
        // Fetch ONLY tasks specifically assigned to them in the task table
        $stmt = $pdo->prepare("
            SELECT
                p.policyID,
                p.title AS policyTitle,
                a.fullName AS author,
                t.dateCreated AS dateSubmitted,
                p.versionNo AS version,
                tt.taskTypeName AS status,
                p.contentPath AS pdfPath
            FROM tasktbl t
            LEFT JOIN policytbl p ON t.policyAssigned = p.policyID
            LEFT JOIN tasktypetbl tt ON t.taskTypeID = tt.tasktypeID
            LEFT JOIN accdatatbl a ON t.assignedTo = a.accID
            WHERE t.assignedTo = ?
            ORDER BY t.dateCreated
        ");
        $stmt->execute([$accID]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Return data as JSON 
    if ($tasks && count($tasks) > 0) {
        echo json_encode($tasks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['message' => 'No tasks found']);
    }
    
} catch (PDOException $e) { 
     echo json_encode(['error' => 'An error occurred while fetching tasks: ' . $e->getMessage()]);
}

?>