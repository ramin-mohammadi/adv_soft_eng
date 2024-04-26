<?php
function db_connect($db)
{
	$username="webuser";
	$password="i8je!YFyidaWGb_X";
	$host="localhost"; // in real setting, this would be an ip address
	$dblink=new mysqli($host, $username, $password, $db);
	return $dblink;
}
?>