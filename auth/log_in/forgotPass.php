<?php require_once '../../genMsg/message_box.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Istok Web', sans-serif;
            background-color: #fbaf41;
            background-image: url('../../assets/bg/bgImage.jpg');
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .logIn-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        .login-container {
            background-color: white;
            padding: 40px;
            width: 90%;
            max-width: 450px; /* Match the Log In box size */
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            border: 1px solid #293A82;
            text-align: left;
        }

        /* Header logic to align logo and text */
        .login-container img {
            width: 50px;
            vertical-align: middle;
        }

        .login-container h1 {
            display: inline-block;
            vertical-align: middle;
            font-size: 1.4rem;
            margin-left: 10px;
            color: black;
            line-height: 1.2;
        }

        .forgot-h2 {
            font-size: 1.1rem;
            font-weight: bold;
            color: black;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-bottom: 1.5px solid #293A82;
            background-color: #f0f4ff;
            font-size: 14px;
            outline: none;
            border-radius: 6px;
        }

        /* Responsive Action Row */
        .action-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .action-container p {
            font-size: 14px;
            color: #333;
        }

        .action-container a {
            color: #293A82;
            text-decoration: underline;
            font-weight: bold;
        }

        .submit-button {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .submit-button:hover {
            background-color: #0A185F;
        }
    </style>
</head>

<body>
<div class="logIn-overlay">
    <div class="login-container">
        <div class="header-row">
            <img src="../../assets/logos/logo.png" alt="Asia Pacific College Logo">
            <h1>Asia Pacific College</h1>
        </div>

        <div class="forgot-h2">Forgot Password?</div>

        <form action="forgotPassBE.php" method="POST">
            <input type="text" name="email" placeholder="Enter email for password reset" required>

            <div class="action-container">
                <p>Back to <a href="login.php">Log in</a></p>
                <button type="submit" class="submit-button" name="submit">Send Password</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>