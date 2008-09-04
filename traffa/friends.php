<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	require(PATHS_INCLUDE . 'libraries/userblock.lib.php');

	$ui_options['title'] = 'DATOR';
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'friends.css';
	
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$user_id = $_GET['id'];
	}
	elseif(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
	{
		$user_id = $_GET['user_id'];
	}
	elseif(login_checklogin())
	{
		$user_id = $_SESSION['login']['id'];
	}
	else
	{
		ui_top();
		echo '<p>I think I know I mean "Yes," but it\'s all wrong.</p>';
		ui_bottom();
		exit;
	}
		
	function render_friends_list($friends, $params = array())
	{
		$return .= '<ul class="friends_list">' . "\n";
		foreach($friends AS $friend)
		{
			$return .= '<li>' . "\n";
			if($friend['image'] == 1 || $friend['image'] == 2)
			{
				$return .= ui_avatar($friend['user_id']) . "\n";
			}
			$return .= '<a href="/traffa/profile.php?id=' . $friend['user_id'] . '">' . $friend['username'] . '</a>' . "\n";
			$genders = array('f' => 'F', 'm' => 'P');
			$return .= (isset($genders[$friend['gender']])) ? $genders[$friend['gender']] : ''; 
			$return .= ($friend['birthday'] != '0000-00-00') ? date_get_age($friend['birthday']) : '';
			$return .= ($friend['lastaction'] > time() - 600) ? '<span class="online">online</span>' : '';
			if($params['user_id'] == $_SESSION['login']['id'] && ($params['friend_type'] == 'idol' || $params['friend_type'] == 'friend'))
			{
				$return .= '<a class="relation_end_control" href="/traffa/friends.php?user_id=' . $friend['user_id'] . '&action=removefriend">[Avsluta relationen]</a>' . "\n";
			}
			$return .= '</li>' . "\n";
		}
		$return .= '</ul>' . "\n";
				
		return $return;
	}

	$params['user_id'] = $user_id;
	$profile = profile_fetch($params);
	$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';

	$output .= profile_mini_page($profile);

	$query = 'SELECT username FROM login WHERE id ="' . $user_id . '" LIMIT 1';
	$result = mysql_query($query);
	$user = mysql_fetch_assoc($result);
	
	/* If the users adds/removes friendship */
	if(login_checklogin() && $_GET['action'] == 'addfriend' && $_GET['user_id'] != $_SESSION['login']['id'])
	{
		$query = 'INSERT INTO friendslist (user_id, friend_id) VALUES("' . $_SESSION['login']['id'] . '", "' . $_GET['id'] . '")';
		mysql_query($query);
		
		$query = 'INSERT INTO user_action_log (timestamp, user, action, url, label)';
		$query .= ' VALUES("' . time() . '", "' . $_SESSION['login']['id'] . '", "friendship", "/traffa/profile.php?id=' . $_GET['id'] . '", "' . $user['username'] . '")';
		mysql_query($query);
		
		friends_actions_insert(array(
			'action' => 'friendship',
			'url' => '/traffa/profile.php?user_id=' . $_GET['user_id'],
			'label' => $user['username']
		));
		
		$output .= '<form action="/traffa/guestbook.php?action=send_new_message&userid=' . $_GET['user_id'] . '" method="post">' . "\n";
		$output .= '<p>Vi tänkte att du kanske vill tala om för ' . $user['username'] . ' att du lagt till henne/honom som vän. Här har du ett gästboksformulär</p>' . "\n";
		$output .= '<textarea name="message" class="textbox" rows="3" cols="75">Hej, jag har lagt till dig som vän nu :)</textarea>' . "\n";
		$output .= '<input name="recipient" type="hidden" value="' . $_GET['id'] . '" />' . "\n";
		$output .= '<input type="submit" value="Skicka" class="button_60" />' . "\n";
		$output .= '</form>' . "\n";
	}
	if(login_checklogin() && $_GET['action'] == 'removefriend' && $_GET['user_id'] != $_SESSION['login']['id'])
	{
		$query = 'DELETE FROM friendslist WHERE user_id = "' . $_SESSION['login']['id'] . '" AND friend_id = "' . $_GET['user_id'] . '" LIMIT 1';
		mysql_query($query);		
	}
	
	
	/* Fetch everyone that the user has marked as a friend */
	$query = 'SELECT f.friend_id AS user_id, l.username, l.lastaction, u.image, u.gender, u.birthday FROM friendslist AS f, login AS l, userinfo AS u WHERE f.user_id = "' . $user_id . '" AND l.id = f.friend_id AND u.userid = l.id AND l.username NOT LIKE "Borttagen" ORDER BY l.username ASC';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));	
	while($data = mysql_fetch_assoc($result))
	{
		$idols[$data['user_id']] = $data;
	}

	/* Fetch all users that wants to be the users friend */
	$query = 'SELECT f.user_id AS user_id, l.username, l.lastaction, u.image, u.gender, u.birthday FROM friendslist AS f, login AS l, userinfo AS u WHERE f.friend_id = "' . $user_id . '" AND l.id = f.user_id AND u.userid = l.id AND l.username NOT LIKE "Borttagen"';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$fans[$data['user_id']] = $data;
	}
	
	/* Merge idols and fans to utkristallisera common friends :) */
	foreach($idols AS $idol_id => $idol_info)
	{
		if(isset($fans[$idol_id]))
		{
			$friends[$idol_id] = $idol_info;
			unset($idols[$idol_id]);
			unset($fans[$idol_id]);
		}
	}
	
	if(login_checklogin() && $_GET['user_id'] != $_SESSION['login']['id'])
	{
		if(!isset($friends[$_SESSION['login']['id']]) && !isset($fans[$_SESSION['login']['id']]))
		{
			$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
			$output .= $user['username'] . ' ligger inte på din vännerlista, <a href="?id=' . $_GET['user_id'] . '&action=addfriend">lägg till</a>';
			$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
		}
		if(isset($friends[$_SESSION['login']['id']]) || isset($fans[$_SESSION['login']['id']]))
		{
			$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
			$output .= 'Du har lagt till ' . $user['username'] . ' i din vännerlista, <a href="?id=' . $_GET['user_id'] . '&action=removefriend">ta bort från vännerlistan</a>';
			$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
		}
		

	}
	
	if(count($friends) > 0)
	{
		$output .= '<h1 class="friends_header">Kompisar</h1>' . "\n";
		$output .= render_friends_list($friends, array('user_id' => $user_id, 'friend_type' => 'friend'));
	}
	else
	{
		$output .= '<p>' . $user['username'] . ' har inga vänner :( Kanske är det så att ' . $user['username'] . ' luktar lite illa?</p>' . "\n";
	}
	if(count($fans) > 0)
	{
		$output .= '<h1 class="friends_header">Fans</h1>' . "\n";
		$output .= '<p>Fans är användare som har lagt till ' . $user['username'] . ' som vän, men som ' . $user['username'] . ' inte har på sin vännerlista</p>';
		$output .= render_friends_list($fans, array('user_id' => $user_id, 'friend_type' => 'fan'));		
	}
	else
	{
		$output .= '<p>' . $user['username'] . ' har inga fans</p>' . "\n";		
	}
	if(count($idols) > 0)
	{
		$output .= '<h1 class="friends_header">Idoler</h1>' . "\n";
		$output .= '<p>Idoler är användare som ' . $user['username'] . ' lagt till som vän, men som i sin tur inte lagt till ' . $user['username'] . ' som vän</p>';
		$output .= render_friends_list($idols, array('user_id' => $user_id, 'friend_type' => 'idol'));	
	}
	else
	{
		$output .= '<p>' . $user['username'] . ' har inga idoler, inte på Hamsterpartaj i alla fall</p>' . "\n";				
	}	
	
	// Slakta Joar om det här är fel.
	if (strlen($profile['error_message']) > 0)
	{
		$ui_options['title'] .= 'Presentationsfel - Hamsterpaj.net';
		ui_top($ui_options);
		echo '<h1>Presentationsfel</h1>';
		echo '<p>' . $profile['error_message'] . '</p>';
		ui_bottom();
		exit; //Important!
	}
	else
	{
	$ui_options['title'] = $profile['username'] . 's vänner, Hamsterpaj.net';
	}
	
	ui_top($ui_options);
	echo $output;
	ui_bottom();
?>