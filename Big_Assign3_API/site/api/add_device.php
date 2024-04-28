<?php
	include("../functions_FINAL.php");

	// connect to db called devices
	$dblink=db_connect("devices"); 

	$sql="Insert into `device_types` (`device_type_name`) values ('$device_input')";
	$dblink->query($sql) or
		die("Something went wrong with DEVICE_TYPES INSERT query $sql\n".$dblink->error);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode("Device successfully added");
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;

	die();
?>

