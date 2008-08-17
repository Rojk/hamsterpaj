<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE . 'libraries/discussion_forum.lib.php');
	
	if(isset($_GET['post_id']) && is_numeric($_GET['post_id']) && intval($_GET['post_id']) > 0)
	{
		$post_id = intval($_GET['post_id']);
		
		$url_to_post = forum_get_url_by_post($post_id);
		
		if(trim($url_to_post) == '')
		{
			jscript_alert('Kunde inte hitta forumposten.');
			jscript_go_back();
		}
		else
		{
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $url_to_post);
			die($url_to_post);
		}
	}
	else
	{
		die('Död');
	}
?>