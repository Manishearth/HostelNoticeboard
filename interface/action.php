<?php
define('READ_FILESYSTEM',		0);
define('WRITE_FILESYSTEM',		1);
define('DELETE_OTHER_USER_FILES',	2);
define('ADD_DELETE_USER',       	4);
define('ADD_DELETE_PI',	       		8);

include 'backend/MySQL_frontend.class.php';
include 'backend/config.inc';

if ($_FILES["Copy"]["error"] > 0)
  {
  echo "Error: " . $_FILES["Copy"]["error"] . "<br>";
  }
else
  {
  echo "Upload: " . $_FILES["Copy"]["name"] . "<br>";
  echo "Type: " . $_FILES["Copy"]["type"] . "<br>";
  echo "Size: " . ($_FILES["Copy"]["size"] / 1024) . " kB<br>";
  echo "Stored in: " . $_FILES["Copy"]["tmp_name"] . "</br>";
  }
//echo "Type: " . $_POST["category"];

$path = "/var/www/root/";
echo $path.$_POST["category"]."/".$_FILES["Copy"]["name"];
echo shell_exec("touch root/text.txt");
print_r(error_get_last());
if (move_uploaded_file($_FILES["Copy"]["tmp_name"], $path.$_FILES["Copy"]["name"])) echo "done";
print_r(error_get_last());
?>
