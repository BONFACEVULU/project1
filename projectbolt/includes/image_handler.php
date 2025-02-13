<?php
class ImageHandler {
    public function resizeImage($filePath, $width, $height) {
        list($originalWidth, $originalHeight) = getimagesize($filePath);
        $image = imagecreatefromjpeg($filePath);
        $resizedImage = imagecreatetruecolor($width, $height);
        
        // Resize the image
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
        
        // Save the resized image
        imagejpeg($resizedImage, $filePath);
        
        // Free up memory
        imagedestroy($image);
        imagedestroy($resizedImage);
    }

    private $uploadDir = '../uploads/';

    public function uploadImage($file) {
        $targetFile = $this->uploadDir . basename($file['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            throw new Exception("File is not an image.");
        }

        // Check if file already exists
        if (file_exists($targetFile)) {
            throw new Exception("Sorry, file already exists.");
        }

        // Check file size
        if ($file['size'] > 500000) {
            throw new Exception("Sorry, your file is too large.");
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Check if $uploadOk is set to 0 by an error
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            throw new Exception("Sorry, there was an error uploading your file.");
        }

        return $targetFile;
    }
}
?>
