<?
	require('include/core/common.php');
	$req_uri = $_SERVER['REQUEST_URI'];
	list($username, $action) = split('\/', substr($req_uri, 1, strlen($req_uri)));
	if($username == 'amusefiles')
	{
		header('location: /underhallning/');
		exit;
	}
	if($username == 'underhallning')
	{
		header('location: /underhallning/');
		exit;
	}
	if($username == 'images')
	{
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	if($username == 'forum_new')
	{
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	if ($username == 'id')
	{
		preg_match('/^[0-9]+$/', $action, $matches_a);
		header('Location: /traffa/user_facts.php?user_id=' . $matches_a[0]);
		exit;
	}
	if(preg_match("/^[0-9a-zA-Z_-]+$/i", $username))
	{
		$query = 'SELECT id FROM login WHERE username = "' . $username . '" LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query);
		if (mysql_num_rows($result) > 0)
		{
			$id = mysql_result($result, 0, 0);
			if ($action == 'gb')
			{
				$location = '/traffa/guestbook.php?view=' . $id;
			}
			else
			{
				$location = '/traffa/profile.php?id=' . $id;
			}
			header('Location: ' . $location);
		}
		else
		{
			header("HTTP/1.0 404 Not Found");
			$ui_options['menu_path'] = array('hamsterpaj');
			ui_top($ui_options);
			echo '<h1>Användaren ' . $username . ' finns inte på hamsterpaj</h1>';
			ui_bottom();
		}
	}
	else
	{
		header($_SERVER['SERVER_PROTOCOL'] . '404 Not Found');
		echo '404/Not found';
		exit;
	}
?>
