<?php
include 'MySQL_backend.class.php';
include 'SSH.class.php';
include 'config.inc';

if($asyncnumber<2){
	chdir($path)
	exec("php daemon.php");
}

$dbLink=new MySQL($dbUsername,$dbPassword);

$pendingPis=$dbLink->getPendingPis(true);//True argument sets all pending pi's PendLock status to 1
chdir($path);
chdir('../backend');
for($i=0;i<sizeof($pendingPis);i++){
	$dbLink->setPiLockStatus($pendingPis[$i],2); //Lock Pi
	exec("php backend.php ".$pendingPis[$i]." $asyncnumber &");
	if($asyncnumber==$i){
		break;
	}
}
?>
