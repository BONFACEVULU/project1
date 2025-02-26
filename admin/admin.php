<?php
require_once '../config/dbconnection.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch statistics for the dashboard
$currentDate = date('Y-m-d H:i:s');

// Total number of instructors
$stmt = $db->query("SELECT COUNT(*) FROM instructors");
$totalInstructors = $stmt->fetchColumn();

// Total number of active classes
$stmt = $db->query("SELECT COUNT(*) FROM classes WHERE end_date > NOW()");
$totalClasses = $stmt->fetchColumn();

// Total number of students (confirmed bookings)
$stmt = $db->query("SELECT COUNT(DISTINCT user_id) FROM bookings WHERE status = 'confirmed'");
$totalStudents = $stmt->fetchColumn();

// Total revenue from confirmed bookings
$stmt = $db->query("SELECT SUM(c.price) FROM bookings b JOIN classes c ON b.class_id = c.id WHERE b.status = 'confirmed'");
$totalRevenue = $stmt->fetchColumn() ?: 0;

// Fetch pending bookings
$stmt = $db->query("SELECT b.*, u.name AS user_name, c.name AS class_name, u.email AS user_email 
                    FROM bookings b
                    JOIN users u ON b.user_id = u.id
                    JOIN classes c ON b.class_id = c.id
                    WHERE b.status = 'pending'");
$pending_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pending_count = count($pending_bookings);

// Fetch data for charts
// Activity Chart Data
$activityData = $db->query("SELECT DATE_FORMAT(booking_date, '%b') AS month, COUNT(*) AS bookings 
                            FROM bookings 
                            WHERE booking_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
                            AND status = 'confirmed'
                            GROUP BY month 
                            ORDER BY booking_date")->fetchAll(PDO::FETCH_ASSOC);

// Popular Classes Chart Data
$popularClassesData = $db->query("SELECT c.name AS class_name, COUNT(b.id) AS bookings 
                                  FROM bookings b 
                                  JOIN classes c ON b.class_id = c.id 
                                  WHERE b.status = 'confirmed'
                                  GROUP BY c.name 
                                  ORDER BY bookings DESC 
                                  LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);

// Fetch upcoming classes with confirmed bookings
$upcomingClasses = $db->query("SELECT c.name as class_name, i.name as instructor_name, 
                               c.start_date, c.max_capacity, 
                               (SELECT COUNT(*) FROM bookings b WHERE b.class_id = c.id AND b.status = 'confirmed') as confirmed_bookings
                               FROM classes c 
                               JOIN instructors i ON c.instructor_id = i.id
                               WHERE c.start_date > NOW()
                               AND (SELECT COUNT(*) FROM bookings b WHERE b.class_id = c.id AND b.status = 'confirmed') > 0
                               ORDER BY c.start_date ASC
                               LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dance Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/projectbolt/css/admin.css">
    <link rel="stylesheet" href="/projectbolt/css/custom.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-black">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="instructor_management.php">Instructors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="class_management.php">Classes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_bookings.php">Manage Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_messages.php">View Messages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="notificationBell" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php if ($pending_count > 0): ?>
                            <span class="badge bg-danger"><?php echo $pending_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($pending_count > 0): ?>
                            <li><h6 class="dropdown-header">Pending Bookings</h6></li>
                            <?php foreach ($pending_bookings as $booking): ?>
                                <li id="notification-<?php echo $booking['id']; ?>">
                                    <a class="dropdown-item" href="#">
                                        <?php echo htmlspecialchars($booking['user_name']); ?> booked 
                                        <strong><?php echo htmlspecialchars($booking['class_name']); ?></strong> 
                                        (Email: <?php echo htmlspecialchars($booking['user_email']); ?>)
                                        <button class="btn btn-link text-danger" onclick="clearNotification(<?php echo $booking['id']; ?>)">X</button>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li class="dropdown-footer">
                                <button class="btn btn-danger w-100" onclick="clearAllNotifications()">Clear All</button>
                            </li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="#">No pending bookings</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Instructors</h5>
                <p class="card-text display-4"><?php echo $totalInstructors; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Active Classes</h5>
                <p class="card-text display-4"><?php echo $totalClasses; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Students</h5>
                <p class="card-text display-4"><?php echo $totalStudents; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Revenue</h5>
                <p class="card-text display-4">KSH<?php echo number_format($totalRevenue); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Recent Activity</h5>
            </div>
            <div class="card-body">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="instructor_management.php?action=add" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-plus"></i> Add New Instructor
                    </a>
                    <a href="class_management.php?action=add" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle"></i> Create New Class
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar"></i> View Schedule
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar"></i> Generate Reports
                    </a>
                    <a href="generate_report.php?format=pdf" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </a>
                    <a href="generate_report.php?format=excel" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                    <a href="view_messages.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-envelope"></i> View Messages
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Upcoming Classes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Instructor</th>
                                <th>Date</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingClasses as $class): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($class['instructor_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($class['start_date'])); ?></td>
                                <td><?php echo $class['confirmed_bookings'] . '/' . $class['max_capacity']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Popular Classes</h5>
            </div>
            <div class="card-body">
                <canvas id="popularClassesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/c:/Apache24/htdocs/class codes/confirmproj/projectbolt/assets/js/notifications.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    const activityData = <?php echo json_encode($activityData); ?>;
    const activityLabels = activityData.map(data => data.month);
    const activityCounts = activityData.map(data => data.bookings);

    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: activityLabels,
            datasets: [{
                label: 'Class Bookings',
                data: activityCounts,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Add background color for line chart
                tension: 0.4 // Increase tension for a more curved line
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Popular Classes Chart
    const popularCtx = document.getElementById('popularClassesChart').getContext('2d');
    const popularClassesData = <?php echo json_encode($popularClassesData); ?>;
    const popularLabels = popularClassesData.map(data => data.class_name);
    const popularCounts = popularClassesData.map(data => data.bookings);

    new Chart(popularCtx, {
        type: 'doughnut',
        data: {
            labels: popularLabels,
            datasets: [{
                data: popularCounts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

function clearNotification(id) {
    // AJAX call to clear the notification
    fetch('clear_notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the notification from the UI
            document.getElementById('notification-' + id).remove();
            console.log("Notification cleared:", id);
        } else {
            console.error("Error clearing notification:", data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function clearAllNotifications() {
    // AJAX call to clear all notifications
    fetch('clear_all_notifications.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove all notifications from the UI
            document.querySelectorAll('[id^="notification-"]').forEach(notification => notification.remove());
            console.log("All notifications cleared");
        } else {
            console.error("Error clearing notifications:", data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
</script>

</body>
</html>
/ /   T h i s   f i l e   c o n t a i n s   t h e   m a i n   f u n c t i o n a l i t i e s   f o r   a d m i n   o p e r a t i o n s .  
 