<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch all bookings with class and instructor details
$query = "SELECT b.*, c.name as class_name, i.name as instructor_name 
          FROM bookings b 
          JOIN schedule s ON b.schedule_id = s.id 
          JOIN classes c ON s.class_id = c.id 
          JOIN instructors i ON s.instructor_id = i.id 
          ORDER BY b.booking_date DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1 class="text-center mb-5">All Bookings</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Booking Date</th>
                <th>Class</th>
                <th>Instructor</th>
                <th>User</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['instructor_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['user_id']); ?></td>
                    <td>
                        <a href="cancel-booking.php?booking=<?php echo $booking['id']; ?>" class="btn btn-danger btn-sm">Cancel</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
