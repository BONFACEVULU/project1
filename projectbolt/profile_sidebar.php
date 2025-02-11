<?php
require_once 'config/dbconnection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user's booked classes
$query = "SELECT c.*, b.booking_date FROM bookings b JOIN classes c ON b.class_id = c.id WHERE b.user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$booked_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile update
    $name = $_POST['name'];
    $email = $_POST['email'];
    $avatar = $_FILES['avatar']['name'] ? 'uploads/' . basename($_FILES['avatar']['name']) : $user['avatar'];

    if ($_FILES['avatar']['name']) {
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar);
    }

    $query = "UPDATE users SET name = :name, email = :email, avatar = :avatar WHERE id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':avatar', $avatar);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $_SESSION['user_name'] = $name;
    $_SESSION['user_avatar'] = $avatar;

    $success = "Profile updated successfully.";
}
?>

<!-- User Profile Sidebar -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="profileSidebar" aria-labelledby="profileSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="profileSidebarLabel">My Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="text-center mb-4">
            <img src="<?php echo htmlspecialchars($user['image_url'] ?? 'path/to/default/avatar.png'); ?>" class="rounded-circle mb-3" width="100" height="100" alt="User Avatar">

            <h5><?php echo htmlspecialchars($user['name']); ?></h5>
            <p><?php echo htmlspecialchars($user['email']); ?></p>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
        </div>
        <h6>My Classes</h6>
        <ul class="list-group">
            <?php foreach ($booked_classes as $class): ?>
                <li class="list-group-item">
                    <h5><?php echo htmlspecialchars($class['name']); ?></h5>
                    <p>Booking Date: <?php echo htmlspecialchars($class['booking_date']); ?></p>
                    <p>Class Date: <?php echo htmlspecialchars($class['start_date']); ?></p>
                    <p>Duration: <?php echo htmlspecialchars($class['duration']); ?> minutes</p>
                    <p>Level: <?php echo htmlspecialchars($class['level']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="avatar" name="avatar">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
