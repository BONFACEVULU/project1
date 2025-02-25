<?php
session_start();
require_once '../config/dbconnection.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action']; // 'confirm' or 'reject'

    $dbconnection = new dbconnection();
    $db = $dbconnection->connect();

    $status = ($action === 'confirm') ? 'confirmed' : 'rejected';
    $query = "UPDATE bookings SET status = :status WHERE id = :booking_id";


    $stmt = $db->prepare($query);
    $stmt->bindParam(':booking_id', $booking_id);

    if ($stmt->execute()) {
        // Fetch booking details for email
        $query = "SELECT b.*, u.email, c.name as class_name 
                  FROM bookings b 
                  JOIN users u ON b.user_id = u.id 
                  JOIN classes c ON b.class_id = c.id 
                  WHERE b.id = :booking_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        // Send confirmation email
        $to = $booking['email'];
        $subject = "Booking Confirmation for " . $booking['class_name'];
        $message = "Dear " . $booking['user_name'] . ",\n\nYour booking for " . $booking['class_name'] . " has been confirmed.\n\nThank you!";
        mail($to, $subject, $message);

        header("Location: pending_bookings.php?success=Booking confirmed and email sent.");
        exit();
    } else {
        header("Location: pending_bookings.php?error=Failed to confirm booking.");
        exit();
    }
}
?>
