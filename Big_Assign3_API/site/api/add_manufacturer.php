<?php
	include("../functions_FINAL.php");

	$dblink_error=db_connect("api_errors"); 
	
	$api_endpoint=3;
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
		$output[]='Action: add_manufacturer';
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
		$output[]='Action: add_manufacturer';
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
		$output[]='Action: add_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$dblink=db_connect("devices"); 

	$result=api_call("check_manufacturer_exists", "manufacturer_input=".$manufacturer_input);
	$num_rows=get_payload($result);

	if ($num_rows > 0) {
		$error_type_num=4;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: Manufacturer name already exists";
		$output[]='Action: add_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}	

	$sql="Insert into `manufacturers` (`manufacturer_name`) values ('$manufacturer_input')";
	$dblink->query($sql) or
		die("Something went wrong with MANUFACTURERS INSERT query $sql\n".$dblink->error);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode("Manufacturer successfully added");
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;

	die();
?>

