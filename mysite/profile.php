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
	
	$username = $_SESSION['username'];
	$db = mysqli_connect('localhost', 'root', '', 'registration');
	$query = "select username from profile_info where username='$showing_user'";
	$row = mysqli_query($db, $query);
	if(mysqli_num_rows($row) == 0){
		header('location: notfound.php');
	}
	if(isset($_POST['ufollow'])){
		$query = "select * from followers where follower='$username' and followed='$showing_user'";
		$row = mysqli_query($db, $query);
		if(!$row || mysqli_num_rows($row)==0){
			$query = "INSERT INTO followers (follower, followed) 
					  VALUES('$username', '$showing_user')";
			mysqli_query($db, $query);
			$noti = new Notification($db, $username);
			$noti -> insertNotification($showing_user, $showing_user, "follow");
		} else{
			$query = "delete from followers where follower='$username' and followed='$showing_user';";
			mysqli_query($db, $query);
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width initial-scale=1">
	<?php
		$db = mysqli_connect('localhost', 'root', '', 'registration');
		$query="select firstname,lastname from users where username='$showing_user'";
		$result= mysqli_query($db,$query);
		$row=$result-> fetch_array();
		$userFL = $row['firstname']." ".$row['lastname'];
	?>
	<title> <?php echo $userFL ?> </title>

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="js/demo.js"></script>


</head>
<body class="profile" >
	<div class="top_bar">
		<a class="logo" href="index.php"> Smart Shop </a>
		
			<!-- <div class="logo"> -->
				<!-- <a href="index.php">SuperShop</a> -->
			<!-- </div> -->
		<nav>

			<?php

				$notifications = new  Notification($db, $username);
				$num_notifications = $notifications-> getUnreadNumber();
				$user_type = $_SESSION['user_type'];
			?>

			<a href="profile.php"> 
				<?php echo strtoupper($_SESSION['username']); ?>
			</a>
			<?php if($user_type=="Shopkeeper"): ?>
				<a href="products.php">
					My products
				</a>
			<?php endif; ?>
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
	<br><br><br><br><br>
	<div class="img-profile">
	<div class= "profile-pic extra-margin" align="center">
		<?php
		$db=mysqli_connect('localhost', 'root', '', 'registration');
		$query = "select profile_pic from profile_info where username='$showing_user'";
		$sth= $db->query($query);
		$result=mysqli_fetch_assoc($sth);
		$profile_pic = $result['profile_pic'];
		// echo '<img src="data:image/jpeg;base64,'.base64_encode( $result['profile_pic'] ).'"/ class="img-responsive img-circle circle-img">';
	    //echo $result['profile_pic'];
	    echo '<img src="'.$profile_pic.'"  class="img-responsive img-circle circle-img" >';
		?>
	</div>
	<div class= "profile-name" align="center">
		<h2 style="color: #555555;">
			<?php
				$db = mysqli_connect('localhost', 'root', '', 'registration');
				$query="select firstname,lastname from users where username='$showing_user'";
				$result= mysqli_query($db,$query);
				$row=$result-> fetch_array();
				echo $row['firstname']." ".$row['lastname'];
			?>
			
		</h2>
	</div>
	<div class= "profile-job" align="center">
		<h3>
			<?php
				$db = mysqli_connect('localhost', 'root', '', 'registration');
				$query="select user_type from users where username='$showing_user'";
				$result= mysqli_query($db,$query);
				$row=$result-> fetch_array();
				echo $row['user_type'];
			?>
		</h3>
		<?php 
			$follow_btn_text = "Follow";
			if(isFollowing($db, $username, $showing_user)) $follow_btn_text = "Unfollow";
		?>
		<div style="margin: 0 0 0 -60px;">
			<?php if($username != $showing_user): ?>
				<form method="post" style="display: inline-block;">
					<input  type="submit" class="btn btn-success btn-sm" value="<?php echo $follow_btn_text; ?>" name="ufollow"  style="margin-left: 54px; margin-bottom: 15px;">
				</form>
			<?php endif; ?>	
		</div>
	</div>
	<hr>
	<!-- <div class="profile-button" align="center">
		<button class= "btn btn-success btn-sm">Follow</button>
	</div> -->
	<div class="about sidebar">
		<h3>
			<i class="glyphicon glyphicon-user">About</i>
		</h3>
		<h4>
			<?php
				$db = mysqli_connect('localhost', 'root', '', 'registration');
				$query="select about from profile_info where username='$showing_user'";
				$result= mysqli_query($db,$query);
				while ($row=mysqli_fetch_assoc($result)) {
					foreach($row as $cname => $cvalue)
					{
				        print "$cvalue\t";
				    }
				    print "\r\n";
				}
			?>
		<h4>
	</div>
	<hr>
	<div class="address sidebar">
		<h3>
			<i class="glyphicon glyphicon-home">Address</i>
		</h3>
		<h4>
		<?php
		$db = mysqli_connect('localhost', 'root', '', 'registration');
		$query="select address from profile_info where username='$showing_user'";
		$result= mysqli_query($db,$query);
		while ($row=mysqli_fetch_assoc($result)) {
					foreach($row as $cname => $cvalue)
					{
				        print "$cvalue\t";
				    }
				    print "\r\n";
				}
		?>
	</h4>
	</div>
	<hr>
	<div class="contact_no sidebar" style="margin-bottom: 15px;">
	<h3>
			<i class="glyphicon glyphicon-flag">Contact-no.</i>
		</h3>
		<h4>
		<?php
			$db = mysqli_connect('localhost', 'root', '', 'registration');
			$query="select contact_no from profile_info where username='$showing_user'";
			$result= mysqli_query($db,$query);
			while ($row=mysqli_fetch_assoc($result)) {
					foreach($row as $cname => $cvalue)
					{
				        print "$cvalue\t";
				    }
				    print "\r\n";
				}
		?>
		</h4>
	</div>
	<hr>
	<?php if($_SESSION['username'] == $showing_user): ?>

		<button  type="button" class="btn btn-success btn-sm" name="editprofile" onclick ="location.href='updateprofile.php'" 
					style="margin-left: 54px; margin-bottom: 15px;">Edit Profile
		</button>
	<?php endif; ?>
</div>

</body>

</html>