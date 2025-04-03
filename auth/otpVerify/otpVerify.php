<!-- Include message generation -->
<?php include '../../genMsg/message_box.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification - Asia Pacific College</title>
    <link rel="stylesheet" href="otpVerify.css">
</head>
<body>
    

    <div class="form-container" id="VerifyForm">
        <img src="../../assets/logos/logo.png" alt="Asia Pacific College Logo">
        <h2>Asia Pacific College<br></h2>
        <h3>Email Verification</h3>
        <form action="accActivation.php" method="POST">
            <input type="text" name="OTP" placeholder="Enter Verification Code" required>
            <p>Didn't receive a code? <a href="accActivation.php?resend=true" id="resendCode">Resend Code</a></p>

            <button type="submit" class="button confirm-button" name="signup">Confirm</button>
        </form>
    </div>
</body>
</html>
