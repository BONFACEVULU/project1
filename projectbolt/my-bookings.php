<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$user_id = $_SESSION['user_id'];

$query = "SELECT b.*, s.day_of_week, s.start_time, s.end_time, c.name as class_name, i.name as instructor_name 
          FROM bookings b 
          JOIN schedule s ON b.schedule_id = s.id 
          JOIN classes c ON s.class_id = c.id 
          JOIN instructors i ON s.instructor_id = i.id 
          WHERE b.user_id = :user_id 
          ORDER BY b.booking_date DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="my-bookings-section py-5">
    <div class="container">
        <h1 class="text-center mb-5">My Bookings</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Booking Date</th>
                        <th>Class</th>
                        <th>Instructor</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['instructor_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['day_of_week']); ?></td>
                            <td><?php echo htmlspecialchars($booking['start_time']) . ' - ' . htmlspecialchars($booking['end_time']); ?></td>
                            <td>
                                <a href="cancel-booking.php?booking=<?php echo $booking['id']; ?>" class="btn btn-danger btn-sm">Cancel</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>