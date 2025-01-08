<?php
$servername = "localhost:3307" ;
$username = "root";
$password = "1234";
$dbname="api_moodle";

try {
    $conn = new PDO("mysql:host=$servername;dbname=api_moodle", $username, $password);
    // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "INSERT INTO patients (firstname, lastname, email)
  VALUES ('bony', 'vulu', 'bw@example.com')";
  // use exec() because no results are returned
  $conn->exec($sql);
  echo "New record created successfully";
} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>