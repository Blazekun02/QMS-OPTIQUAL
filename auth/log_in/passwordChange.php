<?php 

//include filepaths
require_once __DIR__ .  '/../../filepaths.php';

//include message box
require_once genMsg_dir . '/message_box.php'; 

?>

<!-- Password Change -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="passwordChange.css">
</head>
<body>

<div class ="logIn-overlay">
    <div class="main-content-wrapper">
        <div class="login-container">
            <img src="/qms_optiqual/assets/logos/logo.png" alt="Asia Pacific College Logo">
            <h1>Asia Pacific College</h1>
            <h2>Change Password</h2>
            <form action="passwordChangeBE.php" method="POST">
                <h5>New Password</h5>
                <input type="password" id="password" name="newPassword" placeholder="Enter new password">
                <h5>Confirm New Password</h5>
                <input type="password" id="confirmPassword" name="confirmNewPassword" placeholder="Confirm new password">
                
                <!-- Show Password Checkbox -->
                <div class="checkbox-container">
                    <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                    <label for="showPassword">Show Password</label>
                </div>

                <button type="submit" class="button confirm-button" name="confirmPasswordButton">Confirm</button>
            </form>
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
</div>
<!-- Floating message box -->
<div class="requirementsMessage" id="emailRequirements">
    <p>Email must be a valid APC email</p>
</div>

<script src="passwordChange.js"></script>
</body>
</html>