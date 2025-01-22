<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Register a new user
    public function register($data) {
        $this->db->query('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        // Hash the password before storing it
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));

        return $this->db->execute();
    }

    // Get user details by email
    public function getUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single(); // Returns a single row for the user
    }

    // Send a verification code to the user's email
    public function sendVerificationCode($email, $code) {
        // Use PHP's mail function or a library like PHPMailer to send the email
        mail($email, "Your Verification Code", "Your verification code is: " . $code, "From: no-reply@yourdomain.com");
    }
}
?>
