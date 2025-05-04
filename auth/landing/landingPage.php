<?php
// Start the session
if (!session_id()) {
    session_start();
}

// Include the path file
require_once __DIR__ . '/../../filepaths.php';

// Include the message setting function
require_once genMsg_dir . '/setMessage.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Management System</title>
    <link rel="stylesheet" href="landingPage.css">
</head>
<body>
<div class="landing-overlay">
<div class="box">
    <!-- use ../../assets/ to access the image -->
    <img src="../../assets/logos/logo.png" alt="School Logo">
    <p style="color: #fbaf41; font-size: 18px;">Asia Pacific College</p>
    <p style="color: #293A82; font-size: 18px;">Quality Management System</p>
    <button onclick="redirecttodataPrivacy()">Sign Up</button>
    <button onclick="redirectToLogin()">Log In</button>
</div>
</div>    
<script>
    function redirecttodataPrivacy() {
        window.location.href = "../../auth/dataPriv/dataPrivacy.html";
    }

    function redirectToLogin() {
        window.location.href = "../../auth/connectionVerification.php";
    }

</script>
</body>
</html>