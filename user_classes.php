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

// Fetch user's classes
$query = "SELECT c.*, b.booking_date 
          FROM bookings b
          JOIN classes c ON b.class_id = c.id
          WHERE b.user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Your Classes</h2>
    <?php if (empty($classes)): ?>
        <div class="alert alert-info text-center">No classes found.</div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Booking Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($class['name']); ?></td>
                        <td><?php echo htmlspecialchars($class['booking_date']); ?></td>
                        <td>
                            <a href="clear_class.php?class_id=<?php echo $class['id']; ?>" class="btn btn-danger">Clear</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
