<?php
include '../config.inc';

$dbLink = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if (mysqli_connect_error()) {
	echo "MySQL Error: ".mysqli_connect_errno() . ' .' . mysqli_connect_error();
	die();
}
$sql = "INSERT ";
?>
