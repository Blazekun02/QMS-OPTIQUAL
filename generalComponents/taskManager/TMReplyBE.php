<?php
// Start session
if (!session_id()) {
    session_start();
}

// Include filepaths
require_once __DIR__ . '/../../filepaths.php';

// Include set message
require_once genMsg_dir . '/setMessage.php';

if (isset($_POST['submitReplybtn'])) {
    var_dump($_POST);
    $replyMessage = trim($_POST['replyMessage'] ?? '');
    if (empty($replyMessage)) {
        echo "Error: Reply message cannot be empty.";
        exit;
    }
    if (strlen($replyMessage) > 255) {
        echo "Error: Reply message exceeds 255 characters.";
        exit;
    }
    
    //Submit reply to the database

    setMessage("Reply sent successfully!", "success");
    header("Location: /qms_optiqual/generalComponents/redirectToHome.php");
    exit();
}

echo "wathafen";

?>