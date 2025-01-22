<?php
require_once 'dbconnection.php';
require 'vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception;

class ResetPassword extends dbconnection {

    public function sendResetLink($email) {
        $token = bin2hex(random_bytes(50));
        $sql = "INSERT INTO password_resets (email, token) VALUES (:email, :token)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['email' => $email, 'token' => $token]);
        $resetLink = "http://localhost/class%20codes/projectmain/resetpassword.php?token=$token";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'bonface.vulu@strathmore.edu'; // Replace with your email
            $mail->Password   = 'qiio mkft bmdo aypw'; // Replace with your app-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

          
    //Recipients
    $mail->setFrom('APIapp@gmail.com', 'Mailer');
    $mail->addAddress('bonywhisky9@gmail.com', 'bonface vulu'); 
            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset';
            $mail->Body    = "Click the following link to reset your password: <a href='$resetLink'>$resetLink</a>";
            $mail->AltBody = "Click the following link to reset your password: $resetLink";

            $mail->send();
            echo "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function resetPassword($token, $new_password) {
        $sql = "SELECT email FROM password_resets WHERE token=:token";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch();
        if ($result) {
            $email = $result['email'];
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET password=:password WHERE email=:email";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute(['password' => $new_password_hashed, 'email' => $email]);
            $sql = "DELETE FROM password_resets WHERE token=:token";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute(['token' => $token]);
            echo "Your password has been reset.";
        } else {
            echo "Invalid token.";
        }
    }
}

$resetPassword = new ResetPassword();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 0;
        }

        .reset-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 500px;
            margin-top: 30px;
        }

        .btn-primary {
            background-color: #4CAF50;
            border: none;
        }

        .btn-primary:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        a {
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }

        .form-check-label {
            font-size: 14px;
        }

        .nav-links {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <h3 class="text-center mb-4">Reset Password</h3>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
            $resetPassword->sendResetLink($_POST['email']);
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
            echo '<form method="post" action="resetpassword.php">
                    <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-4">Reset Password</button>
                    </div>
                  </form>';
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token'])) {
            if ($_POST['new_password'] == $_POST['confirm_password']) {
                $resetPassword->resetPassword($_POST['token'], $_POST['new_password']);
            } else {
                echo "Passwords do not match.";
            }
        } else {
            echo '<form method="post" action="resetpassword.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-4">Send Reset Link</button>
                    </div>
                  </form>';
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>