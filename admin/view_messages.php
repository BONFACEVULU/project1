<?php
require_once '../config/dbconnection.php';
require_once '../includes/header.php';

$dbconnection = new dbconnection();
$db = $dbconnection->connect();

// Fetch all contact messages
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <a href="admin.php" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
    </a>
    <h1 class="text-center mb-4">Contact Messages</h1>
    <?php if (count($messages) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['id']); ?></td>
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['message']); ?></td>
                            <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No messages found.</div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
/ /   T h i s   f i l e   d i s p l a y s   a l l   c o n t a c t   m e s s a g e s   f o r   a d m i n   r e v i e w .  
 / /   T h i s   f i l e   h a n d l e s   t h e   d i s p l a y   o f   u s e r   m e s s a g e s   f o r   a d m i n   r e v i e w .  
 / /   T h i s   f i l e   d i s p l a y s   u s e r   m e s s a g e s   f o r   a d m i n   r e v i e w   a n d   m a n a g e m e n t .  
 / /   T h i s   f i l e   d i s p l a y s   a l l   c o n t a c t   m e s s a g e s   f o r   a d m i n   r e v i e w .  
 