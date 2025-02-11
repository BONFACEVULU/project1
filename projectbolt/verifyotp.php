<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userCode = htmlspecialchars($_POST['otp'], ENT_QUOTES, 'UTF-8');
    require_once 'config/dbconnection.php';

    $dbconnection = new dbconnection();
    $db = $dbconnection->connect();

    if ($userCode == $_SESSION['otp']) {
        unset($_SESSION['otp']);
        header('Location: index.php');
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Dance Studio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        .verify-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container verify-container">
        <h1 class="text-center">Verify OTP</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="verifyotp.php">
            <div class="mb-3">
                <label for="otp" class="form-label">OTP Code</label>
                <input type="text" class="form-control" id="otp" name="otp" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>
    </div>
</body>
</html>
