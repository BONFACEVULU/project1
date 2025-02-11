<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/dbconnection.php';

    $dbconnection = new dbconnection();
    $db = $dbconnection->connect();

    $token = $_POST['token'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Check if the token is valid
    $query = "SELECT * FROM users WHERE reset_token = :token";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Update the user's password
        $query = "UPDATE users SET password = :new_password, reset_token = NULL WHERE reset_token = :token";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':new_password', $newPassword);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        // Redirect to login page with success message
        $_SESSION['success'] = "Your password has been reset successfully. Please log in with your new password.";
        header('Location: login.php');
        exit();
    } else {
        $error = "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Dance Studio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        .reset-password-container {
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
    <div class="container reset-password-container">
        <h1 class="text-center">Reset Password</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="reset_password.php">
            <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </form>
    </div>
</body>
</html>
