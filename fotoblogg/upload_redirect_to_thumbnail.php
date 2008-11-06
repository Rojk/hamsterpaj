<?php
	require('../include/core/common.php');
	
	if(login_checklogin() && isset($_GET['upload_ticket']) && isset($_GET['photo_id']) && $_GET['upload_ticket'] && is_numeric($_GET['photo_id']))
	{
		if(isset($_SESSION['photoblog']['upload']['upload_tickets'][$_GET['upload_ticket']][$_GET['photo_id']]))
		{
			$real_photo_id = $_SESSION['photoblog']['upload']['upload_tickets'][$_GET['upload_ticket']][$_GET['photo_id']];
			$folder = floor($real_photo_id / 5000);
			
			header('HTTP/1.0 301 Moved Permanently');
			header('Location: http://images.hamsterpaj.net/photos/thumb/' . $folder . '/' . $real_photo_id . '.jpg');
		}
	}
?>