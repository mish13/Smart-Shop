<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
	<title>Registration system PHP and MySQL</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body class="register">
	<?php include('errors.php'); ?>

	<form method="post" action="register.php">

		<div class="loginBox" style="top: 75%; height: 115%;">
			<img src="user.png" class="user">
			<h2>Smart Shop</h2>
			<form method="post" action="register.php">
				<p>First Name</p>
				<input type="text" name="firstname" placeholder="Enter First Name">
				<p>Last Name</p>
				<input type="text" name="lastname" placeholder="Enter Last Name">
				<p> Username</p>
				<input type="text" name="username" placeholder="Enter Username">
				<p>Email</p>
				<input type="text" name="email" placeholder="Enter Email">
				<p>Password</p>
				<input type="password" name="password_1" placeholder="Enter Password">
				<p>Confirm Password</p>
				<input type="password" name="password_2" placeholder="Confirm Password">
				<select name = "user_type" type = "submit" style="margin-bottom: 20px;">
					<option value="" disabled selected>Select your account type</option>
					<option value="Producer">Producer</option>
					<option value="Shopkeeper">Shopkeeper</option>
				</select>
				<input type="submit" name="reg_user" value="Register">
				<p>
					Already a member? <a href="login.php">Sign in</a>
				</p>
			</form>
		</div>

		<!-- <div class="input-group">
			<label>Username</label>
			<input type="text" name="username" value="<?php echo $username; ?>" required>
		</div>
		<div class="input-group">
			<label>First Name</label>
			<input type="text" name="firstname" value="<?php echo $firstname; ?>" required>
		</div>
		<div class="input-group">
			<label>Last Name</label>
			<input type="text" name="lastname" value="<?php echo $lastname; ?>" required>
		</div>
		<div class="input-group">
			<label>Email</label>
			<input type="email" name="email" value="<?php echo $email; ?>" required>
		</div>
		<div class="input-group">
			<label>Password</label>
			<input type="password" name="password_1" required>
		</div>
		<div class="input-group">
			<label>Confirm password</label>
			<input type="password" name="password_2" required>
		</div>
		<div class="input-group">
			<select name = "user_type" type = "submit">
				<option value="" disabled selected>Select your account type</option>
				<option value="Producer">Producer</option>
				<option value="Shopkeeper">Shopkeeper</option>
			</select>
		</div>
		<div class="input-group">
			<button type="submit" class="btn" name="reg_user">Register</button>
		</div>
		<p>
			Already a member? <a href="login.php">Sign in</a>
		</p>
	</form> -->
</body>
</html>