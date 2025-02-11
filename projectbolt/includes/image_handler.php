<?php
class ImageHandler {
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    private $maxSize = 5242880; // 5MB

    public function __construct($uploadDir = '../uploads/') {
        $this->uploadDir = $uploadDir;
        $this->ensureUploadDirectory();
    }

    private function ensureUploadDirectory() {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function uploadImage($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('No file uploaded');
        }

        // Verify file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG and PNG are allowed.');
        }

        // Check file size
        if ($file['size'] > $this->maxSize) {
            throw new Exception('File too large. Maximum size is 5MB.');
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $this->uploadDir . $newFilename;

        // Move and optimize the image
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to move uploaded file.');
        }

        // Optimize the image
        $this->optimizeImage($targetPath, $mimeType);

        return str_replace('../', '', $targetPath); // Return relative path for database
    }

    private function optimizeImage($path, $mimeType) {
        if ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg') {
            $image = imagecreatefromjpeg($path);
        } else {
            $image = imagecreatefrompng($path);
        }

        // Resize if too large while maintaining aspect ratio
        $maxWidth = 1200;
        $maxHeight = 1200;
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth/$width, $maxHeight/$height);
            $newWidth = round($width * $ratio);
            $newHeight = round($height * $ratio);
            
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            if ($mimeType === 'image/png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
            }
            
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $image = $newImage;
        }

        // Save with appropriate quality/compression
        if ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg') {
            imagejpeg($image, $path, 85); // 85% quality
        } else {
            imagepng($image, $path, 8); // Compression level 8
        }

        imagedestroy($image);
    }
}
?>
