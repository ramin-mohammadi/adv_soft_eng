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
//				   		if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="InvalidInput_Search")
//						{
//							echo '<div class="alert alert-danger" role="alert">Invalid input for Search</div>';
//						}
				   		
				   
				  		if(isset($_SESSION['ids']) && isset($_REQUEST['searchBy'])){
							$searchBy = $_REQUEST['searchBy'];
							$ids = $_SESSION['ids'];
							echo "<p>Search By: '$searchBy'</p>";
//							for($i=0; $i<10; $i++){
//								echo $ids[$i];
//								echo "<br>";  
//							}
							foreach($ids as $id){
								echo $id;
								echo "<br>";  
							}
				   
//						<h2>Bootstrap List with Links</h2>
//						<ul class="list-group">
//							<li class="list-group-item"><a href="#">Item 1</a></li>
//							<li class="list-group-item"><a href="#">Item 2</a></li>
//							<li class="list-group-item"><a href="#">Item 3</a></li>
//							<li class="list-group-item"><a href="#">Item 4</a></li>
//							<li class="list-group-item"><a href="#">Item 5</a></li>
//						</ul>
						}
//				   		unset($_SESSION['ids']);
					?>

               </div>
          </div>
     </section>
</body>
</html>
