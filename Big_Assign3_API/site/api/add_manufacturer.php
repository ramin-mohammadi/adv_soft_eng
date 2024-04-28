<?php
	include("../functions_FINAL.php");

	// connect to db called devices
	$dblink=db_connect("devices"); 

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

