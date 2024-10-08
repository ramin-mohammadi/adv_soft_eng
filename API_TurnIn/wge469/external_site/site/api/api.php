<?php
//include("../functions_FINAL.php");

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
//echo $url."aaaaaaaa";
//echo $path."aaaaa";
//echo $pathComponents."aaaaaa";
//echo $endPoint."aaaaaaaa";
//echo $_SERVER['HTTP_HOST'];

// if making api call from outside server, then make same call (with same data) through the api server URL
if($_SERVER['HTTP_HOST'] != "ec2-3-142-218-191.us-east-2.compute.amazonaws.com:63221"){
	$data = explode("?", $url);
//	echo $data[1];

	$ch=curl_init("https://ec2-3-142-218-191.us-east-2.compute.amazonaws.com:63221/api/".$endPoint);
	//	$data="test";
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//ignore ssl
	curl_setopt($ch, CURLOPT_POST,1);//tell curl we are using post
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data[1]);//this is the data
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//prepare a response
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'content-type: application/x-www-form-urlencoded',
		'content-length: '.strlen($data[1]))
				);
	$result=curl_exec($ch); // $result is the data RECEIVED from executing that api endpoint
	curl_close($ch);
	echo $result;
	die();
}

else{

switch($endPoint)
{
    case "add_equipment":
        $next_device_num=$_REQUEST['next_device_num'];
		$device=$_REQUEST['device'];
		$manufacturer=$_REQUEST['manufacturer'];
		$serialNumber=$_REQUEST['serialNum'];
        include("add_equipment.php"); // this file will have access to the above va $_REQUEST variables
        break;
	case "add_device":
		$device_input=$_REQUEST['device_input'];
		include("add_device.php");
		break;
	case "add_manufacturer":
		$manufacturer_input=$_REQUEST['manufacturer_input'];
		include("add_manufacturer.php");
		break;
	case "list_equipments":
		$list=$_REQUEST['list_deviceNum'];
		$first_num=$_REQUEST['first_num'];
		include("list_equipments.php");
		break;
	case "list_manufacturers":
		include("list_manufacturers.php");
		break;
	case "list_devices":
		include("list_devices.php");
		break;
	case "modify_equipment":
		$serial_num=$_REQUEST['serial_num'];
		$device_type_num=$_REQUEST['device_type_num'];
		$manufacturer_num=$_REQUEST['manufacturer_num'];
		$status=$_REQUEST['status'];
		$device_num=$_REQUEST['device_num'];
		include("modify_equipment.php");
		break;
	case "modify_device":
		$device_input=$_REQUEST['device_input'];
		$status=$_REQUEST['status'];
		$device_type_num=$_REQUEST['device_type_num'];
		include("modify_device.php");
		break;
	case "modify_manufacturer":
		$manufacturer_input=$_REQUEST['manufacturer_input'];
		$status=$_REQUEST['status'];
		$manufacturer_num=$_REQUEST['manufacturer_num'];
		include("modify_manufacturer.php");
		break;
	case "check_serialNum_exists":
	 	$serialNumber=$_REQUEST['serialNum'];
		include("check_serialNum_exists.php");
		break;
	case "check_device_exists":
	 	$device_input=$_REQUEST['device_input'];
		include("check_device_exists.php");
		break;
	case "check_manufacturer_exists":
	 	$manufacturer_input=$_REQUEST['manufacturer_input'];
		include("check_manufacturer_exists.php");
		break;
	case "get_lastEquipAutoID":
		include("get_lastEquipAutoID.php");
		break;
	case "search_device":
		$search_input=$_REQUEST['search_input'];
		$first_num=$_REQUEST['first_num'];
		$inactive_option=$_REQUEST['inactive_option'];
		include("search_device.php");
		break;
	case "search_manufacturer":
		$search_input=$_REQUEST['search_input'];
		$first_num=$_REQUEST['first_num'];
		$inactive_option=$_REQUEST['inactive_option'];
		include("search_manufacturer.php");
		break;
	case "search_serialNum":
		$search_input=$_REQUEST['search_input'];
		$first_num=$_REQUEST['first_num'];
		$inactive_option=$_REQUEST['inactive_option'];
		include("search_serialNum.php");
		break;
	case "view_single_equipment":
		$device_num=$_REQUEST['device_num'];
		include("view_single_equipment.php");
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
}
die();
?>