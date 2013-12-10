<?php
include 'MySQL_backend.class.php';
include 'SSH.class.php';
include 'config.inc';

$dbLink=new MySQL($dbUsername,$dbPassword);

$pendingPis=$dbLink->getPendingPis();
chdir($path);
chdir('../backend');
foreach($pendingPis as $pendingPi){
	
	exec("php backend.php $pendingPi");
}
?>
