<?php
set_time_limit(0);

include 'MySQL.class.php';
include 'SSH.class.php';
include 'config.inc';
include 'functions.inc';

$dbLink=new MySQL($dbUsername,$dbPassword);

//-------------------Main Code---------------------//
	$piData        = $dbLink->getPiData($_GET['PiID']);
	$ssh_host      = $piData["IP"];
	$ssh_port      = $piData["Port"];
	$ssh_auth_user = $piData["Uid"];
	$ssh_auth_pass = $piData["Pass"];

	$PI = new SSH_Connection($ssh_host,$ssh_port,$ssh_auth_user,$ssh_auth_pass);
	$dbLink->loadQueue($_GET['PiID']);

	while($obj=$dbLink->getNextDirective()) {
		if ($obj->Type=="delete" && $obj->Approved==0)
	                if (strtotime($obj->Date)<time())
        	        {
                	        $dbLink->approveFile($obj->Path,"delete");
                        	shell_exec("rm -f '".$path.$obj->Path."'");
				$obj->Approved = 1;
                	}
		if (!$obj->Approved) continue;
		switch ($obj->Type) {
		case "upload":
			if (strtotime($obj->Date)<time())
                        {
                                $status = $PI->send($path.$obj->Path,$remotepath.$obj->Path, 0644);
                        }
                        else    $status = false;
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
			echo "Command issued on ".$obj->Date." to ".$obj->Type." ".$obj->Path." to/from PiID ".$obj->PiID." succesfully executed.\n<br>";
			$dbLink->directiveSuccess($obj);
		}
	}

//-------------Cleaning up before exit--------------//
echo "\n<br>";
echo "Cleaning up...\n<br>";
$PI = NULL;
$dbLink->close();
echo "Work done. Exiting...<br>";
?>
