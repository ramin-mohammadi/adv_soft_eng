<?php
	include("../functions_FINAL.php");

	$dblink_error=db_connect("api_errors"); 
	
	$api_endpoint=13;
	$error_type_num=0;

	//add slahes to avoid sql injection
	$manufacturer_input = addslashes($manufacturer_input);
	$status=addslashes($status);
	$manufacturer_num=addslashes($manufacturer_num);

	// Error checking
	if ($manufacturer_input==NULL || empty($manufacturer_input) || strlen(trim($manufacturer_input)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing manufacturer name input.';
		$output[]='Action: modify_manufacturer';
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
		$output[]='Action: modify_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	if ($manufacturer_num==NULL || empty($manufacturer_num) || strlen(trim($manufacturer_num)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing manufacturer id.';
		$output[]='Action: modify_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	//check if input is too long
	$max_len=32;
	if (strlen($manufacturer_input) > $max_len){
		$error_type_num=2;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer input is too long, must be less than '$max_len' characters";
		$output[]='Action: modify_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
	if($dirty_data=preg_match($pattern, $manufacturer_input ) ){
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer name has invalid input";
		$output[]='Action: modify_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$pattern = "/[^0-9]/";		
	if($dirty_data=preg_match($pattern, $manufacturer_num ) ){
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer id has invalid input";
		$output[]='Action: modify_manufacturer';
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
		$output[]='Action: modify_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// connect to db called devices
	$dblink=db_connect("devices"); 

	// check if manufacturer name exists already or the new name is the same as before the update
	$sql = "SELECT `manufacturer_num` FROM `manufacturers` WHERE `manufacturer_name`='$manufacturer_input'";
	$rst=$dblink->query($sql) or
		 die("<p>Something went wrong with $sql<br>".$dblink->error);
	

	if ($rst->num_rows > 0) {
		$temp_num=-1;
		while ($data=$rst->fetch_array(MYSQLI_ASSOC))
			$temp_num=$data['manufacturer_num'];
		if($temp_num != $manufacturer_num){ // if same name as before modify, allow update
			$error_type_num=4;
			log_api_error($dblink_error, $api_endpoint, $error_type_num);

			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]='Status: ERROR';
			$output[]="MSG: Manufacturer name already exists";
			$output[]='Action: modify_manufacturer';
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
	}	

	//see if device type num exists and is an active device
	$result=api_call("list_manufacturers", "");
	$manufacturers=get_payload($result);

	$manufacturerNumExists=false;

	foreach($manufacturers as $key=>$value){
		if($manufacturer_num == $key){
			$manufacturerNumExists=true;
			break;
		}
	}

	
	if (!$manufacturerNumExists) {
		$error_type_num=7;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer id does not exist or is set to inactive";
		$output[]='Action: modify_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}	

	$sql="UPDATE `manufacturers` SET `manufacturer_name`='$manufacturer_input', `status`='$status' WHERE `manufacturer_num`='$manufacturer_num'";
	$dblink->query($sql) or
		 die("<p>Something went wrong with $sql<br>".$dblink->error);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode("Successfully modified specified manufacturer #".$manufacturer_num." to $manufacturer_input and status: ".$status);
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>