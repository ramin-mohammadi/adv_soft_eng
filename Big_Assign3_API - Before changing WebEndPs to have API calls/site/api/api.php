<?php
include("../functions.php");
/*$url=$_SERVER['REQUEST_URI'];
header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output[]='Status: ERROR';
$output[]='MSG: System Disabled';
$output[]='Action: None';
//log_error($_SERVER['REMOTE_ADDR'],"SYSTEM DISABLED","SYSTEM DISABLED: $endPoint",$url,"api.php");*/
$url=$_SERVER['REQUEST_URI'];
$path = parse_url($url, PHP_URL_PATH);
$pathComponents = explode("/", trim($path, "/"));
$endPoint=$pathComponents[1];
switch($endPoint)
{
    case "add_equipment":
        $did=$_REQUEST['did'];
        $mid=$_REQUEST['mid'];
        $sn=$_REQUEST['sn'];
        include("add_equipment.php"); // this file will have access to the above va $_REQUEST variables
        break;
	case "add_device":
		include("add_devices.php");
		break;
	case "add_manufacturer":
		include("add_manufacturer.php");
		break;
	case "list_equipments":
		include("list_equipment.php");
		break;
	case "list_manufacturers":
		include("list_manufacturers.php");
		break;
	case "list_devices":
		include("list_devices.php");
		break;
	case "modify_equipment":
		include("modify_equipment.php");
		break;
	case "modify_device":
		include("modify_device.php");
		break;
	case "modify_manufacturer":
		include("modify_manufacturer.php");
		break;
    default:
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]='Status: ERROR';
        $output[]='MSG: Invalid or missing endpoint';
        $output[]='Action: None';
        $responseData=json_encode($output);
        echo $responseData;
        break;
}
die();
?>