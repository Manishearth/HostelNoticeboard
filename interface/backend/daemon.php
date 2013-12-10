<?php
include 'MySQL_backend.class.php';
include 'SSH.class.php';
include 'config.inc';

$dbLink=new MySQL($dbUsername,$dbPassword);

$pendingPis=$dbLink->getPendingPis();
chdir($path);
chdir('../backend');
for($i=0;i<sizeof($pendingPis);i++){
	
	exec("php backend.php ".$pendingPis[$i]);
}
?>
