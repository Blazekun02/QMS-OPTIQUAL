<?php

session_start(); // Start the session

// Include the message setting function
include_once '../../genMsg/setMessage.php'; 

//Include connection verification
include_once '../../auth/connectionVerification.php';


//Check if submitted
if (isset($_POST["confirmButton"])) {

    // //Track number of attempts
    // if(!isset($_SESSION['attempts'])){
    //     $_SESSION['attempts'] = 0;
    // }else {
    //     $_SESSION['attempts']++;
    // }

    // //Check if attempts are more than 5
    // if($_SESSION['attempts'] > 5){
    //     setMessage("Too many attempts!, Try again in 10 minutes","error");
    //     header("Location: login.php");
    //     exit;
    // }

    //Check if email and password are not empty
    if(!empty(trim($_POST['email'])) && !empty(trim($_POST['password']))) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Include database connection
        require_once "../../connect.php";

        //retrieve accID from database
        $sql_retreive_accID = "SELECT accID FROM accdatatbl WHERE email = ? AND password = ?";
        $sql_retreive_accID = mysqli_query($conn, $sql_retreive_accID);
        if (mysqli_num_rows($sql_retreive_accID) > 0) {
            $row = mysqli_fetch_assoc($sql_retreive_accID);
            $accID = $row['accID'];
            $_SESSION['accID'] = $accID; // Store accID in session

            // Call the function to redirect based on role
            directTo($accID);
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