<?php
	session_start();
	if (isset($_POST['update_button'])) {
		$_SESSION['device_num'] = $_POST['update_button'];
	}
?>
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
                    <a href="#" class="navbar-brand">Update Equipment Database</a>
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
	
	
<!--     FEATURE -->
     <section id="feature">
          <div class="container">
               <div class="row">
					<h4>Current Values:</h4>
					<table class="table table-striped">
					  <thead>
						<tr>
						  <th scope="col">Device Num</th>
						  <th scope="col">Device Name</th>
						  <th scope="col">Manufacturer Name</th>
						  <th scope="col">Serial Number</th>
						  <th scope="col">Status</th>
						</tr>
					  </thead>
					  <tbody>
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
						  
							if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="EquipmentUpdated"){
								echo '<div class="alert alert-success" role="alert">Equipment successfully updated.</div>';
							} 
						  	else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="DeviceNameUpdated"){
								echo '<div class="alert alert-success" role="alert">Device Name in device types table successfully updated.</div>';
							} 
						  	else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="ManufacturerNameUpdated"){
								echo '<div class="alert alert-success" role="alert">Manufacturer Name in manufacturer table successfully updated.</div>';
							} 
						  
						  	// Checking error messages
							else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="Empty") 
							{
								echo '<div class="alert alert-danger" role="alert">Input was empty</div>';
							}
							else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="TooLong")
							{
								echo '<div class="alert alert-danger" role="alert">Input was too many characters</div>';
							}
						  	else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="InvalidInput")
							{
								echo '<div class="alert alert-danger" role="alert">Invalid input</div>';
							}
						  	else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="SerialNumberExists")
							{
								echo '<div class="alert alert-danger" role="alert">Serial Number already exists, must be unique</div>';
							}
						  	else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="DeviceDoesntExist")			
							{
								echo '<div class="alert alert-danger" role="alert">Device doesnt exist</div>';
							}
						  	else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="ManufacturerDoesntExist")			
							{
								echo '<div class="alert alert-danger" role="alert">Manufacturer doesnt exist</div>';
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


							else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="InvalidInput_Device")
							{
								echo '<div class="alert alert-danger" role="alert">Invalid input for Device</div>';
							}
							else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="InvalidInput_Manufacturer")
							{
								echo '<div class="alert alert-danger" role="alert">Invalid input for Manufacturer</div>';
							}

							else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="Empty_Device")
							{
								echo '<div class="alert alert-danger" role="alert">Device input is empty</div>';
							}
							else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="Empty_Manufacturer")
							{
								echo '<div class="alert alert-danger" role="alert">Manufacturer input is empty</div>';
							}

						  						  
							
							if(isset($_SESSION['device_num']) ){
								$device_num = $_SESSION['device_num'];
								$sql = "SELECT `device_num`, `device_type_num`, `manufacturer_num`, `serial_num`, `status` FROM `devices` WHERE `device_num`=$device_num";
								$rst=$dblink->query($sql) or
									die("<p>Something went wrong with $sql<br>".$dblink->error);
								while($row = $rst->fetch_assoc()) {
									$device_num = $row['device_num'];
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

									echo "<tr>";
									  echo "<th scope='row'>".$device_num."</th>";
									  echo "<td>".$device_type_name."</td>";
									  echo "<td>".$manufacturer_name."</td>";
									  echo "<td>".$serial_num."</td>";
									  echo "<td>".$status."</td>";
									echo "</tr>";
								}
							}

						?>
						</tbody>
				   </table>
				   
				   <h4>Update Device For Equipment:</h4>
				   <form method="post" action="">
<!--
                    <div class="form-group">
                        <label for="exampleUpdateOption">Update Option</label>
                        <select class="form-control" name="UpdateOption">
							<option value="device">Device Name</option>;
							<option value="manufacturer">Manufacturer Name</option>;
							<option value="serial_number">Serial Number</option>;
                        </select>
                    </div>
-->
<!--
                    <div class="form-group">
                        <label for="exampleSearch">New Value:</label>
                        <input type="text" class="form-control" id="updateInput" name="updateInput">
                    </div>
