<?php
	require('../include/core/common.php');
	
	$ui_options['menu_path'] = array('traeffa', 'digga');
	$ui_options['title'] = 'Vem lyssnar på ' . $data['name'] . '?';
	$ui_options['stylesheets'][] = 'digga.css';

	ui_top($ui_options);

	function digga_top_100()
	{
		$query = 'SELECT name, id, popularity FROM artists ORDER BY popularity DESC LIMIT 100';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		echo '<table style="width: 90%;">' . "\n";
		for($i = 1; $data = mysql_fetch_assoc($result); $i++)
		{
			echo '<tr>' . "\n";
			echo '<td>#' . $i . '</td>' . "\n";
			echo '<td><a href="?action=view_info&artist_id=' . $data['id'] . '">' . $data['name'] . '</a></td>' . "\n";
			echo '<td>' . $data['popularity'] . '</td>' . "\n";
			echo '</tr>' . "\n\n";
		}
		echo '</table>' . "\n";
	}
	
	function digga_dig($artist_id)
	{
		$query = 'INSERT INTO user_artists (user, artist) VALUES("' . $_SESSION['login']['id'] . '", "' . $artist_id . '")';
		if(mysql_query($query))
		{
			$query = 'UPDATE artists SET popularity = popularity + 1 WHERE id = "' . $artist_id . '"';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
	}
	
	function digga_new_artist($artist_name)
	{
		$query = 'SELECT id FROM artists WHERE name LIKE "' . htmlspecialchars($artist_name) . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			$artist_id = $data['id'];
		}
		else
		{
			$query = 'INSERT INTO artists (name) VALUES("' . htmlspecialchars($artist_name) . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			
			$artist_id = mysql_insert_id();
		}
		
		return $artist_id;
	}
	
	function digga_new_form($artist_name = null)
	{
		echo '<div id="digga_new_artist">' . "\n";
		if($artist_name == null)
		{
			echo '<h2>Börja digga en artist eller grupp</h2>';
		}
		echo '<form action="/traffa/digga.php?action=new_artist" method="post">' . "\n";
		echo '<input type="text" name="artist_name" value="' . $artist_name . '" />' . "\n";
		echo '<input type="submit" class="button" value="Börja digga" />' . "\n";
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}
	
	function digga_search_form()
	{
		echo '<div id="digga_search">' . "\n";
		echo '<h2>Sök i digga</h2>' . "\n";
		echo '<form action="/traffa/digga.php" method="get">' . "\n";
		echo '<input type="hidden" name="action" value="search" />' . "\n";
		echo '<input type="text" name="artist_name" class="textbox" />' . "\n";
		echo '<input type="submit" class="button" value="Sök" />' . "\n";
		echo '</form>';
		echo '</div>' . "\n";
	}
	
	function digga_fetch_info($artist_id)
	{
		$query = 'SELECT id, name, popularity, description FROM artists WHERE id = "' . $artist_id . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 0)
		{
			return false;
		}
		$data = mysql_fetch_assoc($result);
		return $data;
	}
	
	function digga_fetch_diggers($artist_id)
	{
		$query = 'SELECT ua.user AS id, l.username, l.lastaction, u.gender, u.birthday, z.spot ';
		$query .= 'FROM user_artists AS ua, login AS l, userinfo AS u, zip_codes AS z ';
		$query .= 'WHERE ua.artist = "' . $artist_id. '" AND l.id = ua.user AND u.userid = ua.user AND ';
		$query .= 'z.zip_code = u.zip_code ';
		$query .= 'ORDER BY l.lastaction DESC LIMIT 50';
		
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		while($data = mysql_fetch_assoc($result))
		{
			$return[] = $data;
		}
		
		return $return;
	}
	
	function digga_view($artist_id)
	{
		$artist_info = digga_fetch_info($artist_id);
		
		echo '<h1>Digga ger dig info om ' . $artist_info['name'] . '</h1>' . "\n";
		echo '<h3>' . cute_number($artist_info['popularity']) . ' Hamsterpajare diggar ' . $artist_info['name'] . '!</h3>' . "\n";
	
		if(strlen($artist_info['description']) > 0)
		{
			echo '<p class="digga_description">' . "\n";
			echo nl2br($artist_info['description']);
			echo '</p>' . "\n";
		}
		else
		{
			echo '<p class="digga_no_description">' . "\n";
			echo 'Vi har tyvärr ingen beskrivning om ' . $artist_info['name'] . ' än. Om du känner att du kan mycket om det ';
			echo 'här bandet eller den här artisten så får du hemskt gärna skriva en egen beskrivning och skicka den som ett ';
			echo 'privat meddelande till <a href="/traffa/profile.php?id=301872">Fridh</a>. Glöm inte att tala om vilken artist ';
			echo 'det gäller!' . "\n";
			echo '</p>' . "\n";
		}
		
		if(is_privilegied('digga_admin'))
		{
			echo 'Du kan <a href="?action=edit&artist_id=' . $artist_id . '">';
			echo 'ändra beskrivningen och namnet</a>';
		}
		
		$diggers = digga_fetch_diggers($artist_id);
		
		if(count($diggers) > 0)
		{
			echo '<div id="digga_diggers">' . "\n";
			echo '<h2>Hamsterpajare som diggar ' . $artist_info['name'] . '</h2>' . "\n";
			echo '<table>' . "\n";
			foreach($diggers AS $digger)
			{
				echo '<tr>' . "\n";
				echo '<td><a href="/traffa/profile.php?id=' . $digger['id'] . '">' . $digger['username'] . '</a></td>' . "\n";
				echo '<td>' . $digger['gender'] . date_get_age($digger['birthday']) . '</td>' . "\n";
				echo '<td>';
				echo ($digger['lastaction'] > time() - 900) ? 'online' : 'offline';
				echo '</td>' . "\n";
				echo '</tr>' . "\n";
			}
			echo '</table>' . "\n";
			echo '</div>' . "\n";
		}
		
		echo '<div id="digga_songs">' . "\n";
		echo '<h2>Låtar av ' . $artist_info['name'] . '</h2>' . "\n";
		$query = 'SELECT title FROM songs WHERE artist = "' . $artist_id . '" ORDER BY popularity DESC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		echo '<ul style="list-style-type: none;">' . "\n";
		while($data = mysql_fetch_assoc($result))
		{
			echo '<li>' . $data['title'] . '</li>' . "\n";
		}
		echo '</ul>' . "\n";
		echo '</div>' . "\n";
	}
	
	function digga_instructions()
	{
?>
	<div id="digga_instructions">
		<h2>Så gör du för att digga</h2>
	</div>
<?php
	}

	if($_GET['action'] == 'view_info' && is_numeric($_GET['artist_id']))
	{

		digga_view($_GET['artist_id']);
		digga_search_form();
	}
	elseif($_GET['action'] == 'new_artist' && strlen($_POST['artist_name']) > 0)
	{
		$artist_id = digga_new_artist($_POST['artist_name']);
		digga_dig($artist_id);
		digga_view($artist_id);
		digga_search_form();
		digga_new_form();
	}
	elseif($_GET['action'] == 'search' && strlen($_GET['artist_name']) > 0)
	{
		$artist_name = htmlspecialchars($_GET['artist_name']);
		$query = 'SELECT id FROM artists WHERE name = "' . $artist_name . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 0)
		{
			echo '<h1>' . $artist_name . ' diggas inte av någon Hamsterpajare än</h1>' . "\n";
			echo '<h2>Du kanske vill börja digga ' . $artist_name . '? Det är bara ett klick bort!</h2>' . "\n";
			digga_new_form($artist_name);
		}
		else
		{
			$data = mysql_fetch_assoc($result);
			digga_view($data['id']);
			digga_search_form();
			digga_new_form($artist_name);
		}
	}
	elseif($_GET['action'] == 'edit' && is_privilegied('digga_admin') && is_numeric($_GET['artist_id']))
	{
		$query = 'SELECT name, description FROM artists WHERE id = "' . $_GET['artist_id'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$data = mysql_fetch_assoc($result);
		
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?action=update" method="post">' . "\n";
		echo '<input type="hidden" name="artist_id" value="' . $_GET['artist_id'] . '" />' . "\n";
		echo '<input type="text" class="textbox" name="artist_name" value="' . addslashes($data['name']) . '" /><br />' . "\n";
		echo '<textarea name="description">' . $data['description'] . '</textarea><br />' . "\n";
		echo '<input type="submit" class="button" value="Uppdatera" />' . "\n";
		echo '</form>' . "\n";
	}
	elseif($_GET['action'] == 'update' && is_privilegied('digga_admin') && is_numeric($_POST['artist_id']))
	{
		$query = 'UPDATE artists SET name = "' . htmlspecialchars($_POST['artist_name']) . '", description = "';
		$query .= nl2br(htmlspecialchars($_POST['description'])) . '" WHERE id = "' . $_POST['artist_id'] . '"';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		digga_view($_POST['artist_id']);
		digga_search_form();
		digga_new_form();
	}
	else
	{
		echo '<h1>Digga visar dig vad andra diggar och du visar andra vad du diggar</h1>' . "\n";
		
		echo '<div id="digga_top_100">' . "\n";
		digga_top_100();
		echo '</div>' . "\n";

		echo '<div id="digga_instructions">' . "\n";
		digga_instructions();
		echo '</div>' . "\n";
		
		digga_new_form();

		digga_search_form();

	}

	ui_bottom();

?>


