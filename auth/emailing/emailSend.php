<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include path file
require_once __DIR__ . '/../../filepaths.php';

//Load Composer's autoloader
require BASE_DIR . '/vendor/autoload.php';

function sendEmail_Verify($email, $email_subject, $email_template) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0; // Disable debug output for production
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'joaquind.rizal@gmail.com';                     //SMTP username
        $mail->Password   = 'gkgw qich dyob xpoy';                               //SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                              //Enable implicit TLS encryption
        //$mail->Port       =  587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Use Implicit SSL
        $mail->Port       =  465;                                   //TCP port to connect to; use 465 for SMTPS

        // Bypass SSL verification (Useful for local XAMPP environments)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('joaquind.rizal@gmail.com', 'Quality Management System');
        $mail->addAddress($email);     //Add a recipient

        $mail->isHTML(true);
        $mail->Subject = $email_subject;
        $mail->Body = $email_template;
        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
    }
}
?>