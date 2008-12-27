<?php
	$options['output'].= ui_avatar($options['photoblog_current_view_user_id']);
	$options['output'].= '<h3>' . $options['photoblog_current_view_username'] . '</h3>' . "\n";
	$options['output'].= '<br /><a href="/traffa/profile.php?user_id=' . $options['photoblog_current_view_user_id'] . '">Gå till presentation &raquo;</a>' . "\n";
?>