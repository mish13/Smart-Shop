<?php
include("../../config/config.php");
include("Classes/class.php");

$limit = 7; //Number of messages to load

$notification = new Notification($db, $_REQUEST['username']);
echo $notification->getNotifications($_REQUEST, $limit);

?>