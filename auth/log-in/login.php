<!-- Include message generation -->
<?php require_once '../../genMsg/message_box.php'; 

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
        <input type="password" name="password" placeholder="Password">
        <a id="forgotPass" href="#">Forgot Password?</a>

        <button type="submit" class="button confirm-button" name="confirmButton">Confirm</button>
    </form>
    <p>Don't have an account? <a href="../../auth/sign-up/signup.php" id="signUpLink">Sign Up</a></p>
</div>

<script src="login.js"></script>
</body>
</html>
