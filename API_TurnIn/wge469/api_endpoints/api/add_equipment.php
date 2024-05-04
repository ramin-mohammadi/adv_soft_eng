<?php
	include("../functions_FINAL.php");

	$dblink_error=db_connect("api_errors"); 

	$api_endpoint=2;
	$error_type_num=0;

	//add slahes to avoid sql injection
	$serialNumber = addslashes($serialNumber);
	$device = addslashes($device);
	$manufacturer = addslashes($manufacturer);
	$next_device_num = addslashes($next_device_num);

	// Error checking
	if ($next_device_num==NULL || empty($next_device_num) || strlen(trim($next_device_num)) == 0)//decive id is missing
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing auto id for the insert.';
		$output[]='Action: get_lastEquipAutoID';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($device==NULL || empty($device) || strlen(trim($device)) == 0)//decive id is missing
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing device id.';
		$output[]='Action: add_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($manufacturer==NULL || empty($manufacturer) || strlen(trim($manufacturer)) == 0)//missing manufacturer id
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing manufacturer id.';
		$output[]='Action: add_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($serialNumber==NULL || empty($serialNumber) || strlen(trim($serialNumber)) == 0)//missing serial number
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing serial number.';
		$output[]='Action: add_equipment';
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
		$output[]='Action: add_equipment';
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
		$output[]='Action: add_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$pattern = "/[^0-9]/";		
	if($dirty_data=preg_match($pattern, $device) ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Device number has invalid input";
		$output[]='Action: add_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	if($dirty_data=preg_match($pattern, $manufacturer) ){
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer number has invalid input";
		$output[]='Action: add_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	if($dirty_data=preg_match($pattern, $next_device_num) ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Auto id has invalid input";
		$output[]='Action: add_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}



	// Check if serial number already exists
	$result=api_call("check_serialNum_exists", "serialNum=".$serialNumber);
	$num_rows=get_payload($result);
	$error_type_num=4;
	if ($num_rows>0)
	{
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Serial number already exists";
		$output[]='Action: add_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	
	//check device exists
	$result=api_call("list_devices", "");
	$device_list=get_payload($result);
	$deviceExists=false;
	foreach($device_list as $key=>$value){
//		echo $key;
		if($key == $device){
			$deviceExists=true;
			break;
		}
	}
	if (!$deviceExists)
	{
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Device number doesn't exist or is inactive";
		$output[]='Action: list_devices';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	//check manufacturer exists
	$result=api_call("list_manufacturers", "");
	$manufacturer_list=get_payload($result);
	$manufacturerExists=false;
	foreach($manufacturer_list as $key=>$value){
		if($key == $manufacturer){
			$manufacturerExists=true;
			break;
		}
	}
	if (!$manufacturerExists)
	{
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer number doesn't exist or is inactive";
		$output[]='Action: list_devices';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	
	//check auto id value is the next auto id value
	$result=api_call("get_lastEquipAutoID", "");
	$auto_id=get_payload($result);
	if($next_device_num != $auto_id){
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: next_device_num value is not the next auto id index in equipment table";
		$output[]='Action: get_lastEquipAutoID';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	// connect to db called devices
	$dblink=db_connect("devices"); 

	// Perform SQL Insert into equipment table
	$sql="Insert into `devices` (`device_num`, `device_type_num`,`manufacturer_num`,`serial_num`) values ('$next_device_num', '$device','$manufacturer','$serialNumber')";
	$dblink->query($sql) or
		 die("<p>Something went wrong with $sql<br>".$dblink->error);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode("Equipment successfully added");
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;

	die();
?>