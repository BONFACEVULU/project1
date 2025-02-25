<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/header.php';
require_once 'config/dbconnection.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT *, emergency_contact_name, emergency_contact_phone, parent_consent FROM users WHERE id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile update
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'] ?? null;
    $birth_month = $_POST['birth_month'] ?? null;
    $birth_day = $_POST['birth_day'] ?? null;
    $birth_year = $_POST['birth_year'] ?? null;
    $mobile = $_POST['mobile'] ?? null;
    $address = $_POST['address'] ?? null;
    $city = $_POST['city'] ?? null;
    $postal_code = $_POST['postal_code'] ?? null;
    $emergency_contact_name = $_POST['emergency_contact_name'] ?? null;
    $emergency_contact_phone = $_POST['emergency_contact_phone'] ?? null;
    $parent_consent = isset($_POST['parent_consent']) ? 1 : 0;
    $avatar = $_FILES['avatar']['name'] ? 'uploads/' . basename($_FILES['avatar']['name']) : ($user['profile_picture'] ?? 'uploads/default_avatar.png');

    if ($_FILES['avatar']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file);
        $avatar = $target_file;
    }

    $query = "UPDATE users SET 
                name = :name, 
                email = :email, 
                gender = :gender,
                birth_month = :birth_month,
                birth_day = :birth_day,
                birth_year = :birth_year,
                mobile = :mobile,
                address = :address,
                city = :city,
                postal_code = :postal_code,
                profile_picture = :avatar,
                emergency_contact_name = :emergency_contact_name,
                emergency_contact_phone = :emergency_contact_phone,
                parent_consent = :parent_consent 
              WHERE id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':birth_month', $birth_month);
    $stmt->bindParam(':birth_day', $birth_day);
    $stmt->bindParam(':birth_year', $birth_year);
    $stmt->bindParam(':mobile', $mobile);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postal_code', $postal_code);
    $stmt->bindParam(':avatar', $avatar);
    $stmt->bindParam(':emergency_contact_name', $emergency_contact_name);
    $stmt->bindParam(':emergency_contact_phone', $emergency_contact_phone);
    $stmt->bindParam(':parent_consent', $parent_consent);
    $stmt->bindParam(':user_id', $user_id);

    $stmt->execute();

    $_SESSION['user_name'] = $name;
    $_SESSION['user_avatar'] = $avatar;

    $success = "Profile updated successfully.";
}
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-image-container">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'uploads/default_avatar.png'); ?>" 
                 class="profile-image" 
                 alt="Profile Picture">
        </div>
        <h1 class="profile-name"><?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?></h1>
        <p class="profile-email"><?php echo htmlspecialchars($user['email'] ?? 'No email provided'); ?></p>
        <button class="btn btn-primary" onclick="openEditProfileModal()">Edit Profile</button>
    </div>

    <div class="profile-details">
        <div class="detail-section">
            <h2>Personal Information</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Gender:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['gender'] ?? 'Not specified'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Birthdate:</span>
                    <span class="detail-value"><?php 
                        $birthdate = [];
                        if (!empty($user['birth_day'])) $birthdate[] = $user['birth_day'];
                        if (!empty($user['birth_month'])) $birthdate[] = $user['birth_month'];
                        if (!empty($user['birth_year'])) $birthdate[] = $user['birth_year'];
                        echo htmlspecialchars($birthdate ? implode('/', $birthdate) : 'Not specified'); 
                    ?></span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h2>Contact Information</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Mobile:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['mobile'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">City:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['city'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Postal Code:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['postal_code'] ?? 'N/A'); ?></span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h2>Emergency Contact Information</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Emergency Contact Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['emergency_contact_name'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Emergency Contact Phone:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['emergency_contact_phone'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Parent Consent:</span>
                    <span class="detail-value"><?php echo ($user['parent_consent'] ?? 0) ? 'Yes' : 'No'; ?></span>
                </div>
            </div>
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
                            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'uploads/default_avatar.png'); ?>" class="profile-image" id="modalProfileImage">
                            <div class="profile-icon">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <label for="avatar" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" onchange="loadFile(event)">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender (optional)</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">Select</option>
                            <option value="Male" <?php echo ($user['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($user['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($user['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Birthdate (optional)</label>
                        <div class="row g-3">
                            <div class="col">
                                <select class="form-select" name="birth_month">
                                    <option value="">Month</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($user['birth_month'] ?? '') == $i ? 'selected' : ''; ?>><?php echo date('F', mktime(0, 0, 0, $i, 10)); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-select" name="birth_day">
                                    <option value="">Day</option>
                                    <?php for ($i = 1; $i <= 31; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($user['birth_day'] ?? '') == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-select" name="birth_year">
                                    <option value="">Year</option>
                                    <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($user['birth_year'] ?? '') == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile Phone</label>
                        <input type="tel" class="form-control" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address (optional)</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City (optional)</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="postal_code" class="form-label">Postal Code</label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                        <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo htmlspecialchars($user['emergency_contact_name'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                        <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo htmlspecialchars($user['emergency_contact_phone'] ?? ''); ?>">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="parent_consent" name="parent_consent" value="1" <?php echo ($user['parent_consent'] ?? 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="parent_consent">Parent Consent</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.profile-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.profile-header {
    text-align: center;
    margin-bottom: 2rem;
}

.profile-image-container {
    width: 150px;
    height: 150px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #007bff;
}

.profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-name {
    font-size: 2rem;
    color: #343a40;
    margin-bottom: 0.5rem;
}

.profile-email {
    color: #6c757d;
    font-size: 1.1rem;
}

.detail-section {
    margin-bottom: 2rem;
}

.detail-section h2 {
    color: #007bff;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.detail-item {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.detail-label {
    display: block;
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.detail-value {
    color: #343a40;
    font-size: 1.1rem;
}
</style>

<script>
function openEditProfileModal() {
    const editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
    editProfileModal.show();
}

function loadFile(event) {
    var output = document.getElementById('modalProfileImage');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src)
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
