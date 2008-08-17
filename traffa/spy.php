<?php

require('../include/core/common.php');
require(PATHS_INCLUDE . 'traffa-functions.php');

$ui_options['current_menu'] = 'traffa';
$ui_options['dom_tt_lib'] = true;

ui_top($ui_options);
echo 'Stängt på oviss tid';
/*
	$view = ($_GET['view'] > 0) ? $_GET['view'] : $_SESSION['login']['id'];
	if($view < 1)
	{
		die('Du måste antingen vara inloggad, eller ange vilken användare du vill titta på...');
	}

	$fetch['login'] = array('id', 'username', 'userlevel', 'regtimestamp', 'regip', 'lastlogon', 'lastip', 'lastaction', 'lastusernamechange', 'lastusername');
	$fetch['userinfo'] = array('contact1', 'contact2', 'gender', 'birthday', 'geo_location', 'geo_municipal', 'image', 'forum_signature', 'forum_posts');
	$fetch['traffa'] = array('firstname', 'profile_modules', 'color_theme');
	$userid = $view;
	
	$userinfo = login_load_user_data($userid, $fetch);
	
	traffa_draw_user_div($userid, $userinfo);

	if(!file_exists(PATHS_CACHE . 'spy/' . $view))
	{
		$method = 'database';
	}
	else
	{
		$cache_content = file(PATHS_CACHE . 'spy/' . $view);
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

	echo '<h1>Visar kontaktnät</h1>';

	if($method == 'database')
	{
		$cache_content[0] = time();

		$query = 'SELECT l.username, ui.image, ui.birthday, ui.gender, ui.geo_location, gb.recipient, COUNT( * ) AS messages, gb.timestamp
			FROM traffa_guestbooks AS gb, login AS l, userinfo AS ui
			WHERE gb.sender = ' . $view . ' 
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
			WHERE gb.recipient = ' . $view . ' 
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
			WHERE m.sender = ' . $view . ' 
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
			WHERE m.recipient = ' . $view . ' 
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

		$cache_file_handle = fopen(PATHS_CACHE . 'spy/' . $view, 'w');
		fwrite($cache_file_handle, time() . "\n" . serialize($users));
		fclose($cache_file_handle);
	}	

	$i = 0;
	foreach($users AS $userid => $user)
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
		$div[$i] = '<a href="' . $_SERVER['PHP_SELF'] . '?view=' . $userid . '"><img src="/images/termometer/' . $degrees . '.png" style="float: left; border: none; margin: 1px;" /></a>';
		$div[$i] .= '<strong><a href="/traffa/profile.php?id=' .  $userid . '" onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'' . $user['gender'] . $user['birthday'] . $user['geo_location'] . '\', \'trail\', true));">' . $user['username'] . '</a></strong><br />';
		if($user['image'] == 1 || $user['image'] == 2)
		{
			$div[$i] .= insert_avatar($userid);
		}
		$i++;
		if($i == 15)
		{
			break;
		}
	}
?>
	<div style="height: 220p;">
		<div style="float: left; width: 125px;"><?= $div[0]; ?></div>
		<div style="float: left; width: 125px;"><?= $div[1]; ?></div>
		<div style="float: left; width: 125px;"><?= $div[2]; ?></div>
		<div style="float: left; width: 125px;"><?= $div[3]; ?></div>
		<div style="float: left; width: 125px;"><?= $div[4]; ?></div>
	</div>

	<div style="height: 220px;">
		<div style="float: left; width: 120px;"><?= $div[5]; ?></div>
		<div style="float: left; width: 120px;"><?= $div[6]; ?></div>
		<div style="float: left; width: 120px;"><?= $div[7]; ?></div>
		<div style="float: left; width: 120px;"><?= $div[8]; ?></div>
		<div style="float: left; width: 120px;"><?= $div[9]; ?></div>
	</div>

	<div style="height: 220px;">
		<div style="float: left; width: 120px;"><?= $div[10]; ?></div>
		<div style="float: left; width: 120px;"><?= $div[11]; ?></div>
		<div style="float: left; width: 120px;"><?= $div[12]; ?></div>
		<div style="float: left; width: 120px;"><?= $div[13]; ?></div>
		<div style="float: left; width: 120px;"><?= $div[14]; ?></div>
	</div>

	<div class="grey_faded_div">
	<h2>Hur funkar detta?</h2>
	Denna sida visar de femton personer som <?= $userdata['login']['username']; ?> pratat mest med via meddelanden och gästboksinlägg under de senaste två veckorna.<br />
	Informationen på denna sidan uppdateras varannan dag, senaste uppdateringen gjordes <?= date('d/m H:i', $cache_content[0]); ?>.<br /><br />
	Termometrarna visar på ett ungefär hur intensiv kontakten är, ju högre temperatur desto tätare kontakt.<br />
	För att titta på en annan persons kontaktnät, klicka bara på personens termometer :)<br /><br />
	<strong>Exakt hur funkar beräkningarna?</strong><br />
	Först räknar vi antal meddelanden och gästboksinlägg som du skickat och tagit emot från en användare. Därefter tar vi detta talet och delar med sig självt upphöjt till 0.3. Ex:<br />
	Poäng = meddelanden / meddelanden<sup>0.3</sup><br />
	Sedan har vi fjorton olika termometerbilder, som vi sätter in utifrån det värdet vi fick genom den lilla beräkningen. Får vi ett värde över 14 så sänker vi det till 14.
	</div>
*/
?>
<?php
	ui_bottom();
?>
