<?php
require_once '../includes/image_handler.php';

function handleUpload($file, $type) {
    try {
        $imageHandler = new ImageHandler();
        $imagePath = $imageHandler->uploadImage($file);
        
        return [
            'success' => true,
            'path' => $imagePath
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Handle AJAX upload request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $type = $_POST['type'] ?? 'general';
    $result = handleUpload($_FILES['image'], $type);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
