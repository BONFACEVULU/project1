<?php
require_once 'includes/header.php';
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
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file);
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

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-circle">
                        <img src="<?php echo htmlspecialchars($user['avatar'] ?? 'path/to/default/avatar.png'); ?>" class="profile-image" id="profileImage">
                        <div class="profile-icon" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    <h5 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($user['email']); ?></p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h3>My Classes</h3>
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
                    <div class="mb-3 text-center">
                        <div class="profile-circle">
                            <img src="<?php echo htmlspecialchars($user['avatar'] ?? 'path/to/default/avatar.png'); ?>" class="profile-image" id="modalProfileImage">
                        </div>
                        <label for="avatar" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" onchange="loadFile(event)">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<style>
/* Custom Styles */
.profile-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto 15px;
    border: 2px solid var(--secondary);
    position: relative;
}

.profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-icon {
    position: absolute;
    bottom: 0;
    right: 0;
    background-color: var(--secondary);
    color: white;
    border-radius: 50%;
    padding: 5px;
    cursor: pointer;
}
</style>

<script>
function loadFile(event) {
    var output = document.getElementById('modalProfileImage');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Update profile image in the main profile circle when the modal is closed
    const editProfileModal = document.getElementById('editProfileModal');
    editProfileModal.addEventListener('hidden.bs.modal', function () {
        const profileImage = document.getElementById('profileImage');
        const modalProfileImage = document.getElementById('modalProfileImage');
        profileImage.src = modalProfileImage.src;
    });
});
</script>
