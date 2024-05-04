<?php
	include("../functions_FINAL.php");

	$dblink_error=db_connect("api_errors"); 
	
	$api_endpoint=15;
	$error_type_num=0;

	//add slahes to avoid sql injection
	$search_input = addslashes($search_input);
	$first_num=addslashes($first_num);
	$inactive_option=addslashes($inactive_option);

	// Error checking
	if ($search_input==NULL || empty($search_input) || strlen(trim($search_input)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing manufacturer name input to search by.';
		$output[]='Action: search_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($first_num==NULL || empty($first_num) || strlen(trim($first_num)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing first_num LIMIT attribute for query';
		$output[]='Action: search_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	//check if input is too long
	$max_len=32;
	if (strlen($search_input) > $max_len){
		$error_type_num=2;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Input is too long, must be less than '$max_len' characters";
		$output[]='Action: search_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$pattern = "/[^0-9]/";		
	if($dirty_data=preg_match($pattern, $first_num) || $first_num != 1000 ){ 
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: first_num LIMIT number has invalid input or is not 1000 (cannot adjust this value)";
		$output[]='Action: search_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	//check if device name is valid input
	$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
	if($dirty_data=preg_match($pattern, $search_input ) ){ 
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Search input is invalid";
		$output[]='Action: search_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	// connect to db called devices
	$dblink=db_connect("devices"); 
	$action="none";		
	$ids = [];

	$sql = "SELECT `manufacturer_num` FROM `manufacturers` WHERE `manufacturer_name`='$search_input'";
	$rst=$dblink->query($sql) or
		die("<p>Something went wrong with $sql<br>".$dblink->error);
	if ($rst->num_rows<=0)//device doesnt exist
	{
		$error_type_num=7;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer name trying to search by does not exist";
		$output[]='Action: list_manufacturers';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	else{		
		$manufacturer_num=0;
		while($row = $rst->fetch_assoc()) { //this loop should essentially only loop once (one row with value we want)
			$manufacturer_num = $row["manufacturer_num"];
		}

		if($inactive_option !== "True")
			$sql="Select `device_num` from `devices` where `manufacturer_num`='$manufacturer_num' AND `status`='active' LIMIT $first_num"; 
		else
			$sql="Select `device_num` from `devices` where `manufacturer_num`='$manufacturer_num' LIMIT $first_num"; 

		$rst=$dblink->query($sql) or
			die("<p>Something went wrong with $sql<br>".$dblink->error);

		 //search exists
		if ($rst->num_rows>0){
			// get ids for equipment records
			while($row = $rst->fetch_assoc()) {
				array_push($ids, $row["device_num"]);
			}
		}
	}

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode($ids);
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: '.$action;
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>