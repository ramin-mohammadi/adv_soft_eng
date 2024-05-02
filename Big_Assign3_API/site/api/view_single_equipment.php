<?php
	include("../functions_FINAL.php");

	// connect to db called devices
	$dblink=db_connect("devices"); 

	$sql = "SELECT `device_num`, `device_type_num`, `manufacturer_num`, `serial_num`, `status` FROM `devices` WHERE `device_num`=$device_num";
	$rst=$dblink->query($sql) or
		die("<p>Something went wrong with $sql<br>".$dblink->error);
	
	$device_type_name = "";
	$manufacturer_name = "";
	$serial_num = "";
	$status = "";
	$device_type_num = "";
	$manufacturer_num = "";

	if($rst->num_rows > 0){
		while($row = $rst->fetch_assoc()) {
			$device_type_num = $row['device_type_num'];
			$manufacturer_num = $row['manufacturer_num'];
			$serial_num = $row['serial_num'];
			$status = $row['status'];

			// Replace Foreign keys with dedicated values (device name and manufacturer name)
			$sql = "SELECT `device_type_name` FROM `device_types` WHERE `device_type_num`='$device_type_num'";
			$result_dtype=$dblink->query($sql) or
				die("<p>Something went wrong with $sql<br>".$dblink->error);
			if ($result_dtype->num_rows > 0) {
				while($row = $result_dtype->fetch_assoc()) { 
					$device_type_name = $row["device_type_name"];
				}
			}	

			$sql = "SELECT `manufacturer_name` FROM `manufacturers` WHERE `manufacturer_num`='$manufacturer_num'";
			$result=$dblink->query($sql) or
				die("<p>Something went wrong with $sql<br>".$dblink->error);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) { 
					$manufacturer_name = $row["manufacturer_name"];
				}
			}
		}
	}
	$jsonEquipment=json_encode(array("device_type_name"=>$device_type_name, "manufacturer_name"=>$manufacturer_name, "serial_num"=>$serial_num,"status"=>$status,"device_type_num"=>$device_type_num,"manufacturer_num"=>$manufacturer_num));

	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$output[]='MSG: '.json_encode($jsonEquipment);
	$output[]='Action: none';
	$responseData=json_encode($output);
	echo $responseData;
	die();
?>