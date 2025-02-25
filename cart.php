<?php
session_start();
require_once 'config/dbconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

$user_id = $_SESSION['user_id'];

// Fetch user's classes with instructor details
$query = "SELECT c.*, b.booking_date, b.status, i.name AS instructor_name 
          FROM bookings b
          JOIN classes c ON b.class_id = c.id
          JOIN instructors i ON c.instructor_id = i.id
          WHERE b.user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle class removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_class'])) {
    $class_id = $_POST['class_id'];
    $deleteQuery = "DELETE FROM bookings WHERE user_id = :user_id AND class_id = :class_id";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bindParam(':user_id', $user_id);
    $deleteStmt->bindParam(':class_id', $class_id);
    $deleteStmt->execute();
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ffecd2, #fcb69f);
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .container {
            max-width: 1000px;
            background: #ffffff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            text-align: center;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #007bff;
            color: #ffffff;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-danger {
            background-color: #ff6b6b;
            border: none;
        }
        .btn-danger:hover {
            background-color: #e63946;
        }
        .btn-back {
            background-color: #6c757d;
            color: #ffffff;
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
        }
        .btn-back:hover {
            background-color: #495057;
        }
        .btn-back .arrow {
            margin-left: 10px;
            animation: blink 1s infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
        .alert-info {
            background-color: #e3f2fd;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="javascript:history.back()" class="btn btn-back mb-4">
            &larr; Go Back <span class="arrow">&rarr;</span>
        </a>
        <h2>Your Dance Classes</h2>
        <?php if (empty($classes)): ?>
            <div class="alert alert-info text-center">No classes found. Start booking your favorite sessions!</div>
        <?php else: ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Instructor</th>
                        <th>Booking Date</th>
                        <th>Class Date</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class['name']); ?></td>
                            <td><?php echo htmlspecialchars($class['instructor_name']); ?></td>
                            <td><?php echo htmlspecialchars($class['booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($class['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($class['duration']); ?> minutes</td>
                            <td><?php echo htmlspecialchars($class['status']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                                    <button type="submit" name="remove_class" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
