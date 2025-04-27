<?php
//Include set message
require_once '../../genMsg/setMessage.php';
//include database connection
require_once '../../connect.php';
//Start session
session_start();

//Function to redirect to the login page
function redirectToLogin() {
    //Destroy the session
    session_unset();
    session_destroy();
    // Set a message to inform the user about the logout
    setMessage("Logged out successfully!", "success");
    // Redirect to the login page
    header("Location: ../../auth/log-in/login.php");
    exit();
}

//Call the function to redirect to the login page
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    redirectToLogin();
}

?>