<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = $_POST['schedule_id'];
    $user_id = $_SESSION['user_id'];
    $booking_date = $_POST['booking_date'];

    $query = "INSERT INTO bookings (user_id, schedule_id, booking_date) VALUES (:user_id, :schedule_id, :booking_date)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':schedule_id', $schedule_id);
    $stmt->bindParam(':booking_date', $booking_date);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Class booked successfully!";
        header('Location: my-bookings.php');
        exit();
    }
}

$schedule_id = $_GET['schedule'] ?? null;
if (!$schedule_id) {
    header('Location: schedule.php');
    exit();
}

$query = "SELECT s.*, c.name as class_name, i.name as instructor_name, c.price 
          FROM schedule s 
          JOIN classes c ON s.class_id = c.id 
          JOIN instructors i ON s.instructor_id = i.id 
          WHERE s.id = :schedule_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':schedule_id', $schedule_id);
$stmt->execute();
$class = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<section class="booking-section py-5">
    <div class="container">
        <h1 class="text-center mb-5">Book a Class</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($class['class_name']); ?></h5>
                        <p class="card-text">
                            Instructor: <?php echo htmlspecialchars($class['instructor_name']); ?><br>
                            Day: <?php echo htmlspecialchars($class['day_of_week']); ?><br>
                            Time: <?php echo htmlspecialchars($class['start_time']) . ' - ' . htmlspecialchars($class['end_time']); ?><br>
                            Price: $<?php echo htmlspecialchars($class['price']); ?>
                        </p>
                        <form method="POST">
                            <input type="hidden" name="schedule_id" value="<?php echo $schedule_id; ?>">
                            <div class="mb-3">
                                <label for="booking_date" class="form-label">Select Date</label>
                                <input type="date" class="form-control" id="booking_date" name="booking_date" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Confirm Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>