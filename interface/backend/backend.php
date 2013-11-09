<?php
/****************************************
IIT Bombay Notice Board BackEnd

Author: Kamal Galrani
Date:	Nov, 8, 2013
*************************************/

//ini_set("log_errors", 1);
//ini_set("error_log", "/home/singularity/php-error.log");

include 'config.inc';
include 'SSH.class.php';

//-------------Initialising Variables--------------//
$hostel = $argv[1];
$ssh_host = $argv[2];
$ssh_port = $argv[3];
$ssh_auth_user = $argv[4];
$ssh_auth_pass = $argv[5];

//-------------------Main Code---------------------//

$PI = new SSH_Connection($ssh_host,$ssh_port,$ssh_auth_user,$ssh_auth_pass,0);
//$PI->execute('ls',$det);
//echo $det;
$dbLink = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if (mysqli_connect_error()) {
	user_error("MySQL Error: ".mysqli_connect_errno() . ' .' . mysqli_connect_error());
	die();
}
$sql = "SELECT * FROM queue where Hostel = 6";
while(true) {
	$result = $dbLink->query($sql);
	if (mysqli_error($dbLink)){
		user_error("MySQL Error: ".mysqli_errno($dbLink) . ': ' . mysqli_error($dbLink));
		die();
	}

	while($obj=$result->fetch_Object()) {
		switch ($obj->Type) {
		case "Copy":
			$status = $PI->send("/home/singularity/".$obj->Path,"/home/physics26/".$obj->Path,0644);
			break;
		case "Delete":
			$status = $PI->execute("rm -f /home/physics26/".$obj->Path, $dump);
			break;
		case "MkDir":
			$status = $PI->execute("mkdir /home/physics26/".$obj->Path, $dump);
			if (stripos($dump, "File Exists")!==false) $status=true; 
			break;
		default: 
			$status = false;
		}
		if ($status) {
			user_error("Command issued on ".$obj->Date." to ".$obj->Type." ".$obj->Path." to/from Hostel ".$obj->Hostel." succesfully executed.\n");
			$dbLink->query("DELETE FROM queue where Date = '".$obj->Date."'");
			if (mysqli_error($dbLink)){
				user_error("MySQL Error: ".mysqli_errno($dbLink) . ': ' . mysqli_error($dbLink));
				die();
			}
		}		
	}
	break;
}



//-------------Cleaning up before exit--------------//
echo "\n";
echo "Cleaning up...\n";
$PI = NULL;
$dbLink->close();
echo "Work done. Exiting...\n";
?>
