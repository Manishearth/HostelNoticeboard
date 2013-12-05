<?php 
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
private $shell;
    
//-------------Function Definitions--------------//

public function SSH_Connection($ssh_host,$ssh_port,$ssh_auth_user,$ssh_auth_pass,$reconnect=-1) {	//Constructor
	global $ssh_host, $ssh_port, $ssh_auth_user, $ssh_auth_pass, $keepAlive, $FLAG;					//Copying data to global variables
	$keepAlive = $reconnect;
	$FLAG = 0;
	$this->connect();
	$this->showConnection();
}

public function connect() {																			//Connects to SSH session
																									//Returns false on failure
	global $ssh, $shell;
	global $ssh_host, $ssh_port, $ssh_auth_user, $ssh_auth_pass;
	while (!($ssh = ssh2_connect($ssh_host,$ssh_port))) {
//		echo "Cannot connect to server.\n";
		if ($GLOBALS['keepAlive'] == -1) {
			$GLOBALS['FLAG'] = 2;
			return false;
		}
		sleep($GLOBALS['keepAlive']);
	}
//	echo "Connected to the server. Authenticating...\n";

	if (!(ssh2_auth_password($ssh,$ssh_auth_user,$ssh_auth_pass))) {
		$GLOBALS['FLAG'] = 1;
		echo "Authentication failed. Exiting :(\n";
		return false;
	}
	user_error("Successfully connected to ".$ssh_host."!");
	return true;
}

public function showConnection() {																	//Shows ssh information
																									//Returns false if session disconnected
	echo "\n";
	echo "SSH Host: ".$GLOBALS['ssh_host'].":".$GLOBALS['ssh_port']."\n";
	echo "SSH User: ".$GLOBALS['ssh_auth_user']."\n";
	echo "Reconnect if dropped every ".$GLOBALS['keepAlive']." seconds.\n";
	
	if ($GLOBALS['FLAG']!==0) {
		echo "Session Disconnected!\n";
		return false;
	}

	if (!($this->execute("lastlog | grep '".$GLOBALS['ssh_auth_user']."'", $details))) {
		if ($GLOBALS['keepAlive']!==-1) {
			if ($this->connect()) {return $this->showConnection();}
		}
		echo "Session Disconnected!\n";
		return false;
	}
	echo 'Session Details: '.$details."\n";
	return true;
}

public function execute($command, &$reply = null) {													//Executes command on ssh_host
																									//Returns false on failure
	global $ssh, $FLAG;
	if ($FLAG !== 0) return false;
	if (!isset($ssh)) return false;

	$stream = ssh2_exec($ssh, $command.'; Argh=$?; echo "@Arr!="; echo $Argh');
	$stderr = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	if (!($stream)) return false;
	stream_set_blocking($stream, true);
	stream_set_blocking($stderr, true);
	while($buffer = fgets($stream)) {
		flush();
		if (strncmp($buffer,"@Arr!=",6)==0) break;
		$reply .= $buffer;
	}
	$stderr = stream_get_contents($stderr);
	if (!empty($stderr)) {
		$reply = $reply."STDERR:\n".$stderr;
		user_error($stderr);
	}
	$buffer = fgets($stream);
	$ERRORLEVEL = strncmp($buffer,'0',1);
	if ($ERRORLEVEL!=0) return false;
	return true;
}

public function send($src, $dst, $mode) {
	global $ssh, $FLAG;
	if ($FLAG !== 0) return false;

	if (!(ssh2_scp_send($ssh, $src, $dst, $mode))) {
		if ($this->showConnection()) {																//showConnection tries to reconnect
			ssh2_scp_send($ssh, $src, $dst, $mode);
		}
		else return false;
		return false;
	}
	else return true;
}

public function __destruct() { 																		//Destructor
	global $ssh;
	user_error('Closing SSH Connection with '.$GLOBALS['ssh_host'].':'.$GLOBALS['ssh_port']."\n");
	$this->execute('exit');
	$ssh = NULL; 
}

} 
?> 
