<?php 
	class User{
		private $user;
		private $con;
		public function __construct($con, $user) {
			$this->con = $con;
			$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
			$this->user = mysqli_fetch_array($user_details_query);
		}

		public function getFirstAndLastName(){
			$username = $this->user['username'];
			$query = mysqli_query($this->con, "SELECT firstname, lastname from users where username='$username'");
			$row = mysqli_fetch_array($query);
			return $row['firstname']. " " . $row['lastname'];
		}
		public function getNumOfPosts(){
			return $this->user['num_posts'];
		}
		public function getUserName(){
			return $this->user['username'];
		}

		public function getProfilePic()
		{
			$username = $this->getUserName();
			$query = mysqli_query($this->con, "SELECT profile_pic from profile_info where username='$username'");
			return mysqli_fetch_array($query)['profile_pic'];
		}
		public function getAllProducts(){
			$ret = " <table>
						  <tr>
						    <th>Product Name</th>
						    <th>Amount Avaiable</th>
						    <th>Minimum Amount Needed</th>
						  </tr>
						  ";
			$username = $this->getUserName();
			$query = mysqli_query($this->con, "SELECT distinct product_name, amount_available, min_limit from products as p, shop_products as s where s.product_id = p.product_id and username='$username'");
			while($query && $row = mysqli_fetch_array($query)){
				$ret .= '<tr>
					     <th>'. ucwords($row['product_name']) .'</th>
					     <th>'. $row['amount_available'] .'</th>
					     <th>'. $row['min_limit'] .'</th>
					    </tr>
					   ';
			}
			$ret .= '</table>';
			echo $ret;
		}
		
		public function generateProductSellDropDownButton()
		{
			$ret = '<div class="dropdown" style="margin-top: 10px;">
    					<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> Select Sold Product
					<span class="caret"></span></button>
    				<ul class="dropdown-menu">
    				';
    		$username = $this->getUserName();
			$query = mysqli_query($this->con, "SELECT distinct product_name from products as p, shop_products as s where s.product_id = p.product_id and username='$username'");
			while($query && $row = mysqli_fetch_array($query)){
				$ret .= '<li><a href="products.php?sell=1&update='. $row['product_name'] .'">'. $row['product_name'] .'</a></li>
					   ';
			}
			$ret .= '</ul>
  				</div>';
  			echo $ret;
		}
		public function generateProductBuyDropDownButton()
		{
			$ret = '<div class="dropdown" style="margin-top: 10px;">
    					<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> Select Bought Product
					<span class="caret"></span></button>
    				<ul class="dropdown-menu">
    				';
    		$username = $this->getUserName();
			$query = mysqli_query($this->con, "SELECT distinct product_name from products as p, shop_products as s where s.product_id = p.product_id and username='$username'");
			while($query && $row = mysqli_fetch_array($query)){
				$ret .= '<li><a href="products.php?buy=1&update='. $row['product_name'] .'">'. $row['product_name'] .'</a></li>
					   ';
			}
			$ret .= '</ul>
  				</div>';
  			echo $ret;
		}
		public function generateProductDropDownButton()
		{
			$ret = '<div class="dropdown" style="margin-top: 10px;">
    					<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> Add a product
					<span class="caret"></span></button>
    				<ul class="dropdown-menu">
    				';
    		$username = $this->getUserName();
			$query = mysqli_query($this->con, "select product_name from products where product_name not in (SELECT distinct product_name from products as p, shop_products as s where s.product_id = p.product_id and username='$username')");
			while($query && $row = mysqli_fetch_array($query)){
				$ret .= '<li><a href="products.php?add='. $row['product_name'] .'">'. ucwords($row['product_name']) .'</a></li>
					   ';
			}
			$ret .= '</ul>
  				</div>';
  			echo $ret;

		}
		public function getAllPosts(){
			$username = $this->getUserName();
			$query = mysqli_query($this->con, "SELECT distinct posts.id as id, body, added_by, date_added, likes, image_id from posts, followers where deleted = 'no' and  (added_by='$username' or (follower='$username' and followed=added_by)) order by posts.id desc");
			$ret = "";
			$row = array();
			$cnt = 0;
			while($query && $row = mysqli_fetch_array($query)){
				if($cnt == 20) break;
				$added_by = $row['added_by'];
				$added_by_obj = new User($this->con, $added_by);
				$profile_pic = $added_by_obj->getProfilePic();
				$date_added = $row['date_added'];
				$likes = $row['likes'];
				$body = $row['body'];
				date_default_timezone_set('Asia/Dhaka');
				$hehe = date("Y-m-d H:i:s");
				$nowtime = new DATETIME($hehe);
				$post_time = new datetime($date_added);
				$interval = $post_time->diff($nowtime);
				$ago = "<b><div class=\"ago\">";
				$id = $row['id'];
				if($interval->y == 1){
					$ago .= "one year ago";
				} else if($interval->y > 1){
					$ago .= $interval.y ." years ago";
				} else{
					if($interval->m == 1){
					$ago .= "one month ago";
					} else if($interval->m > 1){
						$ago .= $interval->m ." months ago";
					} else{
						if($interval->d == 1){
							$ago .= "one day ago";
						} else if($interval->d > 1){
							$ago .= $interval->d ." days ago";
						} else{
							if($interval->h == 1){
								$ago .= "one hour ago";
							} else if($interval->h > 1){
								$ago .= $interval->h ." hours ago";
							} else{
								if($interval->i == 1){
									$ago .= "one minute ago";
								} else if($interval->i > 1){
									$ago .= $interval->i ." minutes ago";
								} else{
									$ago .= "just now";
								}
							}
						}
					}
				}

				$ago.="</b></div><br>";

				// herea
				$img_a = '<img src="'.$profile_pic.'"  class="post_pro_pic" width=50 >';
				$body = nl2br($body);
				$name = $added_by_obj->getFirstAndLastName();
				$addtocart = "";
				$user_type = $added_by_obj->user['user_type'];
				$image = $row['image_id'];
				if($image == "no image" || $image =="") $image = "";
				else $image = "<img src=".$image." style=\"max-width:600px; border-radius: 6px;\">";
				if($user_type=="Shopkeeper")
					$addtocart="</div>";
				else
					$addtocart="<button class=\"button\" onclick=\"document.getElementById('modal-wrapper').style.display='block'\" style=\"width:100px; height: 20px;text-align: center center; margin-left:30px; margin-bottom:10px;\">
								Order</button>

								<div id=\"modal-wrapper\" class=\"modal\">
								  
								  <form class=\"modal-content animate\" action=\"index.php?added_to_cart=$id\" method=\"POST\">
								        
								    <div class=\"imgcontainer\">
								      <span onclick=\"document.getElementById('modal-wrapper').style.display='none'\" class=\"close\" title=\"Close\">&times;</span>
								      <img src=\"addtocart.png\" alt=\"Avatar\" class=\"avatar\">
								      <h1 style=\"text-align:center\">Order Quantity Needed</h1>
								    </div>

								    <div class=\"container\">
								      <input type=\"text\" placeholder=\"Quantity\" name=\"quantity\"> 
								     </div>
								     <div class= \"container\">     
								      <button class=\"button_b\" type=\"submit\" name=\"order\">Order</button>   
								     </div>
								      
								    </div>
								    
								  </form>
								  
								</div>

								<script>
								var modal = document.getElementById('modal-wrapper');
								window.onclick = function(event) {
								    if (event.target == modal) {
								        modal.style.display = \"none\";
								    }
								}
								</script>";
				$ret .= "<div class=\"post_class column\">
							<div class=\"pro_pic_class\">
								<a href='profile.php?user=$added_by' >
									$img_a
								</a>
								<a href='profile.php?user=$added_by'>
									$name
								</a>
								&nbsp&nbsp&nbsp $ago
							</div> 
							$body
							<br>
							$image;
							<div class='newsfeedPostOptions'>
								<iframe src='like.php?post_id=$id' scrolling='no' style='background: #555;'></iframe>
							
						";
				$ret.=$addtocart;
				$ret.="</div>";
				$cnt++;

			}
			
			echo $ret;

		}
		public function getSearchedPosts($text){
			$username = $this->getUserName();
			if(substr($text,0,1)=='-') $text[0] = '#';
			$query = mysqli_query($this->con, "SELECT distinct posts.id as id, body, added_by, date_added, likes, image_id from posts, followers where deleted = 'no' and (body like '%$text%' or added_by like '%$text%') order by posts.id desc");
			$ret = "";
			$row = array();
			$cnt = 0;
			while($query && $row = mysqli_fetch_array($query)){
				if($cnt == 20) break;
				$added_by = $row['added_by'];
				$added_by_obj = new User($this->con, $added_by);
				$profile_pic = $added_by_obj->getProfilePic();
				$date_added = $row['date_added'];
				$likes = $row['likes'];
				$body = $row['body'];
				$image = $row['image_id'];
				$id = $row['id'];
				/*if(substr($text,0,1)=='-') $text[0] = '#';*/
				date_default_timezone_set('Asia/Dhaka');
				$hehe = date("Y-m-d H:i:s");
				$nowtime = new DATETIME($hehe);
				$post_time = new datetime($date_added);
				$interval = $post_time->diff($nowtime);

				$ago = "";
				if($interval->y == 1){
					$ago .= "one year ago";
				} else if($interval->y > 1){
					$ago .= $interval.y ." years ago";
				} else{
					if($interval->m == 1){
					$ago .= "one month ago";
					} else if($interval->m > 1){
						$ago .= $interval->m ." months ago";
					} else{
						if($interval->d == 1){
							$ago .= "one day ago";
						} else if($interval->d > 1){
							$ago .= $interval->d ." days ago";
						} else{
							if($interval->h == 1){
								$ago .= "one hour ago";
							} else if($interval->h > 1){
								$ago .= $interval->h ." hours ago";
							} else{
								if($interval->i == 1){
									$ago .= "one minute ago";
								} else if($interval->i > 1){
									$ago .= $interval->i ." minutes ago";
								} else{
									$ago .= "just now";
								}
							}
						}
					}
				}

				// herea
				$img_a = '<img src="'.$profile_pic.'"  class="post_pro_pic" width=50 >';
				$body = nl2br($body);
				$name = $added_by_obj->getFirstAndLastName();
				$user_type = $added_by_obj->user['user_type'];
				$image = $row['image_id'];
				if($image == "no image" || $image =="") $image = "";
				else $image = "<img src=".$image." style=\"max-width:600px; border-radius: 6px;\">";
				if($user_type=="Shopkeeper")
					$addtocart="</div>";
				else
					$addtocart="<button class=\"button\" onclick=\"document.getElementById('modal-wrapper').style.display='block'\" style=\"width:100px; height: 20px;text-align: center center; margin-left:30px; margin-bottom:10px;\">
								Order</button>

								<div id=\"modal-wrapper\" class=\"modal\">
								  
								  <form class=\"modal-content animate\" action=\"index.php?added_to_cart=$id\" method=\"POST\">
								        
								    <div class=\"imgcontainer\">
								      <span onclick=\"document.getElementById('modal-wrapper').style.display='none'\" class=\"close\" title=\"Close\">&times;</span>
								      <img src=\"addtocart.png\" alt=\"Avatar\" class=\"avatar\">
								      <h1 style=\"text-align:center\">Order Quantity Needed</h1>
								    </div>

								    <div class=\"container\">
								      <input type=\"text\" placeholder=\"Quantity\" name=\"quantity\"> 
								     </div>
								     <div class= \"container\">     
								      <button class=\"button_b\" type=\"submit\" name=\"order\">Order</button>   
								     </div>
								      
								    </div>
								    
								  </form>
								  
								</div>

								<script>
								// If user clicks anywhere outside of the modal, Modal will close

								var modal = document.getElementById('modal-wrapper');
								window.onclick = function(event) {
								    if (event.target == modal) {
								        modal.style.display = \"none\";
								    }
								}
								</script>";
				$ret .= "<div class=\"post_class column\">
							<div class=\"pro_pic_class\">
								<a href='profile.php?user=$added_by' >
									$img_a
								</a>
								<a href='profile.php?user=$added_by'>
									$name
								</a>
								&nbsp&nbsp&nbsp $ago
							</div> 
							$body
							<br>
							$image;
							<div class='newsfeedPostOptions'>
								<iframe src='like.php?post_id=$id' scrolling='no' style='background: #555;'></iframe>
							
						";
				$ret.=$addtocart;
				$ret.="</div>";


				$cnt++;
			}
			
			echo $ret;

		}
		public function printPost($id)
		{
			$username = $this->getUserName();
			$query = mysqli_query($this->con, "SELECT distinct posts.id as  id, image_id, body, added_by, date_added, likes from posts, followers where posts.id='$id' and deleted = 'no' and  (added_by='$username' or (follower='$username' and followed=added_by)) order by posts.id desc");
			$ret = "";
			$row = array();
			while($query && $row = mysqli_fetch_array($query)){
				$added_by = $row['added_by'];
				$added_by_obj = new User($this->con, $added_by);
				$profile_pic = $added_by_obj->getProfilePic();
				$date_added = $row['date_added'];
				$likes = $row['likes'];
				$body = $row['body'];
				date_default_timezone_set('Asia/Dhaka');
				$hehe = date("Y-m-d H:i:s");
				$nowtime = new DATETIME($hehe);
				$post_time = new datetime($date_added);
				$interval = $post_time->diff($nowtime);
				$ago = "";
				$id = $row['id'];
				if($interval->y == 1){
					$ago .= "one year ago";
				} else if($interval->y > 1){
					$ago .= $interval.y ." years ago";
				} else{
					if($interval->m == 1){
					$ago .= "one month ago";
					} else if($interval->m > 1){
						$ago .= $interval->m ." months ago";
					} else{
						if($interval->d == 1){
							$ago .= "one day ago";
						} else if($interval->d > 1){
							$ago .= $interval->d ." days ago";
						} else{
							if($interval->h == 1){
								$ago .= "one hour ago";
							} else if($interval->h > 1){
								$ago .= $interval->h ." hours ago";
							} else{
								if($interval->i == 1){
									$ago .= "one minute ago";
								} else if($interval->i > 1){
									$ago .= $interval->i ." minutes ago";
								} else{
									$ago .= "just now";
								}
							}
						}
					}
				}

				// herea
				$img_a = '<img src="'.$profile_pic.'"  class="post_pro_pic" width=50 >';
				$body = nl2br($body);
				$name = $added_by_obj->getFirstAndLastName();
				$user_type = $added_by_obj->user['user_type'];
				$image = $row['image_id'];
				if($image == "no image" || $image =="") $image = "";
				else $image = "<img src=".$image." style=\"max-width:600px; border-radius: 6px;\">";
				if($user_type=="Shopkeeper")
					$addtocart="</div>";
				else
					$addtocart="<button class=\"button\" onclick=\"document.getElementById('modal-wrapper').style.display='block'\" style=\"width:100px; height: 20px;text-align: center center; margin-left:30px; margin-bottom:10px;\">
								Order</button>

								<div id=\"modal-wrapper\" class=\"modal\">
								  
								  <form class=\"modal-content animate\" action=\"index.php?added_to_cart=$id\" method=\"POST\">
								        
								    <div class=\"imgcontainer\">
								      <span onclick=\"document.getElementById('modal-wrapper').style.display='none'\" class=\"close\" title=\"Close\">&times;</span>
								      <img src=\"addtocart.png\" alt=\"Avatar\" class=\"avatar\">
								      <h1 style=\"text-align:center\">Order Quantity Needed</h1>
								    </div>

								    <div class=\"container\">
								      <input type=\"text\" placeholder=\"Quantity\" name=\"quantity\"> 
								     </div>
								     <div class= \"container\">     
								      <button class=\"button_b\" type=\"submit\" name=\"order\">Order</button>   
								     </div>
								      
								    </div>
								    
								  </form>
								  
								</div>

								<script>

								var modal = document.getElementById('modal-wrapper');
								window.onclick = function(event) {
								    if (event.target == modal) {
								        modal.style.display = \"none\";
								    }
								}
								</script>";
				$ret .= "<div class=\"post_class column\">
							<div class=\"pro_pic_class\">
								<a href='profile.php?user=$added_by' >
									$img_a
								</a>
								<a href='profile.php?user=$added_by'>
									$name
								</a>
								&nbsp&nbsp&nbsp $ago
							</div> 
							$body
							<br>
							$image;
							<div class='newsfeedPostOptions'>
								<iframe src='like.php?post_id=$id' scrolling='no' style='background: #555;'></iframe>
							
						";
				$ret.=$addtocart;
				$ret.="</div>";


			}

			echo $ret;
		}
		 public function getAllNotifications(){
			$username = $this->getUserName();
			$query = mysqli_query($this->con, "SELECT distinct notifications.id as id, message, user_from,link, date_time from notifications where user_to='$username' order by notifications.id desc limit 15");
			$ret = "";
			$row = array();
			//echo "hello";
			while($query && $row = mysqli_fetch_array($query)){
				
				$user_from = $row['user_from'];
				$user_from_obj = new User($this->con, $user_from);
				$profile_pic = $user_from_obj->getProfilePic();
				$link=$row['link'];
				$date_time = $row['date_time'];
				$message = $row['message'];
				date_default_timezone_set('Asia/Dhaka');
				$hehe = date("Y-m-d H:i:s");
				$nowtime = new DATETIME($hehe);
				$post_time = new datetime($date_time);
				$interval = $post_time->diff($nowtime);
				$ago = "";
				$id = $row['id'];
				if($interval->y == 1){
					$ago .= "one year ago";
				} else if($interval->y > 1){
					$ago .= $interval.y ." years ago";
				} else{
					if($interval->m == 1){
					$ago .= "one month ago";
					} else if($interval->m > 1){
						$ago .= $interval->m ." months ago";
					} else{
						if($interval->d == 1){
							$ago .= "one day ago";
						} else if($interval->d > 1){
							$ago .= $interval->d ." days ago";
						} else{
							if($interval->h == 1){
								$ago .= "one hour ago";
							} else if($interval->h > 1){
								$ago .= $interval->h ." hours ago";
							} else{
								if($interval->i == 1){
									$ago .= "one minute ago";
								} else if($interval->i > 1){
									$ago .= $interval->i ." minutes ago";
								} else{
									$ago .= "just now";
								}
							}
						}
					}
				}

				// herea
				//$img_a = '<img src="'.$profile_pic.'"  class="post_pro_pic" width=50 >';
				//echo "<button class=\"button\" href=\"index.php\">";
				$message = nl2br($message);
				$name = $user_from_obj->getFirstAndLastName();
				$ret .= "<div class=\"post_class column\">
							
							$message
							
							<div>
								$ago
							</div>
							<div>
								<a href=\"$link\" /*class=\"button_n\"*/>
								View</a>
								  
							</div>
						</div>

							";


			}
			
			echo $ret;

		}
	}

	class Post{
		private $user_object;
		private $con;
		public function __construct($con, $user) {
			$this->con = $con;
			$this->user_object = new User($con, $user);
		}

		public function submitPost($body, $image){
			$username = $this->user_object->getUserName();
			//$body = strip_tags($body);
			// $body = mysqli_real_escape_string($this->con, $body);
			$check = str_replace(' ', '', $body);
			if($check != ""){
				date_default_timezone_set('Asia/Dhaka');
				$date_added = date("Y-m-d H:i:s");
				$len = strlen($body);
				$flag = 0;
				$res = "";
				$word = "";
				$i = 0;
				for($i = 0; $i <= $len; $i++)
				{
					if(substr($body, $i, 1) == '#') {
						$res .= "<a href=\"search.php?query=";
						$flag = 1;
						continue;
					}
					else if(!((substr($body, $i, 1) >='a' && substr($body, $i, 1) <='z')|| (substr($body, $i, 1) >='A' && substr($body, $i, 1) <='Z') || (substr($body, $i, 1) >='0' && substr($body, $i, 1) <='9') ||  (substr($body, $i, 1) =='-') ||  (substr($body, $i, 1) =='_'))){
						if($flag == 1){
							$res .= "-$word\" > #$word </a>";

						}
						$word = "";
						$flag = 0;
					}
					if($flag) $word .= substr($body, $i, 1);
					/*if(substr($body, $i, 1) == '#') $res .= '-'*/
					else $res .= substr($body, $i, 1);
					//$res .= $i;
				}
				$body = $res;

				$query = mysqli_query($this->con, "INSERT into posts values('', '$body', '$username', '$date_added', 'no', '0', '$image')");

				//	notification

				$num_posts = $this->user_object->getNumOfPosts() + 1;
				mysqli_query($this->con, "UPDATE users set num_posts='$num_posts' where username='$username'");
			}
		}
	}

	// get image id
	function getImageId($db)
	{
		$query = mysqli_query($db, "SELECT image_id from global");
		$row = mysqli_fetch_array($query);
		$imageId = $row['image_id'] + 1;
		$query = mysqli_query($db, "update global set image_id = '$imageId'");
		return $imageId-1;
	}

	function isFollowing($db, $follower, $followed)
	{
		$query = mysqli_query($db, "SELECT * from followers where follower='$follower' and followed='$followed';");
		$ret = 1;
		if( !$query || mysqli_num_rows($query)==0) $ret = 0;
		return $ret;
	}

	class Notification {

		private $user_object;
		private $con;
		public function __construct($con, $user) {
			$this->con = $con;
			$this->user_object = new User($con, $user);
		}
    
    public function getUnreadNumber(){

    	$username = $this->user_object->getUserName();
    	$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed = 'no' AND user_to = '$username'");
    	if(!$query) {
    		return 0;
    	}
    	return mysqli_num_rows($query);

    }

    public function seen(){
    	$username = $this->user_object->getUserName();
    	$query = mysqli_query($this->con, "UPDATE users set viewed='yes' where viewed = 'no' AND user_to = '$username'");
    }


    public function insertNotification($post_id, $user_to, $type, $quantity = 0) {

		$username = $this->user_object->getUserName();
		$userFirstandLastName = $this->user_object->getFirstAndLastName();
		date_default_timezone_set('Asia/Dhaka');
		$date_time = date("Y-m-d H:i:s");

		switch($type) {
			case 'order':
				$message = '<a href="profile.php?user='.$username. '">'.$userFirstandLastName . "</a> Ordered ".$quantity." unit of your product. ";
				break;
			case 'like':
				$message = '<a href="profile.php?user='.$username. '">'.$userFirstandLastName . "</a> liked your post";
				break;
			/*case 'profile_post':
				$message = $userLoggedInName . " posted on your profile";
				break;*/
			/*case 'comment_non_owner':
				$message = $userFirstandLastName . " commented on a post you commented on";
				break;*/
			case 'limit_cross':
				$message =  $post_id . " is now Running Out of limit";
				break;
			case 'follow':
				$message = '<a href="profile.php?user='.$username. '">'.$userFirstandLastName . "</a> followed you";
				break;
		}

		// if($type=='like')
		// {
		// 	array_push($notification, $userFirstandLastName." liked your post.");
		// }
		// if($type=='follow')
		// {
		// 	array_push($notification, $userFirstandLastName." followed you.");
		// }
		// if($type=='order')
		// {
		// 	array_push($notification, $userFirstandLastName." ordered something from you.");
		// }

		$link = "post.php?id=" . $post_id;
		if($type == 'follow'){
			$link = "profile.php?user=".$username;
		}
		if($type == 'limit_cross'){
			$link = "products.php";
		}
		$insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES('', '$user_to', '$username', '$message', '$link', '$date_time', 'no', 'no')");
	}




	public function getNotifications($data, $limit) {

		echo "dhuksi";//*

		$page = $data['page'];
		$username = $this->user_object->getUserName();
		$return_string = "";

		if($page == 1)
			$start = 0;
		else 
			$start = ($page - 1) * $limit;

		$set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed='yes' WHERE user_to='$username'");

		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to='$username' ORDER BY id DESC");

		if(mysqli_num_rows($query) == 0) {
			echo "You have no notifications!";
			return "";
		}

		$num_iterations = 0;
		$count = 1;

		while($row = mysqli_fetch_array($query)) {

			if($num_iterations++ < $start)
				continue;

			if($count > $limit)
				break;
			else 
				$count++;


			$user_from = $row['user_from'];

			$user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$user_from'");
			$user_data = mysqli_fetch_array($user_data_query);


			//Timeframe
			date_default_timezone_set('Asia/Dhaka');
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($row['datetime']); //Time of post
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 
			if($interval->y >= 1) {
				if($interval == 1)
					$time_message = $interval->y . " year ago"; //1 year ago
				else 
					$time_message = $interval->y . " years ago"; //1+ year ago
			}
			else if ($interval->m >= 1) {
				if($interval->d == 0) {
					$days = " ago";
				}
				else if($interval->d == 1) {
					$days = $interval->d . " day ago";
				}
				else {
					$days = $interval->d . " days ago";
				}


				if($interval->m == 1) {
					$time_message = $interval->m . " month". $days;
				}
				else {
					$time_message = $interval->m . " months". $days;
				}

			}
			else if($interval->d >= 1) {
				if($interval->d == 1) {
					$time_message = "Yesterday";
				}
				else {
					$time_message = $interval->d . " days ago";
				}
			}
			else if($interval->h >= 1) {
				if($interval->h == 1) {
					$time_message = $interval->h . " hour ago";
				}
				else {
					$time_message = $interval->h . " hours ago";
				}
			}
			else if($interval->i >= 1) {
				if($interval->i == 1) {
					$time_message = $interval->i . " minute ago";
				}
				else {
					$time_message = $interval->i . " minutes ago";
				}
			}
			else {
				if($interval->s < 30) {
					$time_message = "Just now";
				}
				else {
					$time_message = $interval->s . " seconds ago";
				}
			}

			$opened = $row['opened'];
			$style = ($opened == 'no') ? "background-color: #DDEDFF;" : "";

			$return_string .= "<a href='" . $row['link'] . "'> 
									<div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
										<div class='notificationsProfilePic'>
											<img src='" . $user_data['profile_pic'] . "'>
										</div>
										<p class='timestamp_smaller' id='grey'>" . $time_message . "</p>" . $row['message'] . "
									</div>
								</a>";
		}


		//If posts were loaded
		if($count > $limit)
			$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else 
			$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more notifications to load!</p>";

		return $return_string;

		//$notification= array();

	}


	}


	class OrderLog
	{
		private $user_object;
		private $con;
		public function __construct($con, $user) 
		{
			$this->con = $con;
			$this->user_object = new User($con, $user);
		}

		public function insertOrderLog($post_id, $user_to, $quantity = 0) 
		{

			$username = $this->user_object->getUserName();
			$userFirstandLastName = $this->user_object->getFirstAndLastName();
			date_default_timezone_set('Asia/Dhaka');
			$date_time = date("Y-m-d H:i:s");
			$message = '<a href="profile.php?user='.$username. '">'.$userFirstandLastName . "</a> Ordered ".$quantity." unit of ".'<a href="profile.php?user='.$user_to. '">'.$user_to."</a>\'s product. ";
			$link = "post.php?id=" . $post_id;
			$insert_query = mysqli_query($this->con, "INSERT INTO orders VALUES('', '$quantity', '$post_id', '$date_time', '$username','$user_to','$message', '$link')");
		}
        
        public function getUserName(){
			return $this->user_object->getUserName();
		}

		public function getAllOrderLog(){
			$username = $this->getUserName();
			$query = mysqli_query($this->con, "SELECT distinct orders.id as id, message, user_from,link, order_time as date_time from orders where user_to='$username' or user_from='$username' order by orders.id desc limit 15");
			$ret = "";
			$row = array();
			//echo "hello";
				// echo $username;
			while($query && $row = mysqli_fetch_array($query)){
				// echo "hiii2";
				$user_from = $row['user_from'];
				$user_from_obj = new User($this->con, $user_from);
				$link=$row['link'];
				$date_time = $row['date_time'];
				$message = $row['message'];
				date_default_timezone_set('Asia/Dhaka');
				$hehe = date("Y-m-d H:i:s");
				$nowtime = new DATETIME($hehe);
				$post_time = new datetime($date_time);
				$interval = $post_time->diff($nowtime);
				$ago = "";
				$id = $row['id'];
				if($interval->y == 1){
					$ago .= "one year ago";
				} else if($interval->y > 1){
					$ago .= $interval.y ." years ago";
				} else{
					if($interval->m == 1){
					$ago .= "one month ago";
					} else if($interval->m > 1){
						$ago .= $interval->m ." months ago";
					} else{
						if($interval->d == 1){
							$ago .= "one day ago";
						} else if($interval->d > 1){
							$ago .= $interval->d ." days ago";
						} else{
							if($interval->h == 1){
								$ago .= "one hour ago";
							} else if($interval->h > 1){
								$ago .= $interval->h ." hours ago";
							} else{
								if($interval->i == 1){
									$ago .= "one minute ago";
								} else if($interval->i > 1){
									$ago .= $interval->i ." minutes ago";
								} else{
									$ago .= "just now";
								}
							}
						}
					}
				}

				// herea
				//$img_a = '<img src="'.$profile_pic.'"  class="post_pro_pic" width=50 >';
				//echo "<button class=\"button\" href=\"index.php\">";
				$message = nl2br($message);
				$name = $user_from_obj->getFirstAndLastName();
				$ret .= "<div class=\"post_class column\">
							
							$message
							
							<div>
								$ago
							</div>
							<div>
								<a href=\"$link\" /*class=\"button_n\"*/>
								View</a>
								  
							</div>
						</div>

							";


			}
			
			echo $ret;

		}


	}


?>