<?php
include 'MySQL.class.php';
include 'SSH.class.php';
include 'config.inc';

$dbLink=new MySQL($dbUsername,$dbPassword);

//---------------Approving delete for expired files----------------//
$dbLink->loadQueue();
while($obj=$dbLink->getNextDirective()) {
	if ($obj->Type=="delete" && $obj->Approved==0)
		if (strtotime($obj->Date)<time()) 
		{
			$dbLink->approveFile($obj->Path,"delete");
			shell_exec("rm -f '".$path.$obj->Path."'");
		}
}

//---------Run backend.php recursively for each pending Pi----------//
$pendingPis=$dbLink->getPendingPis();
chdir($path);
chdir('../backend');
foreach($pendingPis as $pendingPi)
	exec("php backend.php $pendingPi");
?>
