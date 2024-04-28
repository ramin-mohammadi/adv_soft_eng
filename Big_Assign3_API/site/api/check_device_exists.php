<?php
	include("../functions_FINAL.php");

	// connect to db called devices
	$dblink=db_connect("devices"); 

	$sql = "SELECT `device_type_num` FROM `device_types` WHERE `device_type_name`='$device_input'";
	$rst=$dblink->query($sql) or
		 die("<p>Something went wrong with $sql<br>".$dblink->error);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode($rst->num_rows);
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>