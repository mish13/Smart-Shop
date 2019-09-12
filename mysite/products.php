
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

	$product_name = "";
	if(isset($_GET['add']))
	{
		$product_name = $_GET['add'];
	}
	if(isset($_POST['add_the_product'])){
		$amount = $_POST['amount'];
		$product_id = 0;
		$query = "select product_id from products where product_name='$product_name'";
		$row = mysqli_query($db, $query);
		$row = mysqli_fetch_array($row);
		$product_id = $row['product_id'];
		$min_limit = $_POST['min_limit'];
		$query = "insert into shop_products (id, username, product_id, amount_available, min_limit) values('', '$username', '$product_id', '$amount', '$min_limit')";
		mysqli_query($db, $query);
		$product_name = "";
		header('location: products.php');
	}
	if(isset($_POST['update_the_product_sell'])){
		$amount = $_POST['amount'];
		$product_name = $_GET['update'];
		$query = "select product_id from products where product_name='$product_name'";
		$row = mysqli_query($db, $query);
		$row = mysqli_fetch_array($row);
		$product_id = $row['product_id'];
		$row = mysqli_query($db, "select amount_available from shop_products where product_id='$product_id' and username='$username'");
		$row = mysqli_fetch_array($row);
		$prev_amount = $row['amount_available'];
		$amount = $prev_amount - $amount;
		$min_limit = $_POST['min_limit'];
		$query = "update shop_products set amount_available='$amount', min_limit='$min_limit' where username='$username' and product_id='$product_id'";
		mysqli_query($db, $query);
		$query = "update shop_products set amount_available='$amount', min_limit='$min_limit' where username='$username' and product_id='$product_id'";
		mysqli_query($db, $query);
		$product_name = "";
		$query = "select distinct product_name from products as p, shop_products as s where s.product_id = p.product_id and amount_available < min_limit;";
		$query = mysqli_query($db, $query);

		while($row = mysqli_fetch_array($query))
		{
			$noti = new Notification($db, $username);
			$noti -> insertNotification($row['product_name'], $username, "limit_cross");
		}

	}
	if(isset($_POST['update_the_product_buy'])){
		$amount = $_POST['amount'];
		$product_name = $_GET['update'];
		$query = "select product_id from products where product_name='$product_name'";
		$row = mysqli_query($db, $query);
		$row = mysqli_fetch_array($row);
		$product_id = $row['product_id'];
		$row = mysqli_query($db, "select amount_available from shop_products where product_id='$product_id' and username='$username'");
		$row = mysqli_fetch_array($row);
		$prev_amount = $row['amount_available'];
		$amount += $prev_amount;
		$min_limit = $_POST['min_limit'];
		$query = "update shop_products set amount_available='$amount', min_limit='$min_limit' where username='$username' and product_id='$product_id'";
		mysqli_query($db, $query);
		$query = "update shop_products set amount_available='$amount', min_limit='$min_limit' where username='$username' and product_id='$product_id'";
		mysqli_query($db, $query);
		$product_name = "";
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
	<style>
		table {
		    font-family: arial, sans-serif;
		    border-collapse: collapse;
		    width: 100%;
		}

		td, th {
		    border: 1px solid #dddddd;
		    text-align: left;
		    padding: 8px;
		}

		tr:nth-child(even) {
		    background-color: #a8ad67;
		}
	</style>
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
			<a href="index.php?logout=1"> Logout </a>

		</nav>
	</div>
	<br><br><br><br><br><br><br>
	<div class="form" style="width: 600px;">
		<?php 
			$user = new User($db, $username);
			$user->getAllProducts();
			$user->generateProductDropDownButton();
		 ?>
		<?php if($product_name != ""): ?>
			<form method="post" action='<?php echo 'products.php?add='.$product_name; ?>'>
				Initial Amount: <input type="text"  class="text_product" name="amount" required> <br>
				Minimum Limit: <input type="text"  class="text_product" name="min_limit" required> <br>
				 <input type="submit" class="button" style="padding-bottom: 5px; width: 75px;" name="add_the_product">
			</form>
		<?php endif ?>
		<div class="dropdown" style="margin-top: 10px;">
		  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> Update A Product
		  <span class="caret"></span></button>
		  <ul class="dropdown-menu">
		    <li><a href="products.php?sell=1">Sell</a></li>
		    <li><a href="products.php?buy=1">Buy</a></li>
		  </ul>
		</div>
		<?php 
			if(isset($_GET['sell'])){
				$user->generateProductSellDropDownButton();
			}
			if(isset($_GET['buy'])) {
				$user->generateProductBuyDropDownButton();
			}
		?>
		<?php if(isset($_GET['sell']) && isset($_GET['update'])): ?>
			<?php 
				$product_name = $_GET['update'];
				$query = mysqli_query($db, "SELECT distinct min_limit from products as p, shop_products as s where s.product_id = p.product_id and username='$username' and product_name='$product_name'");
				$query = mysqli_fetch_array($query);
				$min_limit = $query['min_limit'];
			?>
			<form method="post" action='<?php echo 'products.php?sell=1&update='.$product_name; ?>'>
				Sold Amount: <input type="text" style="width: 70px;" name="amount" required> <br>
				Minimum Limit: <input type="text" style="width: 70px;" name="min_limit" value='<?php echo $min_limit?>'> <br>
				<input type="submit" name="update_the_product_sell">
			</form>
		<?php endif ?>

		<?php if(isset($_GET['buy']) && isset($_GET['update'])): ?>
			<?php 
				$product_name = $_GET['update'];
				$query = mysqli_query($db, "SELECT distinct min_limit from products as p, shop_products as s where s.product_id = p.product_id and username='$username' and product_name='$product_name'");
				$query = mysqli_fetch_array($query);
				$min_limit = $query['min_limit'];
			?>
			<form method="post" action='<?php echo 'products.php?buy=1&update='.$product_name; ?>'>
				Bought Amount: <input type="text" style="width: 70px;" name="amount" required> <br>
				Minimum Limit: <input type="text" style="width: 70px;" name="min_limit" value='<?php echo $min_limit?>'> <br>
				<input type="submit" name="update_the_product_buy">
			</form>
		<?php endif ?>
			
	</div>

</body>
</html>