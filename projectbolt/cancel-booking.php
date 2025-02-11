<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$booking_id = $_GET['booking'] ?? null;
if (!$booking_id) {
    header('Location: my-bookings.php');
    exit();
}

$query = "DELETE FROM bookings WHERE id = :booking_id AND user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':booking_id', $booking_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Booking cancelled successfully!";
} else {
    $_SESSION['error_message'] = "Failed to cancel booking.";
}

header('Location: my-bookings.php');
exit();
