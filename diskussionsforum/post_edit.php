<?php
	require('../include/core/common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ändra eller gör ett tillägg - Hamsterpajs forum</title>
<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />
<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />


<!-- Load stylesheets, version is timestamp of last file modification. Current timestamp is: 1203276417 -->
<style type="text/css">
@import url('/stylesheets/forum_post_edit.css');
</style>


<!-- Load javascripts, version is timestamp of last file modification. -->
<script type="text/javascript" language="javascript" src="/javascripts/jquery.js?version=1196987943"></script>
</head>
<?
	if (isset($_POST['post_id']) && is_numeric($_POST['post_id']))
	{
		echo '<body onLoad="setTimeout(\'window.close()\', 500)">';
	}
	else
	{
		echo '<body>';
	}
		?>
	
	<div class="forum_post_edit_area">
<?php
	if(isset($_POST['post_id']) && is_numeric($_POST['post_id']))
	{
		$post = discussion_forum_post_fetch(array('post_id' => $_POST['post_id']));
		if(count($post) == 1)
		{
			$post = array_pop($post);
			$query = 'UPDATE forum_posts SET content = ';
			if(forum_security(array('action' => 'post_addition', 'post' => $post)) && strlen($_POST['addition']) > 0)
			{
				$addition = "\n\n" . '[tillagg:' . $_SESSION['login']['username'] . ':' . time() . ']' . "\n" . $_POST['addition'] . '[/tillagg]';
			}
			
			if(forum_security(array('action' => 'edit_post', 'post' => $post)))
			{
				$query .= '"' . $_POST['content'] . $addition . '"';
			}
			else
			{
				$query .= '"' . $post['content'] . $addition . '"';
			}
			
			$query .= ' WHERE id = "' . $_POST['post_id'] . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}
		echo '<h1>Ändring och tillägg sparat!</h1>' . "\n";
	}
	elseif(isset($_GET['post_id']) && is_numeric($_GET['post_id']))
	{
		$post = discussion_forum_post_fetch(array('post_id' => $_GET['post_id']));
		if(count($post) == 1)
		{
			$post = array_pop($post);
			$disabled = (forum_security(array('action' => 'edit_post', 'post' => $post)) == true) ? '' : ' disabled="disabled"';
			echo '<form method="post">' . "\n";
			echo '<input type="hidden" name="post_id" value="' . $_GET['post_id'] . '" />' . "\n";
			echo '<h5>Inläggstext</h5>' . "\n";
			echo '<textarea name="content"' . $disabled . ' class="content_editor">' . $post['content'] . '</textarea>' . "\n";

			if((forum_security(array('action' => 'post_addition', 'post' => $post)) == true))
			{
				echo '<h5>Tillägg</h5>' . "\n";
				echo '<textarea name="addition"></textarea>' . "\n";
			}
			// Edit av Joar
			echo '<input type="submit" value="Spara" />' . "\n";

// Gamla:			echo '<input type="submit" value="Spara" />' . "\n";

		}
	}
?>
</div>
</body>
</html>