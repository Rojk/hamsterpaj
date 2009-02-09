<?php
require('../include/core/common.php');
require_once(PATHS_LIBRARIES . 'comments.lib.php');
require_once(PATHS_LIBRARIES . 'photos.lib.php');
log_to_file('comments', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'comment');

if(login_checklogin() && (isset($_POST['comment']) || $_POST['update'] == true) && isset($_POST['item_id']) && isset($_POST['item_type']))
{
	log_to_file('comments', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'comment', $_POST['item_id'] . ', ' . $_POST['item_type'] . ', ' . $_SESSION['login']['id'] . ', ' . $_POST['comment']);
	
	if(!isset($_POST['update']))
	{
		$content_check = content_check($_POST['comment']);
		$output = '';
		if($content_check === 1)
		{
			comments_new($_POST['item_id'], $_POST['item_type'], $_SESSION['login']['id'], $_POST['comment']);
		}
		else
		{
			$output .= 'Vår server tyckte att ditt meddelande bröt mot våra regler, så det sparades inte. Kommentar: ' . $content_check . "\n";
		}
	}
	
	if(isset($_POST['return_list']))
	{
		$output .= comments_list($_POST['item_id'], $_POST['item_type']);
		echo $output;
	}
}
elseif($_GET['action'] == 'comments_list_all')
{
	echo '<style type="text/css">@import url(\'/stylesheets/ui.css.php?\');</style>' . "\n";
	echo '<div id="content">' . "\n";
	echo comments_list($_GET['item_id'], $_GET['item_type'], array('list_style' => 'compact', 'limit' => 'no_limit'));
	echo '</div>';
}
elseif($_GET['action'] == 'comment_remove')
{
	if(is_numeric($_GET['id']))
	{
		comments_remove($_GET['id']);
	}
	else
	{
		echo 'You h4xx0r...';
	}
}
elseif($_GET['action'] == 'comment_answer')
{
	if(is_numeric($_GET['id']))
	{

		
		comment_answer($_GET['id'], $_GET['reply']);
		$output .= comments_list($_GET['item_id'], 'photos');

		echo $output;
	}
	else
	{
		jscript_alert('hejdå');
		echo 'You h4xx0r...';
	}
}


?>