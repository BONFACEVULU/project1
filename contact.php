<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbconnection = new dbconnection();
    $db = $dbconnection->connect();

    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $query = "INSERT INTO contact_messages (name, email, subject, message, created_at) 
              VALUES (:name, :email, :subject, :message, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':message', $message);

    if ($stmt->execute()) {
        $success = "Your message has been sent successfully.";
    } else {
        $error = "There was an error sending your message. Please try again.";
    }
}
?>

<section class="contact-section py-5">
    <div class="container">
        <h1 class="text-center mb-5">Contact Us</h1>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <h3>Get in Touch</h3>
                <p>If you have any questions or need further information, feel free to contact us. We are here to help you!</p>
                <ul class="list-unstyled">
                    <li><strong>Address:</strong> 123 Dance Street, City, State 12345</li>
                    <li><strong>Phone:</strong> (123) 456-7890</li>
                    <li><strong>Email:</strong> info@dancestudio.com</li>
                </ul>
                <div class="social-icons mt-4">
                    <a href="https://www.facebook.com" target="_blank" class="me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com" target="_blank" class="me-3"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.twitter.com" target="_blank" class="me-3"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Send Us a Message</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>