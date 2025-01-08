<?php
require_once 'dbconnection.php';
class otp_verification extends dbconnection{
public function verification(){
if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $stmt = $this->connect()->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch();
    if($user['otp'] == $otp){
        $sql = "UPDATE users SET email_verified_at = NOW() WHERE email = '$email'";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        header("Location: index.php");
    }else {
        echo "Invalid verification code";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        #container {
            border: 1px solid black;
            width: 400px;
            margin: 50px auto;
            padding: 20px;
        }
        form {
            margin: 0 auto;
            text-align: center;
        }
        h1 {
            text-align: center;
        }
        input[type="number"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
        }
        button {
            background-color: orange;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div id="container">
        <h1>Two-Step Verification</h1>
        <form method="post">
        <input type="hidden" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            <label for="otp">Enter OTP Code:</label><br>
            <input type="number" name="otp" placeholder="Six-Digit OTP" required><br><br>
            <button type="submit">Verify OTP</button>
        </form>
    </div>
</body>
</html>
<?php
}
}