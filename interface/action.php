<?php
define('READ_FILESYSTEM',		0);
define('WRITE_FILESYSTEM',		1);
define('DELETE_OTHER_USER_FILES',	2);
define('ADD_DELETE_USER',       	4);
define('ADD_DELETE_PI',	       		8);

include 'backend/MySQL.class.php';
include 'backend/config.inc';

$path = "/var/www/root/";

$dbLink=new MySQL($dbUsername,$dbPassword);

if ( isset($_COOKIE["user"]) && isset($_COOKIE["auth"]) && $dbLink->getAuth($_COOKIE["user"])==$_COOKIE["auth"] ) {
  setcookie("user",$_COOKIE["user"],time()+900);
  setcookie("auth",$_COOKIE["auth"],time()+600);
}
else {
  setcookie("auth", "", time()-3600);
  header('Location: index.php');
  exit();
}

switch ($_POST["task"]) {
case "Copy":
	if ($_FILES["Copy"]["error"] > 0) {echo "Error: ".$_FILES["Copy"]["error"]."<br>"; exit();}
	if (move_uploaded_file($_FILES["Copy"]["tmp_name"], $path.$_POST["category"]."/".$_FILES["Copy"]["name"])) {
		$dbLink->queueTask($_POST["task"], $_POST["category"]."/".$_FILES["Copy"]["name"], $_COOKIE["user"], $_POST["hostel"],0);
		echo "Uploaded!";
	}
	print_r(error_get_last());
	break;
case "Delete":
	$dbLink->queueTask($_POST["task"], $_POST["Delete"], $_COOKIE["user"], $_POST["hostel"],1);
	print_r(error_get_last());
	shell_exec("rm -f '".$path.$_POST["Delete"]."'");
	print_r(error_get_last());
	break;
case "AddUser":
	$perm = array(WRITE_FILESYSTEM=>$_POST["user-permission-upload"],
		DELETE_OTHER_USER_FILES=>$_POST["user-permission-delete"],
		ADD_DELETE_USER=>$_POST["user-permission-users"],
		ADD_DELETE_PI=>$_POST["user-permission-pi"]);
	$dbLink->addUser("",$_POST["user-username"],$_POST["user-password1"], $perm);
	echo "Added user with Uid = ".$_POST["user-username"];
	break;
case "DelUser":
	echo "Removing user with Uid = ".$_POST["deluser-id"];
	$dbLink->removeUser($_POST["deluser-id"]);
	print_r(error_get_last());
	break;
case "AddPI":
//	$dbLink->addPi($IP, $Hostel, $Uid, $Pass, $Port)
	echo "Command not implemented";
	break;
case "DelPI":
	echo "Removing Pi with ID = ".$_POST["delpi-id"];
	$dbLink->removePi($_POST["delpi-id"]);
	print_r(error_get_last());
	break;
case "MkDir":
	echo "Command not implemented";
	break;
case "Approve":
	if($dbLink->approveFile($_POST["path"])) echo "true";
	else "false";
	break;
default:
	echo "Invalid command!";
}
?>
