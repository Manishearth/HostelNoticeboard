<?php
define('UPLOAD_FILE',               1);
define('DELETE_FILE',               2);
define('ADD_DELETE_USER',           4);
define('ADD_DELETE_PI',             8);
define('APPROVE_UPLOAD',            16);
//define('CREATE_DIRECTORY',          32);                                      Not implemented
//define('DELETE_DIRECTORY',          64);                                      Not implemented
//define('ADDITIONAL_PERMISSION',     128);                                     Extra permission
//define('ADDITIONAL_PERMISSION',     256);                                     Extra permission
//define('ADDITIONAL_PERMISSION',     512);                                     Extra permission
//define('ADDITIONAL_PERMISSION',     1024);                                    Extra permission
define('ALLOW_HOSTEL',              2048);                                      //Not implemented
define('ALLOW_TECHNICAL',           4096);                                      //Not implemented
define('ALLOW_CULTURAL',            8192);                                      //Not implemented
define('ALLOW_SPORTS',              16384);                                     //Not implemented
define('ALLOW_ACADEMICS',           32768);                                     //Not implemented

include 'backend/MySQL.class.php';                                              //Import MySQL Class
include 'backend/config.inc';                                                   //Import some variables
include 'backend/functions.inc'; 						//Import some functions

$dbLink=new MySQL($dbUsername,$dbPassword);                                     //Starting new MySQL connection

//--------------Basic cookie based authentication------------//                 //To be improved later

if ( isset($_COOKIE["user"]) && 
isset($_COOKIE["auth"]) && $dbLink->getAuth($_COOKIE["user"])==$_COOKIE["auth"]) {
  setcookie("user",$_COOKIE["user"],time()+900);
  setcookie("auth",$_COOKIE["auth"],time()+600);
}
else {
  setcookie("auth", "", time()-3600);
  header('Location: index.php');
  exit();
}

//-------------------Code to process requests----------------//

switch ($_POST["task"]) {
case "upload":
	if ((!isset($_POST["category"]))||($_POST["category"]=='0')) die("Select a acategory!");
	$what = "poster";
	$type = "image/";
	$file = "upload-poster";
	$flag = 0;
	while ($flag<2)
	{
		echo "Trying to	upload " . $what . "...\n<br>";
		if ($_FILES[$file]["error"] > 0)
		{
			echo "Error uploading " . $what . ": ".$_FILES[$file]["error"]."\n<br>";
			$what = "text";
			$type = "text/plain";
			$file = "upload-text";
			$flag = $flag + 1;
			continue;
		}
		else if (strpos($_FILES[$file]["type"],$type) === false)
		{
			echo "Unsupported file type!!!\n<br>";
			$what =	"text";
                        $type = "text/plain";
                        $file = "upload-text";
                        $flag = $flag + 1;
                        continue;
		}
                $start_date = strtotime($_POST[$file."-start-date"]);
		if ($_POST[$file."-expiry"] > $maxExpiry[$what]) die("Maximum duration for " . $what . " is " . $maxExpiry[$what] . " days\n<br>");
		$name = str_replace(".php","",$_FILES[$file]["name"]);

		$name = $_COOKIE["user"] . "_" . $name;
		if (move_uploaded_file($_FILES[$file]["tmp_name"], $path.$_POST["category"]."/".$name))
                {
			$postpone = ceil(($start_date - time())/(60*60*24));
			echo $postpone;
                        $dbLink->queueTask("upload", $_POST["category"]."/".$name, $_COOKIE["user"], $_POST["hostel"], 0, $postpone);
			$postpone = $postpone + $_POST[$file."-expiry"];
			echo $postpone;
                        $dbLink->queueTask("delete", $_POST["category"]."/".$name, $_COOKIE["user"], $_POST["hostel"], 0, $postpone);
			if ($dbLink->error == 0)
	                        echo "<br>File queued for uploading :) Approval awaited...\n<br>";
                }
		else echo "<br>Error uploading file :(<br>";
		$what =	"text";
                $type = "text/plain";
                $file = "upload-text";
                $flag = $flag + 1;
                continue;
        }
        echo "<br>Click <a href='interface.php'>here</a> to go back.";
	break;
case "delete":
	$dbLink->queueTask($_POST["task"], $_POST["delete-data"], $_COOKIE["user"], $_POST["hostel"], 1);
	if ($dbLink->error == 0)
    	{
            echo shell_exec("rm -f '".$path.$_POST["delete-data"]."'")."\n<br>";
            print_r(error_get_last());
            echo "<br>File queued for deleting :)\n<br>";
    	}
        echo "Click <a href='interface.php'>here</a> to go back.";
	break;
case "add-user":
	$perm = array(
		UPLOAD_FILE=>$_POST["user-permission-upload"],
		DELETE_FILE=>$_POST["user-permission-delete"],
		ADD_DELETE_USER=>$_POST["user-permission-users"],
		APPROVE_UPLOAD=>$_POST["user-permission-approve"],
		ADD_DELETE_PI=>$_POST["user-permission-pis"],
		ALLOW_HOSTEL=>$_POST["user-permission-hostel"],
		ALLOW_TECHNICAL=>$_POST["user-permission-technical"],
		ALLOW_CULTURAL=>$_POST["user-permission-cultural"],
		ALLOW_SPORTS=>$_POST["user-permission-sports"],
		ALLOW_ACADEMICS=>$_POST["user-permission-academics"]
	);
	$dbLink->addUser($_POST["user-name"], $_POST["user-username"], $_POST["user-password"], $perm);
	if ($dbLink->error == 0)
		echo "<br>Added user with Uid = ".$_POST["user-username"]."\n<br>";
	echo "Click <a href='interface.php'>here</a> to go back.";
	break;
case "del-user":
	$dbLink->removeUser($_POST["deluser-id"]);
	if ($dbLink->error == 0)
		echo "<br>Removed user with Uid = ".$_POST["deluser-id"]."\n<br>";
	echo "Click <a href='interface.php'>here</a> to go back.";
	break;
case "add-pi":
	$dbLink->addPi($_POST["pi-ip"], $_POST["pi-hostel"], $_POST["pi-uid"], $_POST["pi-pass"], $_POST["pi-port"]);
	if ($dbLink->error == 0)
		echo "Click <a href='interface.php'>here</a> to go back.";
	break;
case "del-pi":
	$dbLink->removePi($_POST["delpi-id"]);
	if ($dbLink->error == 0)
		echo "<br>Removed Pi with ID = ".$_POST["delpi-id"]."\n<br>";
	echo "Click <a href='interface.php'>here</a> to go back.";
	break;
case "approve":
	$dbLink->approveFile($_POST["path"],"upload");
	if ($dbLink->error == 0)
		echo "true";
	else
		echo "false";
	break;
default:
	echo "Invalid command!";
}
?>
