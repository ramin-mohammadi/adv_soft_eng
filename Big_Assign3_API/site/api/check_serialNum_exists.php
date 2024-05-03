<?php
	include("../functions_FINAL.php");

	$dblink_error=db_connect("api_errors"); 

	$api_endpoint=6;
	$error_type_num=0;

	//add slahes to avoid sql injection
	$serialNumber = addslashes($serialNumber);

	if ($serialNumber==NULL || empty($serialNumber) || strlen(trim($serialNumber)) == 0)//missing serial number
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing serial number.';
		$output[]='Action: check_serialNum_exists';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	//check if input is too long
	$max_len=90;
	if (strlen($serialNumber) > $max_len){
		$error_type_num=2;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Serial number input is too long, must be less than '$max_len' characters";
		$output[]='Action: check_serialNum_exists';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	//check if serial number is valid input
	$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
	if($dirty_data=preg_match($pattern, $serialNumber ) ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Serial number has invalid input";
		$output[]='Action: check_serialNum_exists';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	// connect to db called devices
	$dblink=db_connect("devices"); 

	$sql="Select `device_num` from `devices` where `serial_num`='$serialNumber'"; 
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