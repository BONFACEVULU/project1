<?php
session_start();
require_once 'config/dbconnection.php';

// Check if the email session variable is set
if (!isset($_SESSION['email'])) {
    header("Location: register.php"); // Redirect to registration if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbconnection = new dbconnection();
    $db = $dbconnection->connect();

    $otp = filter_var($_POST['otp'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = $_SESSION['email']; // Get the email from the session

    // Fetch the stored OTP from the database
    $query = "SELECT otp, id, password FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['otp'] === $otp) {
        // OTP is valid, log the user in
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['email'] = $email; // Store email in session
        $_SESSION['logged_in'] = true; // Set logged in status
        header("Location: index.php"); // Redirect to homepage
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
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Verify OTP</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="otp" class="form-label">OTP</label>
                                <input type="text" class="form-control" id="otp" name="otp" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
