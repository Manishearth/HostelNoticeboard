<?php
class MySQL
{	
	private $dbUsername;
	private $dbPassword;
	private $dbHost='localhost';
	private $dbName='NoticeBoard';
	private $dbLink;
	
	function MySQL($dbUsername, $dbPassword) {$this->connect($dbUsername, $dbPassword);}																//Constructor
	function __destruct() {$this->close();}																//Destructor

	/*********************************
	 * Method to connect to database *
	 *********************************/
	private function connect($dbUsername, $dbPassword) {	
		$this->dbLink = new mysqli($this->dbHost, $dbUsername, $dbPassword, $this->dbName);
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
			return false;
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
	function getAuth($Uid) {
		$result=false;
		if ($result=$this->query("SELECT * from users where Uid='".$Uid."'")) {;}
		else return false;
		if ($obj=$result->fetch_Object())	return $obj->Pass;
		else	return false;
	}
	function hasPerm($Uid,$_Perm) {
		$result=false;
		if ($result=$this->query("SELECT * from users where Uid='".$Uid."'")) {;}
		$Perm=$result->fetch_Object()->Permission;
		if ($Perm & $_Perm) return true;
		else return false;
	}
	function getHostels() {
		$hostels;
		$result=$this->query("SELECT DISTINCT(Hostel) FROM PI");
		while($obj=$result->fetch_Object()) {
			$hostels[]=$obj->Hostel;
		}
		return $hostels;
	}
	function getPis() {
		$ips;
		$result=$this->query("SELECT PiID,IP,Hostel FROM PI");
		while($obj=$result->fetch_Object()) {
			$ips[]=$obj;
		}
		return $ips;
	}
	function getUsers() {
		$users;
		$result=$this->query("SELECT ID,Uid FROM users");
		while($obj=$result->fetch_Object()) {
			$users[]=$obj;
		}
		return $users;
	}

	/**********************************************
	 * Custom methods for administrative frontend *
	 **********************************************/
	function addUser($name, $uid, $pass, $perm) {
//		$_pass=md5($pass);
		$_pass=$pass;
		$_perm=0x00000000;
		if ($perm[WRITE_FILESYSTEM] == true) 		$_perm = $_perm|0x00000001;
		if ($perm[DELETE_OTHER_USER_FILES] == true) $_perm = $_perm|0x00000010;
		if ($perm[ADD_DELETE_USER] == true) 		$_perm = $_perm|0x00000100;
		if ($perm[ADD_DELETE_PI] == true) 			$_perm = $_perm|0x00001000;

		return $this->query("INSERT INTO users(Name,Uid,Pass,Permission) VALUES ('".$name."','".$uid."','".$_pass."',".$_perm.")");
	}
	function removeUser($uid) {
		$this->query("DELETE FROM users WHERE Uid = '".$uid."'");
		print_r(error_get_last());		
	}
	function addPi($IP, $Hostel, $Uid, $Pass, $Port) {
//		$_Pass=md5($Pass);
		$_Pass=$Pass;
		return $this->query("INSERT INTO PI(IP, Hostel, Uid, Pass, Port) VALUES ('".$IP."',".$Hostel.",'".$Uid."','".$_Pass."',".$Port.")");
	}
	function removePi($IP) {
		$this->query("DELETE FROM PI WHERE PiID = ".$IP);
		$this->query("DELETE FROM queue WHERE PiID = ".$IP);
	}
	function addDirectory() {
	}
	function removeDirectory() {
	}
	function getQueue(){
		$queue=array();
		//return $queue;
		$res=$this->query("Select Q.Date,Q.Path,Q.Type,P.IP,P.Hostel from queue Q left join PI P on Q.PiID=P.PiID");
		while($obj=$res->fetch_Object()) {
			$queue[]=$obj;
			//echo var_dump($obj);
		}
		return $queue;		
	}
	function getFileList($path) {
		$_files = [];
		$folders = scandir($path);
		$_files[0] = $folders;
		foreach ($folders as &$folder) {
			if ($folder == "." || $folder == "..") continue;
			$_files[$folder] = scandir($path.$folder);
		}
		return $_files;
	}

	/**************************************
	 * Custom methods for common frontend *
	 **************************************/
	function queueTask($task,$path,$user,$hostel) {
		if ($hostel==0) {
			$result=$this->query("SELECT PiID FROM PI");
			while($obj=$result->fetch_Object()) $this->query("INSERT INTO queue(Type,Path,Date,PiID,User) VALUES ('".$task."','".$path."',NOW(),".$obj->PiID.",'".$user."')");
		}
		else {
			$result=$this->query("SELECT PiID FROM PI WHERE Hostel = '".$hostel."'");
			while($obj=$result->fetch_Object()) $this->query("INSERT INTO queue(Type,Path,Date,PiID,User) VALUES ('".$task."','".$path."',NOW(),".$obj->PiID.",'".$user."')");
		}
	}
	function changePass() {
	}
	/*********************************************
	 * Custom methods for administrative backend *
	 *********************************************/
	private $_result_a;
	function loadPiTable() {
		$this->_result_a=$this->query("SELECT * from PI");
	}
	function getNextPi() {
		return $this->_result_a->fetch_Object();
	}	

	/****************************************
	 * Custom methods for execution backend *
	 ****************************************/
	private $_result_e;
	function loadQueue($_PiID) {
		$this->_result_e=$this->query("SELECT * from queue where PiID=".$_PiID);
	}
	function getNextDirective() {
		return $this->_result_e->fetch_Object();
	}
	function directiveSuccess($_object) {
		$this->query("DELETE FROM queue where Date = '".$_object->Date."'");
		if (mysqli_error($this->dbLink)){
			user_error("MySQL Error: ".mysqli_errno($this->dbLink) . ': ' . mysqli_error($this->dbLink));
			return false;
		}
		return true;
	}
	
	//Gets list of Pis with pending requests. If the optional argument is true, it also updates the PI table and sets their PendLock statuses to 1
	function getPendingPis($setPendLock=false){
		$res=$this->query("SELECT PiID from queue group by PiID");
		$pendPis=array();
		//$res->fetch_all(MYSQLI_ASSOC);
		while($row = $res->fetch_array(MYSQLI_ASSOC)){
			$pendPis[]=$row["PiID"];
		}
		if($setPendLock){
			foreach($pendPis as $pendPi){
				$this->query("UPDATE TABLE PI SET PendLock=0"); //Clear all locks
				$this->query("UPDATE TABLE PI SET PendLock=1 WHERE PiID=$pendPi");
			}
		}
		return $pendPis;
	}
	
	//Gets relevant SSH data for a given Pi
	function getPiData($_PiID){
		$res=$this->query("SELECT IP, Uid, Pass, Port from PI where PiID=".$_PiID);
		return $res->fetch_assoc();
	}
	
	//Sets the lock status for a Pi. 1=pending, 2=locked, 0=free
	function setPiLockStatus($_PiID,$lockstat){
		$this->query("UPDATE TABLE PI SET PendLock=".$lockstat." WHERE PiID=".$_PiID);
	}
	
	//Gets lock status
	function getPiLockStatus($_PiID){
		$res=$this->query("Select PendLock from PI where PiID=".$_PiID);
		return $res->fetch_assoc()["PendLock"];
	}
	
	//Gets a list of Pis that are pending in the current async daemon run, and not being used ("locked") by any backend.php calls
	function getPendingUnlockedPis(){
		$res=$this->query("SELECT PiID from PI where PendLock=1");
		return $res->fetch_assoc()["PiID"];
	}
}
?>
