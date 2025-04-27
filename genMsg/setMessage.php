<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function setMessage($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}
?>