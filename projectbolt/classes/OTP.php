<?php
class OTP {
    public static function sendOtp($email) {
        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);
        
        // Store OTP in session
        session_start();
        $_SESSION['otp'] = $otp;

        // Send OTP to user's email
        $subject = "Your OTP Code";
        $message = "Your OTP code is: $otp";
        $headers = "From: no-reply@dancestudio.com";

        mail($email, $subject, $message, $headers);
    }
}
?>
