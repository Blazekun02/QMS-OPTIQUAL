<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Include path file
require_once __DIR__ . '/../filepaths.php';

// Include the message setting function
require_once genMsg_dir . '/setMessage.php';

function directTo($accID){
    // Include database connection
    require_once connect_php;

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
        setMessage("Role not Found","error");
        header("Location: /qms_optiqual/auth/log_in/login.php");
        exit();
    }
    $stmt->close();
    $conn->close();

    //Redirect based on RoleID
    switch ($roleID){
        case 1://Admin
            setMessage("Logged In Succesfully!","success");
            header("Location: ../../");
            break;
        case 2://QA Director
            setMessage("Logged In Succesfully!","success");
            header("Location: /qms_optiqual/QADSpecificComponents/QADMain/QAD-POV.php");
            break;
        case 3://QA Personnel
            setMessage("Logged In Succesfully!","success");
            header("");
            break;
        case 4://Staff
            setMessage("Logged In Succesfully!","success");
            header("Location: /qms_optiqual/staffSpecificComponents/staffMain/staff-POV.php");
            break;
        default://Error
            setMessage("Invalid Role","error");
            header("Location: /qms_optiqual/auth/log_in/login.php");
            exit();    
    }
    exit();
}

//Check if accID is set
if (isset($_SESSION['accID']) && isset($_SESSION['authenticated'])) {
    $accID = $_SESSION['accID'];
    directTo($accID);
} else {
    header("Location: /qms_optiqual/auth/log_in/login.php");
    exit();
}


?>