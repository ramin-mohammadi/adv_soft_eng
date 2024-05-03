<?php
	include("../functions_FINAL.php");

	$dblink_error=db_connect("api_errors"); 
	
	$api_endpoint=7;
	$error_type_num=0;

	// connect to db called devices
	$dblink=db_connect("devices"); 
	$sql="Select MAX(`device_num`) AS `max_id` from `devices`"; 
	$rst=$dblink->query($sql) or
		error_get_last_auto($dblink, $dblink_error, $sql, $api_endpoint);
	$next_device_num = 0;
	while ($row = mysqli_fetch_array($rst)) {
		$next_device_num = intval($row['max_id']) + 1;       
	}

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode($next_device_num);
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();

?>