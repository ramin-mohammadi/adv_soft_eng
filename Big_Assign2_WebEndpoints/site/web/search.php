<?php
	session_start();
	unset($_SESSION['ids']);

?>
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
                    <a href="#" class="navbar-brand">Search Equipment Database</a>
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
				    <?php 
                        include("../functions_FINAL.php");
				   	    
				   		// connect to db called devices
                        $dblink=db_connect("devices"); 
				   
				   		// Checking error messages				   
				   		if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="InvalidInput_Search")
						{
							echo '<div class="alert alert-danger" role="alert">Invalid input for Search</div>';
						}
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="Empty_Search")
                        {
                            echo '<div class="alert alert-danger" role="alert">Search input is empty</div>';
                        }
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="TooLong")
                        {
                            echo '<div class="alert alert-danger" role="alert">Search input is too many characters</div>';
                        }
				   		
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="NoMatch_Search_SerialNum")
                        {
                            echo '<div class="alert alert-danger" role="alert">No match for searched Serial Number</div>';
                        }
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="NoMatch_Search_Device")
                        {
                            echo '<div class="alert alert-danger" role="alert">No match for searched Device</div>';
                        }
				   		else if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="NoMatch_Search_Manufacturer")
                        {
                            echo '<div class="alert alert-danger" role="alert">No match for searched Manufacturer</div>';
                        }
				   
				   ?>
				   
				   <form method="post" action="">
                    <div class="form-group">
                        <label for="exampleSearchBy">Search By:</label>
                        <select class="form-control" name="searchBy">
							<option value="device">device</option>;
							<option value="manufacturer">manufacturer</option>;
							<option value="serial_number">serial number</option>;
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleSearch">Search:</label>
                        <input type="text" class="form-control" id="searchInput" name="searchInput">
                    </div>
					   	<input type="hidden" name="ids" value="">
                        <button type="submit" class="btn btn-primary" name="submitSearch" value="submitSearch">Search Equipment</button>
                   </form>
				   

               </div>
          </div>
     </section>
</body>
</html>
<?php
    if (isset($_POST['submitSearch']))
    {
		$search_input = $_POST['searchInput'];
		$search_by = $_POST['searchBy'];
		//check if search input is empty or just whitespaces
		if (strlen(trim($search_input)) == 0){
			echo "ERROR: Search input is empty";
			redirect("search.php?msg=Empty_Search");
		}
		
		//check if input is too long
		switch($_POST['searchBy']){
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
		if (strlen($search_input) > $max_len){
			echo "ERROR: Search input is too long, must be less than '$max_len' characters";
			redirect("search.php?msg=TooLong");
		}

		
		//add slahes to avoid sql injection
		$search_input = addslashes($search_input);
		
		//check if search is valid input
		$pattern = "/[^a-zA-Z0-9,\-\n\s]/";		
		if($dirty_data=preg_match($pattern, $search_input ) ){ // matches elements in array that meet regex pattern
            echo "ERROR: Search has invalid input";
			redirect("search.php?msg=InvalidInput_Search");
		}
		
		// TRUNACATE ARRAY bc our Php session and CPU cant handle all of the queries
		$first_num=5000;
		
		// search for equipments with the specified search by. Auto ids for the records will be passed to view page
		switch($_POST['searchBy']){
			case 'serial_number':
				$sql="Select `device_num` from `devices` where `serial_num`='$search_input' LIMIT $first_num"; 
				$rst=$dblink->query($sql);
				if ($rst->num_rows<=0)//serial number doesnt exist
				{
					echo "ERROR: Serial Number doesnt exist";
					redirect("search.php?msg=NoMatch_Search_SerialNum");
				}
				break;
			case 'device':
				$sql = "SELECT `device_type_num` FROM `device_types` WHERE `device_type_name`='$search_input'";
				$rst=$dblink->query($sql);
				if ($rst->num_rows<=0)//device doesnt exist
				{
					echo "ERROR: Device doesnt exist";
					redirect("search.php?msg=NoMatch_Search_Device");
				}
				$device_type_num=0;
				while($row = $rst->fetch_assoc()) { //this loop should essentially only loop once (one row with value we want)
					$device_type_num = $row["device_type_num"];
				}
				$sql="Select `device_num` from `devices` where `device_type_num`='$device_type_num' LIMIT $first_num"; 
				$rst=$dblink->query($sql) or
					die("<p>Something went wrong with $sql<br>".$dblink->error);
				break;
			case 'manufacturer':
				$sql = "SELECT `manufacturer_num` FROM `manufacturers` WHERE `manufacturer_name`='$search_input'";
				$rst=$dblink->query($sql);
				if ($rst->num_rows<=0)//manufacturer doesnt exist
				{
					echo "ERROR: Manufacturer doesnt exist";
					redirect("search.php?msg=NoMatch_Search_Manufacturer");
				}
				$manufacturer_num=0;
				while($row = $rst->fetch_assoc()) { //this loop should essentially only loop once (one row with value we want)
					$manufacturer_num = $row["manufacturer_num"];
				}
				$sql="Select `device_num` from `devices` where `manufacturer_num`='$manufacturer_num' LIMIT $first_num"; 
				$rst=$dblink->query($sql) or
					die("<p>Something went wrong with $sql<br>".$dblink->error);
				break;
			default:
				die("ERROR: no matched searchBY value when performing search query");
				break;
		}
		
		//if made it here without redirect or die(), search success
        if ($rst->num_rows>0)//search exists
        {
			// get ids for equipment records
			$ids = [];
			while($row = $rst->fetch_assoc()) {
    			array_push($ids, $row["device_num"]);
			}
//			echo "<p>'$ids'</p>";
			
//			// TRUNACATE ARRAY bc our Php session and CPU cant handle all of the queries
//			$first_num=2000;
//			$ids = array_slice($ids, 0, $first_num);
			
			// pass ids to $_SESSION for view.php
			$_SESSION['ids'] = $ids;
			
			// pass the searchBY value
            redirect("view.php?searchBy='$search_by'");
        }
	


	}
?>