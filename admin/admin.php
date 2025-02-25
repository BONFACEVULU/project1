<?php
require_once '../includes/header.php';
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

// Total number of students (bookings)
$stmt = $db->query("SELECT COUNT(DISTINCT user_id) FROM bookings");
$totalStudents = $stmt->fetchColumn();

// Total revenue
$stmt = $db->query("SELECT SUM(c.price) FROM bookings b JOIN classes c ON b.class_id = c.id");
$totalRevenue = $stmt->fetchColumn() ?: 0;

// Fetch pending bookings
$stmt = $db->query("SELECT b.*, u.name AS user_name, c.name AS class_name, u.email AS user_email 
                    FROM bookings b
                    JOIN users u ON b.user_id = u.id
                    JOIN classes c ON b.class_id = c.id
                    WHERE b.status = 'pending'");
$pending_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pending_count = count($pending_bookings);
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
                    <a class="nav-link active" href="admin.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
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
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <?php echo htmlspecialchars($booking['user_name']); ?> booked 
                                        <strong><?php echo htmlspecialchars($booking['class_name']); ?></strong> 
                                        (Email: <?php echo htmlspecialchars($booking['user_email']); ?>)
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="#">No pending bookings</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mb-5">
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
                                <?php
                                $stmt = $db->query("SELECT c.name as class_name, i.name as instructor_name, 
                                                         c.start_date, c.max_capacity, c.current_bookings
                                                  FROM classes c 
                                                  JOIN instructors i ON c.instructor_id = i.id
                                                  WHERE c.start_date > NOW()
                                                  ORDER BY c.start_date ASC
                                                  LIMIT 5");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['instructor_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['start_date'])); ?></td>
                                    <td><?php echo $row['current_bookings'] . '/' . $row['max_capacity']; ?></td>
                                </tr>
                                <?php endwhile; ?>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activity Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Class Bookings',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
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
    new Chart(popularCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hip Hop', 'Ballet', 'Contemporary', 'Jazz'],
            datasets: [{
                data: [30, 25, 20, 15],
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
</script>
</body>
</html>
