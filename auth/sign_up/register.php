<?php
// Start session to store messages
if(!session_id()) {
    session_start();
}

// Include path file
require_once __DIR__ . '/../../filepaths.php';

// Include the message setting function
require_once genMsg_dir . '/setMessage.php'; 


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Validate required fields
    if (empty($_POST["firstName"]) || empty($_POST["lastName"]) || empty($_POST["email"]) || empty($_POST["password"])) {
        setMessage("Error: All fields are required.", "error");
        header("Location: signup.php");
        exit();
    }

    // Retrieve form data and sanitize input
    $fname = trim($_POST["firstName"]);
    $lname = trim($_POST["lastName"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirmPassword = $_POST["confirmPassword"];
    $PIN = random_int(100000, 999999); // Generate random verification token

    // Validate email domain
    // $emailDomain = substr(strrchr($email, "@"), 1); // Extract the domain from the email
    // if ($emailDomain !== "gmail.com") {
    //     setMessage("Email must be an APC email (apc.edu.ph)", "warning");
    //     header("Location: signup.php");
    //     exit();
    // }

    // Validate password and confirm password match
    if ($password !== $confirmPassword) {
        setMessage("Password and Confirm Password do not match.", "error");
        header("Location: signup.php");
        exit();
    }

    // Validate password strength
    if (strlen($password) < 12) {
        setMessage("Password must be at least 12 characters long.", "warning");
        header("Location: signup.php");
        exit();
    }
    if (!preg_match('/[A-Z]/', $password)) {
        setMessage("Password must include at least one uppercase letter.", "warning");
        header("Location: signup.php");
        exit();
    }
    if (!preg_match('/[a-z]/', $password)) {
        setMessage("Password must include at least one lowercase letter.", "warning");
        header("Location: signup.php");
        exit();
    }
    if (!preg_match('/[0-9]/', $password)) {
        setMessage("Password must include at least one number.", "warning");
        header("Location: signup.php");
        exit();
    }
    if (!preg_match('/[\W_]/', $password)) {
        setMessage("Password must include at least one special character.", "warning");
        header("Location: signup.php");
        exit();
    }

    // Include database connection
    require_once BASE_DIR . '/connect.php'; 

    // Check if email already exists
    $SELECT = "SELECT email FROM accdatatbl WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($SELECT);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        setMessage("Email already exists.", "error");
        header("Location: signup.php");
        exit();
    } else {
        // Close the SELECT statement
        $stmt->close();

        // Hash the password
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user record
        $INSERT = "INSERT INTO accdatatbl (fname, lname, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($INSERT);
        $stmt->bind_param("ssss", $fname, $lname, $email, $password);
        
        if ($stmt->execute()) {
            //Retrieve accID from database
            $SELECT = "SELECT accID FROM accdatatbl WHERE email = ?";
            $stmt = $conn->prepare($SELECT);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt-> get_result();
            if ($result->num_rows > 0) {
                $row = $result -> fetch_assoc();
                $accID = $row["accID"];
            } else {
                setMessage("Connection error: ANF","error");
                header("Location: signup.php");
                exit();
            }
            // Store accID in session
            $_SESSION['accID'] = $accID;

            //Insert new OTP Verification Token
            $INSERT_OTP = "INSERT INTO otptbl (requestBy, PIN, isUsedFor) VALUES (?, ?, 0)";
            $stmt = $conn->prepare($INSERT_OTP);
            $stmt->bind_param("ii", $accID, $PIN);

            if ($stmt->execute()) {
                // Include email sending function
                require_once emailing_dir . '/emailSend.php'; 
                
                $email_subject = "Email Verification";
                $email_template = "
                    <h2>You have Registered with the Quality Management System</h2>
                    <h5>Here is the code for your email verification</h5>
                    <br/><br/>
                    <h1><b>$PIN<b/></h1>
                "; 
    
                // Send verification email
                sendEmail_Verify( $email, $email_subject, $email_template);
    
                //Set OTP type to 0 (email verification)
                $_SESSION["otpType"] = 0;

                // Redirect to OTP verification page
                setMessage("Verification email has been sent succesfully!","success");
                header("Location: ../otpVerify/otpVerify.php");
                exit();

            } else {
                setMessage("Connection Error: OIE","error");
                header("Location: signup.php");
                exit();
            }

        } else {
            setMessage("Connection Error: AIE","error");
            header("Location: signup.php");
            exit();
        }
    }

} else {
    setMessage("Invalid request method", "error");
    header("Location: signup.php");
    exit();
}
?>
