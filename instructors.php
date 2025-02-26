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
        <h1 class="text-center mb-5 instructor-title">Our Expert Instructors</h1>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($instructors as $instructor): ?>
                <div class="col">
                    <div class="instructor-card card h-100 shadow-sm">
                        <div class="position-relative instructor-image-container">
    <img src="<?php echo htmlspecialchars(str_replace('../', '', $instructor['instructor_image'] ?? 'uploads/default_instructor_image.jpg')); ?>"
         class="card-img-top instructor-image"
         alt="<?php echo htmlspecialchars($instructor['name']); ?>"
         style="object-fit: contain; max-height: 300px;">

                            <div class="instructor-overlay">
                                <div class="specialties">
                                    <?php 
                                    $specialties = explode(',', $instructor['specialties']);
                                    foreach ($specialties as $specialty): ?>
                                        <span class="badge instructor-specialty"><?php echo trim($specialty); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="card-body text-center">
                            <h3 class="card-title instructor-name"><?php echo htmlspecialchars($instructor['name']); ?></h3>
                            <p class="card-text instructor-bio"><?php echo htmlspecialchars($instructor['bio']); ?></p>
                        </div>

                        <div class="card-footer bg-white border-0 text-center">
                            <a href="classes.php?instructor=<?php echo $instructor['id']; ?>"
                               class="btn instructor-classes-btn">View Classes</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<style>
    .instructor-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .instructor-card:hover {
        transform: translateY(-10px);
    }

    .instructor-image {
        width: 100%;
        height: auto; /* Allow height to adjust based on the image aspect ratio */
        object-fit: contain; /* Ensures the entire image fits without cropping */
        max-height: 300px; /* Optional: limit max height to keep images consistent */
    }


    .instructor-info {
        padding: 20px;
        text-align: center;
    }

    .instructor-info h5 {
        font-size: 1.25rem;
        font-weight: bold;
        color: #333;
    }

    .instructor-info p {
        font-size: 1rem;
        color: #777;
    }

    .instructor-image-container {
        width: 100%;
        height: 250px;
        overflow: hidden;
    }

    .instructor-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .instructor-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
        padding: 10px;
        text-align: center;
    }

    .instructor-specialty {
        background-color: #ff69b4;
        color: #fff;
        margin: 2px;
    }
</style>
