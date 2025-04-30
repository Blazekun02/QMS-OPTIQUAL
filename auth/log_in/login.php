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
<style>


</style>

<body>
<div class="logIn-overlay">    
    <div class="login-container">
    <img src="../../assets/logos/logo.png" alt="Asia Pacific College Logo">
        <h1>Asia Pacific College<br></h1>
        <h2>Log In<br></h2>
        <label for="email">Email address:</label>
        <input type="email" id="email" name="email">
        <label for="pwd">Password:</label>
        <input type="password" id="pwd" name="pwd" autocomplete="current-password">
        <a href="#">Forgot Password?</a>

        <div class="button-container">
            <button type="button" class="button back-button" id="backButton">Back</button>
            <button type="button" class="button signIn-button" id="signInButton">Sign In</button>
        </div>
        <b>Don't have an account?<a href="signUp.php">Sign up here</a></b>
    </div>
</div>
<script src="login.js"></script>
</body>
</html>
