<?php
require_once 'includes/header.php';
require_once 'config/dbconnection.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: instructors.php');
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$query = "SELECT * FROM instructors WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$instructor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$instructor) {
    header('Location: instructors.php');
    exit();
}
?>

<section class="instructor-details-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($instructor['image_url']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($instructor['name']); ?>">
            </div>
            <div class="col-md-6">
                <h1><?php echo htmlspecialchars($instructor['name']); ?></h1>
                <p><?php echo htmlspecialchars($instructor['bio']); ?></p>
                <p><strong>Specialties:</strong> <?php echo htmlspecialchars($instructor['specialties']); ?></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
