
<?php 
	include("classes/class.php");
	session_start(); 

	if (!isset($_SESSION['username'])) {
		$_SESSION['msg'] = "You must log in first";
		header('location: login.php');
	}
   $db = mysqli_connect('localhost', 'root', '', 'registration');
   $username = $_SESSION['username'];

	if (isset($_GET['logout'])) {
		session_destroy();
		unset($_SESSION['username']);
		header("location: login.php");
	}

	if(isset($_POST['post']))
	{
		$post = new Post($db, $username);
		$imageName = mysqli_real_escape_string($db, $_FILES["post_pic"]["name"]);
		$imageType = mysqli_real_escape_string($db, $_FILES["post_pic"]["type"]);
		$image_added = 0;
		if(substr($imageType,0,5)=="image") $image_added = 1;
		if($image_added) $imageData = mysqli_real_escape_string($db, file_get_contents($_FILES["post_pic"]["tmp_name"]));
		$image_id = getImageId($db);
		$imageType = substr($imageType, 6, strlen($imageType));
		$target_file = "uploads/".$image_id.".".$imageType;
		if($image_added) move_uploaded_file($_FILES["post_pic"]["tmp_name"], $target_file);
		else $target_file = "no image";
		$post->submitPost($_POST['post_text'], $target_file);
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

		$orderlog = new OrderLog($db, $username);
		$orderlog->insertOrderLog($post_id, $user_to, $quantity);

	}
	if(isset($_POST['search']))
	{
		header("location: search.php?query=".$_POST['search_text']);
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Newsfeed</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="js/demo.js"></script>
</head>
<body class="index">
	<?php
	   $sql = "SELECT * FROM profile_info WHERE username='$username'";
	   $mq = mysqli_query($db, $sql) or die ("not working query");
	   $row = mysqli_fetch_array($mq) or header('location: updateprofile.php');
	   $sql = "SELECT * FROM users WHERE username='$username'";
	   $mq = mysqli_query($db, $sql) or die ("not working query");
	   $users = mysqli_fetch_array($mq) or die("I am confused!");
	   $s=$row['profile_pic'];
	   $user_type = $_SESSION['user_type'];
	?>
	<div class="top_bar">
		<a class="logo" href="index.php"> Smart Shop </a>
		<div>
			<form method="post">
				<input type="text"  style=" margin: auto;
											margin-top: 10px;
											height: 10px;
											width: 20%;
											position: fixed;
											left: 200px;
											border-radius: 0;
											outline: 0px; 
											display: inline-block;" name="search_text">       <input type="submit" class="button_s" style="  margin: auto;
																															margin-top: 9px;
																															height: 30px;
																															width: 70px;
																															padding: 1px 2px;
																															position: fixed;
																															left: 500px;
																															border-radius: 0;
																															outline: 0px; 
																															display: inline-block;" name="search" value="Search">
			</form>
		</div>
			<!-- <div class="logo"> -->
				<!-- <a href="index.php">SuperShop</a> -->
			<!-- </div> -->
		<nav>

			<?php

			$notifications = new  Notification($db, $username);
			$num_notifications = $notifications-> getUnreadNumber();

			?>

			<a href="profile.php"> 
				<?php echo strtoupper($_SESSION['username']); ?>
			</a>
			<?php if($user_type=="Shopkeeper"): ?>
				<a href="products.php">
					My products
				</a>
			<?php endif; ?>
			<a href="#"> 
				<i class="fas fa-home fa-lg"></i>
			</a>
			<a href="order_log.php">
				<i class="fa fa-list-alt fa-lg"></i>
			</a>
			<a href="notification.php" onclick="getDropdownData('<?php echo $username; ?>', 'notification')">
				<i class="fa fa-bell fa-lg"></i>
				<?php
				if($num_notifications > 0)
				 echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
				?>
			</a>
			<a href="index.php?logout=1"> 
				<i class="glyphicon glyphicon-log-out"></i>
		    </a>

		</nav>
	</div>

	<div class="wrapper" style="color: #bbbbbb;">
		<div class="user_details column_column" style="margin-top: 30px;">
			<a class="inline" href="profile.php"> <?php echo '<img src="'.$s.'"  class="img-responsive img-circle user_index_profile" >'; ?></a>
		 	<a class="inline" href="profile.php">
		 		<?php echo $users['firstname']." ".$users['lastname']; ?>
		 	</a>
		</div>
		
		<div class = "main_column column" style="margin-top: 20px; margin-bottom: 40px;">
			<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
				<textarea name="post_text" id="post_text" placeholder="Got Something to post?"> </textarea>
				<input type="submit" name="post" id="post_button" value="Post" style="color: #ffffff;">
				<br>
				<input type="file" name="post_pic" value="Choose Image">
			</form>

			<?php 
				$user_obj = new User($db, $username);
				$user_obj -> getAllPosts();

			?>
		</div>


		


	</div>

</div>

</body>
</html>