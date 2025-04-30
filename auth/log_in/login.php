<!-- Include message generation -->
<?php 


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
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-container">
    <img src="../../assets/logos/logo.png" alt="Asia Pacific College Logo">
    <h1>Asia Pacific College<br></h1>
    <h2>Log In<br></h2>
    <form action="loginBE.php" method="POST">
        <input type="text" name="email" placeholder="Email">
        <input type="password" id="password" name="password" placeholder="Password">

        <!-- Show Password Checkbox -->
        <p><input type="checkbox" id="showPassword"> Show Password</p>

        <a id="forgotPass" href="forgotPass.php">Forgot Password?</a>

        <button type="submit" class="button confirm-button" name="confirmButton">Confirm</button>
    </form>
    <p>Don't have an account? <a href="../../auth/sign_up/signup.php" id="signUpLink">Sign Up</a></p>
</div>

<script src="login.js"></script>
</body>
</html>
