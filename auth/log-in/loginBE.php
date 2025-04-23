<?php
date_default_timezone_set('Asia/Manila');
if (!session_id()) {
    session_start();
}

// Include the message setting function
require_once "../../genMsg/setMessage.php";


//Check if submitted
if (isset($_POST["confirmButton"])) {

    //Check if attempts are more than 5 and if there is a cooldown period
    if($_SESSION['attempts'] > 5){
        //set cooldown period
        if (!isset($_SESSION['cooldownEndTime'])) {
            $_SESSION['cooldownEndTime'] = time() + 300; // 5 minutes cooldown
        }

        //Check if cooldown period is still active
        if (time() < $_SESSION['cooldownEndTime']){
            setMessage("Please wait for 5 minutes before trying again","error");
            header("Location: login.php");
            exit;
        } else {
            // Reset attempts and cooldown time
            unset($_SESSION['attempts']);
            unset($_SESSION['cooldownEndTime']);
        }
        
    }
    else {
        //Track number of attempts
        if(!isset($_SESSION['attempts'])){
            $_SESSION['attempts'] = 0;
        }else {
            $_SESSION['attempts']++;
        }
    }

    //Check if email and password are not empty
    if(!empty(trim($_POST['email'])) && !empty(trim($_POST['password']))) {
        $email = trim($_POST['email']);
        $inputtedPassword = trim($_POST['password']);

        // Include database connection
        require_once "../../connect.php";

        //check if acc exists
        $sql_retreive_accID = "SELECT accID, password FROM accdatatbl WHERE email = ?";
        $stmt = $conn->prepare($sql_retreive_accID);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result-> num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            //Check if passwords match
            if (password_verify($inputtedPassword, $hashedPassword)) {
                // Store accID in session
                $accID = $row['accID'];
                $_SESSION['accID'] = $accID;
                // Unset attempts on successful login
                unset($_SESSION['attempts']);
                // Call the function to redirect based on role
                directTo($accID);
            } else {
                setMessage("Invalid Email or Password 2","error");
                header("Location: login.php");
                exit;
            }
            
            
        } else {
            setMessage("Invalid Email or Password","error");
            header("Location: login.php");
            exit;
        }
    } else {
        setMessage("Email or Password is Empty","error");
        header("Location: login.php");
        exit;
    }

}


?>