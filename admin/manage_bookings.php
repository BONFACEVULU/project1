<?php
require_once '../config/dbconnection.php';
require_once '../includes/header.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../vendor/autoload.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['confirm_booking']) || isset($_POST['reject_booking']))) {
    $booking_id = $_POST['booking_id'];
    $status = isset($_POST['confirm_booking']) ? 'confirmed' : 'rejected';
    $stmt = $db->prepare("UPDATE bookings SET status = :status WHERE id = :id");
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Fetch user email and class name
        $stmt = $db->prepare("SELECT u.email, u.name AS user_name, c.name AS class_name, c.start_date, c.location 
                              FROM bookings b 
                              JOIN users u ON b.user_id = u.id 
                              JOIN classes c ON b.class_id = c.id 
                              WHERE b.id = :id");
        $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $to = $booking['email'];
            $subject = "Booking " . ucfirst($status) . " for " . $booking['class_name'];
            if ($status === 'confirmed') {
                $message = "Dear " . $booking['user_name'] . ",\n\n";
                $message .= "Thank you for booking your dance class with Dance Studio! We’re thrilled to have you join us and can’t wait to dance with you.\n\n";
                $message .= "Your booking for the following class has been successfully confirmed:\n\n";
                $message .= "Class: " . $booking['class_name'] . "\n";
                $message .= "Date & Time: " . date('l, F j, Y \a\t g:i A', strtotime($booking['start_date'])) . "\n";
                $message .= "Location: " . $booking['location'] . "\n\n";
                $message .= "Please arrive 10-15 minutes early to prepare and ensure a smooth start to your session. If you have any questions or need to make changes to your booking, feel free to reach out to us at 0701284812.\n\n";
                $message .= "We look forward to seeing you soon!\n\n";
                $message .= "Best regards,\n";
                $message .= "Dance Studio Team";
            } else {
                $message = "Dear " . $booking['user_name'] . ",\n\n";
                $message .= "We regret to inform you that your booking for the class " . $booking['class_name'] . " has been rejected.\n\n";
                $message .= "Please contact us at 0701284812 for any inquiries or further assistance.\n\n";
                $message .= "Best regards,\n";
                $message .= "Dance Studio Team";
            }

            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Ensure this is a valid SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'bonface.vulu@strathmore.edu'; // Your Gmail
                $mail->Password = 'wbsy jqwg oeqp nkta'; // Your App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                // Recipients
                $mail->setFrom('APIapp@gmail.com', 'DANCE STUDIO');
                $mail->addAddress($to, $booking['user_name']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = nl2br($message);
                $mail->AltBody = $message;

                $mail->send();
                $success = "Booking $status and email sent.";
            } catch (Exception $e) {
                $error = "Booking $status but email failed. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Booking not found.";
        }
    } else {
        $error = "Failed to update booking status.";
    }
}

$query = "SELECT b.*, u.name AS user_name, c.name AS class_name, u.email AS user_email, 
          i.name AS instructor_name, c.price AS class_price 
          FROM bookings b 
          JOIN users u ON b.user_id = u.id 
          JOIN classes c ON b.class_id = c.id 
          JOIN instructors i ON c.instructor_id = i.id";

$result = $db->query($query);

?>

<div class="container mt-5">
    <a href="admin.php" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
    </a>
    <h1 class="text-center mb-4" style="font-family: 'Arial', sans-serif; font-size: 36px; color: #ff69b4; font-weight: bold;">Manage Bookings</h1>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($result->rowCount() > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered" style="border-radius: 10px; overflow: hidden;">
                <thead class="table-dark" style="background-color: #343a40; color: #ffffff;">
                    <tr>
                        <th>ID</th>
                        <th>Client Name</th>
                        <th>Instructor Name</th>
                        <th>Class Name</th>
                        <th>Amount</th>
                        <th>Booking Date</th>
                        <th>Payment Reference</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $id = 1; while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $id++; ?></td>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['instructor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['class_price']); ?></td>
                            <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_reference']); ?></td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php elseif ($row['status'] === 'confirmed'): ?>
                                    <span class="badge bg-success">Confirmed</span>
                                <?php elseif ($row['status'] === 'rejected'): ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="confirm_booking" class="btn btn-primary btn-sm" style="border-radius: 20px;">Confirm</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="reject_booking" class="btn btn-secondary btn-sm" style="border-radius: 20px;">Reject</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No bookings found.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
/ /   T h i s   f i l e   m a n a g e s   t h e   b o o k i n g s   m a d e   b y   u s e r s .  
 