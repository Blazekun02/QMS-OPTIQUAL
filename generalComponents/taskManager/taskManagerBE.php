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

    //bind the session variable to the query
    $accID = $_SESSION['accID']; 

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch task data
    $stmt = $pdo->prepare("
        SELECT
            p.title AS policyTitle,
            a.fullName AS author,
            t.dateCreated AS dateSubmitted,
            p.versionNo AS version,
            tt.taskTypeName AS status,
            p.contentPath AS pdfPath
            
        FROM tasktbl t
        LEFT JOIN policytbl p ON t.policyAssigned = p.policyID -- Join with policy table
        LEFT JOIN tasktypetbl tt ON t.taskTypeID = tt.tasktypeID -- Join with tasktypes table
        LEFT JOIN accdatatbl a ON t.assignedTo = a.accID -- Join with accdata table
        WHERE t.assignedTo = ?
        ORDER BY t.dateCreated DESC
    ");
    
    // Execute the query with the accID parameter
    $stmt->execute([$accID]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return data as JSON 
    if ($tasks) {
        echo json_encode($tasks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['message' => 'No tasks found']);
    }
} catch (PDOException $e) { 
     // Return a generic error message in production
     echo json_encode(['error' => 'An error occurred while fetching tasks']);
}

?>