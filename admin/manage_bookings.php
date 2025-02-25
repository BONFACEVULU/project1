<?php
session_start();
require_once '../../config/dbconnection.php';
require_once '../../includes/header.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    $query = "UPDATE bookings SET status = 'confirmed' WHERE id = :booking_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':booking_id', $booking_id);
    
    if ($stmt->execute()) {
        // Send confirmation email
        header("Location: send_booking_email.php?booking_id=" . $booking_id);
        exit();
    } else {
        $error = "Error confirming booking.";
    }
}

// Get all bookings
$query = "SELECT b.*, u.name AS user_name, c.name AS class_name 
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          JOIN classes c ON b.class_id = c.id
          ORDER BY b.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check for pending bookings
$pending_count = 0;
foreach ($bookings as $booking) {
    if ($booking['status'] === 'pending') {
        $pending_count++;
    }
}
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Manage Bookings</h1>
    
    <?php if ($pending_count > 0): ?>
        <div class="alert alert-warning">
            You have <?php echo $pending_count; ?> pending bookings that need confirmation.
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>User</th>
                    <th>Class</th>
                    <th>Payment Method</th>
                    <th>Payment Reference</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['payment_method']); ?></td>
                    <td><?php echo htmlspecialchars($booking['payment_reference']); ?></td>
                    <td><?php echo htmlspecialchars($booking['status']); ?></td>
                    <td>
                        <?php if ($booking['status'] === 'pending'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" name="confirm_booking" class="btn btn-success btn-sm">
                                    Confirm
                                </button>
                            </form>
                        <?php elseif ($booking['status'] === 'confirmed'): ?>
                            <span class="badge bg-success">Confirmed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
