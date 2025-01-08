<?php
require 'vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception; 
//use Kinde\TwoFactorAuth\TwoFactorAuth;
//session_start();
session_start();
class LoginMain extends dbconnection {

    private $vCode;

    protected function generateCode(){
        $this->vCode = rand(100000, 999999);
        $_SESSION['verification_code'] = $this->vCode;
        echo "Generated code: {$this->vCode}<br>";
 }
    protected function getuser($email,$pw){
      
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        $user = $stmt->fetch();
        
                    if ($user) {
                        // Verify password
                        if (password_verify($pw, $user['password'])) {
                           echo '<script>alert("Login successful")</script>';
                                // Start user session and redirect
                               // $_SESSION['email'] = $email;
                               // header("Location: verify.php");
                               // exit();
                            } else {
                                echo '<script>alert("Invalid password")</script>';
                            }
                        }else{
                            echo '<script>alert("No user found with this email")</script>';
                        }
     }
    
     public function sendMail($email){
    $mail = new PHPMailer(true);
    $verification_code=rand(100000, 999999);
    
    try {
        //Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'bonface.vulu@strathmore.edu';                     //SMTP username
        $mail->Password   = 'kvnmlqjuuapqyuwy';                              //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $mail->setFrom('APIapp@gmail.com', 'Mailer');
        $mail->addAddress($email);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = ' vulu';
        $mail->Body    = "Thanks for coming back to us. To log in enter this code {$this->vCode}";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        echo "Generatedb code: $this->vCode<br>";
        $mail->send();
        // echo 'Message has been sent';
       
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


 public function verifyCode($userCode) { 
    return $userCode == $_SESSION['verification_code'];
 }
 public function clearCode() { 
    unset($_SESSION['verification_code']);
 }
}

