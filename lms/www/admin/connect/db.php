<?php
class connect{

public $servername = "127.0.0.1";
    private $username = "root";
    private $password = "";
    private $dbname = "lms";
    //create connection

    public function dbconnect()
	{
		$db=mysqli_connect($this->servername,$this->username,$this->password,$this->dbname);
		
		return $db;
	}
}


?>