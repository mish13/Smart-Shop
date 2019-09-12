<?php 
	session_start(); 

	if (!isset($_SESSION['username'])) {
		$_SESSION['msg'] = "You must log in first";
		header('location: login.php');
	}

	if (isset($_GET['logout'])) {
		session_destroy();
		unset($_SESSION['username']);
		header("location: login.php");
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>updateprofile</title>
	<link href="style.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
</head>
<body class="updateprofile">
	<?php
		include("classes/class.php");
		$db = mysqli_connect('localhost', 'root', '', 'registration');
		$errors = array();
	
		if(isset($_POST['upload']))
		{
			$username = $_SESSION['username'];
			$age = mysqli_real_escape_string($db, $_POST['age']);
			$address = mysqli_real_escape_string($db, $_POST['address']);
			$contact_no = mysqli_real_escape_string($db, $_POST['contact']);
			$about = mysqli_real_escape_string($db, $_POST['about']);
			$imageName = mysqli_real_escape_string($db, $_FILES["profile_pic"]["name"]);
			$imageType = mysqli_real_escape_string($db, $_FILES["profile_pic"]["type"]);
			$submit_ready = 1;
			if(empty($age)) {array_push($errors, "Age is Required!"); $submit_ready = 0;}
			if(empty($address)) {array_push($errors, "Address is Required!"); $submit_ready = 0;}
			if(empty($contact_no)) {array_push($errors, "Contanct No. is Required!"); $submit_ready = 0;}
			if(empty($about)) {array_push($errors, "About is Required!"); $submit_ready = 0;}
			if($submit_ready==1 && substr($imageType,0,5)=="image")
			{
				$imageData = mysqli_real_escape_string($db, file_get_contents($_FILES["profile_pic"]["tmp_name"]));
				$query = "DELETE from profile_info where username='$username'";
				mysqli_query($db, $query);
				$image_id = getImageId($db);
				$imageType = substr($imageType, 6, strlen($imageType));
				$target_file = "uploads/".$image_id.".".$imageType;
				move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
				$query="INSERT INTO profile_info (username, age,profile_pic,about,contact_no,is_active,address) values ('$username', '$age','$target_file','$about','$contact_no','1','$address')";
				mysqli_query($db,$query);
				header('location: profile.php');
			} else{
				array_push($errors, "File is not an image or No file found!"); $submit_ready = 0;
			}
		} 
		$user = array();
		if(isset($_SESSION['username']))
		{
			$username = $_SESSION['username'];
			$query = "SELECT * from profile_info where username='$username'";
			$query = mysqli_query($db, $query);
			$user = mysqli_fetch_array($query);
		}
	?>
	<!-- <div class= "header">
			<h2> Required Information </h2>
	</div>
	
	<form method="post" action="updateprofile.php" enctype="multipart/form-data">
		<?php include('errors.php'); ?>
		<div class = "input-group">
			<label> Age(<font color="red">*</font>) </label>
			<input type="text" name="age" value='<?php echo  $user['age'];?>'>
		</div>
		<div class="input-group">
			<label>Address(<font color="red">*</font>)</label>
			<input type="text" name="address" value='<?php echo  $user['address'];?>'>	
		</div>
		<div class="input-group">
			<label>Contact no.(<font color="red">*</font>)</label>
			<input type="text" name="contact" value='<?php echo  $user['contact_no'];?>'>
		</div>
		<div class="input-group">
			<label>About(<font color="red">*</font>)</label>
			<input type="text" name="about" value='<?php echo  $user['about'];?>'>
		</div>
		<div class="input-group">
			<label>Upload Profile Picture</label>
			<input type="file" name="file">
		</div>
		<div class="input-group">
				<input type="submit" name="upload">
		</div>
	</form> -->

	<h2 style="color: #555; margin-top: 30px;"> Required Information </h2>
	
	<form method="post" action="updateprofile.php" enctype="multipart/form-data">
		<?php include('errors.php'); ?>
		<div class="loginBox" style="top: 70%; height: 90%; width:450px; margin-bottom: 20px;">
			<img src="user.png" class="user">
			<h2>Smart Shop</h2>
			<form method="post" action="register.php">
				<p>Age</p>
				<input type="text" name="age" placeholder="Enter your age" value='<?php echo  $user['age'];?>'>
				<p>Address</p>
				<input type="text" name="address" placeholder="Enter your address" value='<?php echo  $user['address'];?>'>
				<p>Contact number</p>
				<input type="text" name="contact" placeholder="Enter Contact number" value='<?php echo  $user['contact_no'];?>'>
				<p>About</p>
				<input type="text" name="about" placeholder="About you and your work" value='<?php echo  $user['about'];?>'>
				<p style="margin-bottom: 10px;">Upload Profile Picture</p>
				<input type="file" name="profile_pic">
				<input type="submit" name="upload" value="Submit">
			</form>
		</div>
	</form>
	
</body>

</html>