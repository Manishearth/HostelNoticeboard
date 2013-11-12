<?php
class MySQL
{	

//--------------------------------------------------Standard MySQL Class--------------------------------------------------//

	private $dbHost='localhost';
	private $dbUsername='root';
	private $dbPassword='toor';
	private $dbName='NoticeBoard';
	private $dbLink;
	
	function MySQL() {$this->connect();}																//Constructor
	function __destruct() {$this->close();}																//Destructor

	/*********************************
	 * Method to connect to database *
	 *********************************/
	private function connect() {	
		$this->dbLink = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
		if (mysqli_connect_error()) {
		user_error("MySQL Error: ".mysqli_connect_errno() . '. ' . mysqli_connect_error());
		die();
		}
	}
	
	/******************************
	 * Method to close connection *
	 ******************************/
	function close()
	{
		@$this->dbLink->close();
	}
	
	/*****************************
	 * Method to run SQL queries *
	 *****************************/
	function  query($sql)
	{	
		$result = $this->dbLink->query($sql);
		if (mysqli_error($this->dbLink)){
			user_error("MySQL Error: ".mysqli_errno($this->dbLink) . '. ' . mysqli_error($this->dbLink));
			die();
		}
		return $result;
	}

//----------------------------------------------Custom methods for frontend-----------------------------------------------//
//
//DONOT UNCOMMENT THESE LINES
//THESE LINES ARE JUST FOR REFERENCE
//
//define('READ_FILESYSTEM',			0);
//define('WRITE_FILESYSTEM',		1);
//define('DELETE_OTHER_USER_FILES',	2);
//define('ADD_DELETE_USER',       	3);
//define('ADD_DELETE_PI',	       	4);

	/**********************************************
	 * Custom methods for frontend user interface *
	 **********************************************/
	function getAuth(&$perm) {
//		return $pass
	}

	/**********************************************
	 * Custom methods for administrative frontend *
	 **********************************************/
	function addUser($name, $uid, $pass, $perm) {
//insert code to check for max length
		if (preg_match('/^[a-zA-Z\40]+$/', $name)!==1) {return "Name can include only alphabets and spaces";}
		else $_name=$name;
		if (preg_match('/^[a-zA-Z0-9]{5,}$/', $uid)!==1) {return "Username has to be alphanumeric and minimum 5 characters";}
		else $_uid=$uid;
		if ((preg_match('/^[a-zA-Z0-9!@#$%^&_-]{5,}$/', $pass)!==1)||(preg_match('/[!@#$%^&_-]/', $pass)!==1))
			{return "Password can only contain alphanumeric characters\n with atleast one of {!, @, #, $, %, ^, &, _, -}. It should be atlest 5 characters long.";}
		else $_pass=md5($pass);

		$_perm=0x00000000;
		if ($perm[WRITE_FILESYSTEM] == true) 		$_perm = $_perm|0x00000001;
		if ($perm[DELETE_OTHER_USER_FILES] == true) $_perm = $_perm|0x00000010;
		if ($perm[ADD_DELETE_USER] == true) 		$_perm = $_perm|0x00000100;
		if ($perm[ADD_DELETE_PI] == true) 			$_perm = $_perm|0x00001000;

		$this->query("INSERT INTO users(Name,Uid,Pass,Permission) VALUES (".$_name.",".$_uid.",".$_pass.",".$_perm.")");
	}
	function removeUser() {
	}
	function addPi() {
	}
	function removePi() {
	}
	function addDirectory() {
	}
	function removeDirectory() {
	}

	/**************************************
	 * Custom methods for common frontend *
	 **************************************/
	function uploadFile() {
	}
	function removeFile() {
	}
	function changePass() {
	}

}





















