<?php
	include("../functions_FINAL.php");

	// connect to db called devices
	$dblink=db_connect("devices"); 

//	if(is_null($inactive_option) || !isset($inactive_option))
	if($inactive_option !== "True")
		$sql="Select `device_num` from `devices` where `serial_num`='$search_input' AND `status`='active' LIMIT $first_num"; 
	else
		$sql="Select `device_num` from `devices` where `serial_num`='$search_input' LIMIT $first_num"; 

	$rst=$dblink->query($sql) or
		die("<p>Something went wrong with $sql<br>".$dblink->error);
	
	$action="none";		
	$ids = [];
	if ($rst->num_rows<=0)//serial number doesnt exist
	{
		echo "ERROR: Serial Number doesnt exist";
		$action="search.php?msg=NoMatch_Search_SerialNum";
	}
	else{ //search exists
		// get ids for equipment records
		while($row = $rst->fetch_assoc()) {
			array_push($ids, $row["device_num"]);
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