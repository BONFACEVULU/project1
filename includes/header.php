<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dance Studio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/class%20codes/confirmproj/projectbolt/index.php">Dance Studio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/class%20codes/confirmproj/projectbolt/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/class%20codes/confirmproj/projectbolt/classes.php">Classes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/class%20codes/confirmproj/projectbolt/schedule.php">Schedule</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/class%20codes/confirmproj/projectbolt/instructors.php">Instructors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/class%20codes/confirmproj/projectbolt/contact.php">Contact</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/class%20codes/confirmproj/projectbolt/view_profile.php">
                            <img src="<?php echo htmlspecialchars($_SESSION['user_avatar'] ?? 'uploads/default_avatar.png'); ?>" 
                                 class="rounded-circle" 
                                 alt="Profile" style="width: 30px; height: 30px; object-fit: cover;">
                        </a>
                    </li>
                    <li class="nav-item"></li>
                            <a class="nav-link btn btn-outline-light ms-2" href="/class%20codes/confirmproj/projectbolt/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                User
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/class%20codes/confirmproj/projectbolt/login.php">Login</a></li>
                                <li><a class="dropdown-item" href="/class%20codes/confirmproj/projectbolt/register.php">Register</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>