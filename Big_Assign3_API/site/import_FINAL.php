<?php
include("functions_FINAL.php");

// connect to dbs
$dblink_devices=db_connect("devices");
$dblink_errors=db_connect("errors");

//first process will be labelled as 2 and incrementally sequential (2,3,4...)
echo "Hello from php process $argv[1] about to process file:$argv[2]\n";

// open .csv partition for reading
$fp=fopen("/home/wge469/adv_soft_eng/parts/$argv[2]","r");

// used for keeping track of line #, (5 mil records total, 5 partitions, so 1 mil each)
$count=($argv[1] - 2) * 1000000 + 1; 

$time_start=microtime(true);
echo "PHP ID:$argv[1]-Start time is: $time_start\n";

while (($row_data=fgetcsv($fp)) !== FALSE)
{	
	$num_fields = count($row_data);
	
	//check for duplicate using serial number
//	$sql = "SELECT `device_num` FROM `devices` WHERE `serial_num`='$row_data[2]'";
//	$result=$dblink_devices->query($sql);
//	//success (found a duplicate)
//	if ($result->num_rows > 0) {	
//		log_error($dblink_errors, $count, 2); // duplicate error has id of 2 (predefined in error_types table in errors db)
//		$count++;
//		continue;
//	}
	
	//check for blank ("" or ",,," or ",," or "," or "     " )
	if( $row_data == array(null) 
		 || ($num_fields == 4 && $row_data[0] == "" && $row_data[1] == "" && $row_data[2] == "" && $row_data[3] == "") 
		 || ($num_fields == 3 && $row_data[0] == "" && $row_data[1] == "" && $row_data[2] == "")
		 || ($num_fields == 2 && $row_data[0] == "" && $row_data[1] == "")
		 || ($num_fields == 1 && $row_data[0] == "")
		 || ($num_fields == 1 && preg_match('/^\s*$/', $row_data[0])) ){
		log_error($dblink_errors, $count, 1);
		$count++;
		continue;
	}
	   
	//missing field(s)
	//device_type,manufacturer,serial_num
	if($num_fields == 3){ // even if a field is empty, it will count it (ex: "aaa,,SN-33333" -> num_fields is 3)
		//device_type and manufacturer missing
		$field_missing = 0;
		if($row_data[0] == "" && $row_data[1] == "")
			$field_missing = 8;
		//device_type and serial_num missing
		else if($row_data[0] == "" && $row_data[2] == "")
			$field_missing = 9;
		//manufacturer and serial_num missing
		else if($row_data[1] == "" && $row_data[2] == "")
			$field_missing = 10;
		// only device_type missing
		else if($row_data[0] == "")
			$field_missing = 5;
		//only manufacturer missing
		else if($row_data[1] == "")
			$field_missing = 6;
		//only serial_num missing
		else if($row_data[2] == "")
			$field_missing = 7;
		if($field_missing !== 0){
			log_error($dblink_errors, $count, $field_missing);
			$count++;
			continue;
		}
	}
	
	//line starting with an extra comma
	if($num_fields == 4 && $row_data[0] == "" && $row_data[1] !== "" && $row_data[2] !== "" && $row_data[3] !== ""){ 
		log_error($dblink_errors, $count, 3);
		// no clean up logic required, simply use indexes 1->3 by offseting the start of the array by 1 index
		insert_device($dblink_devices, array_slice($row_data, 1), $dblink_errors, $count); 
		$count++;
		continue;
	}
	
	//foreign characters (regex pattern) -> get rid of these (clean up)
	// apostrophes were found in the .csv
	// we are expecting a-z, A-Z, 0-9, literal commas, literal dash, newline, space characters
	$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
	$replacement = '';
	if($dirty_data=preg_grep($pattern, $row_data ) ){ // matches elements in array that meet regex pattern
		$indexes = array_keys($row_data, ...$dirty_data); // get indexes of elements with foreign characters
		foreach ( $indexes as $index )
    	{
			//(clean up) get rid of matched foreign characters (replace regex matches with empty string)
        	$row_data[$index] = preg_replace($pattern, $replacement, $row_data[$index]); 
    	}
		log_error($dblink_errors, $count, 4);
		//insert cleaned up data
		insert_device($dblink_devices, $row_data, $dblink_errors, $count); 
		$count++;
		continue;
	}	
		
	//no possible errors found (among the discovered errors), insert data
	insert_device($dblink_devices, $row_data, $dblink_errors, $count); 
	$count++;
}


$time_end=microtime(true);
echo "PHP ID:$argv[1]-End Time:$time_end\n";
$seconds=$time_end-$time_start;
$execution_time=($seconds)/60;
echo "PHP ID:$argv[1]-Execution time: $execution_time minutes or $seconds seconds.\n";
$rowsPerSecond=$count/$seconds;
echo "PHP ID:$argv[1]-Insert rate: $rowsPerSecond per second\n";

fclose($fp);
?>
