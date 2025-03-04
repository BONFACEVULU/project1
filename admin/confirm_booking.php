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
/ /   T h i s   f i l e   h a n d l e s   t h e   c o n f i r m a t i o n   o f   u s e r   b o o k i n g s .  
 / /   T h i s   f i l e   p r o c e s s e s   t h e   c o n f i r m a t i o n   o f   u s e r   b o o k i n g s .  
 / /   T h i s   f i l e   p r o c e s s e s   t h e   c o n f i r m a t i o n   o f   u s e r   b o o k i n g s .  
 