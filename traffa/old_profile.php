<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'profile-modules.php');
	require(PATHS_INCLUDE . 'traffa-functions.php');

	$ui_options['admtoma_category'] = 'profile_pages';
	$ui_options['menu_path'] = array('traeffa');
	$ui_options['stylesheets'] = array('amuse.css', 'digga_module.css', 'user_flags.css', 'promoe_new.css');
	if ($_SESSION['login']['userlevel'] >= 3)
	{
		$ui_options['javascripts'] = array('xmlhttp.js');
	}
	$ui_options['javascripts'][] = 'photoalbum.js';
	$ui_options['javascripts'][] = 'user_flags.js';
	ui_top($ui_options);

	unset($_SESSION['age_guess']);

	if(isset($_GET['id']) && $_GET['id'] > 0 && is_numeric($_GET['id']) && $_GET['id'] != $_SESSION['login']['id'])
	{
		$userid = $_GET['id'];
		$fetch['login'] = array('id', 'username', 'userlevel', 'regtimestamp', 'regip', 'lastlogon', 'lastip', 'lastaction', 'lastusernamechange', 'lastusername', 'password', 'session_id');
		$fetch['userinfo'] = array('contact1', 'contact2', 'gender', 'birthday', 'image', 'forum_signature', 'forum_posts');
		$fetch['traffa'] = array('firstname', 'profile_modules', 'color_theme');
		$userinfo = login_load_user_data($userid, $fetch);
		
		
	if($userinfo['login']['username'] == 'Borttagen')
	{
		echo '<p class="error">Denna användare existerar inte!</p>';
		ui_bottom();
		exit;
	}
			
		if(strlen($userinfo['login']['username']) < 1)
		{
			$die = 1;
		}
	}
	elseif(!is_numeric($_GET['id']) && isset($_GET['id']))
	{
		$die = 1;
	}
	else
	{
/*
if(644314 == $_SESSION['login']['id'])
{
	preint_r($userinfo);
}

*/
		if(isset($_SESSION['login']['id']))
		{
			$userid = $_SESSION['login']['id'];
			$userinfo['login'] = $_SESSION['login'];
			$userinfo['userinfo'] = $_SESSION['userinfo'];
			$userinfo['traffa'] = $_SESSION['traffa'];
			$userinfo['preferences'] = $_SESSION['preferences'];
			/* Since the users presentation isn't stored in the session array, we will have to fetch it from the database... */
			$fetch['traffa'] = array('color_theme');
			
			$color_theme = login_load_user_data($userid, $fetch);
			$userinfo['traffa']['color_theme'] = $color_theme['traffa']['color_theme'];
			unset($presentation);
		}
		else
		{
			$die = 1;
		}
	}
	
	switch($userinfo['traffa']['color_theme'])
	{
		case '1':
			$profile_colors['light'] = '#edf4fd';
			$profile_colors['background'] = '#c9ddf9';
			$profile_colors['dark'] = '#7ba0cf';
			$profile_colors['border'] = '#3f5879';
			break;
		case '2':
			$profile_colors['light'] = '#f1edfd';
			$profile_colors['background'] = '#d1c9f9';
			$profile_colors['dark'] = '#897bcf';
			$profile_colors['border'] = '#493f79';
			break;
		case '3':
			$profile_colors['light'] = '#faedfd';
			$profile_colors['background'] = '#efc9f9';
			$profile_colors['dark'] = '#bb7bcf';
			$profile_colors['border'] = '#6b3f79';
			break;
		case '4':
			$profile_colors['light'] = '#fdedf7';
			$profile_colors['background'] = '#f9c9e7';
			$profile_colors['dark'] = '#cf7bb0';
			$profile_colors['border'] = '#793f64';
			break;
		case '5':
			$profile_colors['light'] = '#fdeeed';
			$profile_colors['background'] = '#f9c9c9';
			$profile_colors['dark'] = '#cf7b7d';
			$profile_colors['border'] = '#793f40';
			break;
		case '6':
			$profile_colors['light'] = '#fdf7ed';
			$profile_colors['background'] = '#f9e6c9';
			$profile_colors['dark'] = '#cfaa7b';
			$profile_colors['border'] = '#79603f';
			break;
		case '7':
			$profile_colors['light'] = '#fafded';
			$profile_colors['background'] = '#f0f9c9';
			$profile_colors['dark'] = '#c1cf7b';
			$profile_colors['border'] = '#6f793f';
			break;
		case '8':
			$profile_colors['light'] = '#f0fded';
			$profile_colors['background'] = '#d4f9c9';
			$profile_colors['dark'] = '#8dcf7b';
			$profile_colors['border'] = '#4b793f';
			break;
		case '9':
			$profile_colors['light'] = '#edfdf4';
			$profile_colors['background'] = '#c9f9dc';
			$profile_colors['dark'] = '#7bcf9a';
			$profile_colors['border'] = '#3f7954';
			break;
		case '10':
			$profile_colors['light'] = '#edfdfd';
			$profile_colors['background'] = '#c9f9f8';
			$profile_colors['dark'] = '#7bcfcd';
			$profile_colors['border'] = '#3f7978';
			break;
		default:
			$profile_colors['light'] = '#edf4fd';
			$profile_colors['background'] = '#c9ddf9';
			$profile_colors['dark'] = '#7ba0cf';
			$profile_colors['border'] = '#3f5879';
			break;
	}

	
	if($die == 1)
	{
		if($_GET['id'] > 0)
		{
			echo 'Medlemmen du söker kunde tyvärr inte hittas.';
			to_logfile('notice', __FILE__, __LINE__, 'user not found', $_GET['id']);
		}
		else
		{
			echo 'Du måste ange ett ID-nummer för att besöka en medlems profilsida!';
		}
	}
	else
	{
		if(strlen($userinfo['traffa']['profile_modules']) < 1)
		{
			$display_modules = array(2);
		}
		else
		{
			$display_modules = explode(',', $userinfo['traffa']['profile_modules']);
		}
		
		array_unshift($display_modules, 18);
		
		if($userinfo['userinfo']['radio_dj'] == 1 && $userinfo['login']['id'] != 15 && $userinfo['login']['id'] != 644314)
		{
			array_unshift($display_modules, 14);
		}
		if($_SESSION['login']['userlevel'] >= 3)
		{
			$display_modules[] = 0; /* Always show module #0 to admins */
			if (!in_array($display_modules))
			{
				$display_modules[] = 1; /* Always show module #1 (forum) to admins and above. */
			}
		}
		if($userid == $_SESSION['login']['id'])
		{
			$display_modules[] = 7; /* Show the "module chooser" to the presentations owner */
		}

		$query = 'SELECT id FROM snyggve WHERE owner = "' . $userid . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 1)
		{
			$userinfo['snyggve'] = 1;
		}

		traffa_draw_user_div($userid, $userinfo);
  	
		foreach($display_modules AS $current)
		{
			if(strlen($current) < 1)
			{
				continue;
			}	
			echo '<div style="margin-top: 5px;">' . "\n";
			if($modules[$current]['disable_title_block'] != true)
			{
				echo '<div style="display: block; z-index: 2; border: 1px solid #3f657a; border-bottom: none; background: ' . $profile_colors['background'] . '; padding: 3px; position: absolute;">' . "\n";
				echo $modules[$current]['title'] . "\n";
				echo '</div>';
				echo '<div style="height: 21px;">&nbsp;</div>';
			}
			if($modules[$current]['active'] != true)
			{
				echo 'Denna modul har deaktiverats';
			}
			elseif($modules[$current]['userlevel_use'] > $userinfo['login']['userlevel'])
			{
				echo 'Denna användaren får inte lov att använda denna modulen, därför visas den inte.';
			}
			else
			{
				include_once(PATHS_INCLUDE . 'profile_modules/' . $modules[$current]['filename'] . '.php');
			}

			echo '</div>' . "\n";
			if($modules[$current]['customizable'] == true && $userid == $_SESSION['login']['id'])
			{
				$label = (isset($modules[$current]['customize_label'])) ? $modules[$current]['customize_label'] : 'Anpassa &raquo;';
				echo '<a href="/traffa/customize_module.php?id=' . $current . '" style="font-weight: bold;">' . $label . '</a>';
			}
		}
	}

	if ($_SESSION['login']['id'] != $userid && isset($_SESSION['login']['id']))
	{
		if(!isset($_SESSION['profile_visits']))
		{
			$_SESSION['profile_visits'][] = array('id' => $userid, 'username' => $userinfo['login']['username'], 'timestamp' => time());
		}
		else
		{
			$add_to_list = true;
			foreach($_SESSION['profile_visits'] AS $current)
			{
				if($current['id'] == $userid)
				{
					$add_to_list = false;
				}
			}
			if($add_to_list == true)
			{
				array_unshift($_SESSION['profile_visits'], array('id' => $userid, 'username' => $userinfo['login']['username'], 'timestamp' => time()));
			}
		}
	}

	if(count($_SESSION['profile_visits']) >= 10)
	{
		array_splice($_SESSION['profile_visits'], 10);
	}
	
	if($_SESSION['login']['id'] > 0 && $userinfo['login']['id'] != $_SESSION['login']['id'])
	{
		/* Log the visit to database */
		$query = 'INSERT INTO user_event_log(user, action, remote_user_id, timestamp) ';
		$query .= 'VALUES("' . $userid . '", "profile_visit", "' . $_SESSION['login']['id'] . '", "' . time() . '")';
		mysql_query($query) or die(report_sql_error($query));
		
		/* Log the visit to database - New system */
		$increase_profile_visitors = true;
		foreach($_SESSION['profile_visits'] AS &$current)
		{
			if($current['id'] == $userid)
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
			$query = 'UPDATE userinfo SET profile_visitors = profile_visitors + 1 WHERE userid = ' . $userid;
			mysql_query($query) or die(report_sql_error($query));	
		}
		
		$querys = array();
		$querys['insert'] = 'INSERT INTO user_visits(user_id, item_id, type, count, timestamp) VALUES(' . $userid . ', ' . $_SESSION['login']['id'] . ', "profile_visit", 1, unix_timestamp())';
		$querys['update'] = 'UPDATE user_visits SET count = count + 1, timestamp = unix_timestamp() WHERE user_id=' . $userid . ' AND item_id=' . $_SESSION['login']['id'] . ' AND type="profile_visit"';
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

	ui_bottom();
?>
