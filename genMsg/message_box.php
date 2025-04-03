<?php
session_start();

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'] ?? 'info';

    // Remove message after displaying
    unset($_SESSION['message'], $_SESSION['message_type']);
?>
    <style>
        .message-box {
            position: fixed;
            top: 2vh;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            max-width: 40rem;
            padding: 1rem;
            color: #fff;
            font-size: 1rem;
            text-align: center;
            border-radius: 0.5rem;
            z-index: 1000;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        .message-success { background-color: #28a745; }
        .message-error { background-color: #dc3545; }
        .message-warning { background-color: #ffc107; color: #000; }
        .message-info { background-color: #17a2b8; }

        .close-btn {
            margin-left: 1rem;
            cursor: pointer;
            font-weight: bold;
        }

        @keyframes fadeIn {
            from { opacity: 0; top: -5vh; }
            to { opacity: 1; top: 0; }
        }
    </style>

    <div class="message-box message-<?php echo htmlspecialchars($messageType); ?>" id="messageBox">
        <?php echo htmlspecialchars($message); ?>
        <span class="close-btn" onclick="document.getElementById('messageBox').style.display='none';">×</span>
    </div>

<?php } ?>
