<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
include_once 'includes/header.php';

$user = new User();
$users = $user->getAllUsers();
?>

    <div class="card">
        <div class="card-header">
            <h4>Registered Users</h4>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($users as $user) : ?>
                    <tr>
                        <td><?php echo $user->id; ?></td>
                        <td><?php echo $user->name; ?></td>
                        <td><?php echo $user->email; ?></td>
                        <td><?php echo $user->created_at; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include_once 'includes/footer.php'; ?>