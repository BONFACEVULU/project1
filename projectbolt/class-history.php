<?php
require_once 'includes/header.php';
require_once 'classes/User.php';

$user = new User();
$userId = $_SESSION['user_id']; // Assuming user ID is stored in session
$classHistory = $user->getClassHistory($userId);
?>

<div class="container">
    <h1>Class History</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($classHistory as $class): ?>
                <tr>
                    <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($class['date']); ?></td>
                    <td><?php echo htmlspecialchars($class['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
