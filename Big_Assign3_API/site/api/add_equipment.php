<?php
	include("../functions_FINAL.php");


	// Error checking
	if ($device==NULL)//decive id is missing
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Invalid or missing device id.';
		$output[]='Action: query_device';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($manufacturer==NULL)//missing manufacturer id
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Invalid or missing manufacturer id.';
		$output[]='Action: query_manufacturer';
		$responseData=json_encode($output);
		echo $responseData;
		die();
	}
	if ($serialNumber==NULL)//missing serial number
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]='Status: ERROR';
		$output[]='MSG: Invalid or missing serial number.';
		$output[]='Action: none';
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