<?php
require_once 'config/dbconnection.php';

class User extends dbconnection {
    public function uploadProfileImage($userId, $image) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($image["tmp_name"]);
        if($check === false) {
            return "File is not an image.";
        }

        // Check file size
        if ($image["size"] > 500000) {
            return "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if(!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Try to upload file
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            // Update user profile image in the database
            $sql = "UPDATE users SET image_url = :image_url WHERE id = :user_id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(':image_url', $targetFile);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return "The file ". htmlspecialchars(basename($image["name"])). " has been uploaded.";
        } else {
            return "Sorry, there was an error uploading your file.";
        }
    }

    public function getUserProfile($userId) {
        $sql = "SELECT name, email, image_url FROM users WHERE id = :user_id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($data) {
        $sql = "INSERT INTO users (name, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->connect()->prepare($sql);
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return $stmt->execute($data);
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    public function sendResetLink($email) {
        // Logic to send reset link to the user's email
    }

    public function updateProfile($data) {
        $sql = "UPDATE users SET name = :name, email = :email, phone_number = :phone_number, emergency_contact_name = :emergency_contact_name, emergency_contact_phone = :emergency_contact_phone, preferences = :preferences WHERE id = :id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute($data);
    }

    public function getProfileDetails($userId) {
        $sql = "SELECT name, email, phone_number, emergency_contact_name, emergency_contact_phone, profile_picture, preferences FROM users WHERE id = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch();
    }

    public function updateProfilePicture($userId, $imageUrl) {
        $sql = "UPDATE users SET profile_picture = :profile_picture WHERE id = :id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute(['profile_picture' => $imageUrl, 'id' => $userId]);
    }

    public function getClassHistory($userId) {
        $sql = "SELECT * FROM class_history WHERE user_id = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getAttendanceRecords($userId) {
        $sql = "SELECT * FROM attendance WHERE user_id = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getPaymentHistory($userId) {
        $sql = "SELECT * FROM payments WHERE user_id = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }

    public function updatePreferences($userId, $preferences) {
        $sql = "UPDATE users SET preferences = :preferences WHERE id = :id";
        $stmt = $this->connect()->prepare($sql);
        return $stmt->execute(['preferences' => $preferences, 'id' => $userId]);
    }
}
