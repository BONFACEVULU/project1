<?php

session_start();

require_once 'config/dbconnection.php';

require_once 'includes/header.php';



$dbconnection = new dbconnection();

$db = $dbconnection->connect();



// Get all classes with instructor details

$query = "SELECT classes.*, instructors.instructor_image

 FROM classes

LEFT JOIN instructors ON classes.instructor_id = instructors.id

WHERE classes.start_date >= NOW()

ORDER BY start_date";



$stmt = $db->prepare($query);

$stmt->execute();

$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<div class="container mt-5">

 <h1 class="text-center mb-4" style="font-family: 'Arial', sans-serif; font-size: 36px; color: #ff69b4; font-weight: bold;">Class Schedule</h1>



<?php foreach ($classes as $class): ?>

 <div class="schedule-container" style="display: flex; justify-content: center;">

<div class="class-block" style="background-color: #fffaf0; border: 1px solid #ff69b4; border-radius: 20px; padding: 24px; width: 80%; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1); margin-bottom: 30px;">

<div class="class-header" style="display: flex; align-items: center;">

 <div class="instructor-image" style="margin-right: 15px;">

<img src="uploads/<?php echo htmlspecialchars($class['instructor_image'] ?? 'default_avatar.png'); ?>"

alt="Instructor Image"

class="instructor-thumbnail" style="width: 60px; height: 60px; border-radius: 50%; border: 2px solid #ff69b4;">

</div>

<div class="class-content" style="flex-grow: 1;">
<div class="class-name" style="font-size: 22px; font-weight: 700; color: #333333;">&#127775; <?php echo htmlspecialchars($class['name']); ?></div>

<div class="class-time" style="font-size: 16px; color: #555555;">&#128197; <?php echo htmlspecialchars($class['start_date']); ?></div>

 <div class="class-details" style="font-size: 16px; color: #555555;">&#128337; <?php echo htmlspecialchars($class['duration']); ?> minutes</div>

</div>

<div class="instructor-name" style="font-size: 18px; font-weight: 500; color: #333333;">&#128100; <?php echo htmlspecialchars($class['instructor_id']); ?></div>

 </div>

 <!-- Spots Left and Book Now Button -->

<div class="spots-and-book" style="margin-top: 15px; text-align: left;">

 <div class="spots-left" style="color: #ff4500; font-size: 18px; font-weight: bold; margin-bottom: 10px;">

📌 <?php echo htmlspecialchars($class['max_capacity'] - $class['current_bookings']); ?> spot(s) left!
</div>

<div class="class-action">

 <?php if (isset($_SESSION['user_id'])): ?>

 <a href="booking_payment.php?class_id=<?php echo $class['id']; ?>" class="book-button" style="background-color: #000000; color: #FFFFFF; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">BOOK NOW</a>

<?php else: ?>
 <a href="login.php" class="btn btn-secondary btn-sm" style="padding: 10px 18px; border-radius: 8px;">Login to Book</a>

<?php endif; ?>

 </div>

 </div>

 <!-- Class Details -->

 <div class="show-details" style="margin-top: 20px;">

<a href="#" class="details-toggle" style="text-decoration: underline; color: #ff69b4; font-size: 16px;">Show Details</a>
 <p style="font-size: 14px;"><strong>Location:</strong> <?php echo htmlspecialchars($class['location'] ?? 'Studio A'); ?></p>
 <p style="font-size: 14px;"><strong>Requirements:</strong> <?php echo htmlspecialchars($class['requirements'] ?? 'None'); ?></p>

 </div>

</div>

 </div>

<?php endforeach; ?>



</div>



<?php require_once 'includes/footer.php'; ?>