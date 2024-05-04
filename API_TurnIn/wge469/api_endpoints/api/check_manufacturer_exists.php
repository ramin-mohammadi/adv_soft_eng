<?php
	include("../functions_FINAL.php");

	$dblink_error=db_connect("api_errors"); 
	
	$api_endpoint=5;
	$error_type_num=0;

	//add slahes to avoid sql injection
	$manufacturer_input = addslashes($manufacturer_input);

	// Error checking
	if ($manufacturer_input==NULL || empty($manufacturer_input) || strlen(trim($manufacturer_input)) == 0)//decive id is missing
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing manufacturer name input.';
		$output[]='Action: check_manufacturer_exists';
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
		$output[]='Action: check_manufacturer_exists';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	//check if device name is valid input
	$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
	if($dirty_data=preg_match($pattern, $manufacturer_input ) ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer name has invalid input";
		$output[]='Action: check_manufacturer_exists';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	// connect to db called devices
	$dblink=db_connect("devices"); 

	$sql = "SELECT `manufacturer_num` FROM `manufacturers` WHERE `manufacturer_name`='$manufacturer_input'";
	$rst=$dblink->query($sql) or
		 die("<p>Something went wrong with $sql<br>".$dblink->error);;

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