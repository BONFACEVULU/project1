<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Handle admin date updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_date'])) {
    $class_id = $_POST['class_id'];
    $new_date = $_POST['new_date'];
    
    // Validate date format
    if (DateTime::createFromFormat('Y-m-d H:i:s', $new_date) !== false) {
        $query = "UPDATE classes SET start_date = :new_date, day = DATE_FORMAT(:new_date, '%a') WHERE id = :class_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':new_date', $new_date);
        $stmt->bindParam(':class_id', $class_id);
        
        if ($stmt->execute()) {
            $success = "Class date updated successfully!";
        } else {
            $error = "Failed to update class date.";
        }
    } else {
        $error = "Invalid date format. Please use YYYY-MM-DD HH:MM:SS";
    }
}

// Fetch all classes with instructor details
$query = "SELECT c.*, i.name as instructor_name, i.instructor_image, c.class_image 
          FROM classes c 
          JOIN instructors i ON c.instructor_id = i.id 
          WHERE c.start_date >= NOW() 
          ORDER BY c.start_date";

$stmt = $db->prepare($query);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique days from classes
$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$classes_by_day = array_fill_keys($days, []);
foreach ($classes as $class) {
    $day = date('D', strtotime($class['start_date']));
    $classes_by_day[$day][] = $class;
}

// Initialize booked classes
$booked_classes = [];
$user = null; // Initialize user variable
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT c.*, b.booking_date FROM bookings b JOIN classes c ON b.class_id = c.id WHERE b.user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $booked_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch user details
    $query = "SELECT * FROM users WHERE id = :user_id";
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
    $avatar = $_FILES['avatar']['name'] ? 'uploads/' . basename($_FILES['avatar']['name']) : ($user['profile_picture'] ?? 'uploads/default_avatar.png');

    if ($_FILES['avatar']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file);
        $avatar = $target_file; // Update avatar path
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
                profile_picture = :avatar 
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
    $stmt->bindParam(':user_id', $user_id);

    $stmt->execute();

    $_SESSION['user_name'] = $name;
    $_SESSION['user_avatar'] = $avatar;

    $success = "Profile updated successfully.";
}
?>

