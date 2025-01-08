<?php
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $email=htmlspecialchars($_POST['email'],ENT_QUOTES,'UTF-8');
  $pw=htmlspecialchars($_POST['password'],ENT_QUOTES,'UTF-8');
  
  require "./dbconnection.php";
  require "./loginMain.php";
  require "./login1.php";
  //require "logForm.php";

  $su=new Logctrl($email,$pw);
  $su->login();
  echo "Code is Here";
  header("location: verifyPage.php");
}