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
						</tr>
					  </thead>
					  <tbody>
						<?php 
							include("../functions_FINAL.php");

							// connect to db called devices
							$dblink=db_connect("devices"); 
						  
							if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="EquipmentUpdated"){
								echo '<div class="alert alert-success" role="alert">Equipment successfully updated.</div>';
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
						  
						  
//							if(isset($_POST['update_button'])ManufacturerDoesnManufacturer doesnt exist_POST['update_button'];
						  
							
							if(isset($_SESSION['device_num']) ){
								$device_num = $_SESSION['device_num'];


//								$device_num=-1;
//								if(isset($_POST['update_button']))
//									$device_num = $_POST['update_button'];
//								else
//									$device_num = $_POST['submitUpdate'];
//
								$sql = "SELECT `device_num`, `device_type_num`, `manufacturer_num`, `serial_num` FROM `devices` WHERE `device_num`=$device_num";
								$rst=$dblink->query($sql) or
									die("<p>Something went wrong with $sql<br>".$dblink->error);
								while($row = $rst->fetch_assoc()) {
									$device_num = $row['device_num'];
									$device_type_num = $row['device_type_num'];
									$manufacturer_num = $row['manufacturer_num'];
									$serial_num = $row['serial_num'];

									// Replace Foreign keys with dedicated values (device name and manufacturer name)
									$sql = "SELECT `device_type_name` FROM `device_types` WHERE `device_type_num`='$device_type_num'";
									$result=$dblink->query($sql) or
										die("<p>Something went wrong with $sql<br>".$dblink->error);
									if ($result->num_rows > 0) {
										while($row = $result->fetch_assoc()) { 
											$device_type_num = $row["device_type_name"];
										}
									}	

									$sql = "SELECT `manufacturer_name` FROM `manufacturers` WHERE `manufacturer_num`='$manufacturer_num'";
									$result=$dblink->query($sql) or
										die("<p>Something went wrong with $sql<br>".$dblink->error);
									if ($result->num_rows > 0) {
										while($row = $result->fetch_assoc()) { 
											$manufacturer_num = $row["manufacturer_name"];
										}
									}	

									echo "<tr>";
									  echo "<th scope='row'>".$device_num."</th>";
									  echo "<td>".$device_type_num."</td>";
									  echo "<td>".$manufacturer_num."</td>";
									  echo "<td>".$serial_num."</td>";
									echo "</tr>";
								}
							}

						?>
						</tbody>
				   </table>
				   
				   <form method="post" action="">
                    <div class="form-group">
                        <label for="exampleUpdateOption">Update Option</label>
                        <select class="form-control" name="UpdateOption">
							<option value="device">Device Name</option>;
							<option value="manufacturer">Manufacturer Name</option>;
							<option value="serial_number">Serial Number</option>;
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleSearch">New Value:</label>
                        <input type="text" class="form-control" id="updateInput" name="updateInput">
                    </div>
                        <button type="submit" class="btn btn-primary" name="submitUpdate" value="submitUpdate">Update Equipment</button>

				   </form>
				   
				   <?php
				   	if(isset($_POST['submitUpdate'])){
						$update_input = $_POST['updateInput'];
//						$device_num = $_POST['update_button'];
//						echo"<p>'$update_input'</p>";
//						echo"<p>'$device_num'</p>";

						//check if input is empty or just whitespaces
						if (strlen(trim($update_input)) == 0){
							echo "ERROR: Update input is empty";
							redirect("update.php?msg=Empty");
						}

						//check if input is too long
						switch($_POST['UpdateOption']){
							case 'serial_number':
								$max_len=90;
								break;
							case 'device':
							case 'manufacturer':
								$max_len=32;
								break;
							default:
								$max_len=32;
								break;
						}						
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
						switch($_POST['UpdateOption']){
							case "serial_number":
								// Check if serial number already exists
								$sql="Select `device_num` from `devices` where `serial_num`='$update_input'"; 
								$rst=$dblink->query($sql) or
									 die("<p>Something went wrong with $sql<br>".$dblink->error);
								if ($rst->num_rows<=0)//sn not previously found
								{
									$sql="UPDATE `devices` SET `serial_num`='$update_input' WHERE `device_num`='$device_num'";
									$dblink->query($sql) or
										 die("<p>Something went wrong with $sql<br>".$dblink->error);
									redirect("update.php?msg=EquipmentUpdated");
								}
								else
									redirect("update.php?msg=SerialNumberExists"); // duplicate serial number
								break;
							case "device":
								// attempt to get device type number from the device types table
								$sql = "SELECT `device_type_num` FROM `device_types` WHERE `device_type_name`='$update_input'";
								$result=$dblink->query($sql) or
										 die("<p>Something went wrong with $sql<br>".$dblink->error);;
								if ($result->num_rows > 0) { // device type exists
									while($row = $result->fetch_assoc()) { 
										$device_type_num = $row["device_type_num"];
									}
									$sql="UPDATE `devices` SET `device_type_num`='$device_type_num' WHERE `device_num`='$device_num'";
									$dblink->query($sql) or
										 die("<p>Something went wrong with $sql<br>".$dblink->error);
									redirect("update.php?msg=EquipmentUpdated");
								}	
								else
									redirect("update.php?msg=DeviceDoesntExist");
								break;
					
							case "manufacturer":
								// attempt to get manufacturer number from the manufacturer table
								$sql = "SELECT `manufacturer_num` FROM `manufacturers` WHERE `manufacturer_name`='$update_input'";
								$result=$dblink->query($sql);
								if ($result->num_rows > 0) { // manufacturer exists
									while($row = $result->fetch_assoc()) { 
										$manufacturer_num = $row["manufacturer_num"];
									}
									$sql="UPDATE `devices` SET `manufacturer_num`='$manufacturer_num' WHERE `device_num`='$device_num'";
									$dblink->query($sql) or
										 die("<p>Something went wrong with $sql<br>".$dblink->error);
									redirect("update.php?msg=EquipmentUpdated");
								}	
								else
									redirect("update.php?msg=ManufacturerDoesntExist");
								break;
						}
					}
						
				 ?>

               </div>
          </div>
     </section>
</body>
</html>
