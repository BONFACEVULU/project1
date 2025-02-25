<?php
require_once '../includes/header.php';
require_once '../config/dbconnection.php';
require_once '../includes/image_handler.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $instructor_id = $_POST['instructor_id'];
        $level = $_POST['level'];
        $price = $_POST['price'];
        $class_type = $_POST['class_type'];
        
        // Calculate duration automatically from start/end times using timestamps
        $start = new DateTime($_POST['start_date']);
        $end = new DateTime($_POST['end_date']);
        $duration = ($end->getTimestamp() - $start->getTimestamp()) / 60; // Convert seconds to minutes

        // Convert datetime-local input to proper MySQL datetime format
        $start_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
        $end_date = date('Y-m-d H:i:s', strtotime($_POST['end_date']));

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
            $query = "INSERT INTO classes (name, description, instructor_id, level, duration, price, class_image, 
                     class_type, start_date, end_date, what_to_bring, location, max_capacity, current_bookings, day) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 

            $stmt = $db->prepare($query);
            $stmt->execute([$name, $description, $instructor_id, $level, $duration, $price, $class_image, 
                          $class_type, $start_date, $end_date, $_POST['what_to_bring'], $_POST['location'], 
                          $_POST['max_capacity'], 0, date('l', strtotime($start_date))]); 

        } else {
            $query = "UPDATE classes SET name = ?, description = ?, instructor_id = ?, level = ?, duration = ?, 
                     price = ?, class_image = ?, class_type = ?, start_date = ?, end_date = ?, what_to_bring = ?, 
                     location = ?, max_capacity = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$name, $description, $instructor_id, $level, $duration, $price, $class_image, 
                          $class_type, $start_date, $end_date, $_POST['what_to_bring'], $_POST['location'], 
                          $_POST['max_capacity'], $id]);
        }
        
        $_SESSION['success'] = "Class " . ($action == 'add' ? 'added' : 'updated') . " successfully!";
        header('Location: class_management.php');
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

if ($action == 'delete') {
    try {
        $query = "DELETE FROM classes WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        $_SESSION['success'] = "Class deleted successfully!";
        header('Location: class_management.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Fetch all instructors for the dropdown
$stmt = $db->query("SELECT * FROM instructors ORDER BY name");
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all classes
$currentDate = date('Y-m-d H:i:s');
$stmt = $db->query("SELECT c.*, i.name as instructor_name 
                    FROM classes c 
                    JOIN instructors i ON c.instructor_id = i.id 
                    WHERE c.start_date >= '$currentDate'
                    ORDER BY c.start_date");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($action == 'edit') {
    $stmt = $db->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management - Dance Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/projectbolt/css/admin.css">
    <link rel="stylesheet" href="/projectbolt/css/custom.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-black">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="instructor_management.php">Instructors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="class_management.php">Classes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin.php?type=reports">Reports</a>
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

    <?php if ($action == 'add' || $action == 'edit'): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h3><?php echo ucfirst($action); ?> Class</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
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
                                <label class="form-label">Price (ksh)</label>
                                <input type="number" name="price" class="form-control" value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars($class['price']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Class Type</label>
                                <input type="text" name="class_type" class="form-control" value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars($class['class_type']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Class Image</label>
                                <input type="file" name="class_image" class="form-control" accept="image/*" <?php echo $action == 'add' ? 'required' : ''; ?>>
                                <?php if ($action == 'edit' && !empty($class['class_image'])): ?>
                                    <div class="image-preview mt-2">
                                        <img src="<?php echo htmlspecialchars($class['class_image']); ?>" class="preview-image mt-2" alt="Preview">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">What to Bring</label>
                                <textarea name="what_to_bring" class="form-control" rows="2"><?php echo $action == 'edit' && isset($class['what_to_bring']) ? htmlspecialchars($class['what_to_bring']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" value="<?php echo $action == 'edit' && isset($class['location']) ? htmlspecialchars($class['location']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Max Capacity</label>
                                <input type="number" name="max_capacity" class="form-control" value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars($class['max_capacity']) : '30'; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="datetime-local" name="start_date" class="form-control" 
                                       value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($class['start_date']))) : ''; ?>" 
                                       min="<?php echo date('Y-m-d\TH:i'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="datetime-local" name="end_date" class="form-control" 
                                       value="<?php echo $action == 'edit' && isset($class) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($class['end_date']))) : ''; ?>" 
                                       min="<?php echo date('Y-m-d\TH:i'); ?>" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-pink">Save Class</button>
                    <a href="class_management.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Classes</h3>
            <a href="class_management.php?action=add" class="btn btn-pink">
                <i class="fas fa-plus"></i> Add Class
            </a>
        </div>
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
                                <strong>Price:</strong> KSH<?php echo htmlspecialchars($class['price']); ?>
                            </p>
                            <div class="d-flex justify-content-between">
                                <a href="class_management.php?action=edit&id=<?php echo $class['id']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="class_management.php?action=delete&id=<?php echo $class['id']; ?>" 
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
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/projectbolt/assets/js/admin.js"></script>
</body>
</html>
