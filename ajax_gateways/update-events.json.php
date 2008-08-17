<?php
	require('../include/core/common.php');

	if(!login_checklogin())
	{
		// Standard-code for "you're logged out".
		die('{ "logged_on": false }');
	}
		
	if($_SESSION['cache']['lastupdate'] < time() - 20)
	{
		cache_update_all();
	}
	
	$output = array();
	$output['new_messages'] = $_SESSION['notices']['unread_messages'];
	$output['new_guestbook_posts'] = $_SESSION['notices']['unread_gb_entries'];
	$output['forum_notices'] = $_SESSION['forum']['new_notices'];		
	$output['group_notices'] = $_SESSION['cache']['unread_group_notices'];

	echo '{ "logged_on": true, "new_messages": ' . $output['new_messages'] . ', "new_guestbook_posts": ' . $output['new_guestbook_posts'] . ', "forum_notices": ' . $output['forum_notices'] . ', "group_notices": ' . $output['group_notices'] . ' }';
?>