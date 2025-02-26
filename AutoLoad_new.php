<?php
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
        }
    }
}
spl_autoload_register('classAutoLoad');

// Creating instances of classes
$ObjLayout = new layout();
$ObjContent = new contents();
$Objlogin = new Login();
$Objsignup = new Signup();
$Objverify = new otp_verification();
?>
