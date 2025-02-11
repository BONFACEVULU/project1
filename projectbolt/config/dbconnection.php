<?php
class dbconnection {
    private $servername = "localhost:3307";
    private $username = "root";
    private $password = "1234";
    private $dbname = "api_moodle";

    public function connect() {
        try {
            $conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
?>
