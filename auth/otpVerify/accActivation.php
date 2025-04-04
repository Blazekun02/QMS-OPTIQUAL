<?php

date_default_timezone_set('Asia/Manila');
session_start();

// Include the message setting function
include_once '../../genMsg/setMessage.php'; 

// Include database connection
require_once "../../connect.php";

//Retrieve accID from session
if (!isset($_SESSION["accID"])) {
    setMessage("Account ID not found", "error");
    header("Location: otpVerify.php");
    exit();
}
$accID = $_SESSION["accID"];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Check if OTP is empty
    if (empty($_POST["OTP"])) {
        setMessage("Verification code is required","error");
        header("Location: otpVerify.php");
        exit();
    }

    $otp = trim($_POST["OTP"]);

    // Retrieve Latest PIN, otpStatus, otpCreated from database
    $sql_retreive_PIN = "SELECT PIN, otpStatus, otpCreated FROM otptbl WHERE requestBy = ? ORDER BY otpCreated DESC LIMIT 1";
    $stmt = $conn->prepare($sql_retreive_PIN);
    if (!$stmt) {
        setMessage("Database error on retrieving PIN", "error");
        header("Location: otpVerify.php");
        exit();
    }
    $stmt->bind_param("i", $accID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $PIN = $row["PIN"];
        $otpStatus = $row["otpStatus"];
        $otpCreated = $row["otpCreated"];
    } else {
        setMessage("PIN not found", "error");
        header("Location: otpVerify.php");
        exit();
    }
    $stmt->close();

    // Check if the OTP is valid and unused
    if ($otp == $PIN && $otpStatus == 'unused') {
        // Update OTP status to used
        $sql_update_otpStatus = "UPDATE otptbl SET  otpStatus = 'used' WHERE requestBy = ? AND PIN = ?";
        $stmt = $conn->prepare($sql_update_otpStatus);
        if (!$stmt) {
            setMessage("Database error on OTP status update","error");
            header("Location: otpVerify.php");
            exit();
        }
        $stmt->bind_param("ii", $accID , $PIN);
        $stmt->execute();

        //Update account status to verified
        $sql_update_accStatus = "UPDATE accdatatbl SET verificationStatus = 'verified' WHERE accID = ?";
        $stmt = $conn->prepare($sql_update_accStatus);
        $stmt->bind_param("i", $accID);
        if (!$stmt->execute()) {
            setMessage("Database error on updating account status","error");
            header("Location: otpVerify.php");
            exit();
        }

        //Close the statement and connection
        $stmt->close();
        $conn->close();
        
        session_unset(); // Unset all session variables
        $_SESSION["accID"] = $accID; // Set accID to null

        setMessage("Account Verified Succesfully!","success");
        header("Location: ../../auth/log-in/login.php");
        exit();
    } else {
        // Redirect back with an error message
        setMessage("Invalid verification code","error");
        header("Location: otpVerify.php");
        exit();
    }
}   

// Function to resend verification code and email
function resendVerificationCode($accID, $conn) {
    // Generate a new verification code
    $newPIN = random_int(100000, 999999);

    //Insert new verification code in the database
    $sql_insertNewPIN = "INSERT INTO otptbl (requestBy, PIN) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insertNewPIN);
    $stmt->bind_param("ii", $accID, $newPIN);
    if (!$stmt->execute()) {
        setMessage("Database error on inserting new PIN","error");
        header("Location: otpVerify.php");
        exit();
    }
    $stmt->close();

    // Retrieve latest `otpCreated` timestamp
    $sql_latest_otp = "SELECT otpCreated FROM otptbl WHERE requestBy = ? ORDER BY otpCreated DESC LIMIT 1";
    $stmt = $conn->prepare($sql_latest_otp);
    if (!$stmt) {
        setMessage("Database error on retrieving latest OTP","error");
        header("Location: otpVerify.php");
        exit();
    }
    $stmt->bind_param("i", $accID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION["otpCreated"] = $row["otpCreated"]; // Store latest timestamp in session
    }
    $stmt->close();

    //Retrieve Account details from database
    $sql_retrieve_accDetails = "SELECT fname, lname, email FROM accdatatbl WHERE accID = ?";
    $stmt = $conn->prepare($sql_retrieve_accDetails);
    if (!$stmt) {
        setMessage("Database error on retrieving account details","error");
        header("Location: otpVerify.php");
        exit();
    }
    $stmt->bind_param("i", $accID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fname = $row["fname"];
        $lname = $row["lname"];
        $email = $row["email"];
    } else {
        setMessage("Account details not found","error");
        header("Location: otpVerify.php");
        exit();
    }

    // Include email sending function
    require_once '../../auth/emailing/emailSend.php'; 
                
    $email_subject = "Resend: Email Verification";
    $email_template = "
        <h2>You have Registered with the Quality Management System</h2>
        <h5>Here is the code for your email verification</h5>
        <br/><br/>
        <h1><b>$newPIN<b/></h1>
    "; 

    // Send verification email
    sendEmail_Verify($fname, $lname, $email, $newPIN, $email_subject, $email_template);

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect to OTP verification page
    setMessage("A new verification code has been sent to your email.","success");
    header("Location: otpVerify.php");
    exit();
}

// Handle resend request
if (isset($_GET['resend']) && $_GET['resend'] === 'true') {

    // Check last resend time properly
    $sql_check_otp_time = "SELECT otpCreated FROM otptbl WHERE requestBy = ? ORDER BY otpCreated DESC LIMIT 1";
    $stmt = $conn->prepare($sql_check_otp_time);
    $stmt->bind_param("i", $accID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();


    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $otpCreated = $row["otpCreated"];
        $otpCreatedTimestamp = strtotime($otpCreated); // Convert string to timestamp

        if ($otpCreatedTimestamp > time() - 60) {
            setMessage("You can only resend the code every 60 seconds","warning");
            header("Location: otpVerify.php");
            exit();
        }
    }
    

    // Resend verification code
    resendVerificationCode($accID, $conn);
    
    setMessage("Verification code resent","success");
    header("Location: otpVerify.php");
    exit();
}

?>