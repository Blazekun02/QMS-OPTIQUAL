<!-- Include message generation -->
<?php 
// Start session if not already started
if (!session_id()) {
    session_start();
}

//include file paths
require_once __DIR__ . '/../../filepaths.php';

//incude message box
require_once genMsg_dir . '/message_box.php'; 

?>

<!-- Login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In Page</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="login.css?v=1.1">
</head>

<body>
<div class="logIn-overlay">    
    <div class="login-container">
        <img src="../../assets/logos/logo.png" alt="Asia Pacific College Logo">
        <h1>Asia Pacific College
</h1>
        <h2>Log In
</h2>
        <form action="loginBE.php" method="POST">
            <input type="text" name="email" placeholder="Email">

            <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="Password">
                <span id="togglePassword" class="field-icon">👁</span>
            </div>

            <a id="forgotPass" href="forgotPass.php">Forgot Password?</a>

            <div class="button-container">
                <button type="button" class="button" id="backButton">Back</button>
            <button type="submit" class="button" name="signInButton">Sign In</button>
        </div>
            <b>Don't have an account?<a href="/qms_optiqual/auth/sign_up/signup.php">Sign up here</a></b>
        </form>
    </div>
</div>

<script src="login.js"></script>
</body>
</html>
 