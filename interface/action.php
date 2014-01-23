<?php
define('UPLOAD_FILE',               0b00000001);
define('DELETE_FILE',               0b00000010);
define('ADD_DELETE_USER',           0b00000100);
define('ADD_DELETE_PI',             0b00001000);
define('APPROVE_UPLOAD',            0b00010000);
//define('CREATE_DIRECTORY',          0b00100000);                              Not implemented
//define('DELETE_DIRECTORY',          0b01000000);                              Not implemented
//define('ADDITIONAL_PERMISSION',     0b10000000);                              Extra permission

include 'backend/MySQL.class.php';                                              //Import MySQL Class
include 'backend/config.inc';                                                   //Import default values for $remotepath, $path, 
                                                                                //$dbUsername, $dbPassword, $asyncnumber
$path = "/var/www/root/";

$dbLink=new MySQL($dbUsername,$dbPassword);                                     //Starting new MySQL connection

//Confirm authentication via cookies.
if ( isset($_COOKIE["user"]) && isset($_COOKIE["auth"]) && $dbLink->getAuth($_COOKIE["user"])==$_COOKIE["auth"]) {
  setcookie("user",$_COOKIE["user"],time()+900);
  setcookie("auth",$_COOKIE["auth"],time()+600);
}
else {
  setcookie("auth", "", time()-3600);
  header('Location: index.php');
  exit();
}


switch ($_POST["task"]) {
case "upload":
	if ($_FILES["upload-data"]["error"] > 0) {echo "Error: ".$_FILES["upload-data"]["error"]."<br>"; exit();}
	if (move_uploaded_file($_FILES["upload-data"]["tmp_name"], $path.$_POST["category"]."/".$_FILES["upload-data"]["name"])) {
		$dbLink->queueTask("upload", $_POST["category"]."/".$_FILES["upload-data"]["name"], $_COOKIE["user"], $_POST["hostel"],0);
		$dbLink->queueTask("delete", $_POST["category"]."/".$_FILES["upload-data"]["name"], $_COOKIE["user"], $_POST["hostel"],0,$_POST["upload-expiry"]);
		echo "File queued for uploading :) Approval awaited...\n";
		echo "Click <a href='interface.php'>here</a> to go back.";
	}
	print_r(error_get_last());
	break;
case "delete":
	$dbLink->queueTask($_POST["task"], $_POST["delete-data"], $_COOKIE["user"], $_POST["hostel"],1);
	if (error_get_last())
        print_r(error_get_last());
    else
    {
        shell_exec("rm -f '".$path.$_POST["delete-data"]."'");
        print_r(error_get_last());
        echo "File queued for deleting :)\n";
		echo "Click <a href='interface.php'>here</a> to go back.";
    }
	break;
case "add-user":
	$perm = array(UPLOAD_FILE=>$_POST["user-permission-upload"],
		DELETE_FILE=>$_POST["user-permission-delete"],
		ADD_DELETE_USER=>$_POST["user-permission-users"],
		APPROVE_UPLOAD=>$_POST["user-permission-approve"],
		ADD_DELETE_PI=>$_POST["user-permission-pis"]);
	$dbLink->addUser($_POST["user-name"],$_POST["user-username"],$_POST["user-password"], $perm);
	echo "Added user with Uid = ".$_POST["user-username"]."\n";
	echo "Click <a href='interface.php'>here</a> to go back.";
	break;
case "del-user":
	$dbLink->removeUser($_POST["deluser-id"]);
	print_r(error_get_last());
	echo "Removed user with Uid = ".$_POST["deluser-id"]."\n";
	echo "Click <a href='interface.php'>here</a> to go back.";
	break;
case "add-pi":
//	$dbLink->addPi($IP, $Hostel, $Uid, $Pass, $Port)
	echo "Command not implemented";
	break;
case "del-pi":
	$dbLink->removePi($_POST["delpi-id"]);
	print_r(error_get_last());
	echo "Removed Pi with ID = ".$_POST["delpi-id"]."\n";
	echo "Click <a href='interface.php'>here</a> to go back.";
	break;
case "mkdir":
	echo "Command not implemented";
	break;
case "Approve":
	if($dbLink->approveFile($_POST["path"],"upload")) echo "true";
	else "false";
	break;
default:
	echo "Invalid command!";
}
?>
