<?php
session_start();
require_once 'config/dbconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$user_id = $_SESSION['user_id'];

// Fetch user information
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>User Profile</h2>
    <div class="mb-3">
        <strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?>
    </div>
    <div class="mb-3">
        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
    </div>
    <div class="mb-3">
        <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?>
    </div>
    <a href="my_bookings.php" class="btn btn-primary">View My Bookings</a>
    <a href="cart.php" class="btn btn-secondary">My Classes</a>
</div>

<?php require_once 'includes/footer.php'; ?>
