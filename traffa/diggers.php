<?php
	require('../include/core/common.php');
	
	$ui_options['current_menu'] = 'traffa';
	$ui_options['title'] = 'Vem lyssnar på ' . $data['name'] . '?';
	$ui_options['stylesheets'][] = 'digga.css';

	ui_top($ui_options);

	function digga_top_100()
	{
		$query = 'SELECT name, id, popularity FROM artists ORDER BY popularity DESC LIMIT 100';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		echo '<table>' . "\n";
		for($i = 1; $data = mysql_fetch_assoc($result); $i++)
		{
			echo '<tr>' . "\n";
			echo '<td>#' . $i . '</td>' . "\n";
			echo '<td>' . $data['name'] . '</td>' . "\n";
			echo '<td>' . $data['popularity'] . '</td>' . "\n";
			echo '</tr>' . "\n\n";
		}
		echo '</table>' . "\n";
	}
	
	function digga_new_form()
	{
		echo '<h2>Börja digga en artist eller grupp</h2>';
	}
	
	function digga_search_form()
	{
		echo '<div id="digga_search">' . "\n";
		echo '<h2>Sök i digga</h2>' . "\n";
		echo '<form action="/traffa/digga.php?action=search" method="post">' . "\n";
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
	
	function digga_instructions()
	{
?>
	<div id="digga_instructions">
		<h2>Så gör du för att digga</h2>
	</div>
<?php
	}

	if($_SESSION['login']['id'] != 3)
	{
		die('Closed for maintenance, som man säger i amerikat'); 
	}
	
	if($_GET['action'] == 'view_info' && is_numeric($_GET['artist_id']))
	{
		$artist_info = digga_fetch_info($_GET['artist_id']);
		
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
			echo 'privat meddelande till <a href="/traffa/profile.php?id=65654">Rojk</a>. Glöm inte att tala om vilken artist ';
			echo 'det gäller!' . "\n";
			echo '</p>' . "\n";
		}
		
		$diggers = digga_fetch_diggers($_GET['artist_id']);
		
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
		
		digga_search_form();
		
	}

	if(!isset($_GET['artist_id']))
	{
		echo '<h1>Digga visar dig vad andra diggar och du visar andra vad du diggar</h1>' . "\n";
		
		echo '<div id="digga_top_100">' . "\n";
		digga_top_100();
		echo '</div>' . "\n";

		echo '<div id="digga_instructions">' . "\n";
		digga_instructions();
		echo '</div>' . "\n";
		
		echo '<div id="digga_new_artist">' . "\n";
		digga_new_form();
		echo '</div>' . "\n";

	}

	ui_bottom();

?>


