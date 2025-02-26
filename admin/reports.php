<?php
require_once '../includes/header.php';
require_once '../config/dbconnection.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch class booking data
$query = "SELECT c.name, COUNT(b.id) as total_bookings 
          FROM classes c 
          LEFT JOIN bookings b ON c.id = b.class_id 
          GROUP BY c.id";
$stmt = $db->prepare($query);
$stmt->execute();
$classBookings = $stmt-><?php
require_once '../includes/header.php';
require_once '../config/dbconnection.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch class booking data
$query = "SELECT c.name, COUNT(b.id) as total_bookings 
          FROM classes c 
          LEFT JOIN bookings b ON c.id = b.class_id 
          GROUP BY c.id";
$stmt = $db->prepare($query);
$stmt->execute();
$classBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch overall booking data
$query = "SELECT DATE(b.booking_date) as booking_date, COUNT(b.id) as total_bookings 
          FROM bookings b 
          GROUP BY DATE(b.booking_date) 
          ORDER BY booking_date";
$stmt = $db->prepare($query);
$stmt->execute();
$overallBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the graphs
$classNames = array_column($classBookings, 'name');
$classTotals = array_column($classBookings, 'total_bookings');

$dates = array_column($overallBookings, 'booking_date');
$bookingCounts = array_column($overallBookings, 'total_bookings');

// Convert dates for the line graph
$formattedDates = array_map(function($date) {
    return date('Y-m-d', strtotime($date));
}, $dates);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Dance Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Class Booking Reports</h1>
    <div class="row">
        <div class="col-md-6">
            <h3>Class Bookings</h3>
            <canvas id="classBookingsChart"></canvas>
        </div>
        <div class="col-md-6">
            <h3>Overall Booking Trend</h3>
            <canvas id="overallBookingsChart"></canvas>
        </div>
    </div>
</div>

<script>
    // Class Bookings Bar Chart
    const ctx1 = document.getElementById('classBookingsChart').getContext('2d');

    const classBookingsChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($classNames); ?>,
            datasets: [{
                label: 'Total Bookings',
                data: <?php echo json_encode($classTotals); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Bookings'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Classes'
                    }
                }
            }
        }
    });

    // Overall Bookings Line Chart
    const ctx2 = document.getElementById('overallBookingsChart').getContext('2d');

    const overallBookingsChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($formattedDates); ?>,
            datasets: [{
                label: 'Total Bookings Over Time',
                data: <?php echo json_encode($bookingCounts); ?>,
                fill: true,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Bookings'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Dates'
                    }
                }
            }
        }
    });
</script>

</body>
</html>
(PDO::FETCH_ASSOC);

// Fetch overall booking data
$query = "SELECT DATE(b.booking_date) as booking_date, COUNT(b.id) as total_bookings 
          FROM bookings b 
          GROUP BY DATE(b.booking_date) 
          ORDER BY booking_date";
$stmt = $db->prepare($query);
$stmt->execute();
$overallBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the graphs
$classNames = array_column($classBookings, 'name');
$classTotals = array_column($classBookings, 'total_bookings');

$dates = array_column($overallBookings, 'booking_date');
$bookingCounts = array_column($overallBookings, 'total_bookings');

// Convert dates for the line graph
$formattedDates = array_map(function($date) {
    return date('Y-m-d', strtotime($date));
}, $dates);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Dance Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.min.js"></script>

</head>
<body>

<div class="container mt-5">
    <h1 class="text-center">Class Booking Reports</h1>
    <div id="chartContainer" style="width: 100%; height: 400px;">

    
    <div class="row">
        <div class="col-md-6">
            <h3>Class Bookings</h3>
            <canvas id="classBookingsChart"></canvas>
        </div>
        <div class="col-md-6">
            <h3>Overall Booking Trend</h3>
            <canvas id="overallBookingsChart"></canvas>
        </div>
    </div>
</div>

<script>
    // Class Bookings Bar Chart
    const ctx1 = document.getElementById('classBookingsChart').getContext('2d');
    console.log('Class Names:', <?php echo json_encode($classNames); ?>);
    console.log('Class Totals:', <?php echo json_encode($classTotals); ?>);

    const classBookingsChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($classNames); ?>,
            datasets: [{
                label: 'Total Bookings',
                data: <?php echo json_encode($classTotals); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Overall Bookings Line Chart
    const ctx2 = document.getElementById('overallBookingsChart').getContext('2d');
    console.log('Booking Dates:', <?php echo json_encode($formattedDates); ?>);
    console.log('Booking Counts:', <?php echo json_encode($bookingCounts); ?>);

    const overallBookingsChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($formattedDates); ?>,
            datasets: [{
                label: 'Total Bookings Over Time',
                data: <?php echo json_encode($bookingCounts); ?>,
                fill: false,
                borderColor: 'rgba(255, 99, 132, 1)',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
/ /   T h i s   f i l e   h a n d l e s   t h e   g e n e r a t i o n   o f   r e p o r t s   f o r   a d m i n   u s e r s .  
 