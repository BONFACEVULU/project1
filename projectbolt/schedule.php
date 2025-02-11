<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch all classes with instructor details
$query = "SELECT c.*, i.name as instructor_name, i.instructor_image 
          FROM classes c 
          JOIN instructors i ON c.instructor_id = i.id 
          ORDER BY c.start_date";
$stmt = $db->prepare($query);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Class Schedule</h1>
    
    <div class="list-group">
        <?php foreach ($classes as $index => $class): ?>
            <div class="list-group-item list-group-item-action d-flex align-items-start p-4 mb-3 shadow-sm">
                <div class="me-3">
                    <span class="badge bg-secondary fs-5"><?php echo $index + 1; ?></span>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1"><?php echo htmlspecialchars($class['name']); ?></h5>
                    <p class="mb-1">
                        <strong>Instructor:</strong> <?php echo htmlspecialchars($class['instructor_name']); ?><br>
                        <strong>Schedule:</strong> <?php echo date('M d, Y g:i A', strtotime($class['start_date'])) . ' - ' . date('g:i A', strtotime($class['end_date'])); ?><br>
                        <strong>Duration:</strong> <?php echo htmlspecialchars($class['duration']); ?> min<br>
                        <strong>Level:</strong> <?php echo htmlspecialchars($class['level']); ?><br>
                        <strong>Type:</strong> <?php echo htmlspecialchars($class['class_type']); ?><br>
                        <strong>Price:</strong> $<?php echo htmlspecialchars($class['price']); ?>
                    </p>
                </div>
                <div class="ms-auto">
                    <img src="<?php echo htmlspecialchars(str_replace('../', '', $class['instructor_image'] ?? 'uploads/default_instructor_image.jpg')); ?>" 
                         class="rounded-circle border border-secondary" 
                         alt="<?php echo htmlspecialchars($class['instructor_name']); ?>" 
                         style="width: 60px; height: 60px; object-fit: cover;">
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>