<!-- Calendar Header -->
<div class="calendar-header bg-dark py-4 mb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <?php foreach ($days as $day): 
                $has_classes = !empty($classes_by_day[$day]);
            ?>
                <div class="calendar-day position-relative <?php echo $has_classes ? 'has-classes' : ''; ?>"
                     data-day="<?php echo $day; ?>">
                    <span class="text-white fs-5"><?php echo $day; ?></span>
                    <?php if ($has_classes): ?>
                        <span class="calendar-dot">
                            <img src="<?php echo htmlspecialchars(str_replace('../', '', $classes_by_day[$day][0]['instructor_image'] ?? 'uploads/default_instructor_image.jpg')); ?>" 
                                 class="rounded-circle" 
                                 alt="<?php echo htmlspecialchars($classes_by_day[$day][0]['instructor_name']); ?>" 
                                 style="width: 20px; height: 20px; object-fit: cover;">
                        </span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="container">
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <div class="admin-date-update mb-4">
            <h3>Update Class Date</h3>
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <select name="class_id" class="form-select" required>
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>">
                                <?php echo htmlspecialchars($class['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="datetime-local" name="new_date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="update_date" class="btn btn-primary">Update Date</button>
                </div>
            </form>
            <?php if (isset($success)): ?>
                <div class="alert alert-success mt-3"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <h1 class="text-center mb-5">Our Dance Classes</h1>

    <!-- Classes Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
        <?php foreach ($classes as $class): ?>
            <div class="col d-flex align-items-stretch">
                <div class="class-card card h-100 shadow-sm hover-effect">
                    <div class="position-relative">
                        <img src="<?php echo htmlspecialchars(str_replace('../', '', $class['class_image'] ?? 'uploads/default_class_image.jpg')); ?>"
                             class="class-image"
                             alt="<?php echo htmlspecialchars($class['name']); ?>">
                        <div class="price-tag">
                            KSH<?php echo htmlspecialchars($class['price']); ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?php echo htmlspecialchars(str_replace('../', '', $class['instructor_image'] ?? 'uploads/default_instructor_image.jpg')); ?>"
                                 class="instructor-image"
                                 alt="<?php echo htmlspecialchars($class['instructor_name']); ?>">
                            <div class="ms-3">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($class['name']); ?></h5>
                                <p class="text-secondary mb-0"><?php echo htmlspecialchars($class['instructor_name']); ?></p>
                            </div>
                        </div>
                        
                        <div class="class-details">
                            <div class="detail-item">
                                <i class="fas fa-clock text-secondary"></i>
                                <span><?php echo date('g:i A', strtotime($class['start_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-hourglass-half text-secondary"></i>
                                <span><?php echo htmlspecialchars($class['duration']); ?> min</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-signal text-secondary"></i>
                                <span><?php echo htmlspecialchars($class['level']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-music text-secondary"></i>
                                <span><?php echo htmlspecialchars($class['class_type']); ?></span>
                            </div>
                        </div>
                        
                        <p class="card-text mt-3"><?php echo htmlspecialchars($class['description']); ?></p>
                    </div>
                    
                    <div class="class-footer">
<a href="schedule.php" 
   class="btn btn-book">
    VIEW CLASS
</a>

                        <div class="show-details" onclick="toggleDetails(this)">
                            Show Details <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="class-details-content">
                            <p><?php echo htmlspecialchars($class['description']); ?></p>
                            <p><strong>What to Bring:</strong> <?php echo htmlspecialchars($class['what_to_bring']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($class['location']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Day Classes Modal -->
<div class="modal fade" id="dayClassesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
            <h5 class="modal-title">Classes on <span id="selectedDay"></span></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="dayClassesList"></div>
            </div>
        </div>
    </div>
</div>

<!-- User Profile Button -->
<div class="user-profile-button" onclick="toggleProfileSidebar()">
    <div class="profile-icon-box">
        <i class="bi bi-chevron-bar-right"></i>
    </div>
    <span>User Profile</span>
</div>

<!-- User Profile Sidebar -->
<div class="user-profile-sidebar">
    <div class="profile-header">
        <h5>My Profile</h5>
        <button type="button" class="btn-close" id="closeProfileSidebar" onclick="toggleProfileSidebar()"></button>
    </div>
    <div class="profile-body">
        <div class="profile-box">
            <div class="text-center mb-4">
                <div class="profile-image-container">
                    <div class="profile-circle">
                        <img src="<?php echo htmlspecialchars($_SESSION['user_avatar'] ?? 'uploads/default_avatar.png'); ?>" 
                             class="profile-image img-fluid rounded-circle shadow"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    <div class="profile-overlay">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <h5><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h5>
                <p><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                <a href="view_profile.php" class="btn btn-primary mb-2 btn-professional">
                    <i class="fas fa-user-circle me-2"></i>View Profile
                </a>
                <a href="cart.php" class="btn btn-secondary btn-professional">
                    <i class="fas fa-shopping-cart me-2"></i>View My Classes
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<link rel="stylesheet" href="css/classes.css">
<style>
.btn-professional {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-professional i {
    margin-right: 8px;
}

.btn-professional:hover {
    background-color: #0056b3;
    color: #fff;
}
</style>
<script>
function toggleDetails(element) {
    const details = element.parentElement.querySelector('.class-details-content');
    const chevron = element.querySelector('i');
    details.classList.toggle('show');
    chevron.classList.toggle('fa-chevron-down');
    chevron.classList.toggle('fa-chevron-up');
}

function toggleProfileSidebar() {
    const userProfileSidebar = document.querySelector('.user-profile-sidebar');
    userProfileSidebar.classList.toggle('open');
}

document.addEventListener('DOMContentLoaded', function() {
    // Store classes by day
    const classesByDay = <?php echo json_encode($classes_by_day); ?>;
    
    // Handle day click
    document.querySelectorAll('.calendar-day').forEach(day => {
        day.addEventListener('click', function() {
            const dayName = this.dataset.day;
            const dayClasses = classesByDay[dayName];
            
            if (dayClasses && dayClasses.length > 0) {
                showDayClasses(dayName, dayClasses);
            }
        });
    });
    
    function showDayClasses(day, classes) {
        document.getElementById('selectedDay').textContent = day;
        
        const classList = document.getElementById('dayClassesList');
        classList.innerHTML = classes.map(classItem => `
            <div class="d-flex align-items-center border-bottom py-3">
                <img src="${classItem.instructor_image}" 
                     class="instructor-image me-3">
                <div class="flex-grow-1">
                    <h5 class="mb-1">${classItem.name}</h5>
                    <p class="text-secondary mb-1">${classItem.instructor_name}</p>
                    <p class="mb-0">
                        ${new Date(classItem.start_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} 
                        | ${classItem.duration} min 
                        | ${classItem.level}
                    </p>
                </div>
                <a href="booking.php?class_id=${classItem.id}" 
                   class="btn btn-secondary">Book</a>
            </div>
        `).join('');
        
        new bootstrap.Modal(document.getElementById('dayClassesModal')).show();
    }

    // Handle user profile button hover
    const userProfileButton = document.querySelector('.user-profile-button');
    const userProfileSidebar = document.querySelector('.user-profile-sidebar');
    const closeProfileSidebar = document.getElementById('closeProfileSidebar');

    userProfileButton.addEventListener('mouseover', function() {
        userProfileSidebar.classList.add('open');
    });

    userProfileSidebar.addEventListener('mouseleave', function() {
        userProfileSidebar.classList.remove('open');
    });

    closeProfileSidebar.addEventListener('click', function() {
        userProfileSidebar.classList.remove('open');
    });
});
</script>
