<?php

// Start session
if (!session_id()) {
    session_start();
}

//Include filepaths
require_once __DIR__ . '/../filepaths.php';

// Include the message setting function
require_once genMsg_dir . '/setMessage.php';

// Include database connection
require_once connect_php;

$accID = $_SESSION['accID'] ?? null;


//Retrieve roleID from database
$sql_retreive_roleID = "SELECT roleID FROM accdatatbl WHERE accID = ?";
$stmt = $conn->prepare($sql_retreive_roleID);
$stmt->bind_param("i", $accID);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $roleID = $row["roleID"];
} else{
    echo "Error: Role not found.";
    exit();
}
$stmt->close();
$conn->close();

//Redirect based on RoleID
switch ($roleID){
    case 1://Admin
        setMessage("Redirected Succesfully!","success");
        header("Location: ../../");
        break;
    case 2://QA Director
        setMessage("Redirected Succesfully!","success");
        header("Location: /qms_optiqual/QADSpecificComponents/QADMain/QAD-POV.php");
        break;
    case 3://QA Personnel
        setMessage("Redirected Succesfully!","success");
        header("");
        break;
    case 4://Staff
        setMessage("Redirected Succesfully!","success");
        header("Location: /qms_optiqual/staffSpecificComponents/staffMain/staff-POV.php");
        break;
    default://Error
        echo "Error: Invalid Role.";
        exit();    
}
exit();

?>