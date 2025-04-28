<!-- Include message generation -->
<?php require_once '../../genMsg/message_box.php'; 

?>

<!-- Forgot Password -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-container">
    <img src="../../assets/logos/logo.png" alt="Asia Pacific College Logo">
    <h1>Asia Pacific College<br></h1>
    <h4>Enter email password for password reset<br></h4>
    <h2>Forgot Password<br></h2>
    <form action="forgotPassBE.php" method="POST">
        <input type="text" name="email" placeholder="Email">

        <button type="submit" class="button confirm-button" name="resetPassword">Reset Password</button>
    </form>
    <p>Back to <a href="login.php" id="signUpLink">Log in</a></p>
</div>

<script src="login.js"></script>
</body>
</html>
