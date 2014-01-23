<?php
include 'MySQL.class.php';
include 'SSH.class.php';
include 'config.inc';

$dbLink=new MySQL($dbUsername,$dbPassword);

//-------------Initialising Variables---------------//
if (sizeof($argv) != 2) {
	echo ":::::ERROR:::::\t".date("d-m-Y H:i:s")."\tInvalid arguments\n";
	echo "\n";
	echo "Usage:\tphp backend.php <PiID>\n";
	die("\n");
}

$PiID = $argv[1];
if (!is_numeric($PiID)) {
	echo ":::::ERROR:::::\t".date("d-m-Y H:i:s")."\tPiID should be a number. Exiting backend for $PiID\n";
	die("\n");
}

//$runningasync=false;
//if($asyncnumber>1){
//	$runningasync=true;
//}

$piData        = $dbLink->getPiData($PiID);
$ssh_host      = $piData["IP"];
$ssh_port      = $piData["Port"];
$ssh_auth_user = $piData["Uid"];
$ssh_auth_pass = $piData["Pass"];

//-------------------Main Code---------------------//
$PI = new SSH_Connection($ssh_host,$ssh_port,$ssh_auth_user,$ssh_auth_pass);
$dbLink->loadQueue($PiID);

while($obj=$dbLink->getNextDirective()) {
	if (!$obj->Approved) continue;
	switch ($obj->Type) {
	case "upload":
		$status = $PI->send($path.$obj->Path,$remotepath.$obj->Path,0644);
		break;
	case "delete":
		$status = $PI->execute("rm -f ".$remotepath.$obj->Path, $dump);
		break;
	case "mkdir":
		$status = $PI->execute("mkdir ".$remotepath.$obj->Path, $dump);
		if (stripos($dump, "File Exists")!==false) $status=true;
		break;
	default: 
		$status = false;
	}

	if ($status) {
		user_error("Command issued on ".$obj->Date." to ".$obj->Type." ".$obj->Path." to/from PiID ".$obj->PiID." succesfully executed.\n");
		$dbLink->directiveSuccess($obj);
	}
}


//If in async mode, spawn backend.php for next available Pi after clearing locks.
//if($runningasync){
//	$dbLink->setPiLockStatus($pendingPis[$i],0); //Release Pi
//	$pis=$dblink->getPendingUnlockedPis();
//	if(sizeof($pis)>0){
//		chdir($path);
//		chdir('../backend');
//		$dbLink->setPiLockStatus($pis[0],2); //Lock Pi
//		exec("php backend.php ".$pis[0]." ".$argv[2]." &");
//	}
//}


//-------------Cleaning up before exit--------------//
echo "\n";
echo "Cleaning up...\n";
$PI = NULL;
$dbLink->close();
echo "Work done. Exiting...\n";
?>
