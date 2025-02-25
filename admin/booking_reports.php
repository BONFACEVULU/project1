<?php
session_start();
require_once '../config/dbconnection.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch booking statistics
$query = "SELECT c.name as class_name, COUNT(b.id) as total_bookings 
          FROM bookings b 
          JOIN classes c ON b.class_id = c.id 
          GROUP BY b.class_id";
$stmt = $db->prepare($query);
$stmt->execute();
$booking_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Reports</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <h1>Booking Reports</h1>
    <table>
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Total Bookings</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($booking_stats as $stat): ?>
                <tr>
                    <td><?php echo htmlspecialchars($stat['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($stat['total_bookings']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
