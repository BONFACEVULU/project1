<?php
require_once '../includes/header.php';
require_once '../config/dbconnection.php';
require_once '../includes/image_handler.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$action = $_GET['action'] ?? 'list';
$type = $_GET['type'] ?? 'instructor';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if ($type == 'instructor') {
            $name = $_POST['name'];
            $bio = $_POST['bio'];
            $specialties = $_POST['specialties'];
            
            // Handle instructor image upload
            if (!empty($_FILES['instructor_image']['name'])) {
                $imageHandler = new ImageHandler();
                $instructor_image = $imageHandler->uploadImage($_FILES['instructor_image']);
            } elseif ($action == 'edit') {
                // Keep existing image if no new one uploaded
                $stmt = $db->prepare("SELECT instructor_image FROM instructors WHERE id = ?");
                $stmt->execute([$id]);
                $instructor_image = $stmt->fetchColumn();
            } else {
                throw new Exception("Instructor image is required.");
            }

            if ($action == 'add') {
                $query = "INSERT INTO instructors (name, bio, specialties, instructor_image) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$name, $bio, $specialties, $instructor_image]);
            } else {
                $query = "UPDATE instructors SET name = ?, bio = ?, specialties = ?, instructor_image = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$name, $bio, $specialties, $instructor_image, $id]);
            }
            
            $_SESSION['success'] = "Instructor " . ($action == 'add' ? 'added' : 'updated') . " successfully!";
            
        } elseif ($type == 'class') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $instructor_id = $_POST['instructor_id'];
            $level = $_POST['level'];
            $duration = $_POST['duration'];
            $price = $_POST['price'];
            $class_type = $_POST['class_type'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            
            // Handle class image upload
            if (!empty($_FILES['class_image']['name'])) {
                $imageHandler = new ImageHandler();
                $class_image = $imageHandler->uploadImage($_FILES['class_image']);
            } elseif ($action == 'edit') {
                $stmt = $db->prepare("SELECT class_image FROM classes WHERE id = ?");
                $stmt->execute([$id]);
                $class_image = $stmt->fetchColumn();
            } else {
                throw new Exception("Class image is required.");
            }

            if ($action == 'add') {
                $query = "INSERT INTO classes (name, description, instructor_id, level, duration, price, class_image, class_type, start_date, end_date) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$name, $description, $instructor_id, $level, $duration, $price, $class_image, $class_type, $start_date, $end_date]);
            } else {
                $query = "UPDATE classes SET name = ?, description = ?, instructor_id = ?, level = ?, duration = ?, 
                         price = ?, class_image = ?, class_type = ?, start_date = ?, end_date = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$name, $description, $instructor_id, $level, $duration, $price, $class_image, $class_type, $start_date, $end_date, $id]);
            }
            
            $_SESSION['success'] = "Class " . ($action == 'add' ? 'added' : 'updated') . " successfully!";
        }
        
        header('Location: admin.php');
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

