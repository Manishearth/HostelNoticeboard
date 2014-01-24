<?php
include 'MySQL.class.php';
include 'SSH.class.php';
include 'config.inc';
/*
echo "::::WARNING::::\t".date("d-m-Y H:i:s")."\tAsynchronous connections not implemented. Running daemon.php instead...\n";
chdir($path);
chdir("../backend");
exec("php daemon.php");
*/
if($asyncnumber<2){
        chdir($path);
        exec("php daemon.php");
}

$dbLink = new MySQL($dbUsername,$dbPassword);

//-------------Approving delete for expired files--------------//
$dbLink->loadQueue();
while($obj=$dbLink->getNextDirective()) {
	if ($obj->Type=="delete" && $obj->Approved==0)
		if (strtotime($obj->Date)<time()) $dbLink->approveFile($obj->Path,"delete");
}

//asyncnumber is the number of simultaneous async backend.php connections to keep open. If it is 1, the behavior is default.
//if($asyncnumber<2){
//	chdir($path)
//	exec("php daemon.php");
//}

$pendingPis=$dbLink->getPendingPis(true);//True argument sets all pending pi's PendLock status to 1

chdir($path);
chdir('../backend');
for($i=0;$i<sizeof($pendingPis);$i++){
	$dbLink->setPiLockStatus($pendingPis[$i],2); //Lock Pi
	exec("php backend.php ".$pendingPis[$i]." $asyncnumber &");
	if($asyncnumber<=$i){
		break;
	}
}

?>
