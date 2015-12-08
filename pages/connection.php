
<?php 
// Create database connection
     $server_name = "journeyatrest.com";
	 $user_name = "keenangeorge";
     $password = "C1JSkScZ4gWkLTTvWQm4";	
     $db_name = "george";
     $connection = mysqli_connect ($server_name, $user_name, $password);
     if (!$connection)
     {

        die ("Database connection failed: " . mysqli_error());
     }
		// Select a database to use
	
	$db_select = mysqli_select_db ($connection,"db_name");
        if (!$db_select)
        {
            die("Database selection failed: " . mysqli_error());
        }	      
?>
