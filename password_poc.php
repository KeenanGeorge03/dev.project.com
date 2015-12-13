<?php

include_once ("includes/password_manager.php");
$plain_password = 'george';
$original = create_hash($plain_password);

echo $original;

echo "<br />";

$password = 'sha256:1000:tnyxtlPZfhAZ4ChDgnZYClz3+TNQCO/c:uNYrfmuV40BKrbVz5A1q2dIjfE6DUhwO';

echo validate_password($plain_password,$password);
	
			session_start();
			$_session['check_login'] = true;
			
		$user = $_POST['username'];
		$pass = $_POST['password'];
		
			
	
		if($user =='') 	{
							echo "The Username Field is Empty";
					}
		
		if($pass =="")	{	

							echo "The Password Field is Empty";
							
						}

			echo "<br/>"
			echo "<br/>"
					<a href = "login.php"> PLEASE CLICK HERE TO TRY AGAIN   </a>
			echo "<br/>"
			echo "<br/>"
			  	
			$query = 	"SELECT *
						FROM authentication_tbl
						WHERE username = '$user' AND password = '$pass'";
						$result = mysqli_query($connection,$query);
						$count = mysqli_num_rows ($result);
						if ($count==1){	
						
							header("location: http://localhost/Project_5/content.php");
						}
			else {
						echo "Wrong Username or Password";
			}	
			
	?>
