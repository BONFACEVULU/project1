<?php
require_once 'config/config.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
include_once 'includes/header.php';

class Signup {
    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password'])
            ];

            // Instantiate User
            $user = new User();

            // Call the register method
            if ($user->register($data)) {
                // Redirect to the home page (index.php) after successful registration
                header("Location: index.php");
                exit(); 
            } else {
                echo '<div class="alert alert-danger">Something went wrong</div>';
            }
        }
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4>Register User</h4>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
<?php
}
}
