<?php
	include("../functions_FINAL.php");

	$dblink_error=db_connect("api_errors"); 
	
	$api_endpoint=11;
	$error_type_num=0;

	//add slahes to avoid sql injection
	$device_input = addslashes($device_input);
	$status = addslashes($status);
	$device_type_num = addslashes($device_type_num);


	// Error checking
	if ($device_input==NULL || empty($device_input) || strlen(trim($device_input)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing device name input.';
		$output[]='Action: modify_device';
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
		$output[]='Action: modify_device';
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
		$output[]='MSG: Missing device type id.';
		$output[]='Action: modify_device';
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
		$output[]='Action: modify_device';
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
		$output[]='Action: modify_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$pattern = "/[^0-9]/";		
	if($dirty_data=preg_match($pattern, $device_type_num ) ){
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Device type id has invalid input";
		$output[]='Action: modify_device';
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
		$output[]='Action: modify_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// connect to db called devices
	$dblink=db_connect("devices"); 
	
	// check if device name exists already or the new name is the same as the device was before the update
	$sql = "SELECT `device_type_num` FROM `device_types` WHERE `device_type_name`='$device_input'";
	$rst=$dblink->query($sql) or
		 die("<p>Something went wrong with $sql<br>".$dblink->error);
	

	if ($rst->num_rows > 0) {
		$temp_num=-1;
		while ($data=$rst->fetch_array(MYSQLI_ASSOC))
			$temp_num=$data['device_type_num'];
		if($temp_num != $device_type_num){ // if same name as before modify, allow update
			$error_type_num=4;
			log_api_error($dblink_error, $api_endpoint, $error_type_num);

			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]="MSG: Device name already exists";
			$output[]='Action: modify_device';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
	}	

	//see if device type num exists and is an active device
	$result=api_call("list_devices", "");
	$devices=get_payload($result);

	$deviceTypeNumExists=false;

	foreach($devices as $key=>$value){
		if($device_type_num == $key){
			$deviceTypeNumExists=true;
			break;
		}
	}

	
	if (!$deviceTypeNumExists) {
		$error_type_num=7;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Device type id does not exist or is set to inactive";
		$output[]='Action: modify_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}	


	$sql="UPDATE `device_types` SET `device_type_name`='$device_input', `status`='$status' WHERE `device_type_num`='$device_type_num'";
	$dblink->query($sql) or
		die("<p>Something went wrong with $sql<br>".$dblink->error);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode("Successfully modified specified device #".$device_type_num." to $device_input and status: ".$status);
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>