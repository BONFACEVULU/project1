<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'bonface.vulu@strathmore.edu'; // Your Gmail
    $mail->Password = 'wbsy jqwg oeqp nkta'; // Your App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Recipients
    $mail->setFrom('APIapp@gmail.com', 'DANCE STUDIO');
$mail->addAddress('bonywhiskyspam@gmail.com', 'Recipient Name'); // Updated to user's email


    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body = '<p>This is a test email sent using PHPMailer.</p>';

    $mail->send();
    echo 'Email has been sent successfully.';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
/ /   T h i s   f i l e   t e s t s   t h e   S M T P   f u n c t i o n a l i t y   u s i n g   P H P M a i l e r .  
 