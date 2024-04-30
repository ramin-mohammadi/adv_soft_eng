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
						  <th scope="col">Status</th>
							<th scope="col">#</th>
						</tr>
					  </thead>
					<tbody>


				    <?php 
                        include("../functions_FINAL.php");
				   	     
				  		if(isset($_SESSION['ids']) && isset($_REQUEST['searchBy'])){
							$searchBy = $_REQUEST['searchBy'];
							$ids = $_SESSION['ids'];
							echo "<h4>Search By: ".str_replace('"', '', $searchBy)."</h4>";
//							for($i=0; $i<10; $i++){
//								echo $ids[$i];
//								echo "<br>";  
//							}
							
							// TRUNACATE ARRAY bc our Php session and CPU cant handle all of the queries
							$first_num=1000;

							$list= implode(', ', $ids); 
							$result=api_call("list_equipments", "list_deviceNum=".$list."&first_num=".$first_num);
							$equipments=json_decode(get_payload($result), true);
							$uri = "https://ec2-3-142-218-191.us-east-2.compute.amazonaws.com:63221/web/update.php";

							for ($i = 0; $i < count($equipments['device_num']); $i++){
								$device_num=$equipments['device_num'][$i];
								echo "<tr>";
									  echo "<th scope='row'>".$device_num."</th>";
									  echo "<td>".$equipments['device_type_name'][$i]."</td>";
									  echo "<td>".$equipments['manufacturer_name'][$i]."</td>";
									  echo "<td>".$equipments['serial_num'][$i]."</td>";
									  echo "<td>".$equipments['status'][$i]."</td>";
									  echo "<td><form method='post' action='$uri'><button type='submit' class='btn btn-primary' name='update_button' value='$device_num'>Update</button></form></td>";
								echo "</tr>";
							}

						}
					?>
	  
				  </tbody>
				</table>
               </div>
          </div>
     </section>
</body>
</html>
