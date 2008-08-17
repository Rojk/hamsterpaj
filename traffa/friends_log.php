<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	require(PATHS_INCLUDE . 'libraries/userblock.lib.php');
	require(PATHS_INCLUDE . 'libraries/photos.lib.php');

	$ui_options['title'] = 'DATOR';
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'friends_log.css';
	$ui_options['stylesheets'][] = 'photos.css';
	
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
	
		$query = 'SELECT * FROM friendslist, user_action_log, login, userinfo WHERE userinfo.userid = login.id AND login.id = friendslist.friend_id AND friendslist.user_id = ' . $user_id . ' AND user_action_log.user = friendslist.friend_id ORDER BY user_action_log.timestamp DESC LIMIT 30';

		$result = mysql_query($query) or  report_sql_error($query, __FILE__, __LINE__);
		while($event = mysql_fetch_assoc($result))
		{
			$events[] = $event;
		}
		array_reverse($events);
		if(count($events) > 0)
		{
			$out .= '<ul class="friends_log">' . "\n";
			foreach($events AS $event)
			{
				switch($event['action'])
				{
					case 'friendship':
						$friendid = substr($event['url'], 23);
						if($friendid != $user_id)
						{
						$options['user_id'] = $event['friend_id'];
						$out .= message_top($options);
						$out .= '<span class="timestamp">' . fix_time($event['timestamp']) . '</span>' . "\n";
						$out .= '<a href="/traffa/profile.php?id=' . $event['friend_id'] . '">' . $event['username'] . '</a> ' . "\n";
						$genders = array('f' => 'F', 'm' => 'P');
						$out .= $genders[$event['gender']];
						$out .= (date_get_age($event['birthday']) > 0) ? date_get_age($event['birthday']) : '';
						$out .= '<p>Ey, pysen! Jag blev precis kompis med <a href="' . $event['url'] . '">' . $event['label'] . '</a>. Så sött med många vänner :)</p>' . "\n";
						$out .= message_bottom();
					}
					break;
					case 'diary':
						$options['user_id'] = $event['friend_id'];
						$out .= message_top($options);
						$out .= '<span class="timestamp">' . fix_time($event['timestamp']) . '</span>' . "\n";
						$out .= '<a href="/traffa/profile.php?id=' . $event['friend_id'] . '">' . $event['username'] . '</a> ' . "\n";
						$genders = array('f' => 'F', 'm' => 'P');
						$out .= $genders[$event['gender']];
						$out .= (date_get_age($event['birthday']) > 0) ? date_get_age($event['birthday']) : '';
						$out .= '<p>Tjoho, läs min senaste bloggning: <a href="' . $event['url'] . '">' . $event['label'] . '</a><br />Och glöm för sablen inte att kommentera!</p>' . "\n";
						$out .= message_bottom();
					break;
					case 'photos':
						$photos = photos_fetch(array('id' => substr($event['url'], 22), 'limit' => 1));
						if(!isset($photos))
						{
							
						}
						else
						{
						$options['user_id'] = $event['friend_id'];
						$out .= message_top($options);
							$out .= '<span class="timestamp">' . fix_time($event['timestamp']) . '</span>' . "\n";
							$out .= '<a href="/traffa/profile.php?id=' . $event['friend_id'] . '">' . $event['username'] . '</a> ' . "\n";
							$genders = array('f' => 'F', 'm' => 'P');
							$out .= $genders[$event['gender']];
							$out .= (date_get_age($event['birthday']) > 0) ? date_get_age($event['birthday']) : '';
							$out .= photos_list_mini($photos);
							$out .= '<p>Jag är en cool kis, så jag ladda precis upp ett foto. Titta på det och kommentera vetja :) <a href="' . $event['url'] . '">' . $event['label'] . '</a></p>' . "\n";
						$out .= message_bottom();
						}
					break;
				}
				
			}
			$out .= '</ul>' . "\n";
		}
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?> 