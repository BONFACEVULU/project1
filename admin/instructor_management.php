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
        header('Location: instructor_management.php');
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

if ($action == 'delete') {
    try {
        $query = "DELETE FROM instructors WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        $_SESSION['success'] = "Instructor deleted successfully!";
        header('Location: instructor_management.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Fetch all instructors
$stmt = $db->query("SELECT * FROM instructors ORDER BY name");
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($action == 'edit') {
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
    <title>Instructor Management - Dance Studio</title>
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
                    <a class="nav-link active" href="instructor_management.php">Instructors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="class_management.php">Classes</a>
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
                        <input type="file" name="instructor_image" class="form-control" accept="image/*" <?php echo $action == 'add' ? 'required' : ''; ?>>
                        <?php if ($action == 'edit' && !empty($instructor['instructor_image'])): ?>
                            <div class="image-preview mt-2">
                                <img src="<?php echo htmlspecialchars($instructor['instructor_image']); ?>" class="preview-image mt-2" alt="Preview">
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-pink">Save Instructor</button>
                    <a href="instructor_management.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Instructors</h3>
            <a href="instructor_management.php?action=add" class="btn btn-pink">
                <i class="fas fa-plus"></i> Add Instructor
            </a>
        </div>
        <div class="row g-4">
            <?php foreach ($instructors as $instructor): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm hover-effect">
    <img src="<?php echo htmlspecialchars($instructor['instructor_image'] ?? 'uploads/default_instructor_image.jpg'); ?>" 
         class="card-img-top instructor-image" 
         alt="<?php echo htmlspecialchars($instructor['name']); ?>"
         style="object-fit: contain; max-height: 300px;">

                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($instructor['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($instructor['bio']); ?></p>
                            <p class="card-text"><strong>Specialties:</strong> <?php echo htmlspecialchars($instructor['specialties']); ?></p>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-between">
                            <a href="instructor_management.php?action=edit&id=<?php echo $instructor['id']; ?>" 
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="instructor_management.php?action=delete&id=<?php echo $instructor['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this instructor?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
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
/ /   T h i s   f i l e   m a n a g e s   t h e   i n s t r u c t o r   d a t a   a n d   o p e r a t i o n s .  
 