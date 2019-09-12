<?php 
	session_start();

	// variable declaration
	$username = "";
	$email    = "";
	$firstname = "";
	$lastname = "";
	$errors = array(); 
	$_SESSION['success'] = "";

	// connect to database
	$db = mysqli_connect('localhost', 'root', '', 'registration');
	
	// REGISTER USER
	if (isset($_POST['reg_user'])) {
		// receive all input values from the form
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
		$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
		$firstname = mysqli_real_escape_string($db, $_POST['firstname']);
		$lastname = mysqli_real_escape_string($db, $_POST['lastname']);
		$user_type = "";
		if(isset($_POST['user_type'])){
			$user_type = mysqli_real_escape_string($db, $_POST['user_type']);
		}
		else {
			array_push($errors, "Account Type is required");
		}

		// form validation: ensure that the form is correctly filled
		if (empty($username)) { array_push($errors, "Username is required"); }
		if (empty($email)) { array_push($errors, "Email is required"); }
		if (empty($password_1)) { array_push($errors, "Password is required"); }

		if ($password_1 != $password_2) {
			array_push($errors, "The two passwords do not match");
		}

		// register user if there are no errors in the form
		if (count($errors) == 0) {
			$password = md5($password_1);//encrypt the password before saving in the database
			$query = "INSERT INTO users (username, firstname, lastname, email, password, user_type, num_posts) 
					  VALUES('$username', '$firstname', '$lastname', '$email', '$password', '$user_type', 0)";
			mysqli_query($db, $query);

			$_SESSION['username'] = $username;
			$_SESSION['user_type'] = $user_type;
			$_SESSION['success'] = "You are now logged in";
			header('location: updateprofile.php');
		}

		

		// if(isset($_POST['upload']))
		// {
		// 	array_push($errors, "I am here!");
		// 	$age = mysqli_real_escape_string($db, $_POST['age']);
		// 	$address = mysqli_real_escape_string($db, $_POST['address']);
		// 	$contact_no = mysqli_real_escape_string($db, $_POST['contact']);
		// 	$about = mysqli_real_escape_string($db, $_POST['about']);
		// 	$imageName = mysqli_real_escape_string($_FILES["image"]["name"]);
		// 	$imageData = mysqli_real_escape_string(file_get_contents($_FILES["image"]["tmp_name"]));
		// 	$imageType = mysqli_real_escape_string($_FILES["image"]["type"]);

		// 	//if(substr($imageType,0,5)=="image")
		// 	//{
		// 		echo "dhuksi";
		// 		// $query="INSERT INTO profile_info (username,profile_pic,about,contact_no,is_active,addresss) values ('$username','$imageData','$about','$contact_no',1,'$address'))";
		// 		$query = "INSERT INTO users (username, email, password, user_type) 
		// 			  VALUES('1', '2', '3', '4')";
		// 		mysqli_query($db,$query);
		// 		header('location: index.php');
		// 	//}
		// }

	}

	// ... 



	if(isset($_POST['upload']))
	{
		$age = mysqli_real_escape_string($db, $_POST['age']);
		$address = mysqli_real_escape_string($db, $_POST['address']);
		$contact_no = mysqli_real_escape_string($db, $_POST['contact']);
		$about = mysqli_real_escape_string($db, $_POST['about']);
		$imageName = mysqli_real_escape_string($_FILES["image"]["name"]);
		$imageData = mysqli_real_escape_string(file_get_contents($_FILES["image"]["tmp_name"]));
		$imageType = mysqli_real_escape_string($_FILES["image"]["type"]);

		if(substr($imageType,0,5)=="image")
		{
			echo "dhuksi";
			$query="INSERT INTO profile_info (username,profile_pic,about,contact_no,is_active,addresss) values ('$username','$imageName','$about','$contact_no',1,'$address'))";
			mysqli_query($db,$query);
			//header('location: userprofile.php');
		}
	}

	// LOGIN USER
	if (isset($_POST['login_user'])) {
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$password = mysqli_real_escape_string($db, $_POST['password']);
		$user_type = "";
		if(isset($_POST['user_type'])){
			$user_type = mysqli_real_escape_string($db, $_POST['user_type']);
		}
		else {
			array_push($errors, "Account Type is required");
		}
		if (empty($username)) {
			array_push($errors, "Username is required");
		}
		if (empty($password)) {
			array_push($errors, "Password is required");
		}
		

		if (count($errors) == 0) {
			$password = md5($password);
			$query = "SELECT * FROM users WHERE username='$username' AND password='$password' AND user_type='$user_type'";
			$results = mysqli_query($db, $query);

			if (mysqli_num_rows($results) == 1) {
				$_SESSION['username'] = $username;
				$_SESSION['user_type'] = $user_type;
				$_SESSION['success'] = "You are now logged in";
				header('location: index.php');
			}else {
				array_push($errors, "Wrong username/password/account type combination");
			}
		}
	}

	if(isset($_POST['order']))
	{
		$quantity=$_POST['quantity'];
		$post_id = $_GET['added_to_cart'];
		$query = "select * from posts where id = '$post_id'";
		$query = mysqli_query($db, $query);
		$row = mysqli_fetch_array($query);
		$user_to = $row['added_by'];

		$notification = new Notification($db, $username);
		$notification->insertNotification($post_id, $user_to, "order", $quantity);
	}

?>