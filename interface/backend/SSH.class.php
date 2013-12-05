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
private $shell;
    
//-------------Function Definitions--------------//

public function SSH_Connection($ssh_host,$ssh_port,$ssh_auth_user,$ssh_auth_pass,$reconnect=-1) {   //Constructor
    global $ssh_host, $ssh_port, $ssh_auth_user, $ssh_auth_pass, $keepAlive, $FLAG;                 //Copying data to global variables
    $keepAlive = $reconnect;
    $FLAG = 0;
    $this->connect();
    $this->showConnection();
}

public function connect() {                                                                         //Connects to SSH session
                                                                                                    //Returns false on failure
    global $ssh,$scp;
    global $ssh_host, $ssh_port, $ssh_auth_user, $ssh_auth_pass;
    while (true) {
        $ssh = new Net_SSH2($ssh_host,$ssh_port);
        if ($ssh->bitmap&NET_SSH2_MASK_CONSTRUCTOR) {
//          user_error("Connected to the ".$ssh_host.":".$ssh_port.". Authenticating...");
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
//        echo "Authentication failed. Exiting :(\n";
        return false;
    }
    else echo "Authenticated :)\n";

    echo $ssh->exec('pwd');
    $this->send('config.inc','config.inc',644);
    return true;
}

public function showConnection() {                                                                  //Shows ssh information
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

public function execute($command , &$reply = null) {												//Executes command on ssh_host
																									//Returns false on failure
	global $ssh, $FLAG;
	if ($FLAG !== 0) return false;
	if (!isset($ssh)) return false;

    $reply = $ssh->exec($command);
    if($reply===false) return false;
	else return true;
}

public function send($src, $dst, $mode) {
	global $ssh, $FLAG;
	if ($FLAG !== 0) return false;
    
    $scp = new Net_SCP($ssh);
	if (!($scp->put($dst,$src,NET_SCP_LOCAL_FILE))) {
		$this->showConnection();															        //showConnection tries to reconnect
		return $this->send($src, $dst, $mode);
	}
	return true;
}

public function __destruct() { 																		//Destructor
	global $ssh;
	user_error('Closing SSH Connection with '.$GLOBALS['ssh_host'].':'.$GLOBALS['ssh_port']."\n");
	$this->execute('exit');
	$ssh = NULL;
}

} 
?> 
