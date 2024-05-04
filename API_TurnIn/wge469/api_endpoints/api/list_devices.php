<?php
	include("../functions_FINAL.php");

	$api_endpoint=8;
	$error_type_num=0;
	$dblink_error=db_connect("api_errors"); 

	// connect to db called devices
	$dblink=db_connect("devices"); 

	// query the auto id and name for device types table
	$sql="Select `device_type_name`,`device_type_num` from `device_types` where `status`='active'";
	$result=$dblink->query($sql) or
		error_list_device($dblink, $dblink_error, $sql, $api_endpoint);
	$devices=array();
	while ($data=$result->fetch_array(MYSQLI_ASSOC))
		$devices[$data['device_type_num']]=$data['device_type_name'];
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode($devices);
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>