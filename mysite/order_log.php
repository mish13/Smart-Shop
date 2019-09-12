<?php 
	include("classes/class.php");
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
	$showing_user = "";
	if (isset($_GET['user'])) {
		$showing_user = $_GET['user'];
	} else $showing_user = $_SESSION['username'];
	$showing_user = strtolower($showing_user);
	
	$db = mysqli_connect('localhost', 'root', '', 'registration');
	$username = $_SESSION['username'];
	$query = "select username from profile_info where username='$showing_user'";
	$row = mysqli_query($db, $query);
	if(mysqli_num_rows($row) == 0){
		header('location: notfound.php');
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width initial-scale=1">

	<title> Order Log </title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<link rel="stylesheet" type="text/css" href="style.css">


</head>
<body class="profile" >

<div class="top_bar" style="top: 0;">
		<a class="logo" href="index.php"> Smart Shop </a>
			<!-- <div class="logo"> -->
				<!-- <a href="index.php">SuperShop</a> -->
			<!-- </div> -->
		<nav>

			<?php 

				$orderlog = new  OrderLog($db, $username);
				$notifications = new Notification($db,$username); 
				$num_notifications = $notifications-> getUnreadNumber();

			?>
			<a href="profile.php"> 
				<?php echo strtoupper($_SESSION['username']); ?>
			</a>
			<a href="index.php"> 
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
	<br><br><br><br>
	<?php 		
				$orderlog_obj = new OrderLog($db, $username);
				$orderlog_obj -> getAllOrderLog();

	?>


</body>
</html>

