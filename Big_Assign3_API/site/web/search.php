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
					<div class="form-group">
					   	<input type="hidden" name="ids" value="">
				   </div>
					<div class="form-group">
					   <input type="checkbox" id="inactive_option" name="inactive_option" value="True">
						<label for="inactive_option">Include inactive equipment records</label><br>
				   </div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary" name="submitSearch" value="submitSearch">Search Equipment</button>
			   		</div>
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
		$inactive_option = $_POST['inactive_option'];
		
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
		$first_num=1000;
		
		// search for equipments with the specified search by. Auto ids for the records will be passed to view page
		switch($_POST['searchBy']){
			case 'serial_number':
				$result=api_call("search_serialNum", "search_input=".$search_input."&first_num=".$first_num."&inactive_option=".$inactive_option);
				$ids=get_payload($result);

				if (!isset($ids) || count($ids) == 0)//serial number doesnt exist
				{
					echo "ERROR: Serial Number doesnt exist";
					redirect("search.php?msg=NoMatch_Search_SerialNum");
				}
				break;
			case 'device':
				$result=api_call("search_device", "search_input=".$search_input."&first_num=".$first_num."&inactive_option=".$inactive_option);
				$ids=get_payload($result);

				if (!isset($ids) || count($ids) == 0)//device doesnt exist
				{
					echo "ERROR: Device doesnt exist";
					redirect("search.php?msg=NoMatch_Search_Device");
				}
				break;
			case 'manufacturer':
				$result=api_call("search_manufacturer", "search_input=".$search_input."&first_num=".$first_num."&inactive_option=".$inactive_option);
				$ids=get_payload($result);

				if (!isset($ids) || count($ids) == 0)//manufacturer doesnt exist
				{
					echo "ERROR: Manufacturer doesnt exist";
					redirect("search.php?msg=NoMatch_Search_Manufacturer");
				}
				break;
			default:
				die("ERROR: no matched searchBY value when performing search query");
				break;
		}
		// if logic reaches this line, there were search matches
		// pass ids to $_SESSION for view.php
		$_SESSION['ids'] = $ids;

		// pass the searchBY value
		redirect("view.php?searchBy='$search_by'");	

	}
?>