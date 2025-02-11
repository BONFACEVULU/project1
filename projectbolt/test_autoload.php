<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'bonface.vulu@strathmore.edu';
    $mail->Password = 'qiio mkft bmdo aypw'; // Correct password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('APIapp@gmail.com', 'Mailer');
    $mail->addAddress('your-email@example.com'); // Replace with your email address

    $mail->isHTML(true);
    $mail->Subject = 'Autoload Test';
    $mail->Body = '<p>This is a test email to verify autoload functionality.</p>';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    error_log("Mailer Error: {$mail->ErrorInfo}");
}
?>