-->
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
					    <div class="form-group">
							<label for="exampleUpdateOption">Update Status:</label>
							<select class="form-control" name="Equipment_Status">
								<option value="active">active</option>;
								<option value="inactive">inactive</option>;
							</select>
						</div>

                        <button type="submit" class="btn btn-primary" name="submitUpdate_Equip" value="submitUpdate_Equip">Update</button>
				   </form>
				   <br>
				   
				   <h4>Update Current Device's Name:</h4>
				   <form method="post" action="">
						<div class="form-group">
							<label for="exampleDeviceName">New Device Name:</label>
							<input type="text" class="form-control" id="deviceName_Input" name="deviceName_Input">
						</div>
					   <div class="form-group">
							<label for="exampleUpdateOption">Update Status:</label>
							<select class="form-control" name="Device_Status">
								<option value="active">active</option>;
								<option value="inactive">inactive</option>;
							</select>
						</div>

                        <button type="submit" class="btn btn-primary" name="submitUpdate_Device" value="submitUpdate_Device">Update</button>
				   </form>
				   <br>
				   
				   <h4>Update Current Manufacturer's Name:</h4>
				   <form method="post" action="">
						<div class="form-group">
							<label for="exampleManufacturerName">New Manufacturer Name:</label>
							<input type="text" class="form-control" id="manufacturerName_Input" name="manufacturerName_Input">
						</div>
					   <div class="form-group">
							<label for="exampleUpdateOption">Update Status:</label>
							<select class="form-control" name="Manufacturer_Status">
								<option value="active">active</option>;
								<option value="inactive">inactive</option>;
							</select>
						</div>

                        <button type="submit" class="btn btn-primary" name="submitUpdate_Manufacturer" value="submitUpdate_Manufacturer">Update</button>
				   </form>
				   

				   
				   
				   <?php
				   	if(isset($_POST['submitUpdate_Equip'])){
						$update_input = $_POST['serialnumber'];
						$device_type_num = $_POST['device'];
						$manufacturer_num = $_POST['manufacturer'];
						$status = $_POST['Equipment_Status'];

						//check if input is empty or just whitespaces
						if (strlen(trim($update_input)) == 0){
							echo "ERROR: Update input is empty";
							redirect("update.php?msg=Empty");
						}
					
						$max_len=90;
						if (strlen($update_input) > $max_len){
							echo "ERROR: input is too long";
							redirect("update.php?msg=TooLong");
						}

						//add slahes to avoid sql injection
						$update_input = addslashes($update_input);

						//check if valid input
						$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
						if($dirty_data=preg_match($pattern, $update_input ) ){ // matches elements in array that meet regex pattern
							echo "ERROR: Update has invalid input";
							redirect("update.php?msg=InvalidInput");
						}
						
						// Perform UPDATE
						
						// SERIAL NUMBER
						// Check if serial number already exists
						$sql="Select `device_num` from `devices` where `serial_num`='$update_input'"; 
						$rst=$dblink->query($sql) or
							 die("<p>Something went wrong with $sql<br>".$dblink->error);
						//sn not previously found or just assigning same serial number as before update
						if ($rst->num_rows<=0 || $serial_num == $update_input)
						{
							$sql="UPDATE `devices` SET `serial_num`='$update_input', `device_type_num`='$device_type_num', `manufacturer_num`='$manufacturer_num', `status`='$status' WHERE `device_num`='$device_num'";
							$dblink->query($sql) or
								 die("<p>Something went wrong with $sql<br>".$dblink->error);
						}
						else
							redirect("update.php?msg=SerialNumberExists"); // duplicate serial number
					
						redirect("update.php?msg=EquipmentUpdated");
					}
				   	else if(isset($_POST['submitUpdate_Device'])){
						$device_input=$_POST['deviceName_Input'];
						$status = $_POST['Device_Status'];
		
						//check if Device input is empty or just whitespaces
						if (strlen(trim($device_input)) == 0){
							echo "ERROR: Device input is empty";
							redirect("update.php?msg=Empty_Device");
						}

						//check if input is too long
						$max_len=32;
						if (strlen($device_input) > $max_len){
							echo "ERROR: Device input is too long, must be less than '$max_len' characters";
							redirect("update.php?msg=TooLong_Device");
						}

						//add slahes to avoid sql injection
						$device_input = addslashes($device_input);

						//check if device name is valid input
						$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
						if($dirty_data=preg_match($pattern, $device_input ) ){ // matches elements in array that meet regex pattern
							echo "ERROR: Device name has invalid input";
							redirect("update.php?msg=InvalidInput_Device");
						}
						
						// get device type number
//						while($row = $result_dtype->fetch_assoc()) {
//							$device_type_num = $row['device_type_num'];						
//							
//						}
						$sql="UPDATE `device_types` SET `device_type_name`='$device_input', `status`='$status' WHERE `device_type_num`='$device_type_num'";
							$dblink->query($sql) or
								die("<p>Something went wrong with $sql<br>".$dblink->error);
						
						redirect("update.php?msg=DeviceNameUpdated");
					}
				   	else if(isset($_POST['submitUpdate_Manufacturer'])){
						$manufacturer_input=$_POST['manufacturerName_Input'];
						$status = $_POST['Manufacturer_Status'];

						//check if manufacturer input is empty or just whitespaces
						if (strlen(trim($manufacturer_input)) == 0){
							echo "ERROR: Manufacturer input is empty";
							redirect("update.php?msg=Empty_Manufacturer");
						}

						//check if input is too long
						$max_len=32;
						if (strlen($manufacturer_input) > $max_len){
							echo "ERROR: Manufacturer input is too long, must be less than '$max_len' characters";
							redirect("update.php?msg=TooLong_Manufacturer");
						}
						


						//add slahes to avoid sql injection
						$manufacturer_input = addslashes($manufacturer_input);

						//check if device name is valid input
						$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
						if($dirty_data=preg_match($pattern, $manufacturer_input ) ){ // matches elements in array that meet regex pattern
							echo "ERROR: Manufacturer name has invalid input";
							redirect("add.php?msg=InvalidInput_Manufacturer");
						}
						
						// get manufacturer number
//						while($row = $rst->fetch_assoc()) {
//								$manufacturer_num = $row['manufacturer_num'];
//						}
						
						$sql="UPDATE `manufacturers` SET `manufacturer_name`='$manufacturer_input', `status`='$status' WHERE `manufacturer_num`='$manufacturer_num'";
						$dblink->query($sql) or
							 die("<p>Something went wrong with $sql<br>".$dblink->error);
						redirect("update.php?msg=ManufacturerNameUpdated");
						
						
					}

						
				 ?>

               </div>
          </div>
     </section>
</body>
</html>
