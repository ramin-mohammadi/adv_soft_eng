<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Advanced Software Engineering</title>
<link href="../assets/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/font-awesome.min.css">
<link rel="stylesheet" href="../assets/css/owl.carousel.css">
<link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">

<!-- MAIN CSS -->
<link rel="stylesheet" href="../assets/css/templatemo-style.css">
</head>
<body>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
     <!-- MENU -->
     <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
          <div class="container">
               <div class="navbar-header">
                    <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                    </button>

                    <!-- lOGO TEXT HERE -->
                    <a href="#" class="navbar-brand">Add New Equipment</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                         <li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
                    </ul>
               </div>
          </div>
     </section>
 <!-- HOME -->
     <section id="home">
          </div>
     </section>
     <!-- FEATURE -->
     <section id="feature">
          <div class="container">
               <div class="row">
                   
                   <?php 
                        include("../functions_FINAL.php");
				   	    
				   		// connect to db called devices
                        $dblink=db_connect("devices"); 
				   
				   		// query the auto id and name for device types table
                        $sql="Select `device_type_name`,`device_type_num` from `device_types` where `status`='active'";
                        $result=$dblink->query($sql) or
                            die("<p>Something went wrong with $sql<br>".$dblink->error); // LOG SELECT ERROR
                        $devices=array();
                        while ($data=$result->fetch_array(MYSQLI_ASSOC))
                            $devices[$data['device_type_num']]=$data['device_type_name'];
				   		
				   		//query the auto id and name from manufacturers table
                        $sql="Select `manufacturer_name`,`manufacturer_num` from `manufacturers` where `status`='active'";
                        $result=$dblink->query($sql) or
                            die("<p>Something went wrong with $sql<br>".$dblink->error); // LOG SELECT ERROR
				   		$manufacturers=array();
                        while ($data=$result->fetch_array(MYSQLI_ASSOC))
                            $manufacturers[$data['manufacturer_num']]=$data['manufacturer_name'];
				   		
				   		// Checking error messages
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="EquipmentExists") //duplicate serial num
                        {
                            echo '<div class="alert alert-danger" role="alert">Serial Number already exists in database!</div>';
                        }
					    else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="DeviceExists") //duplicate device name
                        {
                            echo '<div class="alert alert-danger" role="alert">Device already exists in database!</div>';
                        }
						else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="ManufacturerExists") //duplicate manufacturer
                        {
                            echo '<div class="alert alert-danger" role="alert"><Manufacturer already exists in database!</div>';
                        }
				   
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="TooLong_SerialNum") 
                        {
                            echo '<div class="alert alert-danger" role="alert">Serial Number is too long. Must be less than 90 characters!</div>';
                        }
						else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="TooLong_Device") 
                        {
                            echo '<div class="alert alert-danger" role="alert">Device input is too long. Must be less than 32 characters!</div>';
                        }
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="TooLong_Manufacturer") 
                        {
                            echo '<div class="alert alert-danger" role="alert">Manufacturer input is too long. Must be less than 32 characters!</div>';
                        }

				   
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="InvalidInput_SerialNum")
						{
							echo '<div class="alert alert-danger" role="alert">Invalid input for Serial Number</div>';
						}
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="InvalidInput_Device")
						{
							echo '<div class="alert alert-danger" role="alert">Invalid input for Device</div>';
						}
						else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="InvalidInput_Manufacturer")
						{
							echo '<div class="alert alert-danger" role="alert">Invalid input for Manufacturer</div>';
						}
				   
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="Empty_SerialNum")
                        {
                            echo '<div class="alert alert-danger" role="alert"><Serial Number input is empty</div>';
                        }
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="Empty_Device")
                        {
                            echo '<div class="alert alert-danger" role="alert"><Device input is empty</div>';
                        }
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="Empty_Manufacturer")
                        {
                            echo '<div class="alert alert-danger" role="alert"><Manufacturer input is empty</div>';
                        }
                   ?>
				   
					<!--add new equipment form-->
                    <form method="post" action="">
                    <div class="form-group">
                        <label for="exampleDevice">Device:</label>
                        <select class="form-control" name="device">
                            <?php
                                foreach($devices as $key=>$value)
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleManufacturer">Manufacturer:</label>
                        <select class="form-control" name="manufacturer">
                            <?php
                                foreach($manufacturers as $key=>$value)
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleSerial">Serial Number:</label>
                        <input type="text" class="form-control" id="serialInput" name="serialnumber">
                    </div>
                        <button type="submit" class="btn btn-primary" name="submit_AddEquipment" value="submit_AddEquipment">Add Equipment</button>
                   </form>
				   
				   
					<!--add new device form-->
				 	<form method="post" action="">
                    <div class="form-group">
                        <label for="exampleSerial">Device Name:</label>
                        <input type="text" class="form-control" id="deviceInput" name="deviceInput">
                    </div>
                        <button type="submit" class="btn btn-primary" name="submit_AddDevice" value="submit_AddDevice">Add Device</button>
                   </form>
				   
				   
					<!--add new manufacturer form-->
				 	<form method="post" action="">
                    <div class="form-group">
                        <label for="exampleSerial">Manufacturer Name:</label>
                        <input type="text" class="form-control" id="manufacturerInput" name="manufacturerInput">
                    </div>
                        <button type="submit" class="btn btn-primary" name="submit_AddManufacturer" value="submit_AddManufacturer">Add Manufacturer</button>
                   </form>
               </div>
          </div>
     </section>
