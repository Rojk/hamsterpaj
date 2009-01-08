<?php
	global $photoblog_user;
	$options['output'].= ui_avatar($photoblog_user['id']);
	$options['output'].= '<h3>' . $photoblog_user['username'] . '</h3>' . "\n";
	$options['output'].= '<br /><a href="/traffa/profile.php?user_id=' . $photoblog_user['id'] . '">Gå till presentation &raquo;</a>' . "\n";
?>