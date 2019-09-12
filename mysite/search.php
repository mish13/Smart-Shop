
<?php 
	include("classes/class.php");
	session_start(); 

	if (!isset($_SESSION['username'])) {
		$_SESSION['msg'] = "You must log in first";
		header('location: login.php');
	}
   $db = mysqli_connect('localhost', 'root', '', 'registration');
   $username = $_SESSION['username'];
   $user_type = $_SESSION['user_type'];

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
		
		$imageData = mysqli_real_escape_string($db, file_get_contents($_FILES["post_pic"]["tmp_name"]));
		$image_id = getImageId($db);
		$imageType = substr($imageType, 6, strlen($imageType));
		$target_file = "uploads/".$image_id.".".$imageType;
		if($image_added) move_uploaded_file($_FILES["post_pic"]["tmp_name"], $target_file);
		else $target_file = "no image";
		$post->submitPost($_POST['post_text'], $target_file);
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
	?>
	<div class="top_bar">
		<a class="logo" href="index.php"> Smart Shop </a>
			<!-- <div class="logo"> -->
				<!-- <a href="index.php">SuperShop</a> -->
			<!-- </div> -->
		<div class = "search-box">
			<form method="post">
				<input type="text"  style=" margin: auto;
											margin-top: 10px;
											height: 10px;
											width: 20%;
											position: fixed;
											left: 200px;
											top: 0;
											border-radius: 0;
											outline: 0px; 
											display: inline-block;" name="search_text">       <input type="submit" class="button_s" style="  margin: auto;
																															margin-top: 9px;
																															height: 30px;
																															width: 70px;
																															padding: 1px 2px;
																															position: fixed;
																															left: 500px;
																															top: 0;
																															border-radius: 0;
																															outline: 0px; 
																															display: inline-block;" name="search" value="Search">
			</form>
		</div>
		<nav>
			<a href="profile.php"> 
				<?php echo strtoupper($_SESSION['username']); ?>
			</a>
			<?php 

				$notifications = new  Notification($db, $username);
				$num_notifications = $notifications-> getUnreadNumber();

			?>
			<?php if($user_type=="Shopkeeper"): ?>
				<a href="products.php">
					My products
				</a>
			<?php endif; ?>
			<a href="index.php"> 
				<i class="fas fa-home fa-lg"></i>
			</a>
			<a href="notification.php"> 
				<i class="fas fa-bell fa-lg"></i> 
				<?php
					if($num_notifications > 0)
					 echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
				?>
			</a>
			<a href="index.php?logout=1"> Logout </a>

		</nav>
	</div>
			<!-- <div class="logo"> -->
				<!-- <a href="index.php">SuperShop</a> -->
			<!-- </div> -->

	<div class="wrapper" style="color: #999999;">
		
		
		<div class = "main_column column">
			<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data"> 
				<h1> Searched Results </h1>
				<br>
			</form>

			<?php 
				$text = "";
				if(isset($_GET['query']))
					$text = $_GET['query'];
				$user_obj = new User($db, $username);
				$user_obj -> getSearchedPosts($text);

			?>
			<br><strong>.</strong>
			<br><strong>.</strong>
			<br><strong>.</strong>

			<h1> No more Results </h1>
		</div>


		


	</div>

</div>

</body>
</html>