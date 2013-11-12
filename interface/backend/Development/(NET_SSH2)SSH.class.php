<?php 
include('Net/SSH2.php');
include('Net/SCP.php');

//define('NET_SSH2_MASK_CONSTRUCTOR', 0x00000001);
//define('NET_SSH2_MASK_LOGIN_REQ',   0x00000002);
//define('NET_SSH2_MASK_LOGIN',       0x00000004);
//define('NET_SSH2_MASK_SHELL',       0x00000008);

class SSH_Connection
{ 

//-------------Initialising Variables--------------//

// SSH Host 
private $ssh_host; 
// SSH Port 
private $ssh_port; 
// SSH Username 
private $ssh_auth_user;
private $ssh_auth_pass; 
// SSH Connection 
private $ssh; 
private $FLAG;
private $keepAlive;
private $scp;
    
//-------------Function Definitions--------------//

public function SSH_Connection($ssh_host,$ssh_port,$ssh_auth_user,$ssh_auth_pass,$reconnect=-1) {//Constructor
	global $ssh_host, $ssh_port, $ssh_auth_user, $ssh_auth_pass, $keepAlive, $FLAG;					//Copying data to global variables
	$keepAlive = $reconnect;
	$FLAG = 0;
	$this->connect();
	$this->showConnection();
}

public function connect() {														//Connects to SSH session
																									//Returns false on failure
	global $ssh,$scp;
	global $ssh_host, $ssh_port, $ssh_auth_user, $ssh_auth_pass;
	while (true) {
		$ssh = new Net_SSH2($ssh_host,$ssh_port);
		if ($ssh->bitmap==NET_SSH2_MASK_CONSTRUCTOR) {
			user_error("Connected to the ".$ssh_host.":".$ssh_port.". Authenticating...");
			break;
		}

		if ($GLOBALS['keepAlive'] == -1) {
			$GLOBALS['FLAG'] = 2;
			return false;
		}
		sleep($GLOBALS['keepAlive']);
	}

	if (!($ssh->login($ssh_auth_user,$ssh_auth_pass))) {
		$GLOBALS['FLAG'] = 1;
		echo "Authentication failed. Exiting :(\n";
		return false;
	}
	else echo "Authenticated :)\n";

	echo $ssh->exec('pwd');
//	$scp = new Net_SCP($ssh);
	return true;
}

public function showConnection() {																	//Shows ssh information
																									//Returns false if session disconnected
	echo "\n";
	echo "SSH Host: ".$GLOBALS['ssh_host'].":".$GLOBALS['ssh_port']."\n";
	echo "SSH User: ".$GLOBALS['ssh_auth_user']."\n";
	echo 'Session Details: ';
	echo $ssh->exec('pwd');
	echo $ssh->bitmap;

/*	if ($details) {
		echo $details."\n";
		return true;
	}
	else {
		echo "Session Disconnected!\n";
		return false;
	}
*/
}
/*
public function execute($command , $reconnect = true) {												//Executes command on ssh_host
																									//Returns false on failure
	global $ssh, $FLAG;
	if ($FLAG !== 0) return false;
	$stream = ssh2_exec($ssh, $command);
	stream_set_blocking($stream, true);
	$contents = stream_get_contents($stream);
	if (empty($contents)) {
		echo "Test";
		if ($reconnect) {
			$this->connect();
			return $this->execute($command);
		}
		else return false;
	}
	return $contents;
}

public function sendFile() {
	echo $this->execute("lastlog | grep '".$GLOBALS['ssh_auth_user']."'");
}

public function getErrorNo() {																		//Returns error number
	return $GLOBALS['FLAG'];
}

public function getErrorMsg() {																		//Returns friendly error message
	
	error_get_last();
	switch ($GLOBALS['FLAG']) {
	case 1:
		return "Authentication failure. Please check your username and password.";
	case 2:
		return "Cannot connect to server. To automatically try to reconnect set auto reconnect flag to the desired interval in seconds.\n";
	default:
		return "No or unknown error. See log for more details.";
	}
}

public function __destruct() { 																		//Destructor
	global $ssh;
//	echo 'Closing SSH Connection with '.$GLOBALS['ssh_host'].':'.$GLOBALS['ssh_port']."\n";
	$this->execute('exit',false);
	$ssh = NULL; 
}
*/
} 
?> 
