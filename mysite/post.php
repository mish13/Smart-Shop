
<?php 
	include("classes/class.php");
	session_start(); 

	if (!isset($_SESSION['username'])) {
		$_SESSION['msg'] = "You must log in first";
		header('location: login.php');
	}
   $db = mysqli_connect('localhost', 'root', '', 'registration');
   $username = $_SESSION['username'];
   $id=0;
	if (isset($_GET['logout'])) {
		session_destroy();
		unset($_SESSION['username']);
		header("location: login.php");
	}

	if(isset($_POST['post']))
	{
		$post = new Post($db, $username);
		$post->submitPost($_POST['post_text']);
		unset($_POST['post']);
	}

	if(isset($_GET['id']))
	{
		$id=$_GET['id'];
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
	?>
	<div class="top_bar">
		<a class="logo" href="index.php"> Smart Shop </a>
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
			<a href="index.php"> 
				<i class="fas fa-home fa-lg"></i>
			</a>
			<a href="notification.php" onclick="getDropdownData('<?php echo $username; ?>', 'notification')">
				<i class="fa fa-bell fa-lg"></i>
				<?php
				if($num_notifications > 0)
				 echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
				?>
			</a>
			<a href="index.php?logout=1"> Logout </a>

		</nav>
	</div>
	<br><br><br><br><br><br><br><br><br>

	<div>
		<?php 
			$user = new User($db, $username);
			if($id)
				$user -> printPost($id);
		?>

	</div>

	

</body>
</html>