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

//Retrieve otp type session request
if (isset($_SESSION["otpTypeRequest"])) {
    $otpTypeRequest = $_SESSION["otpTypeRequest"];
} else {
    setMessage("OTP type not found", "error");
    header("Location: otpVerify.php");
    exit();
}

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
    $sql_retreive_PIN = "SELECT PIN, isUsedFor, otpStatus, otpCreated FROM otptbl WHERE requestBy = ? ORDER BY otpCreated DESC LIMIT 1";
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
        $isUsedFor = $row["isUsedFor"];
        $otpStatus = $row["otpStatus"];
        $otpCreated = $row["otpCreated"];
    } else {
        setMessage("PIN not found", "error");
        header("Location: otpVerify.php");
        exit();
    }
    $stmt->close();

    // Check if the OTP is valid, unused and matches the session request
    if ($otp == $PIN && $isUsedFor == $otpTypeRequest && $otpStatus == 'unused') {
        // Update OTP status to used
        $sql_update_otpStatus = "UPDATE otptbl SET  otpStatus = 'used' WHERE requestBy = ? AND PIN = ? AND isUsedFor = ? AND otpStatus = 'unused'";
        $stmt = $conn->prepare($sql_update_otpStatus);
        if (!$stmt) {
            setMessage("Database error on OTP status update","error");
            header("Location: otpVerify.php");
            exit();
        }
        $stmt->bind_param("iii", $accID, $PIN, $otpTypeRequest);
        $stmt->execute();

        //Update account status to verified or redirect to password reset
        if ($otpTypeRequest == 0) {
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
            $_SESSION["accID"] = $accID; // Set the accID again for the next page
            $_SESSION["authenticated"] = true; // Set authenticated to true

            setMessage("Account Verified Succesfully!","success");
            header("Location: ../connectionVerification.php");
            exit();
        } else if ($otpTypeRequest == 1){
            //Direct to password change page
            setMessage("Sucessfully redirected to password change","success");
            header("Location: ../log-in/passwordChange.php");
            exit();
        }

        
    } else {
        // Redirect back with an error message
        setMessage("Invalid verification code","error");
        header("Location: otpVerify.php");
        exit();
    }
}   

// Function to resend verification code and email
function resendVerificationCode($accID, $conn, $otpTypeRequest) {
    // Generate a new verification code
    $newPIN = random_int(100000, 999999);

    //Insert new verification code in the database
    $sql_insertNewPIN = "INSERT INTO otptbl (requestBy, PIN, isUsedFor) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_insertNewPIN);
    $stmt->bind_param("iii", $accID, $newPIN, $otpTypeRequest);
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

    //Retrieve email from database
    $sql_retrieve_accDetails = "SELECT email FROM accdatatbl WHERE accID = ?";
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
        $email = $row["email"];
    } else {
        setMessage("Account details not found","error");
        header("Location: otpVerify.php");
        exit();
    }

    // Include email sending function
    require_once '../../auth/emailing/emailSend.php'; 
    
    if ($otpTypeRequest == 0) {
        $email_subject = "Resend: Email Verification";
        $email_template = "
            <h2>You have Registered with the Quality Management System</h2>
            <h5>Here is the code for your email verification</h5>
            <br/><br/>
            <h1><b>$newPIN<b/></h1>
        ";
    } elseif ($otpTypeRequest == 1) {
        $email_subject = "Resend: Password Reset";
        $email_template = "
            <h2>You have requested a password reset with the Quality Management System</h2>
            <h5>Here is the code for your password reset confirmation</h5>
            <br/><br/>
            <h1><b>$newPIN<b/></h1>
        ";
    } else {
        setMessage("Invalid OTP type","error");
        header("Location: otpVerify.php");
        exit();
    }

    // Send verification email
    sendEmail_Verify( $email, $email_subject, $email_template);

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
    resendVerificationCode($accID, $conn, $otpTypeRequest);
    
    setMessage("Verification code resent","success");
    header("Location: otpVerify.php");
    exit();
}

?>