if ($action == 'delete') {
    try {
        if ($type == 'instructor') {
            $query = "DELETE FROM instructors WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $_SESSION['success'] = "Instructor deleted successfully!";
        } elseif ($type == 'class') {
            $query = "DELETE FROM classes WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $_SESSION['success'] = "Class deleted successfully!";
        }
        
        header('Location: admin.php');
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Fetch all instructors for the dropdown
$stmt = $db->query("SELECT * FROM instructors ORDER BY name");
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all classes
$stmt = $db->query("SELECT c.*, i.name as instructor_name 
                    FROM classes c 
                    JOIN instructors i ON c.instructor_id = i.id 
                    ORDER BY c.start_date");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$class = null; // Initialize class variable to avoid undefined variable warning

if ($action == 'edit' && $type == 'class') {
    $stmt = $db->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Initialize class variable for add action
if ($action == 'add' && $type == 'class') {
    $class = [
        'name' => '',
        'description' => '',
        'instructor_id' => '',
        'level' => '',
        'duration' => '',
        'price' => '',
        'class_type' => '',
        'start_date' => '',
        'end_date' => '',
        'image_url' => ''
    ];
}

if ($action == 'edit' && $type == 'instructor') {
    $stmt = $db->prepare("SELECT * FROM instructors WHERE id = ?");
    $stmt->execute([$id]);
    $instructor = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dance Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/custom.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-black">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin.php?type=instructor">Instructors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin.php?type=class">Classes</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo $type == 'instructor' ? 'active' : ''; ?>" href="admin.php?type=instructor">Instructors</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $type == 'class' ? 'active' : ''; ?>" href="admin.php?type=class">Classes</a>
        </li>
    </ul>
    
    <!-- Add/Edit Forms -->
    <?php if ($action == 'add' || $action == 'edit'): ?>
        <?php if ($type == 'instructor'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h3><?php echo ucfirst($action); ?> Instructor</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="image-upload-container">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $action == 'edit' && isset($instructor) ? htmlspecialchars($instructor['name']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="3" required><?php echo $action == 'edit' && isset($instructor) ? htmlspecialchars($instructor['bio']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specialties</label>
                            <input type="text" name="specialties" class="form-control" value="<?php echo $action == 'edit' && isset($instructor) ? htmlspecialchars($instructor['specialties']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Instructor Image</label>
                            <input type="file" name="instructor_image" class="form-control" accept="image/*" id="instructorImageInput" <?php echo $action == 'add' ? 'required' : ''; ?>>
                            <div class="image-preview mt-2">
                                <?php if ($action == 'edit' && !empty($instructor['instructor_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($instructor['instructor_image']); ?>" class="preview-image mt-2" alt="Preview">
                                <?php endif; ?>
                            </div>
                            <div class="cropper-container mt-2" style="display: none;">
                                <img id="cropperImage" src="" alt="Cropper Image">
                            </div>
                            <button type="button" id="cropButton" class="btn btn-primary mt-2" style="display: none;">Crop Image</button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Desired Width (px)</label>
                            <input type="number" name="width" class="form-control" placeholder="Enter desired width">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Desired Height (px)</label>
                            <input type="number" name="height" class="form-control" placeholder="Enter desired height">
                        </div>
                        <button type="submit" class="btn btn-pink">Save Instructor</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h3><?php echo ucfirst($action); ?> Class</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="image-upload-container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Class Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars($class['name']) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3" required><?php echo $action == 'edit' && isset($class) ? htmlspecialchars($class['description']) : ''; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Instructor</label>
                                    <select name="instructor_id" class="form-control" required>
                                        <?php foreach ($instructors as $instructor): ?>
                                            <option value="<?php echo $instructor['id']; ?>" <?php echo $action == 'edit' && $class['instructor_id'] == $instructor['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($instructor['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Level</label>
                                    <select name="level" class="form-control" required>
                                        <option value="Beginner" <?php echo $action == 'edit' && $class['level'] == 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                                        <option value="Intermediate" <?php echo $action == 'edit' && $class['level'] == 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                        <option value="Advanced" <?php echo $action == 'edit' && $class['level'] == 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Duration (minutes)</label>
                                    <input type="number" name="duration" class="form-control" value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars($class['duration']) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Price ($)</label>
                                    <input type="number" name="price" class="form-control" value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars($class['price']) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Class Type</label>
                                    <input type="text" name="class_type" class="form-control" value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars($class['class_type']) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Class Image</label>
                                    <input type="file" name="class_image" class="form-control" accept="image/*" <?php echo $action == 'add' ? 'required' : ''; ?>>
                                    <div class="image-preview mt-2">
                                        <?php if ($action == 'edit' && !empty($class['class_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($class['class_image']); ?>" class="preview-image mt-2" alt="Preview">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="datetime-local" name="start_date" class="form-control" value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($class['start_date']))) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="datetime-local" name="end_date" class="form-control" value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($class['end_date']))) : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-pink">Save Class</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- List View -->
    <?php if ($action == 'list'): ?>
        <?php if ($type == 'instructor'): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Instructors</h3>
                <a href="admin.php?action=add&type=instructor" class="btn btn-pink">
                    <i class="fas fa-plus"></i> Add Instructor
                </a>
            </div>
            <div class="row g-4">
                <?php foreach ($instructors as $instructor): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm hover-effect">
                            <img src="<?php echo htmlspecialchars($instructor['instructor_image'] ?? 'uploads/default_instructor_image.jpg'); ?>" 
                                 class="card-img-top instructor-image" 
                                 alt="<?php echo htmlspecialchars($instructor['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($instructor['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($instructor['bio']); ?></p>
                                <p class="card-text"><strong>Specialties:</strong> <?php echo htmlspecialchars($instructor['specialties']); ?></p>
                            </div>
                            <div class="card-footer bg-white border-0 d-flex justify-content-between">
                                <a href="admin.php?action=edit&type=instructor&id=<?php echo $instructor['id']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="admin.php?action=delete&type=instructor&id=<?php echo $instructor['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this instructor?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Classes</h3>
                <a href="admin.php?action=add&type=class" class="btn btn-pink">
                    <i class="fas fa-plus"></i> Add Class
                </a>
            </div>
            <div class="container my-4">
                <?php if (isset($classes) && count($classes) > 0): ?>
                    <div class="row">
                        <?php foreach ($classes as $class): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm">
                                    <img src="<?php echo htmlspecialchars($class['class_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($class['name']); ?>" 
                                         class="card-img-top" 
                                         style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($class['name']); ?></h5>
                                        <p class="card-text">
                                            <strong>Instructor:</strong> <?php echo htmlspecialchars($class['instructor_name']); ?><br>
                                            <strong>Schedule:</strong> 
                                            <?php 
                                            echo date('M d, Y g:i A', strtotime($class['start_date'])) . ' - ' . 
                                                 date('g:i A', strtotime($class['end_date']));
                                            ?><br>
                                            <strong>Price:</strong> $<?php echo htmlspecialchars($class['price']); ?>
                                        </p>
                                        <div class="d-flex justify-content-between">
                                            <a href="admin.php?action=edit&type=class&id=<?php echo $class['id']; ?>" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="admin.php?action=delete&type=class&id=<?php echo $class['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this class?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        No classes available. Please add some classes.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const preview = this.parentElement.querySelector('#imagePreview');
        const file = this.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}" class="preview-image mt-2" alt="Preview">
                `;
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>

</body>
</html>