</body>
</html>
<?php
    if (isset($_POST['submit_AddEquipment']))
    {
        $device=$_POST['device'];
        $manufacturer=$_POST['manufacturer'];
        $serialNumber=trim($_POST['serialnumber']);
		
		echo '<h1>'.$device.' '.$manufacturer.' '.$serialNumber.'</h1>';
		
		//check if serial number input is empty or just whitespaces
		if (strlen($serialNumber) == 0){
			echo "ERROR: Serial number input is empty";
			redirect("add.php?msg=Empty_SerialNum");
		}
		
		//check if input is too long
		$max_len=90;
		if (strlen($serialNumber) > $max_len){
			echo "ERROR: Serial number input is too long, must be less than '$max_len' characters";
			redirect("add.php?msg=TooLong_SerialNum");
		}
		
		//add slahes to avoid sql injection
		$serialNumber = addslashes($serialNumber);
		
		//check if serial number is valid input
		$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
		echo "<p>$serialNumber</p>";
		if($dirty_data=preg_match($pattern, $serialNumber ) ){ // matches elements in array that meet regex pattern
            echo "ERROR: Serial number has invalid input";
			redirect("add.php?msg=InvalidInput_SerialNum");
		}
		
		// Check if serial number already exists
        $sql="Select `device_num` from `devices` where `serial_num`='$serialNumber'"; 
        $rst=$dblink->query($sql) or
             die("<p>Something went wrong with $sql<br>".$dblink->error);
        if ($rst->num_rows<=0)//sn not previously found
        {
			// my auto increment could not be added due to large table and low CPU resources
			// Instead, acquire last id to insert manually
			$sql="Select MAX(`device_num`) AS `max_id` from `devices`"; 
			$rst=$dblink->query($sql) or
             	die("<p>Something went wrong with $sql<br>".$dblink->error);
			$next_device_num = 0;
			while ($row = mysqli_fetch_array($rst)) {
//				$n = $row['max_id'];
//				echo "<p>'$n'</p>";
    			$next_device_num = intval($row['max_id']) + 1;       
			}
//			echo "<p>'$next_device_num'</p>";
//			die();
			
            $sql="Insert into `devices` (`device_num`, `device_type_num`,`manufacturer_num`,`serial_num`) values ('$next_device_num', '$device','$manufacturer','$serialNumber')";
            $dblink->query($sql) or
                 die("<p>Something went wrong with $sql<br>".$dblink->error);
            redirect("index.php?msg=EquipmentAdded");
        }
        else
            redirect("add.php?msg=EquipmentExists"); // duplicate serial number
    }


    else if (isset($_POST['submit_AddDevice']))
	{
		$device_input=$_POST['submit_AddDevice'];
		
		//check if Device input is empty or just whitespaces
		if (strlen(trim($device_input)) == 0){
			echo "ERROR: Device input is empty";
			redirect("add.php?msg=Empty_Device");
		}
		
		//check if input is too long
		$max_len=32;
		if (strlen($device_input) > $max_len){
			echo "ERROR: Device input is too long, must be less than '$max_len' characters";
			redirect("add.php?msg=TooLong_Device");
		}


		//add slahes to avoid sql injection
		$device_input = addslashes($device_input);
		
		//check if device name is valid input
		$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
		if($dirty_data=preg_grep($pattern, $device_input ) ){ // matches elements in array that meet regex pattern
            echo "ERROR: Device name has invalid input";
			redirect("add.php?msg=InvalidInput_Device");
		}
		
		// attempt to get device type number from the device types table
		$sql = "SELECT `device_type_num` FROM `device_types` WHERE `device_type_name`='$device_input'";
		$result=$dblink->query($sql);
		if ($result->num_rows <= 0) {	// device_type doesnt exist, add new device_type
			new_device_type($dblink, $device_input);
		}	
		else
            redirect("add.php?msg=DeviceExists"); // duplicate device
	}


	else if (isset($_POST['submit_AddManufacturer']))
	{
		$manufacturer_input=$_POST['submit_AddManufacturer'];
		
		//check if manufacturer input is empty or just whitespaces
		if (strlen(trim($manufacturer_input)) == 0){
			echo "ERROR: Manufacturer input is empty";
			redirect("add.php?msg=Empty_Manufacturer");
		}
		
		//check if input is too long
		$max_len=32;
		if (strlen($manufacturer_input) > $max_len){
			echo "ERROR: Manufacturer input is too long, must be less than '$max_len' characters";
			redirect("add.php?msg=TooLong_Manufacturer");
		}


		//add slahes to avoid sql injection
		$manufacturer_input = addslashes($manufacturer_input);
		
		//check if device name is valid input
		$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
		if($dirty_data=preg_grep($pattern, $manufacturer_input ) ){ // matches elements in array that meet regex pattern
            echo "ERROR: Manufacturer name has invalid input";
			redirect("add.php?msg=InvalidInput_Manufacturer");
		}
		
		// attempt to get manufacturer number from the manufacturer table
		$sql = "SELECT `manufacturer_num` FROM `manufacturers` WHERE `manufacturer_name`='$manufacturer_input'";
		$result=$dblink->query($sql);
		if ($result->num_rows <= 0) {	// manufacturer doesnt exist, add new device_type
			$manufacturer_num = new_manufacturer($dblink, $manufacturer_input);
		}	
		else
            redirect("add.php?msg=DeviceExists"); // duplicate manufacturer
		
	}


?>