<?php
	include("../functions_FINAL.php");

	// connect to db called devices
	$dblink=db_connect("devices"); 
	$sql="UPDATE `devices` SET `serial_num`='$serial_num', `device_type_num`='$device_type_num', `manufacturer_num`='$manufacturer_num', `status`='$status' WHERE `device_num`='$device_num'";
	$dblink->query($sql) or
		 die("<p>Something went wrong with $sql<br>".$dblink->error);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode("");
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>


