<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch all classes with instructor details
$query = "SELECT c.*, i.name as instructor_name, i.instructor_image, c.class_image 
          FROM classes c 
          JOIN instructors i ON c.instructor_id = i.id 
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
    $avatar = $_FILES['avatar']['name'] ? 'uploads/' . basename($_FILES['avatar']['name']) : ($user['profile_picture'] ?? 'path/to/default/avatar.png');

    if ($_FILES['avatar']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file);
        $avatar = $target_file; // Update avatar path
    }

    $query = "UPDATE users SET name = :name, email = :email, profile_picture = :avatar WHERE id = :user_id";
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
                            $<?php echo htmlspecialchars($class['price']); ?>
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
                    
                    <div class="card-footer bg-white border-0 pt-0">
                        <a href="booking.php?class_id=<?php echo $class['id']; ?>" 
                           class="btn btn-secondary w-100">
                            Book Now
                        </a>
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
                <div class="profile-circle">
                    <img src="<?php echo htmlspecialchars($_SESSION['user_avatar'] ?? 'path/to/default/avatar.png'); ?>" class="profile-image">
                    <div class="profile-icon" onclick="openEditProfileModal()">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <h5><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h5>
                <p><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                <button class="btn btn-primary" onclick="openEditProfileModal()">Edit Profile</button>
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
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<link rel="stylesheet" href="css/classes.css">

<script>
function toggleProfileSidebar() {
    const userProfileSidebar = document.querySelector('.user-profile-sidebar');
    userProfileSidebar.classList.toggle('open');
}

function openEditProfileModal() {
    const editProfileModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
    editProfileModal.show();
}

function loadFile(event) {
    var output = document.getElementById('modalProfileImage');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
    }
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
