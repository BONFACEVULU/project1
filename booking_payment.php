<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/header.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/vendor/autoload.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbconnection = new dbconnection();
    $db = $dbconnection->connect();
    
    $user_id = $_SESSION['user_id'];
    $class_id = $_POST['class_id'];
    $payment_method = $_POST['payment_method'];
    $payment_reference = $_POST['payment_reference'];
    
    // Insert booking with status
    $query = "INSERT INTO bookings (user_id, class_id, booking_date, payment_reference, status) 
              VALUES (:user_id, :class_id, NOW(), :payment_reference, 'pending')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':class_id', $class_id);
    $stmt->bindParam(':payment_reference', $payment_reference);
    
    if ($stmt->execute()) {
        // Email Notification Logic
        $user_email = $_SESSION['email'];

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Ensure this is a valid SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'bonface.vulu@strathmore.edu'; // Your Gmail
            $mail->Password = 'wbsy jqwg oeqp nkta'; // Your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('from_email@example.com', 'Dance Studio');
            $mail->addAddress($user_email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Booking Pending Confirmation';
            $mail->Body = 'Dear User,<br><br>Your booking is pending confirmation. You will be notified once it is confirmed.<br><br>Thank you for choosing our service!';
            $mail->AltBody = 'Dear User, Your booking is pending confirmation. You will be notified once it is confirmed. Thank you for choosing our service!';

            $mail->send();
            $success = "Booking submitted successfully. Waiting for confirmation.";
        } catch (Exception $e) {
            $error = "Booking submitted, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Error submitting booking. Please try again.";
    }
}

// Get class details
$class_id = $_GET['class_id'] ?? null;
if ($class_id) {
    $dbconnection = new dbconnection();
    $db = $dbconnection->connect();
    
    $query = "SELECT c.*, i.name AS instructor_name 
             FROM classes c
             JOIN instructors i ON c.instructor_id = i.id
             WHERE c.id = :class_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':class_id', $class_id);
    $stmt->execute();
    $class = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Class</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <style>
        .btn-exit {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: transparent;
            color: #6c757d;
            border: none;
            font-size: 24px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .btn-exit:hover {
            color: #5a6268;
        }
    </style>
</head>
<body>
<button class="btn-exit" onclick="window.location.href='index.php';"><i class="bi bi-chevron-double-left"></i></button>

    <div class="container mt-5">

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Book Class: <?php echo htmlspecialchars($class['name'] ?? ''); ?></h3>
                        <p>Instructor: <?php echo htmlspecialchars($class['instructor_name'] ?? ''); ?></p>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo htmlspecialchars($class['start_date'] ?? ''); ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="M-Pesa">M-Pesa (Send Money)</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">M-Pesa Number</label>
                                <input type="text" class="form-control" value="0701284812" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Reference</label>
                                <input type="text" class="form-control" name="payment_reference" required>
                            </div>
                            
                            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                            <button type="submit" class="btn btn-primary w-100">Submit Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
