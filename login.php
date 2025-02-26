<?php
session_start();
require_once 'config/dbconnection.php';

// Hardcoded admin credentials
$admin_username = 'admin@gmail.com'; // Replace with your admin username
$admin_password = 'amadmin'; // Replace with your admin password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbconnection = new dbconnection();
    $db = $dbconnection->connect();

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Check if admin credentials
    if ($email === $admin_username && $password === $admin_password) {
        $_SESSION['user_id'] = $admin_username; // Set session for admin
        $_SESSION['user_role'] = 'admin'; // Set role
        header("Location: admin/admin.php"); // Redirect to admin dashboard
        exit();
    }

    // Fetch the user data from the database
    $query = "SELECT id, password FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, log the user in
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['email'] = $email; // Store email in session
        $_SESSION['logged_in'] = true; // Set logged in status
        header("Location: index.php"); // Redirect to homepage
        exit();
    } else {
        $error = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dance Studio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .btn-register {
            background-color: #007bff;
            color: white;
        }
        .btn-register:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <h1 class="text-center">Login</h1>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="mt-3 text-center">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
        <div class="text-center mt-3">
            <a href="forgot_password.php" class="btn btn-link">Forgot Password?</a>
        </div>
    </div>
</body>
</html>
