<?php

	function digga_artist_cloud($artists)
	{
		echo '<ul class="digga_cloud">' . "\n";
		$class = 'even';
		foreach($artists AS $id => $name)
		{
			echo '<li class="' . $class . '">' . "\n";
			$class = ($class == 'odd') ? 'even' : 'odd';
			echo '<a href="javascript: void(0);" ';
			echo 'onclick="window.open(\'/traffa/digga_popup.php?action=view&artist=' . $id . '\', \'digga_artist_' . $id . '\', \'location=no, width=450, height=100\');">';
			echo str_replace(' ', '&nbsp;',  $name) . '</a> ' . "\n";
			echo '</li>' . "\n";
		}
		echo '<li style="float: none; clear: both;"></li>' . "\n";
		echo '</ul>' . "\n";
	}

	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";

	/* Now playing stuff */
	$query = 'SELECT np.timestamp, np.artist AS artist_id, np.song AS song_id, s.title AS song_title, a.name AS artist_name ';
	$query .= 'FROM nowplaying AS np, artists AS a, songs AS s ';
	$query .= ' WHERE np.user = "' . $userid . '" AND a.id = np.artist AND s.id = np.song ';
	$query .= 'ORDER BY timestamp DESC LIMIT 5';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if(mysql_num_rows($result) > 0)
	{
		echo '<div class="digga_module_div">' . "\n";
		echo '<h3><a href="/nowplayinghelp.php" class="howto">Hur fungerar det här?</a>Senast spelat i ' . $userinfo['login']['username'] . 's winamp</h3>' . "\n";
		echo '<ul style="list-style-type: none;">' . "\n";
		for($row = 1; $data = mysql_fetch_assoc($result); $row++)
		{
			echo '<li>';
			$link = '<a href="javascript: void(0);" style="text-decoration: none;" ';
			$link .= 'onclick="window.open(\'/traffa/digga_popup.php?action=view&artist=' . $data['artist_id'] . '\', \'digga_artist_' . $data['artist_id'];
			$link .= '\', \'location=no, width=450, height=100\');">';
			$link .= str_replace(' ', '&nbsp;',  htmlspecialchars($data['artist_name'])) . '</a> ' . "\n";

			if($row == 1)
			{
				echo '<strong>' . $link . ' - ' . htmlspecialchars($data['song_title']) . '</strong>';
			}
			else
			{
				echo $link . ' - ' . htmlspecialchars($data['song_title']);
			}
			
			echo '</li>' . "\n";
		}
		echo '</ul>' . "\n";
		echo '</div>' . "\n";
	}


	$query = 'SELECT a.name, ua.artist AS id FROM artists AS a, user_artists AS ua ';
	$query .= 'WHERE ua.user = "' . $userid . '" AND a.id = ua.artist ';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$artists[$data['id']] = $data['name'];
	}

	if($userid != $_SESSION['login']['id'] && login_checklogin())
	{
		$own_query = 'SELECT a.name, ua.artist AS id FROM artists AS a, user_artists AS ua ';
		$own_query .= 'WHERE ua.user = "' . $_SESSION['login']['id'] . '" AND a.id = ua.artist ';
		$own_result = mysql_query($own_query) or die(report_sql_error($own_query, __FILE__, __LINE__));
		while($data = mysql_fetch_assoc($own_result))
		{
			$own_artists[$data['id']] = $data['name'];
		}
		
		foreach(array_keys($artists) AS $current)
		{
			if(isset($own_artists[$current]))
			{
				$common_artists[$current] = $artists[$current];
			}
			else
			{
				$unique_artists[$current] = $artists[$current];
			}
		}
	
		echo '<div class="digga_module_div">' . "\n";
		echo '<h3>Artister och band som både du och ' . $userinfo['login']['username'] . ' diggar</h3>' . "\n";
		asort($common_artists);
		digga_artist_cloud($common_artists);
		echo '</div>' . "\n";
		
		
		echo '<div class="digga_module_div">' . "\n";
		echo '<h3>Artister och band som ' . $userinfo['login']['username'] . ' diggar, men som du inte diggar</h3>' . "\n";
		asort($unique_artists);
		digga_artist_cloud($unique_artists);
		echo '</div>' . "\n";
	}
	else
	{
		echo '<div class="digga_module_div">' . "\n";
		$name = ($userid == $_SESSION['login']['id']) ? 'du' : $userinfo['login']['username'];
		echo '<h3>Artister och band som ' . $name . ' diggar</h3>' . "\n";
		asort($artists);
		digga_artist_cloud($artists);
		echo '<br style="clear: both;" />' . "\n";
		echo '</div>' . "\n";
	}
	
	if($userid == $_SESSION['login']['id'])
	{
		echo '<br />';
		echo '<input type="button" class="button" value="Digga en ny artist" onclick="window.open(\'/traffa/digga_popup.php?action=create\', \'digga_help\', \'location=no, width=500, height=200\');" />' . "\n";
		echo '<input type="button" class="button" value="Visa vad du lyssnar på just nu" onclick="window.location = \'/nowplayinghelp.php\';" />' . "\n";
	}
	
?>

</div>
