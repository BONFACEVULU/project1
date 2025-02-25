<?php
require_once 'includes/header.php';
require_once 'classes/User.php';

$user = new User();
$userId = $_SESSION['user_id'];
$profileDetails = $user->getProfileDetails($userId);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'id' => $userId,
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'emergency_contact_name' => $_POST['emergency_contact_name'],
        'emergency_contact_phone' => $_POST['emergency_contact_phone'],
        'preferences' => $_POST['preferences'],
        'gender' => $_POST['gender'],
        'birthdate' => $_POST['birthdate'],
        'mobile_phone' => $_POST['mobile_phone'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'postal_code' => $_POST['postal_code']
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
        
        <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-control">
                <option value="">Select Gender</option>
                <option value="Male" <?php echo ($profileDetails['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($profileDetails['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($profileDetails['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Birthdate</label>
            <input type="date" name="birthdate" class="form-control" value="<?php echo htmlspecialchars($profileDetails['birthdate'] ?? ''); ?>">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Mobile Phone</label>
            <input type="text" name="mobile_phone" class="form-control" value="<?php echo htmlspecialchars($profileDetails['mobile_phone'] ?? ''); ?>">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($profileDetails['address'] ?? ''); ?>">
        </div>
        
        <div class="mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($profileDetails['city'] ?? ''); ?>">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Postal Code</label>
            <input type="text" name="postal_code" class="form-control" value="<?php echo htmlspecialchars($profileDetails['postal_code'] ?? ''); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
