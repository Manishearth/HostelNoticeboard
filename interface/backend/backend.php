<?php
/****************************************
IIT Bombay Notice Board BackEnd

Author: Kamal Galrani
Date:	Nov, 8, 2013

Usage 
backend.php <PiID> <ssh_host> <ssh_port> <ssh_username> <ssh_password>
*************************************/

include 'MySQL_backend.class.php';
include 'SSH.class.php';
include 'config.inc';

//ini_set("log_errors", 1);
//ini_set("error_log", backend.php);

//-------------Initialising Variables--------------//
$PiID = $argv[1];
if(!is_numeric($PiID)) user_error("PiID should be a number\n");
$ssh_host = $argv[2];
$ssh_port = $argv[3];
$ssh_auth_user = $argv[4];
$ssh_auth_pass = $argv[5];
//-------------------Main Code---------------------//

$PI = new SSH_Connection($ssh_host,$ssh_port,$ssh_auth_user,$ssh_auth_pass,0);

$dbLink=new MySQL($dbUsername,$dbPassword);
$dbLink->loadQueue($PiID);
while($obj=$dbLink->getNextDirective()) {
	switch ($obj->Type) {
	case "Copy":
		$status = $PI->send($path.$obj->Path,$remotepath.$obj->Path,0644);
		echo "Copy\n";
		break;
	case "Delete":
		$status = $PI->execute("rm -f ".$remotepath.$obj->Path, $dump);
		echo "Delete\n";
		break;
	case "MkDir":
		$status = $PI->execute("mkdir ".$remotepath.$obj->Path, $dump);
		if (stripos($dump, "File Exists")!==false) $status=true; 
		echo "MkDir\n";
		break;
	default: 
		$status = false;
	}

	if ($status) {
		user_error("Command issued on ".$obj->Date." to ".$obj->Type." ".$obj->Path." to/from PiID ".$obj->PiID." succesfully executed.\n");
		$dbLink->directiveSuccess($obj);
	}
}



//-------------Cleaning up before exit--------------//
echo "\n";
echo "Cleaning up...\n";
$PI = NULL;
//$dbLink->close();
echo "Work done. Exiting...\n";
?>
