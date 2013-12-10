<?php
//backend.php <PiID> <ssh_host> <ssh_port> <ssh_username> <ssh_password>
include 'MySQL_backend.class.php';
include 'SSH.class.php';
include 'config.inc';

if($asyncnumber<2){
	chdir($path)
	exec("php daemon.php");
}

$dbLink=new MySQL($dbUsername,$dbPassword);

$pendingPis=$dbLink->getPendingPis(true);
chdir($path)
for($i=0;i<sizeof($pendingPis);i++){
	
	exec("php backend.php ".$pendingPis[$i]." $asyncnumber &");
	if($asyncnumber==$i){
		break;
	}
}
?>
