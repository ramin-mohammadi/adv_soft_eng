<?php
	include("../functions_FINAL.php");

	// connect to db called devices
	$dblink=db_connect("devices"); 
	$action="none";		
	$ids = [];

	$sql = "SELECT `device_type_num` FROM `device_types` WHERE `device_type_name`='$search_input'";
	$rst=$dblink->query($sql) or
		die("<p>Something went wrong with $sql<br>".$dblink->error);
	if ($rst->num_rows<=0)//device doesnt exist
	{
		echo "ERROR: Device doesnt exist";
		$action="search.php?msg=NoMatch_Search_Device";
	}
	else{
		$device_type_num=0;
		while($row = $rst->fetch_assoc()) { //this loop should essentially only loop once (one row with value we want)
			$device_type_num = $row["device_type_num"];
		}
		if($inactive_option !== "True")
			$sql="Select `device_num` from `devices` where `device_type_num`='$device_type_num' AND `status`='active' LIMIT $first_num"; 
		else
			$sql="Select `device_num` from `devices` where `device_type_num`='$device_type_num' LIMIT $first_num"; 

		$rst=$dblink->query($sql) or
			die("<p>Something went wrong with $sql<br>".$dblink->error);

		if ($rst->num_rows<=0)//device doesnt exist
		{
			echo "ERROR: Device doesnt exist";
			$action="search.php?msg=NoMatch_Search_Device";
		}
		else{ //search exists
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