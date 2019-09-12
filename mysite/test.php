<html>
<body>
<?php session_start(); ?>
<form method="POST" action="">
  <button type="submit" name = "is_producer"> Login as a Producer </button>
  <button type="submit" name = "is_shopkeeper"> Login as a Shopkeeper </button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // collect value of input field
    if (isset($_POST['is_producer'])) {
        $_SESSION['user_type'] = 'producer';
    } else if(isset($_POST['is_shopkeeper'])){
        $_SESSION['user_type'] = 'shopkeeper';
    } else{
		echo "Please identify yourself!";
	}
	header('location: login.php');
}
?>

</body>
</html>