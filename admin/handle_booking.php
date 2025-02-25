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

    if (!in_array($action, ['confirm', 'reject'])) {
        header("Location: pending_bookings.php?error=Invalid action.");
        exit();
    }

    $dbconnection = new dbconnection();
    $db = $dbconnection->connect();

    $status = ($action === 'confirm') ? 'confirmed' : 'rejected';

    // Update booking status
    $query = "UPDATE bookings SET status = :status WHERE id = :booking_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':booking_id', $booking_id);

    if ($stmt->execute()) {
        // Fetch booking details for email
        $query = "SELECT b.*, u.email, u.name AS user_name, c.name AS class_name 
                  FROM bookings b 
                  JOIN users u ON b.user_id = u.id 
                  JOIN classes c ON b.class_id = c.id 
                  WHERE b.id = :booking_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $to = $booking['email'];
            $subject = "Booking " . ucfirst($status) . " for " . $booking['class_name'];
            $message = "Dear " . $booking['user_name'] . ",\n\nYour booking for " . $booking['class_name'] . " has been " . $status . ".\n\n";
            $message .= ($status === 'confirmed') ? "Thank you!" : "Please contact us if you have any questions.";
            $headers = "From: no-reply@dancestudio.com";

            if (mail($to, $subject, $message, $headers)) {
                header("Location: pending_bookings.php?success=Booking $status and email sent.");
            } else {
                header("Location: pending_bookings.php?error=Booking $status but email failed.");
            }
        } else {
            header("Location: pending_bookings.php?error=Booking not found.");
        }
    } else {
        header("Location: pending_bookings.php?error=Failed to $action booking.");
    }
    exit();
}
?>
