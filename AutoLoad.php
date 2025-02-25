<?php
<<<<<<< HEAD
// Method Auto Load
function classAutoLoad($classname){
    $directories = ["class", "contents", "forms", "processes", "global", "menus"];
    foreach($directories AS $dir){
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $classname . ".php";
        if(file_exists($filename) AND is_readable($filename)){
            require_once($filename);
=======
// Include Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Method Auto Load
function classAutoLoad($classname){
    $directories = ["classes", "contents", "forms", "processes", "global", "menus"];
    foreach($directories as $dir){
        $filename = __DIR__ . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $classname . ".php";
        if(file_exists($filename) && is_readable($filename)){
            require_once($filename);
            return;
>>>>>>> origin/dance_final_system
        }
    }
}
spl_autoload_register('classAutoLoad');
<<<<<<< HEAD
// Creating an instance of a class
$ObjLayout = new layout();
$ObjContent = new contents();
$Objlogin = new Login();
$Objsignup = new Signup();
$Objverify = new otp_verification();
=======
?>
>>>>>>> origin/dance_final_system
