<?php
//backend.php <PiID> <ssh_host> <ssh_port> <ssh_username> <ssh_password>
include 'MySQL_backend.class.php';
include 'SSH.class.php';
include 'config.inc';

$dbLink=new MySQL($dbUsername,$dbPassword);

$pendingPis=$dbLink->getPendingPis();

for($i=0;i<sizeof($pendingPis);i++){
	chdir($path)
	exec("php backend.php ".$pendingPis[$i]);
}
?>
