<?php

// Start session
if(!session_id()){
    session_start();
}

// Include filepaths
require_once __DIR__ . "/../../filepaths.php";

// Include the message setting function
require_once genMsg_dir . "/setMessage.php";

//initialize variables
$newPassword = $confirmNewPassword = "";

//Check if submitted
if (isset($_POST["confirmPasswordButton"])) {
    //Trim all input values to remove extra spaces
    $_POST = array_map('trim', $_POST);

    //Check if password and confirm password are not empty
    if (empty($_POST["newPassword"]) || empty($_POST["confirmNewPassword"])) {
        setMessage("Error: All fields are required.", "error");
        header("Location: passwordChange.php");
        exit();
    }

    //Retrieve form data and sanitize input
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];

    // Validate password and confirm password match
    if ($newPassword !== $confirmNewPassword) {
        setMessage("Password and Confirm Password do not match.", "error");
        header("Location: passwordChange.php");
        exit();
    }

    // Validate password strength
    if (strlen($newPassword) < 12) {
        setMessage("Password must be at least 12 characters long.", "warning");
        header("Location: passwordChange.php");
        exit();
    }
    if (!preg_match('/[A-Z]/', $newPassword)) {
        setMessage("Password must include at least one uppercase letter.", "warning");
        header("Location: passwordChange.php");
        exit();
    }
    if (!preg_match('/[a-z]/', $newPassword)) {
        setMessage("Password must include at least one lowercase letter.", "warning");
        header("Location: passwordChange.php");
        exit();
    }
    if (!preg_match('/[0-9]/', $newPassword)) {
        setMessage("Password must include at least one number.", "warning");
        header("Location: passwordChange.php");
        exit();
    }
    if (!preg_match('/[\W_]/', $newPassword)) {
        setMessage("Password must include at least one special character.", "warning");
        header("Location: passwordChange.php");
        exit();
    }

    // Include database connection
    require_once BASE_DIR . "/connect.php";


    //retieve the email from the session
    $email = $_SESSION["email"];

    if (empty($email)) {
        die("Error: Email is not set in the session.");
    }

    //Hash the password
    $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    //Update Password
    $sql_update_password = "UPDATE accdatatbl SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql_update_password);
    $stmt->bind_param("ss", $newPassword, $email);
    if ($stmt->execute()) {
        //unset the session variable
        session_unset();

        // Password updated successfully
        setMessage("Password changed successfully.", "success");
        header("Location: login.php");

        //close the statement and connection
        $stmt->close();
        $conn->close();

        exit();

    } else {
        // Error updating password
        setMessage("Error changing password. Please try again.", "error");
        header("Location: passwordChange.php");

        //close the statement and connection
        $stmt->close();
        $conn->close();

        exit();
    }
       
}

?>