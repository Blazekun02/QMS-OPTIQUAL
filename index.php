<?php
// Start session to check authentication status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include filepaths to access connectionVerification.php easily
require_once __DIR__ . '/filepaths.php';

// Check if the user is already logged in
if (isset($_SESSION['accID']) && isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    // If logged in, send them to the verification script to find their specific dashboard
    header("Location: auth/connectionVerification.php");
    exit();
} else {
    // If not logged in, show the landing page as the default
    header("Location: auth/landing/landingPage.php");
    exit();
}
?>