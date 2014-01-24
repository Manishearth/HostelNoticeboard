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
		$this->pdb=new PDO("mysql:host=$this->dbHost;dbname=$this->dbName", $dbUsername, $dbPassword);
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
	function query($sql)
	{	
		$result = $this->dbLink->query($sql);
		if (mysqli_error($this->dbLink)){
			user_error("MySQL Error: ".mysqli_errno($this->dbLink) . '. ' . mysqli_error($this->dbLink));
			return false;
		}
		return $result;
	}
	function pquery($sql,$arr)
	{
		$stmt = $this->pdb->prepare($sql);
		$stmt->execute($arr);
		return $stmt;
	}

//----------------------------------------------Custom methods for frontend-----------------------------------------------//
//
//DONOT UNCOMMENT THESE LINES
//THESE LINES ARE JUST FOR REFERENCE
//
//define('UPLOAD_FILE',               0b00000001);
//define('DELETE_FILE',               0b00000010);
//define('ADD_DELETE_USER',           0b00000100);
//define('ADD_DELETE_PI',             0b00001000);
//define('APPROVE_UPLOAD',            0b00010000);
//define('CREATE_DIRECTORY',          0b00100000);                              Not implemented
//define('DELETE_DIRECTORY',          0b01000000);                              Not implemented
//define('ADDITIONAL_PERMISSION',     0b10000000);                              Extra permission

	/**********************************************
	 * Custom methods for frontend user interface *
	 **********************************************/
	function getAuth($Uid) {
		$result=false;
		if ($result=$this->pquery("SELECT * from users where Uid=:uid",array('uid'=>$Uid))) {;}
		else return false;
		if ($obj=$result->fetch())
			return md5($obj['Pass']);
		else
			return false;		
	}
	function hasPerm($Uid,$_Perm) {
		$result=false;
		if ($result=$this->pquery("SELECT * from users where Uid=:uid",array('uid'=>$Uid))) {;}
		$Perm=$result->fetch()['Permission'];
		if ($Perm & $_Perm) return true;
		else return false;
	}
	function getHostels() {
		$hostels;
		$result=$this->pquery("SELECT DISTINCT(Hostel) FROM PI",array());
		while($obj=$result->fetch()) {
			$hostels[]=$obj['Hostel'];
		}
		return $hostels;
	}
	function getPis() {
		$ips;
		$result=$this->pquery("SELECT PiID,IP,Hostel FROM PI",array());
		while($obj=$result->fetch()) {
			$obj2=new stdClass();
			$obj2->PiID=$obj['PiID'];
			$obj2->IP=$obj['IP'];
			$obj2->Hostel=$obj['Hostel'];
			$ips[]=$obj2;
		}
		return $ips;
	}
	function getUsers() {
		$users;
		$result=$this->pquery("SELECT ID,Uid FROM users",array());
		while($obj=$result->fetch()) {
			$obj2=new stdClass();
			$obj2->ID=$obj['ID'];
			$obj2->Uid=$obj['Uid'];
			$users[]=$obj2;
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
		if ($perm[UPLOAD_FILE] == true) 		$_perm = $_perm|0x00000001;
		if ($perm[DELETE_FILE] == true) 		$_perm = $_perm|0x00000010;
		if ($perm[ADD_DELETE_USER] == true) 		$_perm = $_perm|0x00000100;
		if ($perm[ADD_DELETE_PI] == true) 		$_perm = $_perm|0x00001000;
		if ($perm[APPROVE_UPLOAD] == true) 		$_perm = $_perm|0x00010000;

		return $this->pquery("INSERT INTO users(Name,Uid,Pass,Permission) VALUES (:name,:uid,:pass,:permission)",array('name'=>$name,'uid'=>$uid,'pass'=>$_pass,'permission'=>$_perm));
	}
	function removeUser($uid) {
		return $this->pquery("DELETE FROM users WHERE Uid = :uid",array('uid'=>$uid));
	
	}
	function approveFile($path,$task) {
		return $this->pquery("UPDATE queue SET Approved=1 WHERE Path=:path AND Type=:task",array('path'=>$path,'task'=>$task));
	}
	function addPi($IP, $Hostel, $Uid, $Pass, $Port) {
//		$_Pass=md5($Pass);
		$_Pass=$Pass;
		return $this->pquery("INSERT INTO PI(IP, Hostel, Uid, Pass, Port) VALUES (:ip, :hostel, :uid, :pass, :port)",array('ip'=>$IP,'hostel'=>$Hostel,'uid'=>$Uid,'pass'=>$_Pass,'port'=>$Port));
	}
	function removePi($IP) {
		$this->pquery("DELETE FROM PI WHERE PiID = :ip",array('ip'=> $IP));
		$this->pquery("DELETE FROM queue WHERE PiID = :ip",array('ip'=> $IP));
	}
	function addDirectory() {
	}
	function removeDirectory() {
	}
	function getQueue(){
		$queue=array();
		$res=$this->pquery("Select Q.Date,Q.Path,Q.Type,P.IP,P.Hostel,Q.Approved from queue Q left join PI P on Q.PiID=P.PiID",array());
		while($obj=$res->fetch(PDO::FETCH_OBJ)) {
			$queue[]=$obj;
		}
		return $queue;		
	}
	function getFileList($path) {
		$_files = [];
		$folders = scandir($path);
		$_files[0] = array_slice($folders,2);
		foreach ($_files[0] as &$folder) {
			if ($folder == "." || $folder == "..") continue;
			$_files[$folder] = scandir($path.$folder);
		}
		return $_files;
	}

	/**************************************
	 * Custom methods for common frontend *
	 **************************************/
	function queueTask($task,$path,$user,$hostel,$approve,$expiry="") {
		if ($task=='delete' && $approve=='0') {
			if (!is_numeric($expiry)) $expiry=5;
			$stmt="INSERT INTO queue(Type,Path,Date,PiID,User,Approved) VALUES (:type,:path,NOW() + interval $expiry day,:piid ,:user, :approved)";
		}
		else
			$stmt="INSERT INTO queue(Type,Path,Date,PiID,User,Approved) VALUES (:type,:path,NOW(),:piid ,:user, :approved)";
		if ($hostel==0) {
			$result=$this->pquery("SELECT PiID FROM PI",array());
			while($obj=$result->fetch()) 
				$this->pquery($stmt,array('type'=>$task,'path'=>$path,'piid'=>$obj['PiID'],'user'=>$user, 'approved'=>$approve));
		}
		else {
			$result=$this->pquery("SELECT PiID FROM PI WHERE Hostel = :hostel",array('hostel'=>$hostel));
			while($obj=$result->fetch())
				$this->pquery($stmt,array('type'=>$task,'path'=>$path,'piid'=>$obj['PiID'],'user'=>$user,'approved'=>$approve));
		}
	}
	function changePass() {
	}
	/*********************************************
	 * Custom methods for administrative backend *
	 *********************************************/
	private $_result_a;
	function loadPiTable() {
		$this->_result_a=$this->pquery("SELECT * from PI",array());
	}
	function getNextPi() {
		return $this->_result_a->fetch(PDO::FETCH_OBJ);
	}	

	/****************************************
	 * Custom methods for execution backend *
	 ****************************************/
	private $_result_e;
	function loadQueue($_PiID=-1) {
		if ($_PiID==-1)
			$this->_result_e=$this->pquery("SELECT * from queue",array());
		else
			$this->_result_e=$this->pquery("SELECT * from queue where PiID=:piid",array('piid'=>$_PiID));
	}
	function getNextDirective() {
		return $this->_result_e->fetch(PDO::FETCH_OBJ);
	}
	function directiveSuccess($_object) {
		$this->pquery("DELETE FROM queue where ID = :ID",array('ID'=>$_object->ID));
		/*if (mysqli_error($this->dbLink)){
			user_error("MySQL Error: ".mysqli_errno($this->dbLink) . ': ' . mysqli_error($this->dbLink));
			return false;
		}*/
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
				$this->pquery("UPDATE PI SET PendLock=0",array()); //Clear all locks
				$this->pquery("UPDATE PI SET PendLock=1 WHERE PiID=:pp",array('pp'=>$pendPi));
			}
		}
		return $pendPis;
	}
	
	//Gets relevant SSH data for a given Pi
	function getPiData($_PiID){
		$res=$this->pquery("SELECT IP, Uid, Pass, Port from PI where PiID=:piid",array('piid'=>$_PiID));
		return $res->fetch(PDO::FETCH_ASSOC);
	}
	
	//Sets the lock status for a Pi. 1=pending, 2=locked, 0=free
	function setPiLockStatus($_PiID,$lockstat){
		$this->pquery("UPDATE TABLE PI SET PendLock= :ls WHERE PiID=:piid",array('ls'=>$lockstat,'piid'=>$_PiID));
	}
	
	//Gets lock status
	function getPiLockStatus($_PiID){
		$res=$this->pquery("Select PendLock from PI where PiID=:piid",array('piid'=>$_PiID));
		return $res->fetch(PDO::FETCH_ASSOC)["PendLock"];
	}
	
	//Gets a list of Pis that are pending in the current async daemon run, and not being used ("locked") by any backend.php calls
	function getPendingUnlockedPis(){
		$res=$this->query("SELECT PiID from PI where PendLock=1");
		return $res->fetch_assoc()["PiID"];
	}
}
?>
