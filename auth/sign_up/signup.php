<?php 

//Include filepaths
require_once __DIR__ . '/../../filepaths.php';

//Include connection verification

require_once genMsg_dir . '/message_box.php'; ?>


<!-- Purpose: Sign up page for the user to create an account -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Asia Pacific College</title>
    <link rel="stylesheet" href="signUp.css">
</head>
<body>
<div class="signUp-overlay">
    <div class="form-container" id="signUpForm">
        <img src="../../assets/logos/logo.png" alt="Asia Pacific College Logo">
        <h2>Asia Pacific College<br></h2>
        <h3>Sign Up</h3>


        <form action="register.php" method="POST">
            <input type="text" name="firstName" placeholder="First Name" required>
            <input type="text" name="lastName" placeholder="Last Name" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="password" id= "confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
            
            <!-- Show Password Checkbox -->
            <p><input type="checkbox" id="showPassword"> Show Password</p>


            <button type="submit" class="button confirm-button" name="confirmButton">Confirm</button>
        </form>
        <p>Already have an account? <a href="../../auth/log_in/login.php" id="loginLink">Login</a></p>
    </div>

    <!-- Floating message box -->
    <div class="requirementsMessage" id="emailRequirements">
        <p>Email must be a valid APC email</p>
    </div>

    <div class="requirementsMessage" id="passwordRequirements">
        <p>Password must meet the following requirements:</p>
        <ul>
            <li id="length" class="invalid">At least 12 characters</li>
            <li id="uppercase" class="invalid">At least one uppercase letter</li>
            <li id="lowercase" class="invalid">At least one lowercase letter</li>
            <li id="number" class="invalid">At least one number</li>
            <li id="special" class="invalid">At least one special character</li>
        </ul>
    </div>    
</div>    

    <script src="signup.js"></script>
</body>
</html>
