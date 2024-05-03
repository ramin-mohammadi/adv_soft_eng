<?php
	include("../functions_FINAL.php");

	$api_endpoint=10;
	$error_type_num=0;
	$dblink_error=db_connect("api_errors"); 

	// connect to db called devices
	$dblink=db_connect("devices"); 

	//query the auto id and name from manufacturers table
	$sql="Select `manufacturer_name`,`manufacturer_num` from `manufacturers` where `status`='active'";
	$result=$dblink->query($sql) or
		error_list_manufacturer($dblink, $dblink_error, $sql, $api_endpoint);
	$manufacturers=array();
	while ($data=$result->fetch_array(MYSQLI_ASSOC))
		$manufacturers[$data['manufacturer_num']]=$data['manufacturer_name'];
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode($manufacturers);
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>