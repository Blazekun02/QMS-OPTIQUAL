<?php
//check if session is started
session_start();

// Include the message setting function
include_once '../../genMsg/setMessage.php';

//check if form is submitted
if ($_SERVER["REQUEST_METHOD"]==="POST"){

    // Check if email is empty
    if (empty($_POST["email"])){
        setMessage("Email is required", "error");
        header("Location: forgotPass.php");
        exit();
    }

    // Retrieve email and sanitize input
    $email = trim($_POST["email"]);

    // Generate random verification token
    $PIN = random_int(100000, 999999); 

    // Validate email domain
    // $emailDomain = substr(strrchr($email, "@"), 1); // Extract the domain from the email
    // if ($emailDomain !== "gmail.com") {
    //     setMessage("Email must be an APC email (apc.edu.ph)", "warning");
    //     header("Location: signup.php");
    //     exit();
    // }

    //Include database connection
    require_once "../../connect.php";

    //Retrieve accID from database
    $sql_retrieve_accID = "SELECT accID FROM accdatatbl WHERE email = ?";
    $stmt = $conn->prepare($sql_retrieve_accID);
    if (!$stmt) {
        setMessage("Database error on retrieving account ID", "error");
        header("Location: forgotPass.php");
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $accID = $row["accID"];
    } else {
        setMessage("Email not found", "error");
        header("Location: forgotPass.php");
        exit();
    }
    $stmt->close();

    //Insert PIN into database
    $sql_insert_PIN = "INSERT INTO otptbl (PIN, requestBy, isUsedFor, otpStatus) VALUES (?, ?, 1, 'unused')";
    $stmt = $conn->prepare($sql_insert_PIN);
    if (!$stmt) {
        setMessage("Database error on inserting PIN", "error");
        header("Location: forgotPass.php");
        exit();
    }
    $stmt->bind_param("ii", $PIN, $accID);
    if ($stmt->execute()){
        //include the email sending function
        include_once '../emailing/emailSend.php';

        //Send Reset PIN
        $email_subject = "Password Reset";
        $email_template = "
                    <h2>You have requested a password reset with the Quality Management System</h2>
                    <h5>Here is the code for your password reset confirmation</h5>
                    <br/><br/>
                    <h1><b>$PIN<b/></h1>
        ";

        sendEmail_Verify($email, $email_subject, $email_template);

        //set otp type request to 1 for password reset
        $_SESSION["otpTypeRequest"] = 1; // 1 for password reset

        //set accID to session
        $_SESSION["accID"] = $accID;

        //redirect to the OTP verification page
        setMessage("A verification code has been sent to your email.","success");
        header("Location: ../otpVerify/otpVerify.php");
        exit();
    }
}

?>