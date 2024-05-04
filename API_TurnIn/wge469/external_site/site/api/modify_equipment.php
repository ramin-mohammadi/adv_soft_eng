<?php
	include("../functions_FINAL.php");


	$dblink_error=db_connect("api_errors"); 

	$api_endpoint=12;
	$error_type_num=0;
	
	//add slahes to avoid sql injection
	$serial_num = addslashes($serial_num);
	$device_type_num = addslashes($device_type_num);
	$manufacturer_num = addslashes($manufacturer_num);
	$device_num = addslashes($device_num);
	$status = addslashes($status);

	// Error checking
	if ($device_num==NULL || empty($device_num) || strlen(trim($device_num)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing equipment id for the insert.';
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($device_type_num==NULL || empty($device_type_num) || strlen(trim($device_type_num)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing device id.';
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($manufacturer_num==NULL || empty($manufacturer_num) || strlen(trim($manufacturer_num)) == 0)//missing manufacturer id
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing manufacturer id.';
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($serial_num==NULL || empty($serial_num) || strlen(trim($serial_num)) == 0)//missing serial number
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing serial number.';
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($status==NULL || empty($status) || strlen(trim($status)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing status.';
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	//check if input is too long
	$max_len=90;
	if (strlen($serial_num) > $max_len){
		$error_type_num=2;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Serial number input is too long, must be less than '$max_len' characters";
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}



	//check if serial number is valid input
	$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
	if($dirty_data=preg_match($pattern, $serial_num ) ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Serial number has invalid input";
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$pattern = "/[^0-9]/";		
	if($dirty_data=preg_match($pattern, $device_type_num) ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Device number has invalid input";
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	if($dirty_data=preg_match($pattern, $manufacturer_num) ){
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer number has invalid input";
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	if($dirty_data=preg_match($pattern, $device_num) ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Equipment id has invalid input";
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$pattern = "/[^a-z]/";		
	if($dirty_data=preg_match($pattern, $status ) || !($status == "active" || $status == "inactive" ) ){
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Status has invalid input, must either be active or inactive";
		$output[]='Action: modify_equipment';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	//check device_num / equipment id exists
	$result=api_call("list_equipments", "first_num=1000&list_deviceNum=".$device_num);
	$equipments=json_decode(get_payload($result), true);
	$equipIDExists=false;
	for ($i = 0; $i < count($equipments['device_num']); $i++){		
		if($equipments['device_num'][$i] == $device_num){
			$equipIDExists=true;
			break;
		}
	}
	if (!$equipIDExists)
	{
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Equipment id doesn't exist or is inactive";
		$output[]='Action: list_equipments';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	// Check if serial number already exists or is same as before
	$result=api_call("search_serialNum", "inactive_option=True&search_input=".$serial_num."&first_num=1000");
	$ids=get_payload($result);
	$error_type_num=4;
	if ($ids != NULL )
	{
		if( count($ids) > 0 && $ids[0] != $device_num){ // SN found is not the same as the current equipment
			log_api_error($dblink_error, $api_endpoint, $error_type_num);

			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]="MSG: Serial number already exists";
			$output[]='Action: modify_equipment';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
	}
	
	//check device exists
	$result=api_call("list_devices", "");
	$device_list=get_payload($result);
	$deviceExists=false;
	foreach($device_list as $key=>$value){
		if($key == $device_type_num){
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
		if($key == $manufacturer_num){
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
		$output[]='Action: list_manufacturers';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	// connect to db called devices
	$dblink=db_connect("devices"); 
	$sql="UPDATE `devices` SET `serial_num`='$serial_num', `device_type_num`='$device_type_num', `manufacturer_num`='$manufacturer_num', `status`='$status' WHERE `device_num`='$device_num'";
	$dblink->query($sql) or
		 die("<p>Something went wrong with $sql<br>".$dblink->error);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode("Successfully modified the equipment record");
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>


