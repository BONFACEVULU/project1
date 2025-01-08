<?php
class Logctrl extends LoginMain{
    private $email;
    private $pw;
    
    public function __construct($email,$pw)
    {
        $this->email=$email;
        $this->pw=$pw;
    }
    public function login(){
        
        // if($this->emptysu()==false){
        //     header("location: ../login.php?error=emptyfields");
        //     exit();
        // }
        $this->getuser($this->email,$this->pw); 
        $this->generateCode();
        $this->sendMail($this->email);
    }
    // private function emptysu( ){
    //     $result=false;
    //     if(empty($this->email) || empty($this->pw)){
    //         $result=false;
    //     }
    //     else{
    //         $result= true;
    //     }
    //     return $result;
        
    // }
}
