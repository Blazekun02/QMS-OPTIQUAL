<?php
//include filepaths
require_once __DIR__ . '/../../filepaths.php';

//Include set message
require_once genMsg_dir .'/setMessage.php';
//include database connection
require_once BASE_DIR . '/connect.php';

//Start session
if (!session_id()) {
    session_start();
}

//Function to redirect to the login page
function redirectToLogin() {
    //Destroy the session
    session_unset();
    session_destroy();
    // Set a message to inform the user about the logout
    setMessage("Logged out successfully!", "success");
    // Redirect to the login page
    header("Location: /qms_optiqual/auth/log_in/login.php");
    exit();
}

//Call the function to redirect to the login page
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    redirectToLogin();
}

?>