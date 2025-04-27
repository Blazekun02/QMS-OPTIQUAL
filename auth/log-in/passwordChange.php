<!-- Include message generation -->
<?php require_once '../../genMsg/message_box.php'; 

?>

<!-- Password Change -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In Page</title>
    <link rel="stylesheet" href="passwordChange.css">
</head>
<body>

<div class="login-container">
    <img src="../../assets/logos/logo.png" alt="Asia Pacific College Logo">
    <h1>Asia Pacific College<br></h1>
    <h2>Password Change<br></h2>
    <form action="passwordChangeBE.php" method="POST">
        <h5>New Password</h5>
        <input type="password" id="password" name="newPassword" placeholder="New Password">
        <h5>Confirm New Password</h5>
        <input type="password" id="confirmPassword"  name="confirmNewPassword" placeholder="Confirm New Password">

        <!-- Show Password Checkbox -->
        <p><input type="checkbox" id="showPassword"> Show Password</p>

        <button type="submit" class="button confirm-button" name="confirmPasswordButton">Confirm</button>
    </form>
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

<script src="passwordChange.js"></script>
</body>
</html>