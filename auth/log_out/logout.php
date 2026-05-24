<?php
session_start();

// Unset all session variables to clear the account data
$_SESSION = array();

// Destroy the session entirely
session_destroy();

// Redirect to the login page (update 'index.php' if your login page is named differently)
header("Location: /qms_optiqual/auth/landing/landingPage.php");
exit();
?>