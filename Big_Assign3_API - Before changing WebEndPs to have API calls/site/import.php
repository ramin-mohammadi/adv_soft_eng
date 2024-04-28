<?php
include("functions.php");
$dblink=db_connect("test");
/*
echo "Hello from php process $argv[1] about to process file:$argv[2]\n";
$fp=fopen("/home/ubuntu/parts/$argv[2]","r");
*/
$fp=fopen("/var/www/html/test.txt","r");
$count=0;
$time_start=microtime(true);
//echo "PHP ID:$argv[1]-Start time is: $time_start\n";
while (($row=fgetcsv($fp)) !== FALSE)
{
	$num_fields=count($row);
	/*
	echo "$num_fields\n";
	echo "$row[0]:$row[1]:$row[2]\n";
	if($row[0]=="")
		echo "first empty\n";
	if($row[1]=="")
		echo "second empty\n";
	if($row[2]=="")
		echo "third empty\n";
	if($row == array(null))
		echo "blank line\n";
	if($num_fields == 1 && preg_match('/^\s*$/', $row[0]) )
		echo "regex ton of spaces\n";
	if($num_fields == 4 && $row[0] == "" && $row[1] !== "" && $row[2] !== "" && $row[3] !== "")
		echo "extra comma at beginning\n";
		*/
	$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
	$replacement = '';
	if($dirty_data=preg_grep($pattern, $row ) ){ // matches elements in array that meet regex pattern
		//echo 'Foreign Characters';
		foreach($dirty_data as $d){
			echo "$d\n";
		}
		$indexes = array_keys($row, ...$dirty_data);
		echo "Before clean: $row[0],$row[1],$row[2]\n";
		foreach ( $indexes as $index )
    	{
			//get rid of matched foreign characters (replace regex matches with empty string)
        	$row[$index] = preg_replace($pattern, $replacement, $row[$index]); 
    	}
		echo "After clean: $row[0],$row[1],$row[2]\n";
	}
	/*
	$sql="Insert into `devices` (`device_type`,`manufacturer`,`serial_number`) values
	('$row[0]','$row[1]','$row[2]')";
	$dblink->query($sql) or
		die("Something went wrong with $sql\n".$dblink->error);
	$count++;
	*/
}
/*
$time_end=microtime(true);
echo "PHP ID:$argv[1]-End Time:$time_end\n";
$seconds=$time_end-$time_start;
$execution_time=($seconds)/60;
echo "PHP ID:$argv[1]-Execution time: $execution_time minutes or $seconds seconds.\n";
$rowsPerSecond=$count/$seconds;
echo "PHP ID:$argv[1]-Insert rate: $rowsPerSecond per second\n";
*/
fclose($fp);
?>