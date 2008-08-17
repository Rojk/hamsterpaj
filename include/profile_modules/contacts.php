<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";
?>
<?php
echo 'Stängt på oviss tid';
/*
	if(!file_exists(PATHS_CACHE . 'spy/' . $userid))
	{
		$method = 'database';
	}
	else
	{
		$cache_content = file(PATHS_CACHE . 'spy/' . $userid);
		if($cache_content[0] < time() - SPY_CACHE_VALIDITY)
		{
			$method = 'database';
		}
		else
		{
			$method = 'cache';
			$users = unserialize($cache_content[1]);
		}
	}

	if($method == 'database')
	{
		$cache_content[0] = time();

		$query = 'SELECT l.username, ui.image, ui.birthday, ui.gender, ui.geo_location, gb.recipient, COUNT( * ) AS messages, gb.timestamp
			FROM traffa_guestbooks AS gb, login AS l, userinfo AS ui
			WHERE gb.sender = ' . $userid . ' 
			AND gb.timestamp > UNIX_TIMESTAMP( ) -3600 *24 *14
			AND l.username NOT LIKE "Borttagen" 
			AND l.id = gb.recipient AND ui.userid = l.id
			GROUP BY gb.recipient
			ORDER BY messages DESC
			LIMIT 15';

		$result = mysql_query($query) or die(report_sql_error($query));
		while($data = mysql_fetch_assoc($result))
		{
			$users[$data['recipient']]['score'] += $data['messages'];		
			$users[$data['recipient']]['username'] = $data['username'];
			$users[$data['recipient']]['image'] = $data['image'];
			$users[$data['recipient']]['birthday'] = ($data['birthday'] != '0000-00-00') ? date_get_age($data['birthday']). ' år ' : null;
			$users[$data['recipient']]['gender'] = $data['gender'];
			$users[$data['recipient']]['geo_location'] = (strlen($data['geo_location']) > 1) ? ' från ' . $data['geo_location'] : null;
		}
	
		$query = 'SELECT l.username, ui.image, ui.birthday, ui.gender, ui.geo_location, gb.sender, COUNT( * ) AS messages, gb.timestamp
			FROM traffa_guestbooks AS gb, login AS l, userinfo AS ui
			WHERE gb.recipient = ' . $userid . ' 
			AND gb.timestamp > UNIX_TIMESTAMP( ) -3600 *24 *14
			AND l.username NOT LIKE "Borttagen" 
			AND l.id = gb.sender AND ui.userid = l.id
			GROUP BY gb.sender
			ORDER BY messages DESC
			LIMIT 15';
	
		$result = mysql_query($query) or die(report_sql_error($query));
		while($data = mysql_fetch_assoc($result))
		{
			$users[$data['sender']]['score'] += $data['messages'];		
			$users[$data['sender']]['username'] = $data['username'];
			$users[$data['sender']]['image'] = $data['image'];
			$users[$data['sender']]['birthday'] = ($data['birthday'] != '0000-00-00') ? date_get_age($data['birthday']). ' år ' : null;
			$users[$data['sender']]['gender'] = $data['gender'];
			$users[$data['sender']]['geo_location'] = (strlen($data['geo_location']) > 1) ? ' från ' . $data['geo_location'] : null;
		}


		$query = 'SELECT l.username, ui.image, ui.birthday, ui.gender, ui.geo_location, m.recipient, COUNT( * ) AS messages, m.timestamp
			FROM messages_new AS m, login AS l, userinfo AS ui
			WHERE m.sender = ' . $userid . ' 
			AND m.timestamp > UNIX_TIMESTAMP( ) -3600 *24 *14
			AND l.username NOT LIKE "Borttagen" 
			AND l.id = m.recipient AND ui.userid = l.id
			GROUP BY m.recipient
			ORDER BY messages DESC
			LIMIT 15';
	
		$result = mysql_query($query) or die(report_sql_error($query));
		while($data = mysql_fetch_assoc($result))
		{
			$users[$data['recipient']]['score'] += $data['messages'];		
			$users[$data['recipient']]['username'] = $data['username'];
			$users[$data['recipient']]['image'] = $data['image'];
			$users[$data['recipient']]['birthday'] = ($data['birthday'] != '0000-00-00') ? date_get_age($data['birthday']). ' år ' : null;
			$users[$data['recipient']]['gender'] = $data['gender'];
			$users[$data['recipient']]['geo_location'] = (strlen($data['geo_location']) > 1) ? ' från ' . $data['geo_location'] : null;
		}		


		$query = 'SELECT l.username, ui.image, ui.birthday, ui.gender, ui.geo_location, m.sender, COUNT( * ) AS messages, m.timestamp
			FROM messages_new AS m, login AS l, userinfo AS ui
			WHERE m.recipient = ' . $userid . ' 
			AND m.timestamp > UNIX_TIMESTAMP( ) -3600 *24 *14
			AND l.username NOT LIKE "Borttagen" 
			AND l.id = m.sender AND ui.userid = l.id
			GROUP BY m.sender
			ORDER BY messages DESC
			LIMIT 15';

		$result = mysql_query($query) or die(report_sql_error($query));
		while($data = mysql_fetch_assoc($result))
		{
			$users[$data['sender']]['score'] += $data['messages'];		
			$users[$data['sender']]['username'] = $data['username'];
			$users[$data['sender']]['image'] = $data['image'];
			$users[$data['sender']]['birthday'] = ($data['birthday'] != '0000-00-00') ? date_get_age($data['birthday']). ' år ' : null;
			$users[$data['sender']]['gender'] = $data['gender'];
			$users[$data['sender']]['geo_location'] = (strlen($data['geo_location']) > 1) ? ' från ' . $data['geo_location'] : null;
		}
	
		arsort($users);

		$cache_file_handle = fopen(PATHS_CACHE . 'spy/' . $userid, 'w');
		fwrite($cache_file_handle, time() . "\n" . serialize($users));
		fclose($cache_file_handle);
	}	

	$i = 0;
	// The underscore is used so as not to fuck up the global $userid which holds the ID of the profile owner 
	foreach($users AS $userid_ => $user)
	{
		$user['username'] = (strlen($user['username']) > 12) ? substr($user['username'],0 , 10) . '...' : $user['username'];
		if($user['gender'] == 'P')
		{
			$user['gender'] = 'Pojke ';
		}
		elseif($user['gender'] == 'F')
		{
			$user['gender'] = 'Flicka ';
		}
		$degrees = (round($user['score'] / pow($user['score'],0.3) > 14) ? 14 : round($user['score'] / pow($user['score'],0.3)));
		$div[$i] = '<strong><a href="/traffa/profile.php?id=' .  $userid_ . '" onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'' . $user['gender'] . $user['birthday'] . $user['geo_location'];
		if($user['image'] == 1 || $user['image'] == 2)
		{
			$div[$i] .= '&lt;br /&gt;&lt;img src=\\\'/images/users/thumb/' . $userid_ . '.jpg\\\' /&gt;';
		}
		$div[$i] .= '\', \'trail\', true));">' . $user['username'] . '</a></strong><br />';
		$div[$i] .= '<a href="' . $_SERVER['PHP_SELF'] . '?view=' . $userid_ . '"><img src="/images/termometer/' . $degrees . '_r.png" style="border: none; margin: 1px; width: 119px; height: 34px;" /></a>';
		$i++;
		if($i == 15)
		{
			break;
		}
	}
?>
	<div style="height: 60px;">
		<div style="float: left; width: 145px;"><?= $div[0]; ?></div>
		<div style="float: left; width: 145px;"><?= $div[1]; ?></div>
		<div style="float: left; width: 145px;"><?= $div[2]; ?></div>
		<div style="float: left; width: 145px;"><?= $div[3]; ?></div>
	</div>

	<div style="height: 60px;">
		<div style="float: left; width: 145px;"><?= $div[4]; ?></div>
		<div style="float: left; width: 145px;"><?= $div[5]; ?></div>
		<div style="float: left; width: 145px;"><?= $div[6]; ?></div>
		<div style="float: left; width: 145px;"><?= $div[7]; ?></div>
	</div>

	<div style="height: 60px;">
		<div style="float: left; width: 145px;"><?= $div[8]; ?></div>
		<div style="float: left; width: 145px;"><?= $div[9]; ?></div>
		<div style="float: left; width: 145px;"><?= $div[10]; ?></div>
		<div style="float: left; width: 145px;"><?= $div[11]; ?></div>
	</div>
*/
?>
</div>


