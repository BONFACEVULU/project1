<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
  $userCode=htmlspecialchars($_POST['otp'],ENT_QUOTES,'UTF-8');
  require "./dbconnection.php";
  require "./loginMain.php";
  require "./login1.php";
  $apiTwoFactor=new LoginMain();
  if ($apiTwoFactor->verifyCode($userCode)) {
     echo "Verification successful! Access granted."; 
     $apiTwoFactor->clearCode();
     session_start();
     $_SESSION['Connect']='Login verified';
     header("location: index.php");
     } else { 
      echo "Verification failed. Please try again.";
   } 
  }
