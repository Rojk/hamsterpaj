<?php #Nyhetsscript för hamsterpaj.net kodat av: Schneaker 2004-06-29
	/* OPEN_SOURCE */

  require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/discussions.php');
	require(PATHS_INCLUDE . 'libraries/posts.php');
	require(PATHS_INCLUDE . 'libraries/quality.php');
	require(PATHS_INCLUDE . 'libraries/forum-antispam.php');
	require(PATHS_INCLUDE . 'libraries/msnbot.lib.php');
	include($hp_path . 'forum_new/parser.php');

	$ui_options['menu_path'] = array('hamsterpaj', 'nytt');
  $ui_options['enable_rte'] = true;
  ui_top($ui_options);

	echo '<h1>Senaste nytt på hamsterpaj.net</h1>';
	echo '<p class="intro">Har det hänt något nytt sedan du senast kollade in siten? Här hittar du de 30 senaste '; 
	echo 'nyheterna och uppdateringarna prydligt uppradade, kan det bli bättre?</p>';

	if(isset($_GET['delete']) && is_privilegied('news_admin'))
	{
		mysql_query('DELETE FROM nyheter WHERE id = "' . $_GET['delete'] . '" LIMIT 1');
		echo 'Nyheten med ID-nummer ' . $_GET['delete'] . ' togs bort ur databasen.';
		echo '<br /><a href="' . $_SERVER['PHP_SELF'] . '">&lt;&lt;Tillbaka</a>';
	}
	else if(isset($_GET['edit']))
	{
		$result = mysql_query('SELECT * FROM nyheter WHERE id="' . $_GET['edit'] . '"');
		$data = mysql_fetch_assoc($result);
		echo 'Redigerar nyhet med ID-nummer ' . $_GET['edit'] . '<br />';
		echo '<div id="contentPostbox">';
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?update=' . $_GET['edit'] . '" method="post">';
		echo 'Titel:<input type="text" name="title" value="' . $data['title'] . '" class="textbox"><br />';
		echo 'Text:<br /><textarea name="body" rows="5" cols="70" class="textbox">' . $data['body'] . '</textarea><br />';
		echo 'Skriven av:<input type="text" name="who" value="' . $data['who'] . '" class="textbox"><br />';
		echo '<input type="submit" value="Redigera" class="button">';
		echo '</form></div>';
	}
	else if(isset($_GET['update']) && is_privilegied('news_admin'))
	{
		$query = 'UPDATE nyheter SET title="' . $_POST['title'] . '",body="' . $_POST['body'] . '",who="';
		$query .= $_POST['who'] . '" WHERE id="' . $_GET['update'] . '" LIMIT 1';
		mysql_query($query) or die('Error while updating data, used query:<br />' . $query);
		echo 'Nyheten har redigerats.<br />';
		echo '<a href="' . $_SERVER['PHP_SELF'] . '">&lt;&lt;Tillbaka</a>';
	}
	else if(isset($_GET['add']) && is_privilegied('news_admin'))
	{
		$thread_options['forum_id'] = 2;
		$thread_options['content'] = $_POST['body'];
		$thread_options['title'] = $_POST['title'];
		$thread_options['mode'] = 'new_thread';
		$thread_id = discussion_forum_post_create($thread_options);

		$thread_url = forum_get_url_by_post($thread_id);
		
		$query = 'INSERT INTO nyheter (who, tstamp, title, body, thread_url) VALUES ("' . $_SESSION['login']['username'] . '",';
		$query .= 'UNIX_TIMESTAMP(),"' . $_POST['title'] . '","' . nl2br($_POST['body']) . '","' . $thread_url . '")';
		mysql_query($query) or die(report_sql_error($query));
		echo 'Nyheten har lagts in i databasen.<br />';
		echo '<a href="' . $_SERVER['PHP_SELF'] . '">&lt;&lt;Tillbaka</a>';
//		write_cache();

		$query = 'INSERT INTO recent_updates (type, timestamp, url, label) VALUES ("text_news", "' . time() . '", "' . $thread_url . '", "' . $_POST['title'] . '")';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		msnbot_queue_add_everyone(array('message' => 'Nyhet på www.hamsterpaj.net!' . "\r\n\r\n" . $_POST['body'] . "\r\n\r\n" . 'Klicka på den här länken för att kommentera nyheten:' . "\r\n" . $thread_url));
	}
	else 
	{
		if(is_privilegied('news_admin'))
		{
			echo '<div id="contentPostbox">';
			echo '<form action="' . $_SERVER['PHP_SELF'] . '?add" method="post">';
			echo 'Titel:<input type="text" name="title" length="40" cols="40" class="textbox"><br />';
			echo 'Text:<br /><textarea name="body" rows="5" cols="70 class="textbox" class="textbox"></textarea><br />';
			echo '<input type="submit" value="OK" class="button">';
			echo '</form></div><br />';
		}
		$result = mysql_query('SELECT * FROM nyheter ORDER BY id DESC LIMIT 30');
		while($data = mysql_fetch_assoc($result))
		{
			echo '<div style="background: #f7f7f7">';
			echo '<a name="newsitem' . $data['id'] . '"></a>';
			echo '<strong>' . $data['title'] . '</strong> (' . fix_time($data['tstamp'], false) . ')<br />';
			echo $data['body'];
			if ($data['forumthread'] != '0')
			{
				unset($options);
				$options['id'] = $data['forumthread'];
				$discussions = discussions_fetch($options);
				echo '<br /><br /><a href="/forum/hamsterpaj/nyheter/' . $discussions[0]['handle'] . '/">Kommentera nyheten »</a>';
			}
			echo '<br /><i>Skriven av: <strong>' . $data['who'] . '</strong></i>';
			if(is_privilegied('news_admin'))
			{
				echo '<br />';
				echo '[<a href="' . $_SERVER['PHP_SELF'] . '?delete=' . $data['id'] . '">Radera</a>]';
				echo '[<a href="' . $_SERVER['PHP_SELF'] . '?edit=' . $data['id'] . '">Redigera</a>]';
			}
			echo '</div><br />';
		}
	}

	ui_bottom();
?>
