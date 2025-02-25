<?php
session_start();
<<<<<<< HEAD
session_destroy();
header("Location: index.php");
=======

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

header("Location: index.php");

>>>>>>> origin/dance_final_system
exit();
?>
