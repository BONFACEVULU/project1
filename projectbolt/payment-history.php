<?php
require_once 'includes/header.php';
require_once 'classes/User.php';

$user = new User();
$userId = $_SESSION['user_id']; // Assuming user ID is stored in session
$paymentHistory = $user->getPaymentHistory($userId);
?>

<div class="container">
    <h1>Payment History</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paymentHistory as $payment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment['date']); ?></td>
                    <td>$<?php echo htmlspecialchars($payment['amount']); ?></td>
                    <td><?php echo htmlspecialchars($payment['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
