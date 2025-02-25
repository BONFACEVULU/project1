<?php
require_once 'includes/header.php';
require_once 'classes/User.php';

$user = new User();
$userId = $_SESSION['user_id']; // Assuming user ID is stored in session
$attendanceRecords = $user->getAttendanceRecords($userId);
?>

<div class="container">
    <h1>Attendance Records</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Class Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendanceRecords as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['date']); ?></td>
                    <td><?php echo htmlspecialchars($record['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
