<?php
	/* OPEN_SOURCE */
	
	require('../include/core/common.php');
	
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	require(PATHS_INCLUDE . 'libraries/photos.lib.php');
	require(PATHS_INCLUDE . 'libraries/userblock.lib.php');
	require(PATHS_INCLUDE . 'xhpml.php');

	$ui_options['javascripts'][] = 'user_flags.js';
	
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['menu_path'] = array('traeffa');
	
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$user_id = $_GET['id'];
	}
	// NEW Standards, always use ?user_id= when retrieving or sending an user id.
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
		exit; //Important!
	}
	
	
	/* Fetch profile data... */
	$params['user_id'] = $user_id;
	$params['show_removed_users'] = (isset($_GET['show_removed_users']) && is_privilegied('use_ghosting_tools'));
	$profile = profile_fetch($params);
	
	// Introduces the new design!
	if(login_checklogin() && in_array($profile['username'], array('iPhone' || 'AnoosMonkey')))
	{
		if(isset($_SESSION['new_design']))
		{
			unset($_SESSION['new_design']);
		}
		else
		{
			$_SESSION['new_design']  = true;
			jscript_alert('Hey där tjockis! Vet du om att jag har varit med och gjort den nya designen?');
		}
	}

	/* lvl 3+ benefit */
	if (is_privilegied('use_ghosting_tools'))
	{
		$_GET['override'] = false;
	}
	
	/* ...and check for errors. */
	
	if(strlen($profile['error_message']) > 0 && !$_GET["override"])
	{
		$ui_options['title'] .= 'Presentationsfel - Hamsterpaj.net';
		ui_top($ui_options);
		echo '<h1>Presentationsfel</h1>';
		echo '<p>' . 
		$profile['error_message'] . '</p>';
		ui_bottom();
		exit; //Important!
	}
	
	/* Set page title */
	
	$ui_options['title'] .= $profile['username'] . ' - Hamsterpaj.net';
	
	$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';

	/* Start of profile */
	$output .= profile_top($profile);
	
	
	/* Including Profile-head */
	$output .= profile_head($profile);
	
	/* Presentation changed... */
	if(isset($_GET['show_change_profile_notice']))
	{
		$rounded_corners_config['color'] = 'orange_deluxe';
		$rounded_corners_config['return'] = true;
		$output .= rounded_corners_top($rounded_corners_config);
		$output .= 'Blev det inte riktigt som du hade tänkt dig? Gå tillbaka och 
		<a href="/installningar/profilesettings.php">ändra din presentation</a> igen!';
		$output .= rounded_corners_bottom($rounded_corners_config);
	}
	
	/* Photos */
	$photos = photos_fetch(array('user' => $user_id, 'order-by' => 'up.id', 'order-direction' => 'DESC', 'limit' => 11));
	if(count($photos) > 4)
	{
		$output .= photos_list_mini($photos);
	}
	elseif(count($photos) > 0)
	{
		$output .= photos_list_mini($photos);
	}
	
	
	/* User flags */
	include(PATHS_INCLUDE . 'user_flags.php');
	$query = 'SELECT ufl.* FROM user_flags AS uf, user_flags_list AS ufl WHERE user = "' . 
	$params['user_id'] . '" AND ufl.id = uf.flag';
	$result = mysql_query($query) or die(report_sql_error($query));
	while($data = mysql_fetch_assoc($result))
	{
		$flags[] = $data;
	}

	if(count($flags) > 0)
	{
		$output .= '<div style="margin-bottom: 10px;" class="repeat">' . "\n";
		$output .= '<div class="top">' . "\n";
		$output .= '<div class="bottom" style="padding: 10px;">' . "\n";
		$output .= '<div id="user_flags">' . "\n";
		foreach($flags AS $data)
		{
			$output .= '<img src="http://images.hamsterpaj.net/user_flags/' . 
			$data['handle'] . '.png" alt="' . $data['title'] . '" title="' . $data['title'] . '" id="' . $data['id'] . '" />' . "\n";
		}
		$output .= '</div>' . "\n";
		$output .= '<div id="flag_info">' . "\n";
		$output .= '</div>' . "\n";
		$output .= '</div>' . "\n";
		$output .= '</div>' . "\n";
		$output .= '</div>' . "\n";
	}

	/* Presentation text */
	$output .= '<div class="repeat">' . "\n";
	$output .= '<div class="top">' . "\n";
	$output .= '<div class="bottom">' . "\n";
	$output .= profile_presentation_parse(profile_presentation_load(array('user_id' => $user_id) ));
	$output .= '</div>' . "\n";
	$output .= '</div>' . "\n";
	$output .= '</div>' . "\n";
	
	if($user_id == $_SESSION['login']['id'])
	{
		$output .= '<a href="/installningar/profilesettings.php" id="profile_change_presentation">Gå till inställningar för din presentation &raquo;</a>';
	}
	
	// This is Lef damping on Joel for never fixing new_visitors. So he copypasted from the old profile. Joel will have to change this later on.
	if(isset($user_id) && $user_id > 0 && is_numeric($user_id) && $user_id != $_SESSION['login']['id'])
	{
		$fetch['login'] = array('id', 'username', 'regtimestamp', 'regip', 'lastlogon', 'lastip', 'lastaction', 'lastusernamechange', 'lastusername', 'password', 'session_id');
		$fetch['userinfo'] = array('contact1', 'contact2', 'gender', 'birthday', 'image', 'forum_signature', 'forum_posts');
		$fetch['traffa'] = array('firstname', 'profile_modules', 'color_theme');
		$userinfo = login_load_user_data($user_id, $fetch);
	}
	
	
	if ($_SESSION['login']['id'] != $user_id && isset($_SESSION['login']['id']))
	{
		if(!isset($_SESSION['profile_visits']))
		{
			$_SESSION['profile_visits'][] = array('id' => $user_id, 'username' => $userinfo['login']['username'], 'timestamp' => time());
		}
		else
		{
			$add_to_list = true;
			foreach($_SESSION['profile_visits'] AS $current)
			{
				if($current['id'] == $user_id)
				{
					$add_to_list = false;
				}
			}
			if($add_to_list == true)
			{
				array_unshift($_SESSION['profile_visits'], array('id' => $user_id, 'username' => $userinfo['login']['username'], 'timestamp' => time()));
			}
		}
	}
	
	if(count($_SESSION['profile_visits']) >= 10)
	{
		array_splice($_SESSION['profile_visits'], 10);
	}
	
	if(login_checklogin() == 1 && $user_id != $_SESSION['login']['id'])
	{
		/* Log the visit to database */
		$query = 'INSERT INTO user_event_log(user, action, remote_user_id, timestamp) ';
		$query .= 'VALUES("' . $user_id . '", "profile_visit", "' . $_SESSION['login']['id'] . '", "' . time() . '")';
		mysql_query($query) or die(report_sql_error($query));
		
		/* Log the visit to database - New system */
		$increase_profile_visitors = true;
		foreach($_SESSION['profile_visits'] AS &$current)
		{
			if($current['id'] == $user_id)
			{
				if(isset($current['timestamp']) && $current['timestamp'] > (time() - 120))
				{
					$increase_profile_visitors = false;
				}
				// Note: $current is a pointer!

				$current['timestamp'] = time();
			}
		}
		if($increase_profile_visitors == true)
		{
			$query = 'UPDATE userinfo SET profile_visitors = profile_visitors + 1 WHERE userid = ' . $user_id;
			mysql_query($query) or die(report_sql_error($query));	
		}
		
		$querys = array();
		$querys['insert'] = 'INSERT INTO user_visits(user_id, item_id, type, count, timestamp) VALUES(' . $user_id . ', ' . $_SESSION['login']['id'] . ', "profile_visit", 1, unix_timestamp())';
		$querys['update'] = 'UPDATE user_visits SET count = count + 1, timestamp = unix_timestamp() WHERE user_id=' . $user_id . ' AND item_id=' . $_SESSION['login']['id'] . ' AND type="profile_visit"';
		@mysql_query($querys['insert']) or @mysql_query($querys['update']);
		unset($querys);
				

		/* Read remote session and log this visit */		
		$remote_session = session_load($userinfo['login']['session_id']);
		
		if($_SESSION['userinfo']['image'] == 1 || $_SESSION['userinfo']['image'] == 2)
		{
			$visited = false;
			foreach($remote_session['visitors_with_image'] AS $visitor)
			{
				$visited = ($visitor['id'] == $_SESSION['login']['id']) ? true : $visited;
			}
			
			if($visited == false)
			{
				while(count($remote_session['visitors_with_image']) >= 8)
				{
					array_pop($remote_session['visitors_with_image']);
				}
				array_unshift($remote_session['visitors_with_image'], array('id' => $_SESSION['login']['id'], 'timestamp' => time(), 'username' => $_SESSION['login']['username']));
			}
		}
		
		$remote_session['notice_message'] = 'Hey där, <a href="/traffa/profile.php?id=' . $_SESSION['login']['id'] . '">' . $_SESSION['login']['username'] . '</a> sladdade just in på din profil! - <a href="/traffa/my_visitors_joel.php">Visa alla dina besökare!</a>';
		session_save($userinfo['login']['session_id'], $remote_session);
	}
	//End of Lef
	
	$output .= profile_bottom($profile);
	ui_top($ui_options);
	echo $output;
	ui_bottom();
?>