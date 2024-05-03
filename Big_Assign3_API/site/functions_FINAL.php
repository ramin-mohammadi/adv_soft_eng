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

function redirect($uri)
{ ?>
	<script type="text/javascript">
		document.location.href="<?php echo $uri?>";
	</script>
<?php die;
}

function api_call($endpoint, $data){
	$ch=curl_init("https://ec2-3-142-218-191.us-east-2.compute.amazonaws.com:63221/api/".$endpoint);
//	$data="test";
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//ignore ssl
	curl_setopt($ch, CURLOPT_POST,1);//tell curl we are using post
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//this is the data
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//prepare a response
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: '.strlen($data))
				);
	$result=curl_exec($ch); // $result is the data RECEIVED from executing that api endpoint
	curl_close($ch);
	//$jsonResult=json_decode($result,true);
	//echo "<pre>";
//	echo $result;
	//echo "</pre>";
	return $result;
}

function get_payload($result){
	$resultsArray=json_decode($result, true);
	$tmp=$resultsArray[1]; // get MSG / payload
	$payloadData=explode("MSG:",$tmp);
	return json_decode($payloadData[1], true); // get right side of colon in "MSG: <payload>" 
}

function get_action($result){
	$resultsArray=json_decode($result, true);
	$tmp=$resultsArray[2]; // get Action
	$payloadData=explode("Action:",$tmp);
	return json_decode($payloadData[1], true); // get right side of colon in "Action: <text>" 	
}

function log_api_error($dblink_error, $api_endpoint, $error_type_num){
	$sql="Insert into `errors` (`api_endpoint_num`, `error_type_num`) values ('$api_endpoint', '$error_type_num')";
	$dblink_error->query($sql) or
		die("Something went wrong with DEVICE_TYPES INSERT query $sql\n".$dblink_error->error);
}

function error_get_last_auto($dblink, $dblink_error, $sql, $api_endpoint){
	$error_type_num=5;
	log_api_error($dblink_error, $api_endpoint, $error_type_num);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: ERROR';
	$output[]="MSG: There are no records in the equipments table, cannot retrieve the index";
	$output[]='Action: add_equipment';
	$responseData=json_encode($output);
	echo $responseData;
	die("<p>Something went wrong with $sql<br>".$dblink->error);
}

function error_list_device($dblink, $dblink_error, $sql, $api_endpoint){
	$error_type_num=6;
	log_api_error($dblink_error, $api_endpoint, $error_type_num);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: ERROR';
	$output[]="MSG: There are no records in the device types table";
	$output[]='Action: add_device';
	$responseData=json_encode($output);
	echo $responseData;
	die("<p>Something went wrong with $sql<br>".$dblink_error->error);
}


function error_list_manufacturer($dblink, $dblink_error, $sql, $api_endpoint){
	$error_type_num=6;
	log_api_error($dblink_error, $api_endpoint, $error_type_num);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: ERROR';
	$output[]="MSG: There are no records in the manufacturers table";
	$output[]='Action: add_manufacturer';
	$responseData=json_encode($output);
	echo $responseData;
	die("<p>Something went wrong with $sql<br>".$dblink->error);
}

function error_list_equipments($dblink, $dblink_error, $sql, $api_endpoint){
	$error_type_num=6;
	log_api_error($dblink_error, $api_endpoint, $error_type_num);

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: ERROR';
	$output[]="MSG: Something went wrong when querying specified equipment ids. Possible extra commas.";
	$output[]='Action: list_equipments';
	$responseData=json_encode($output);
	echo $responseData;
	die("<p>Something went wrong with $sql<br>".$dblink->error);
}

?>