<?php
require_once 'includes/header.php';
require_once 'classes/User.php';

$user = new User();
$userId = $_SESSION['user_id']; // Assuming user ID is stored in session
$profileDetails = $user->getProfileDetails($userId);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'id' => $userId,
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'emergency_contact_name' => $_POST['emergency_contact_name'],
        'emergency_contact_phone' => $_POST['emergency_contact_phone'],
        'preferences' => $_POST['preferences']
    ];
    $user->updateProfile($data);
    header('Location: profile.php');
    exit();
}
?>

<div class="container">
    <h1>Edit Profile</h1>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($profileDetails['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($profileDetails['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($profileDetails['phone_number']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Emergency Contact Name</label>
            <input type="text" name="emergency_contact_name" class="form-control" value="<?php echo htmlspecialchars($profileDetails['emergency_contact_name']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Emergency Contact Phone</label>
            <input type="text" name="emergency_contact_phone" class="form-control" value="<?php echo htmlspecialchars($profileDetails['emergency_contact_phone']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Preferences</label>
            <textarea name="preferences" class="form-control"><?php echo htmlspecialchars($profileDetails['preferences']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
