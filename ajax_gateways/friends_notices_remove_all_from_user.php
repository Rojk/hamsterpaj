<?php
	require('../include/core/common.php');
	if(login_checklogin() && is_numeric($_GET['friend_id']))
	{
		$options['friend_id'] = $_GET['friend_id'];
		friends_notices_remove($options);
		echo 'lol';
		unset($_SESSION['friends_actions'][$options['friend_id']]);
	}
?>