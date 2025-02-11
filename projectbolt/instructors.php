<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch all instructors
$query = "SELECT * FROM instructors ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();
$instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="instructor-section py-5">
    <div class="container">
        <h1 class="text-center mb-5">Our Expert Instructors</h1>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($instructors as $instructor): ?>
                <div class="col">
                    <div class="instructor-card card h-100 shadow-sm hover-effect">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars(str_replace('../', '', $instructor['instructor_image'] ?? 'uploads/default_instructor_image.jpg')); ?>"
                                 class="card-img-top instructor-image"
                                 alt="<?php echo htmlspecialchars($instructor['name']); ?>">
                            <div class="instructor-overlay">
                                <div class="specialties">
                                    <?php 
                                    $specialties = explode(',', $instructor['specialties']);
                                    foreach ($specialties as $specialty): ?>
                                        <span class="badge bg-pink"><?php echo trim($specialty); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body text-center">
                            <h3 class="card-title"><?php echo htmlspecialchars($instructor['name']); ?></h3>
                            <p class="card-text"><?php echo htmlspecialchars($instructor['bio']); ?></p>
                        </div>
                        
                        <div class="card-footer bg-white border-0 text-center">
                            <a href="classes.php?instructor=<?php echo $instructor['id']; ?>" 
                               class="btn btn-pink">View Classes</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
:root {
    --pink: #ff4081;
    --dark: #1a1a1a;
}

.instructor-section {
    background-color: #f8f9fa;
}

.instructor-card {
    transition: transform 0.3s ease;
    border: none;
    overflow: hidden;
}

.instructor-card:hover {
    transform: translateY(-5px);
}

.instructor-image {
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.instructor-card:hover .instructor-image {
    transform: scale(1.05);
}

.instructor-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    transition: opacity 0.3s ease;
}

.specialties {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
}

.bg-pink {
    background-color: var(--pink);
}

.btn-pink {
    background-color: var(--pink);
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    transition: all 0.3s ease;
}

.btn-pink:hover {
    background-color: #e91e63;
    color: white;
    transform: translateY(-2px);
}

.hover-effect {
    transition: all 0.3s ease;
}

.hover-effect:hover {
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}
</style>

<?php require_once 'includes/footer.php'; ?>