<?php
	include("../functions_FINAL.php");

	$api_endpoint=9;
	$error_type_num=0;
	$dblink_error=db_connect("api_errors"); 

	$list=addslashes($list); // $list variable treated as a string with comma separated values
	$first_num=addslashes($first_num);

	// Remove any characters to the right of the last digit
	$list = preg_replace('/\D*\z/', '', $list);

	if ($first_num==NULL || empty($first_num) || strlen(trim($first_num)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing first_num LIMIT attribute for query';
		$output[]='Action: list_equipments';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($list==NULL || empty($list) || strlen(trim($first_num)) == 0)
	{
		$error_type_num=1;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Missing list of equipment ids as comma separated values.';
		$output[]='Action: list_equipments';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$pattern = "/[^0-9]/";		
	if($dirty_data=preg_match($pattern, $first_num) || $first_num != 1000 ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: first_num LIMIT number has invalid input or is not 1000 (cannot adjust this value)";
		$output[]='Action: list_equipments';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}

	$pattern = "/^\d+(,\d+)*$/";  //a list of digits separated by a single comma or one digit
	if(! $dirty_data=preg_match($pattern, $list) ){ // matches elements in array that meet regex pattern
		$error_type_num=3;
		log_api_error($dblink_error, $api_endpoint, $error_type_num);

		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]="MSG: values in equipment list are invalid";
		$output[]='Action: list_equipments';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}


	// connect to db called devices
	$dblink=db_connect("devices"); 

	$sql = "SELECT `device_num`, `device_type_num`, `manufacturer_num`, `serial_num`, `status` FROM `devices` WHERE `device_num` IN($list) LIMIT $first_num";
	$rst=$dblink->query($sql) or
		error_list_equipments($dblink, $dblink_error, $sql, $api_endpoint);
	
	// create json object and store data in there
	$device_num = array();
	$device_type_name = array();
	$manufacturer_name = array();
	$serial_num = array();
	$status = array();
	while($row = $rst->fetch_assoc()) {
		$device_num[] = $row['device_num'];
		$device_type_num = $row['device_type_num'];
		$manufacturer_num = $row['manufacturer_num'];
		$serial_num[] = $row['serial_num'];
		$status[] = $row['status'];

		// Replace Foreign keys with dedicated values (device name and manufacturer name)
		$sql = "SELECT `device_type_name` FROM `device_types` WHERE `device_type_num`='$device_type_num'";
		$result=$dblink->query($sql) or
			die("<p>Something went wrong with $sql<br>".$dblink->error);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) { 
				$device_type_name[] = $row["device_type_name"];
			}
		}	

		$sql = "SELECT `manufacturer_name` FROM `manufacturers` WHERE `manufacturer_num`='$manufacturer_num'";
		$result=$dblink->query($sql) or
			die("<p>Something went wrong with $sql<br>".$dblink->error);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) { 
				$manufacturer_name[] = $row["manufacturer_name"];
			}
		}	
	}

	$jsonEquipments=json_encode(array("device_num"=>$device_num, "device_type_name"=>$device_type_name,"manufacturer_name"=>$manufacturer_name,"serial_num"=>$serial_num,"status"=>$status));


	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$jsonDevices=json_encode($jsonEquipments);
	$output[]='MSG: '.$jsonDevices;
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>