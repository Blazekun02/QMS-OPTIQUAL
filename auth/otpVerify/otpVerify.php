<?php 
//include filepaths
require_once __DIR__ . '/../../filepaths.php';

//include message box
include_once genMsg_dir . '/message_box.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification - Asia Pacific College</title>
    <link rel="stylesheet" href="otpVerification.css?v=1.1">
</head>
<body>

<div class="overlay"></div>
    <div class="form-container" id="VerifyForm">
        <div class="header-group">
            <img src="../../assets/logos/logo.png" alt="APC Logo">
            <h2>Asia Pacific College</h2>
        </div>

        <h3>OTP Verification</h3>
        <form action="otpVerifyBE.php" method="POST">
            <input type="text" name="OTP" placeholder="Enter Verification Code" required>
            <p>Didn't receive a code? <a href="otpVerifyBE.php?resend=true">Resend Code</a></p>
            <button type="submit" class="button confirm-button" name="signup">Confirm</button>
        </form>
    </div>
</div>    
</body>
</html>
 