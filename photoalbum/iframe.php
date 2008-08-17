<?php
	session_start();
	require('../include/core/common.php');

	if($_GET['action'] == 'comment' && $_SESSION['login']['id'] > 0)
	{
		if($_SESSION['photoalbum']['comments'][$_POST['photo_id']] > time() - PHOTOALBUM_COMMENT_TIME)
		{
			jscript_alert('Hey, du kommenterade ju detta fotot nyss!');
		}
		elseif(strlen($_POST['text']) < 4)
		{
			jscript_alert('Lite väl kort kommentar det där va?');
		}
		else
		{
			/* Check if user is blocked */
			$query = 'SELECT owner FROM photos WHERE id ="' . $_POST['photo_id'] . '"';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$data = mysql_fetch_assoc($result);
			if(userblock_check($data['owner'], $_SESSION['login']['id']) == 1)
			{
				jscript_alert('Den gubben gick inte, du är blockerad :(');
				exit;
			}
			$query = 'INSERT INTO comments(type, item_id, user, timestamp, text) VALUES("photos", "' . $_POST['photo_id'] . '", ' . $_SESSION['login']['id'] . ', UNIX_TIMESTAMP(), "' . htmlspecialchars($_POST['text']) . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			echo '<script>' . "\n";
			echo 'window.location = "iframe.php?id=' . $_POST['photo_id'] . '";';
			echo '</script>';
			$_SESSION['photoalbum']['comments'][$_POST['photo_id']] = time();
		}
	}
	elseif($_GET['action'] == 'delete' && $_SESSION['login']['id'] > 0 && is_numeric($_GET['photo_id']) && is_numeric($_GET['comment_id']))
	{
		$query = 'SELECT owner FROM photos WHERE id = "' . $_GET['photo_id'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$data = mysql_fetch_assoc($result);
		if($data['owner'] == $_SESSION['login']['id'])
		{
			$query = 'DELETE FROM comments WHERE item_id = "' . $_GET['photo_id'] . '" AND id = "' . $_GET['comment_id'] . '" LIMIT 1';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
		echo '<script>' . "\n";
		echo 'alert(\'Ordnat!\');';
		echo 'window.location = "iframe.php?id=' . $_GET['photo_id'] . '&rand=' . rand() . '";';
		echo '</script>';
	}
	else
	{
		$query = 'SELECT owner, description, views, timestamp FROM photos WHERE id = "' . $_GET['id'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result == 0))
		{
			$description = '<i>Det finns ingen beskrivning för denna bild.</i>';
		}
		else
		{
			$data = mysql_fetch_assoc($result);
			if(strlen($data['description']) < 1)
			{
				$description = '<i>Det finns ingen beskrivning för denna bild.</i>';
			}
			else
			{
				$description = str_replace('\'', '\\\'', $data['description']);
			}
			$description .= '<br /><i>' . $data['views'] . ' visningar sedan ' . date('Y-m-d H:i', $data['timestamp']) . '</i>';
			$owner = $data['owner'];
		}
	
		echo '<script>' . "\n";
		echo 'top.document.getElementById(\'photo_description\').innerHTML = \'' . $description . '\';' . "\n";
		echo '</script>' . "\n";
	
		$query = 'SELECT l.username, c.id, c.user, c.timestamp, c.text FROM comments AS c, login AS l WHERE item_id = "' . $_GET['id'] . '" AND type = "photos" AND l.id = c.user ORDER BY c.id DESC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 0)
		{
			$comments = '<i>Det finns inga kommentarer för denna bild.</i>';
		}
		else
		{
			while($data = mysql_fetch_assoc($result))
			{
				if($div_bg == 'blue')
				{
					$comments .= '<div style="background: #efefef; border-bottom: 1px solid #686868;">';
					$div_bg = 'white';
				}
				else
				{
					$comments .= '<div style="border-bottom: 1px solid #686868;">';
					$div_bg = 'blue';
				}
				$comments .= '<a href="/traffa/profile.php?id=' . $data['user'] . '">' . $data['username'] . '</a> - ' . date('Y-m-d H:i', $data['timestamp']) . '<br />';
				$comments .= '<i>' . str_replace(array("'", "\n", "\r"), array('&#145;', null, null), $data['text']) . '</i>';
				if($owner == $_SESSION['login']['id'])
				{
					//$comments .= '<span style="cursor: pointer; text-decoration: underline;" onclick="alert(\\\'data\\\'); document.getElementById(\\\'photoalbum_iframe\\\').src = \\\'' . $_SERVER['PHP_SELF'] . '?action=delete&photo_id=' . $_GET['id'] . '&comment_id=' . $data['id'] . '\\\');">[Ta&nbsp;bort]</span>';
					$comments .= '<span style="cursor: pointer; text-decoration: underline;" onclick="document.getElementById(\\\'photoalbum_iframe\\\').src = \\\'' . $_SERVER['PHP_SELF'] . '?action=delete&photo_id=' . $_GET['id'] . '&comment_id=' . $data['id'] . '\\\';">[Ta&nbsp;bort]</span>';
				}
				$comments .= '</div>';
			}
		}
		echo '<script>' . "\n";
		echo 'top.document.getElementById(\'photo_comments\').innerHTML = \'' . $comments . '\';' . "\n";
		if($_SESSION['login']['id'] > 0)
		{
			echo 'top.document.getElementById(\'photo_comment_submit\').disabled = false;' . "\n";
		}
		echo 'top.document.getElementById(\'photo_comment_textarea\').value = \'\';' . "\n";
		echo '</script>' . "\n";

		if(!in_array($_GET['id'], $_SESSION['photoalbum']['views']))
		{
			$query = 'UPDATE photos SET views = views + 1 WHERE id = "' . $_GET['id'] . '" LIMIT 1';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$_SESSION['photoalbum']['views'][] = $_GET['id'];
		}

	}

?>
