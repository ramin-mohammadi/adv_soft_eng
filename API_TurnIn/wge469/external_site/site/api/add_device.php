<?php
	include("../functions_FINAL.php");

	$dblink_error=db_connect("api_errors"); 
	
	$api_endpoint=1;
	$error_type_num=0;

	//add slahes to avoid sql injection
	$device_input = addslashes($device_input);

	// Error checking
	if ($device_input==NULL || empty($device_input) || strlen(trim($device_input)) == 0)//decive id is missing
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing device name input.';
		$output[]='Action: add_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	//check if input is too long
	$max_len=32;
	if (strlen($device_input) > $max_len){
		$error_type_num=2;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Device input is too long, must be less than '$max_len' characters";
		$output[]='Action: add_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	//check if device name is valid input
	$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
	if($dirty_data=preg_match($pattern, $device_input ) ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Device name has invalid input";
		$output[]='Action: add_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// connect to db called devices
	$dblink=db_connect("devices"); 

	// attempt to get device type number from the device types table
	$result=api_call("check_device_exists", "device_input=".$device_input);
	$num_rows=get_payload($result);

	if ($num_rows > 0) {
		$error_type_num=4;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Device name already exists";
		$output[]='Action: add_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}	

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

