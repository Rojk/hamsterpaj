<?php
	require('../include/core/common.php');
	
	$options['friend_id'] = intval($_GET['friend_id']) ? $_GET['friend_id'] : die('no valid id');
	friends_notices_remove($options);
	echo 'lol';
	unset($_SESSION['friends_actions'][$options['friend_id']]);
?>