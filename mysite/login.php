<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Registration system PHP and MySQL</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body class="login">
  <?php include('errors.php'); ?>
  <form method="post" action="login.php">
  	<div class="loginBox" style="height: 68%; top: 50%;">
      <img src="user.png" class="user">
      <h2>Smart Shop</h2>
        <p>Username</p>
        <input type="text" name="username" placeholder="Enter Username">
        <p>Password</p>
        <input type="password" name="password" placeholder="Enter Password">
        <select name = "user_type" type = "submit" style="margin-bottom: 20px;">
          <option value="" disabled selected>Select your account type</option>
          <option value="Producer">Producer</option>
          <option value="Shopkeeper">Shopkeeper</option>
        </select>
        <input type="submit" name="login_user" value="Sign In">
        <p>
          Not yet a member? <a href="register.php">Sign up</a>
        </p>
      </form>
    </div>
</body>
</html>