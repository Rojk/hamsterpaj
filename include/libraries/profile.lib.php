<?php
	/* OPEN_SOURCE */
	
	function profile_fetch($options)
	{
		$options['viewer'] = isset($options['viewer']) ? $options['viewer'] : (login_checklogin() ? $_SESSION['login']['id'] : 0);
		
		//preint_r(array('viewer' => $options['viewer'], 'userblock_check' => userblock_check($options['user_id'], $options['viewer'])), 'Joel är en testare!');
		
		if($options['viewer'] > 0 && userblock_check($options['user_id'], $options['viewer']) == 1)
		{
			$options['error_message'] = 'Användaren har blockerat dig.';
		}
		else
		{
			$query = 'SELECT l.username, l.lastaction, l.lastlogon, u.gender, u.birthday, u.image, u.user_status, u.profile_theme, u.gb_entries, z.spot, z.zip_code, z.x_rt90, z.y_rt90, u.presentation_text, p.gb_anti_p12';
			$query .= ' FROM login AS l, userinfo AS u, zip_codes AS z, preferences AS p';
			$query .= ' WHERE l.id = "' . $options['user_id'] . '" AND u.userid = l.id AND z.zip_code = u.zip_code AND p.userid = l.id';
			if((!isset($options['show_removed_users'])) || (isset($options['show_removed_users']) && $options['show_removed_users'] == false))
			{
				$query .= ' AND l.is_removed = 0';
			}
			$query .= ' LIMIT 1';
			
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
			if(mysql_num_rows($result) > 0)
			{
				$data = mysql_fetch_assoc($result);
				
				if(strlen($data['presentation_text']) == 0)
				{
					$old_presentation_query = 'SELECT freetext AS presentation_text FROM traffa_freetext WHERE userid = "' . $options['user_id'] . '" LIMIT 1';
					$result = mysql_query($old_presentation_query) or report_sql_error($old_presentation_query);
					if(mysql_num_rows($old_presentation_result) > 0)
					{
						$old_presentation = mysql_fetch_assoc($old_presentation_result);
						$options['presentation_text'] = $old_presentation['presentation_text'];
						profile_presentation_save($options);
					}
					else
					{
						$options['presentation_text'] = 'Användaren har inte skapat någon presentation ännu.';
					}
				}
					
				$options = array_merge($options, $data);
			}
			else
			{
				$options['error_message'] = 'Den användaren hittade vi tyvärr inte.';
			}
		}
		
		return $options;
	}
	
	function profile_top($options)
	{
		$id = isset($options['profile_top_id']) ? ' id="' . $options['profile_top_id'] . '"': '';
		$output .= '<div class="profile_' . $options['profile_theme'] . '"' . $id . '>' . "\n";
		return $output;
	}
	
	function profile_bottom($options = array())
	{
		$output .= '</div>' . "\n";
		return $output;
	}
	
	function profile_mini_page($options)
	{
		$output .= profile_top($options);
		$output .= profile_head($options);
		$output .= profile_bottom($options);
		return $output;
	}
	
	function profile_head($params)
	{
		$include_profile_theme_style = isset($params['include_profile_theme_style']) ? ($params['include_profile_theme_style'] == true) : true;

		$id = isset($params['id']) ? ' id="' . $params['id'] . '"' : '';
		$out .= '<div class="profile_head"' . $id . '>' . "\n";

		$img = ($params['image'] == 1 || $params['image']== 2) ? IMAGE_URL  . 'images/users/thumb/' . $params['user_id'] : IMAGE_URL . 'user_no_image.png';
		$class = ($params['image'] == 1 || $params['image']== 2) ? 'user_avatar' : 'no_avatar';

		$out .= '<div class="avatar_passepartout">' . "\n";
		$out .= ui_avatar($params['user_id']);
		//$out .= '<img src="' . $img . '" class="' . $class . '" />' . "\n";
		$out .= '</div>' . "\n";
				
		$out .= '<div class="name_asl">' . "\n";
		$out .= '<span class="username">' . $params['username']. '</span> ';
		$genders = array('m' => 'kille', 'f' => 'tjej');
		$out .= (isset($genders[$params['gender']])) ? '<span class="gender">' . $genders[$params['gender']]. '</span> ' : '';		
		$out .= ($params['birthday'] != '0000-00-00') ? '<span class="age">' . date_get_age($params['birthday']) . '</span> ' : '';
		
		if($params['x_rt90'] > 0 && $params['y_rt90'] > 0)
		{
			$location = $params['spot'];
			
		  if(login_checklogin() && $_SESSION['userinfo']['x_rt90'] > 0 && $_SESSION['userinfo']['y_rt90'] > 0 && $params['zip_code'] != $_SESSION['userinfo']['zip_code'])
		  {
		    $location .= ' (' . rt90_readable(rt90_distance($params['x_rt90'], $params['y_rt90'], $_SESSION['userinfo']['x_rt90'], $_SESSION['userinfo']['y_rt90'])) . ')';
		  }
		  
	    /* Note RT90 Y and X values are flipped, due to a "bug" at hitta.se */
	    /* Reference: daniel.eklund@hitta.se */
	    
	    $location .= ' <input type="button" value="Visa på karta" class="button_90" onclick="window.open(\''
			. 'http://www.hitta.se/LargeMap.aspx?ShowSatellite=false&pointX=' . $params['y_rt90']
			. '&pointY=' . $params['x_rt90'] . '&cx=' . $params['y_rt90']
			. '&cy=' . $params['x_rt90'] . '&z=6&name=' . $params['username']
			. '\', \'user_map_' . $params['username'] . '\', \'location=false, width=750, height=500\');" />' . "\n";
			
			$out .= '<span class="spot">' . $location . '</span> ';
		}
		
		if($params['lastaction'] > time() - 600)
		{
			$out .= '<span class="online">online</span>' . "\n";
		}
		else
		{
			$out .= '<span class="last_seen">senast ' . fix_time($params['lastlogon']) . '</span>' . "\n";
		}
		$out .= '</div>' . "\n";
		
		$out .= '<p class="user_status">' . $params['user_status'] . '</p>' . "\n";
		
		$query = 'SELECT * FROM user_action_log WHERE user = "' . $params['user_id'] . '" ORDER BY id DESC LIMIT 3';
		$result = mysql_query($query) or  report_sql_error($query, __FILE__, __LINE__);
		while($event = mysql_fetch_assoc($result))
		{
			$events[] = $event;
		}
		array_reverse($events);
		if(count($events) > 0)
		{
			$out .= '<ul class="user_action_log">' . "\n";
			foreach($events AS $event)
			{
				switch($event['action'])
				{
					case 'friendship':
						$out .= '<li><span class="time">' . fix_time($event['timestamp']) . '</span> blev kompis med <a href="' . $event['url'] . '">' . $event['label'] . '</a></li>' . "\n";
						break;
					case 'diary':
						$out .= '<li><span class="time">' . fix_time($event['timestamp']) . '</span> skrev i dagboken <a href="' . $event['url'] . '">' . $event['label'] . '</a></li>' . "\n";
						break;
					case 'photos':
						$out .= '<li><span class="time">' . fix_time($event['timestamp']) . '</span> ny bild <a href="' . $event['url'] . '">' . substr($event['label'], 0, 45) . '</a></li>' . "\n";
						break;
				}
			}
			$out .= '</ul>' . "\n";
		}
		
		$profile_modules['presentation']['label'] = 'Presentation';
		$profile_modules['presentation']['url'] = '/traffa/profile.php?user_id=%USERID%';

		$profile_modules['guestbook']['label'] = 'Gästbok';
		$profile_modules['guestbook']['url'] = '/traffa/guestbook.php?view=%USERID%';

		$profile_modules['photos']['label'] = 'Fotoalbum';
		$profile_modules['photos']['url'] = '/traffa/photos.php?user_id=%USERID%';
	
		$profile_modules['diary']['label'] = 'Dagbok';
		$profile_modules['diary']['url'] = '/traffa/diary.php?user_id=%USERID%';

		$profile_modules['friends']['label'] = 'Vänner';
		$profile_modules['friends']['url'] = '/traffa/friends.php?user_id=%USERID%';

		$profile_modules['facts']['label'] = 'Fakta';
		$profile_modules['facts']['url'] = '/traffa/user_facts.php?user_id=%USERID%';
		
		$profile_modules['my_visitors']['label'] = 'Besökare';
		$profile_modules['my_visitors']['url'] = '/traffa/my_visitors.php?user_id=%USERID%';
		
		$profile_modules['photoblog']['label'] = 'Fotoblogg (beta)';
		$profile_modules['photoblog']['url'] = '/fotoblogg/%USERNAME%';
			
		$out .= '<div class="navigation">' . "\n";
		$out .= '<ul>' . "\n";
		foreach($profile_modules AS $handle => $module)
		{
			$class = ($handle == $params['active_tab']) ? ' class="active"' : '';
			if(isset($module['url']))
			{
				$out .= '<li' . $class . '><a href="' . str_replace(array('%USERID%', '%USERNAME%'), array($params['user_id'], $params['username']), $module['url']) . '">' . $module['label'] . '</a></li>' . "\n";
			}
			else
			{
				$out .= '<li' . $class . '>' . $module['label'] . '</li>' . "\n";
			}
		}
		$out .= '</ul>' . "\n";
		$out .= '</div>' . "\n";
		$out .= '</div>' . "\n";
		
		return $out;
	}
	
	function profile_presentation_load($options)
	{
		$query = 'SELECT presentation_text FROM userinfo WHERE userid = "' . $options['user_id'] . '" LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query);
		if(mysql_num_rows($result) > 0)
		{
			$data = mysql_fetch_assoc($result);
			$options['presentation_text'] = 'Hej';
			
			if(strlen($data['presentation_text']) > 0)
			{
				$options['presentation_text'] = $data['presentation_text'];
			}
			else
			{
				$query = 'SELECT freetext AS presentation_text FROM traffa_freetext WHERE userid = "' . $options['user_id'] . '" LIMIT 1';
				$result = mysql_query($query) or report_sql_error($query);
				if(mysql_num_rows($result) > 0)
				{
					$data = mysql_fetch_assoc($result);
					if(strlen($data['presentation_text']) > 0)
					{
						// DO NOT FORGET TO MAKE SAFE AGAIN!
						$options['presentation_text'] = mysql_real_escape_string(htmlentities($data['presentation_text']));
						profile_presentation_save($options);
					}
					else
					{
						$options['presentation_text'] = 'Användaren har inte skapat någon presentation ännu.';
					}
				}
				else
				{
					$options['presentation_text'] = 'Användaren har inte skapat någon presentation ännu.';
				}
			}
		}
		else
		{
			$options['presentation_text'] = 'Användaren hittades inte i databasen.';
		}
		
		return $options;
	}
	
	function profile_presentation_save($options)
	{
		$query = 'UPDATE userinfo SET presentation_text = "' . $options['presentation_text'] . '" WHERE userid = "' . $options['user_id'] . '" LIMIT 1';
	
		mysql_query($query) or report_sql_error($update_query, __FILE__, __LINE__);
		
		$rounded_corners_config['color'] = 'orange_deluxe';
		
		$return .= rounded_corners_top($rounded_corners_config);
		$return .= 'Presentationen sparades, så varför inte <a href="/traffa/profile.php?show_change_profile_notice=true">ta en titt på den</a>?';
		$return .= rounded_corners_bottom($rounded_corners_config);
		
		return $return;
	}
	
	function profile_presentation_parse($options)
	{
		if(!isset($options['presentation_text']))
		{
			return 'Presentation data does not exist.';
		}
		
		$bbcode_ruleset = array(
			''=>     array('type' => BBCODE_TYPE_ROOT),
			'i'=>    array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<i>', 'close_tag' => '</i>'),
			'b'=>    array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<strong>', 'close_tag'=>'</strong>'),
			'rubrik'=>  array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<h2>', 'close_tag' => '</h2>', 'childs'=>''),
			'underrubrik'=>  array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<h3>', 'close_tag' => '</h3>', 'childs'=>''),
			'minirubrik'=>  array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<h4>', 'close_tag' => '</h4>', 'childs'=>''),
		);
		
		//$options['presentation_text'] = clickable_links($options['presentation_text']);
		
		$options['presentation_text'] = nl2br($options['presentation_text']);
		$options['presentation_text'] = str_replace(array("\n", "\r"), '', $options['presentation_text']);
		
		$bbcode_handler = bbcode_create($bbcode_ruleset);
		$options['presentation_text'] =  bbcode_parse($bbcode_handler, $options['presentation_text']);

		$pattern = '/\[poll:(.+?)\]/';
		$options['presentation_text'] =  preg_replace_callback($pattern, 'profile_presentation_poll_tag_callback', $options['presentation_text']);
			
		$options['presentation_text'] = setSmilies($options['presentation_text']);
		
		//$options['presentation_text'] = profile_presentation_friends_tag_callback($options);
		
		$pattern = '/\[link:(profile\]([a-zA-Z0-9_-]+)|webb\](.+?))\[\/link\]/';
		$options['presentation_text'] = preg_replace_callback($pattern, 'profile_presentation_link_tag_callback', $options['presentation_text']);
		
		$pattern = '/\[link:(photos|guestbook)\]/';
		$options['presentation_text'] = preg_replace_callback($pattern, 'profile_presentation_link_tag_callback', $options['presentation_text']);
		
		$pattern = '/\[fotoalbum:([0-9]+)\]/';
		$options['presentation_text'] = str_replace('%USERID%', $options['user_id'], preg_replace_callback($pattern, 'profile_presentation_photos_callback', $options['presentation_text']) );

		$output .= '<div class="profile_presentation_text">';
		$output .= $options['presentation_text'];
		$output .= '</div>';
		
		return $output;
	}
	
	function profile_presentation_link_tag_callback($matches)
	{
		$type = substr($matches[1], 0, strpos($matches[1] . ']', ']')); //profile, webb, photos, guestbook, ..., ...
		
		switch($type)
		{
			case 'profile':
			return '<a href="/traffa/quicksearch.php?username=' . $matches[2] . '">' . $matches[2] . '</a>';
			
			case 'webb':
			return clickable_links(unset_smilies($matches[3]));
			
			case 'photos':
			return '<a href="/traffa/photos.php?user=%USERID%">fotoalbum</a>';
			
			case 'guestbook':
			return '<a href="/traffa/guestbook.php?userid=%USERID%">gästbok</a>';
		}
	}
	
	function profile_presentation_photos_callback($matches)
	{
		if(!is_numeric($matches[1]))
		{
			return 'Hacker där :(';
		}
		
		$photo_id = $matches[1];
		$photo_object = photos_fetch(array('id' => $photo_id, 'limit' => 1));
		if(count($photo_object) < 1)
		{
			return '<strong>[Bilden finns inte]</strong>';
		}
		
		$output .= photos_list($photo_object);
		$output .= '<br style="clear: both;" />';
		
		return $output;
	}
	
	function profile_presentation_poll_tag_callback($matches)
	{
		$poll = poll_fetch(array('id' => $matches[1]));
		if(count($poll) == 1)
		{
			return poll_render($poll[0]);
		}
	}
		
	function profile_presentation_change_form($options)
	{
		$options['post_to'] = isset($options['post_to']) ? $options['post_to'] : '/installningar/profilesettings.php';
		
		$return .= '<form action="' . $options['post_to'] . '" method="post" id="profile_presentation_change_form">' . "\n";

		$return .= '	<button id="profile_presentation_change_bold_control" class="button_30">Fet</button>' . "\n";
		$return .= '	<button id="profile_presentation_change_italic_control" class="button_50">Kursiv</button>' . "\n";
		$return .= '	<button id="profile_presentation_change_header_control" class="button_80">R U B R I K</button>' . "\n";
		$return .= '	<input type="button" id="profile_presentation_change_image_control" class="button_130" value="Bild från fotoalbumet" />' . "\n";
		$return .= '	<button id="profile_presentation_change_poll_control" class="button_80">Omröstning</button>' . "\n";
		//$return .= '	<button id="profile_presentation_change_friends_control" class="button_90">Vänner-ruta</button>' . "\n";
		$return .= '	<button id="profile_presentation_change_link_control" class="button_70">Länk till...</button>' . "\n";
		
		unset($rounded_corners_config);
		$rounded_corners_config['color'] = 'blue_deluxe';
		$rounded_corners_config['id'] = 'profile_presentation_change_markup_properties';
		
		$return .= rounded_corners_top($rounded_corners_config) . "\n";		
		$return .= '<div id="profile_presentation_change_markup_properties_content">&nbsp;</div>';
		$return .= '<button id="profile_presentation_change_markup_properties_close" class="button_60">Stäng</button>';
		$return .= '<br style="clear: both" />' . "\n";
		$return .= rounded_corners_bottom($rounded_corners_config) . "\n";
		
		$return .= '<textarea name="presentation_text" id="profile_presentation_change_presentation_text" tabindex="2">' . $options['presentation_text'] . '</textarea>' . "\n";
		
		$return .= '<br />' . "\n";

		$return .= '<button id="profile_presentation_change_preview_button" tabindex="3" class="button_120">Förhandsgranska</button>';
		$return .= ' <input type="submit" value="Spara" id="profile_presentation_change_save" tabindex="3" class="button_60" />' . "\n";
		
		$return .= '<br style="clear: both;" /></form>' . "\n";
		
		
		$return .= '<div id="profile_presentation_change_preview_area">&nbsp;</div>';
		
		return $return;
	}
?>