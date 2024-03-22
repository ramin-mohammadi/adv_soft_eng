<?php
function db_connect($db)
{
	$username="webuser";
	$password="i8je!YFyidaWGb_X";
	$host="localhost"; // in real setting, this would be an ip address
	$dblink=new mysqli($host, $username, $password, $db);
	return $dblink;
}


// this function will only be called if pass all error checks so here assume fields are not empty, etc
function insert_device($dblink_devices, $row_data, $dblink_errors, $count){
	// attempt to get device type number from the device types table
	$sql = "SELECT `device_type_num` FROM `device_types` WHERE `device_type_name`='$row_data[0]'";
	$result=$dblink_devices->query($sql);
	$device_type_num = 0;
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) { //this loop should essentially only loop once (one row with value we want)
			$device_type_num = $row["device_type_num"];
		}
	}	
	// device_type doesnt exist, add new device_type to its dedicated table and get the new device type number
	else
		$device_type_num = new_device_type($dblink_devices, $row_data[0]);
	
	
	// attempt to get manufacturer number from the manufacturer table
	$sql = "SELECT `manufacturer_num` FROM `manufacturers` WHERE `manufacturer_name`='$row_data[1]'";
	$result=$dblink_devices->query($sql);
	$manufacturer_num = 0;
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) { //this loop should essentially only loop once (one row with value we want)
			$manufacturer_num = $row["manufacturer_num"];
		}
	}	
	// device_type doesnt exist, add new device_type to its dedicated table and get the new device type number
	else
		$manufacturer_num = new_manufacturer($dblink_devices, $row_data[1]);
	
	
	// INSERT device into devices table
	$sql="Insert into `devices` (`device_type_num`,`manufacturer_num`,`serial_num`) values
	('$device_type_num','$manufacturer_num','$row_data[2]')";
	
	try{ 
		$dblink_devices->query($sql); 
	}
	catch (Exception $e) { //must catch to avoid duplciate error stopping execution
		if($dblink_devices->errno == 1062) //error code for duplicate entry
			log_error($dblink_errors, $count, 2); // duplicate error has id of 2 (predefined in error_types table in errors db)	
		else
			die("Something went wrong with DEVICES INSERT query $sql\n".$dblink_devices->error);
	}	
}

function log_error($dblink_errors, $line_num, $error_type_num){
	//$error_msg = $dblink_devices->error;
	$sql="Insert into `errors` (`line_num`,`error_type_num`) values
	('$line_num','$error_type_num')";
	$dblink_errors->query($sql) or
		die("Something went wrong with ERRORS INSERT query $sql\n".$dblink_errors->error);
}

function new_device_type($dblink_devices, $device_type){
	$sql="Insert into `device_types` (`device_type_name`) values ('$device_type')";
	$dblink_devices->query($sql) or
		die("Something went wrong with DEVICE_TYPES INSERT query $sql\n".$dblink_devices->error);
	return $dblink_devices->insert_id; // return auto increment attribute value from the insert query
}

function new_manufacturer($dblink_devices, $manufacturer_name){
	$sql="Insert into `manufacturers` (`manufacturer_name`) values ('$manufacturer_name')";
	$dblink_devices->query($sql) or
		die("Something went wrong with MANUFACTURERS INSERT query $sql\n".$dblink_devices->error);
	return $dblink_devices->insert_id; // return auto increment attribute value from the insert query
}

?>