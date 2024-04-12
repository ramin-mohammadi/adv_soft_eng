<?php
	session_start();
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
                    <a href="#" class="navbar-brand">View Equipment Database</a>
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
	
<!--
	<script type="text/javascript">
		function update_button(device_num){
			document.location.href="update.php?device_num="+device_num;
		}
	</script>
-->

	
<!--     FEATURE -->
     <section id="feature">
          <div class="container">
               <div class="row">
				   <table class="table table-striped">
					  <thead>
						<tr>
						  <th scope="col">Device Num</th>
						  <th scope="col">Device Name</th>
						  <th scope="col">Manufacturer Name</th>
						  <th scope="col">Serial Number</th>
							<th scope="col">#</th>
						</tr>
					  </thead>
					<tbody>


				    <?php 
                        include("../functions_FINAL.php");
				   	    
				   		// connect to db called devices
                        $dblink=db_connect("devices"); 
	  
						if(isset($_POST['update_button'])){
							redirect();
						}

	  
	  
				  		if(isset($_SESSION['ids']) && isset($_REQUEST['searchBy'])){
							$searchBy = $_REQUEST['searchBy'];
							$ids = $_SESSION['ids'];
							echo "<h4>Search By: ".str_replace('"', '', $searchBy)."</h4>";
//							for($i=0; $i<10; $i++){
//								echo $ids[$i];
//								echo "<br>";  
//							}
							
							// TRUNACATE ARRAY bc our Php session and CPU cant handle all of the queries
							$first_num=3000;

							$list= implode(', ', $ids); 
							$sql = "SELECT `device_num`, `device_type_num`, `manufacturer_num`, `serial_num` FROM `devices` WHERE `device_num` IN($list) LIMIT $first_num";
							$rst=$dblink->query($sql) or
								die("<p>Something went wrong with $sql<br>".$dblink->error);
							
//							echo "<ul class='list-group'>";
							
						

//							foreach($ids as $id){
//								echo $id;
//								echo "<br>";  
								
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
									$uri = "https://ec2-3-142-218-191.us-east-2.compute.amazonaws.com:63221/web/update.php";
									echo "<tr>";
										  echo "<th scope='row'>".$device_num."</th>";
										  echo "<td>".$device_type_num."</td>";
									  	  echo "<td>".$manufacturer_num."</td>";
										  echo "<td>".$serial_num."</td>";
//										  echo "<td><button type='button' onclick='update_button($device_num)' class='btn btn-primary' name='update_button'>Update</button></td>";
									  	  echo "<td><form method='post' action='$uri'><button type='submit' class='btn btn-primary' name='update_button' value='$device_num'>Update</button></form></td>";

									echo "</tr>";
								}

//								switch($searchBy){
//									case 'serial_number':
//										while($row = $rst->fetch_assoc()) {
//											$device_type_num = $row['device_type_num'];
//											$b = $row['manufacturer_num'];
//											$c = $row['serial_num'];
//
//
//											echo "<li class='list-group-item'><a href=''>" + $device_type_num + $b + $c+"</a></li>";
//										}
//
//										break;
//									case 'device':
//										break;
//									case 'manufacturer':
//										$max_len=32;
//										break;
//								}
//							}
//							echo "</ul>";
							
				   
//						}
						}
					?>
	  
				  </tbody>
				</table>
               </div>
          </div>
     </section>
</body>
</html>
