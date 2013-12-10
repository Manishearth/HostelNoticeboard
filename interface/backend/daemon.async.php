<?php
include 'MySQL_backend.class.php';
include 'SSH.class.php';
include 'config.inc';

//asyncnumber is the number of simultaneous async backend.php connections to keep open. If it is 1, the behavior is default.
if($asyncnumber<2){
	chdir($path);
	chdir('../backend');
	exec("php daemon.php");
}

$dbLink=new MySQL($dbUsername,$dbPassword);

$pendingPis=$dbLink->getPendingPis(true);//True argument sets all pending pi's PendLock status to 1
chdir($path);
chdir('../backend');
for($i=0;$i<sizeof($pendingPis);$i++){
	if($asyncnumber<=$i){
		break;
	}
	$dbLink->setPiLockStatus($pendingPis[$i],2); //Lock Pi
	exec("php backend.php ".$pendingPis[$i]." $asyncnumber > /dev/null 2>/dev/null &");

}
?>
