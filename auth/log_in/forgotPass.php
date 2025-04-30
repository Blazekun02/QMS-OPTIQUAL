<!-- Include message generation -->
<?php require_once '../../genMsg/message_box.php'; 

?>

<!-- Forgot Password -->
<!DOCTYPE html>
<html lang="en">
<h>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="logIn.css">

<style>

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
            height: 100vh;
            margin: 0;
        }

        .logIn-overlay {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 1em;
            width: 23%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .login-container h1 {
            color: black;
            margin-top:-2.7em;
            margin-left: 2.3em;
            margin-bottom: 0;
            font-size: 1.5em;
            font-family: Istok Web;
        }

         .forgot-h2 {
            font-size: 0.8rem;
            color: black;
            text-align: left;
            margin-bottom: 0.3em;
            margin-top: 1.8em;
        }

        .submit-button {
            background-color:#0056b3;  
            color: white;
            border: none;
            padding: 0.5em 0.8em;
            border-radius: 0.5em;
            cursor: pointer;
            font-size: 0.8rem;
            margin: 0; /* Remove extra margins */
            white-space: nowrap; /* Prevent text wrapping */
            margin-left:-18em;
        }

        .submit-button:hover {
            background-color: #0A185F;
        }

        a {
            color: #293A82;
            font-size: 1em;
            text-decoration: underline;
            white-space: nowrap; /* Prevent text wrapping */
            
        }

        .action-container {
            display: flex; /* Align items horizontally */
            justify-content: space-between; /* Space between button and link */
            align-items: center; /* Vertically align items */
            margin-top: 0.2em; /* Add spacing above */
            white-space: nowrap; /* Prevent text wrapping */
            text-align:center;
            margin-left: 0.3em;
        }

        .action-container p {
            margin: 0; /* Remove extra margins */
        }


        input[type="text"] {
            width: 100%;
            padding: 0.5em;
            margin: 0.5em 0;
            border: 1px solid #ccc;
            border-radius: 0.5em;
            font-size: 0.8rem;
            margin-bottom: 0.7em;
        }



    </style>
</head>

<body>
<div class="logIn-overlay">
        <div class="login-container">
            <img src="../../assets/logos/logo.png" alt="Asia Pacific College Logo">
            <h1>Asia Pacific College<br></h1>
            <div class="forgot-h2">Forgot Password?<br></div>
            <form action="forgotPassBE.php" method="POST">
                <input type="text" name="email" placeholder="Enter email for password reset">
                <div class="action-container">
                  <p>Back to <a href="login.php" id="signUpLink">Log in</a></p>
                    <button class="submit-button" name="submit">Send Password</button>
                </div>
            </form>
        </div>
    </div>

</body>
<script src="login.js"></script>
</body>
</html>
