<?php
session_start();
require_once '../config/dbconnection.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch pending bookings count
$query = "SELECT COUNT(*) as pending_count FROM bookings WHERE status = 'pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$pending_count = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <h1>Welcome to the Admin Dashboard</h1>
    <a href="pending_bookings.php" class="btn btn-warning">View Bookings</a>
    <div class="notification">
        <a href="pending_bookings.php">
            <i class="bell-icon">🔔</i>
            <?php if ($pending_count > 0): ?>
                <span class="badge"><?php echo $pending_count; ?></span>
            <?php endif; ?>
        </a>
    </div>
    <!-- Other dashboard content -->
</body>
</html>
#   N e w   c o m m e n t  
 / /   T h i s   f u n c t i o n   i n i t i a l i z e s   t h e   a d m i n   d a s h b o a r d   a n d   f e t c h e s   p e n d i n g   b o o k i n g s .  
 / /   T h i s   f i l e   d i s p l a y s   t h e   a d m i n   d a s h b o a r d   w i t h   k e y   m e t r i c s   a n d   n a v i g a t i o n .  
 