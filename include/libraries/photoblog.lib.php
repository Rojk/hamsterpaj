<?php
	function photoblog_fetch_active_user_data($options)
	{
		if(!isset($options['user_id']) && isset($options['username']) && preg_match('/^[a-zA-Z0-9-_]+$/', $options['username']) && strtolower($options['username']) != 'borttagen')
		{
			$query = 'SELECT id AS user_id FROM login WHERE username LIKE "' . str_replace('_', '\\_', $options['username']) . '" LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
			if(mysql_num_rows($result) == 1)
			{
				$data = mysql_fetch_assoc($result);
				$options['user_id'] = $data['user_id'];
			}
			else
			{
				throw new Exception('The username cannot be found!');
			}
		}
		
		if(!isset($options['user_id']))
		{
			throw new Exception('No user_id passed to photoblog_fetch_active_user_data function.');
		}
		
		if(!is_numeric($options['user_id']))
		{
			throw new Exception('Die die die! Die hard!');
		}
		
		$query = 'SELECT pp.*, l.id, l.username';
		$query .= ' FROM login AS l, photoblog_preferences AS pp';
		$query .= ' WHERE pp.user_id = l.id AND l.id = "' . $options['user_id'] . '"';
		$query .= ' LIMIT 1';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

		if(mysql_num_rows($result) == 0)
		{		
			$insert_query = 'INSERT INTO photoblog_preferences (user_id, color_main, color_detail, members_only, friends_only, copy_protection)';
			$insert_query .= ' VALUES(' . $options['user_id'] . ', "333333", "FF8040", 0, 0, 0)';
			mysql_query($insert_query) or report_sql_error($insert_query, __FILE__, __LINE__);
		}
		
		// Do it again to find out if it works now when the default values are filled in...
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(mysql_num_rows($result) == 1)
		{
			return mysql_fetch_assoc($result);
		}
		else
		{
			throw new Exception('Fatal error: No entries found, expected 1! (Or something like that, I suppose we are fetching SOMETHING).');
		}
	}
	
	function photoblog_upload_upload( $options )
	{
		if( !isset( $options['user'] ) )
		{
			throw new Exception('You must specify an user id.');
		}
		
		if( !isset(  $options['file_temp_path'] ) )
		{
			throw new Exception('Missing parameter: file_temp_path');
		}
		
		$options['category'] = isset($options['category']) ? $options['category'] : 'Övriga Bilder';
		$category = photoblog_categories_fetch(array('user' => $options['user'], 'name' => $options['category'], 'create_if_not_found' => true));
		$category = array_pop($category);
		
		$query = 'INSERT INTO user_photos (user, upload_complete, date, category)';
		$query .= ' VALUES("' . $options['user'] . '", 0, "' . date('Y-m-d') . '", "' . $category['id'] . '")';
		if( ! mysql_query($query) )
		{
			report_sql_error($query, __FILE__, __LINE__);
			throw new Exception('Query failed');
		}
		
		$photo_id = mysql_insert_id();
		
		$folder = floor($photo_id / 5000);
		

		
		// Check if folders exists, otherwise, create it
		foreach(array('mini', 'thumb', 'full') AS $format)
		{
			if(!is_dir(PHOTOS_PATH . $format . '/' . $folder))
			{
				mkdir(PHOTOS_PATH . $format . '/' . $folder);
			}
		}

		$image_size = getimagesize($options['file_temp_path']);
		
		$square = min($image_size[0], $image_size[1]);
		$width = round($square * 0.9);
		$height = ($width / 4) * 3;
		
		$mini = 'convert ' . $options['file_temp_path'] . ' -gravity center -crop ' . $width . 'x' . $height . '+0+0 -resize 50x38! ' . PHOTOS_PATH . 'mini/' . $folder . '/' . $photo_id . '.jpg';
		$thumb = 'convert ' . $options['file_temp_path'] . ' -gravity center -crop ' . $width . 'x' . $height . '+0+0 -resize 150x112! ' . PHOTOS_PATH . 'thumb/' . $folder . '/' . $photo_id . '.jpg';
		$full = 'convert -resize "630x630>" ' . $options['file_temp_path'] . ' ' . PHOTOS_PATH . 'full/' . $folder . '/' . $photo_id . '.jpg';

		system($mini);
		system($thumb);
		system($full);
		
		return $photo_id;
	}
	
	function photoblog_sort_module($photos, $options = array())
	{
		if(!isset($options['user']))
		{
			if(login_checklogin())
			{
				$options['user'] = $_SESSION['login']['id'];
			}
			else
			{
				throw new Exception('No user specified and not logged in.');
			}
		}
		
		$options['save_path'] = isset($options['save_path']) ? $options['save_path'] : '/fotoblogg/sortera/spara_sortering';
		$out = '<div class="photoblog_sort_module">' . "\n";
		$out .= 'Save path: ' . $options['save_path'];
		
		$out .= '<ul class="albums">' . "\n";
		
		$categories = photoblog_categories_fetch(array('user' => $options['user']));
		foreach($categories as $category)
		{
			$out .= '<li id="photoblog_sort_album_' . $category['id'] . '"><pre>' . print_r($category, true) . '</pre></li>' . "\n";
		}
		
		$out .= '</ul>' . "\n";
		
		$out .= '<ul class="photos">';
		foreach($photos as $photo)
		{
			$out .= '<li id="photoblog_sort_' . $photo['id'] . '"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id'] / 5000) . '/' . $photo['id'] . '.jpg" alt="Dra till en kategori..." /></li>' . "\n";
		}
		$out .= '</ul>' . "\n";
		
		$out .= '</div>' . "\n";
		
		return $out;
	}
	
	function photoblog_photos_fetch($options)
	{
		if(isset($options['id']))
		{
			$options['id'] = (is_array($options['id'])) ? $options['id'] : array($options['id']);
		}
		
		if(isset($options['category']))
		{
			$options['category'] = (is_array($options['category'])) ? $options['category'] : array($options['category']);
		}
		
		if(isset($options['date']))
		{
			$options['date'] = (is_array($options['date'])) ? $options['date'] : array($options['date']);
		}
		
		$options['order-by'] = (in_array($options['order-by'], array('up.id', 'up.date'))) ? $options['order-by'] : 'up.id';
		$options['order-direction'] = (in_array($options['order-direction'], array('ASC', 'DESC'))) ? $options['order-direction'] : 'ASC';
		$options['offset'] = (isset($options['offset']) && is_numeric($options['offset'])) ? $options['offset'] : 0;
		$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 9999;
		
		$query = 'SELECT up.*, l.username';
		$query .= ' FROM user_photos AS up, login AS l';
		$query .= ' WHERE l.id = up.user';
		$query .= ' AND up.deleted = 0';
		$query .= (isset($options['include_removed_photos']) && $options['include_removed_photos'] == true) ? '' : ' AND l.is_removed = 0';
		$query .= (isset($options['id'])) ? ' AND up.id IN("' . implode('", "', $options['id']) . '")' : '';
		$query .= (isset($options['user'])) ? ' AND up.user  = "' . $options['user'] . '"' : '';
		$query .= (isset($options['month'])) ? ' AND DATE_FORMAT(up.date, "%Y%m") = "' . $options['month'] . '"' : '';
		$query .= (isset($options['date'])) ? ' AND up.date IN("' . implode('", "', $options['date']) . '")' : '';
		$query .= (isset($options['category'])) ? ' AND up.category IN("' . implode('", "', $options['category']) . '")' : '';
		$query .= (isset($options['force_unread_comments']) && $options['force_unread_comments'] == true) ? ' AND up.unread_comments > 0' : '';
		$query .= ' ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		$photos = array();
		while($data = mysql_fetch_assoc($result))
		{
			$data['description'] = (strlen($data['description']) > 0) ? $data['description'] : 'Ingen beskrivning';
			$photos[] = $data;
			$found_something = true;
		}
		
		return $photos;
	}
	
	function photoblog_photos_update($data, $options = array())
	{
		if(isset($data['id']))
		{
			$options['id'] = (isset($options['id']) && is_numeric($options['id'])) ? $options['id'] : $data['id'];
			unset($data['id']);
		}
		
		if(isset($options['old_data']))
		{
			foreach($options['old_data'] as $key => $value)
			{
				if(isset($data[$key]) && $data['key'] == $value)
				{
					unset($data[$key]);
				}
			}
		}
		
		if(!isset($options['id']) || !is_numeric($options['id']))
		{
			throw new Exception('Could not find a numeric ID in the $options nor the $data array.');
		}
		
		if(!empty($data))
		{
			$update_data = array();
			foreach($data as $key => $value)
			{
				$update_data[] = $key . ' = "' . $value . '"';
			}
			
			$query = 'UPDATE user_photos SET ' . implode(', ', $update_data);
			$query .= ' WHERE id = "' . $options['id'] . '"';
			$query .= ' LIMIT 1';// Note: LIMIT 1 is used!
			
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}
		
		// Add more code for replacing photos etc. later...
	}
	
	function photoblog_categories_fetch($options)
	{
		$query = 'SELECT id, name, photo_count, (SELECT GROUP_CONCAT(id) FROM user_photos WHERE user = upc.user AND deleted = 0 AND category = upc.id LIMIT 9) AS photos';
		$query .= ' FROM user_photo_categories AS upc';
		$query .= ' WHERE 1';
		$query .= (isset($options['user'])) ? ' AND user = "' . $options['user'] . '"' : '';
		$query .= (isset($options['name'])) ? ' AND name LIKE "' . $options['name'] . '"' : '';
		$query .= (isset($options['id'])) ? ' AND id = "' . $options['id'] . '"' : '';
		$query .= ' ORDER BY name ASC';

		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		if(mysql_num_rows($result) == 0 && $options['create_if_not_found'] == true)
		{
			$query = 'INSERT INTO user_photo_categories (user, name) VALUES("' . $options['user'] . '", "' . $options['name'] . '")';

			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			if(mysql_insert_id() > 0)
			{
				$category['id'] = mysql_insert_id();
				$category['name'] = stripslashes($options['name']);
				$category['user'] = $options['user'];
				$category['photo_count'] = 0;
				$categories[] = $category;
			}
			else
			{
				return false;
			}
		}
		else
		{
			while($data = mysql_fetch_assoc($result))
			{
				if(strlen($data['name']) > 0)
				{
					$categories[] = $data;
				}
			}
		}
				
		return $categories;
	}
	
	function photoblog_comments_fetch($options = array())
	{
		$options['order_by_field'] = isset($options['order_by_field']) ? $options['order_by_field'] : 'c.comment_id';
		$options['order_by_order'] = (isset($options['order_by_order']) && in_array($options['order_by_order'], array('ASC', 'DESC'))) ? $options['order_by_order'] : 'DESC';
		
		$options['limit_start'] = (isset($options['limit_start']) && is_numeric($options['limit_start'])) ? $options['limit_start'] : 0;
		$options['limit_end'] = (isset($options['limit_end']) && is_numeric($options['limit_end'])) ? $options['limit_end'] : 100;
		
		$query  = 'SELECT c.*, l.username';
		$query .= ' FROM photoblog_comments AS c, login AS l';
		$query .= ' WHERE l.id = c.author';
		$query .= ' AND l.is_removed = 0';
		$query .= (isset($options['photo_id']) && is_numeric($options['photo_id'])) ? ' AND c.photo_id = ' . $options['photo_id'] : '';
		$query .= (isset($options['author']) && is_numeric($options['author'])) ? ' AND c.author = ' . $options['author'] : '';
		$query .= ' ORDER BY ' . $options['order_by_field'] . ' ' . $options['order_by_order'];
		$query .= ' LIMIT ' . $options['limit_start'] . ', ' . $options['limit_end'];
				
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		$comments = array();
		while($data = mysql_fetch_assoc($result))
		{
			$comments[] = $data;
		}
		
		return $comments;
	}
	
	function photoblog_comments_add($comment)
	{
		if(!isset($comment['comment']))
		{
			throw new Exception('Server error: No comment passed to function in options array. Terminating!');// Because "terminating" is such a cool word *NOT*
		}
		if(empty($comment['comment']))
		{
			throw new Exception('User error: Comment was empty, aborting.');
		}
		
		if(!login_checklogin() && !(isset($comment['author']) && is_numeric($comment['author'])))
		{
			throw new Exception('Server error: No author specified and user not logged on. Cannot post - aborting.');
		}
		
		if(!(isset($comment['photo_id']) && is_numeric($comment['photo_id'])))
		{
			throw new Exception('Server error: No photo_id specified, aborting.');
		}
		
		$query =  'INSERT INTO photoblog_comments(photo_id, author, comment)';
		$query .= ' VALUES ' . '(' . $comment['photo_id'] . ', ' . $comment['author'] . ' , "' . $comment['comment'] . '")';
		
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		return mysql_insert_id();
	}
	
	function photoblog_comments_list($comments, $options)
	{
		$options['use_container'] = (isset($options['use_container']) ? $options['use_container'] : true);
		if(count($comments) == 0)
		{
			 return false;
		}
		$out .= ($options['use_container']) ? '<div id="photoblog_comments_list">' . "\n" : '';
		$out .= '<ul>' . "\n";
		foreach ($comments as $comment)
		{
			$out .= '<li class="photoblog_comment">' . "\n";					
			$out .= '<div class="photoblog_comment_userinfo">' . "\n";
			$out .= ui_avatar($comment['author']);
			$out .= '<a href="/traffa/profile.php?user_id=' . $comment['author'] . '">' . $comment['username'] . '</a>' . "\n";
			$out .= '<span>' . $comment['date'] . '</span>' . "\n"; // 31 December
			$out .= '</div>' . "\n";
						
			$out .= '<div class="photoblog_comment_bubble_pointer">' . "\n";
			$out .= '<div class="photoblog_comment_text">' . "\n";
			$out .= '<p>' . nl2br($comment['comment']) . '</p>' . "\n";
			if(isset($comment['answer']))
			{
				$out .= '<div class="photoblog_comment_answer">' . "\n";
				$out .= '<span>Svar av: ' . $photoblog_user['username'] . '</span>' . "\n";
				$out .= '<p>' . nl2br($comment['answer']) . '</p>' . "\n";
				$out .= '</div>' . "\n";
			}
			$out .= '</div' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<br style="clear: both;" />' . "\n";
			$out .= '</li>' . "\n";
		}		
		$out .= '</ul>';
		$out .= ($options['use_container']) ? '</div>' . "\n" : '';	
		
		return $out;
	}
	
	function photoblog_comments_form($options)
	{
		$out .= '<div id="photoblog_comments_form">' . "\n";
		$out .= '<ul>' . "\n";
		$out .= '<li class="photoblog_comment">' . "\n";					
		$out .= '<div class="photoblog_comment_userinfo">' . "\n";
		$avatar_options['show_nothing'] = true;
		$out .= ui_avatar($_SESSION['login']['id'], $avatar_options);
		$out .= '</div>' . "\n";	
		$out .= '<div class="photoblog_comment_bubble_pointer">' . "\n";
		$out .= '<div class="photoblog_comment_text">' . "\n";
		$out .= '<form action="#" method="post">' . "\n";
		$out .= '<p>' . "\n";
		$out .= '<textarea name="comment">Kommentar</textarea>' . "\n";
		$out .= '<br />' . "\n";
		$out .= '<input class="submit" type="submit" value="Skicka" />' . "\n";
		$out .= '</p>' . "\n";
		$out .= '</form>' . "\n";
		$out .= '</div>' . "\n";
		$out .= '</div>' . "\n";
		$out .= '<br style="clear: both;" />' . "\n";
		$out .= '</li>' . "\n";	
		$out .= '</ul>';
		$out .= '</div>' . "\n";	
		
		return $out;
	}
	
	function photoblog_dates_fetch($options)
	{		
		$photo_options = array(
			'user' => $options['user']
		);
		$photos = photoblog_photos_fetch($photo_options);

		$return = array();
		foreach ( $photos as $photo )
		{
			$time = strtotime($photo['date']);

			list($year, $month, $day) = explode('-', date('Y-m-d', $time));

			$return[$year][$month][$day] = true;
		}
		natsort($return);
		return $return;
	}
	
	function photoblog_access($options)
	{
		$members_only = $options['members_only'];
		$friends_only = $options['friends_only'];
		$action = $options['action'];
		$owner_id = $options['owner_id'];
		
		switch($action)
		{
			case visit:
				if(userblock_checkblock($owner_id))
				{
					throw new Exception('Du är blockerad av användaren och kan därför inte besöka dess fotoblogg');
				}
				if($members_only == 1 && !login_checklogin())
				{
					throw new Exception('Du måste vara inloggad för att se den här personens fotoblogg. Varför inte besöka profilen istället? <a href="/traffa/profile.php?user_id=' . $owner_id . '">Gå till profil</a>');
				}
				if($friends_only == 1 && !friends_is_friends(array('user_id' => $_SESSION['login']['id'], 'friend_id' => $owner_id)))
				{
					throw new Exception('Du måste vara vän med personen för att se dess fotoblogg. Varför inte besöka profilen istället? <a href="/traffa/profile.php?user_id=' . $owner_id . '">Gå till profil</a>');
				}
			break;
			
			default:
			throw new Exception('No action was set');
			break;
		}
	}


	function photoblog_calendar($user_id, $month, $year)
	{
		$options = array('user' => $user_id);
		$used_dates = photoblog_dates_fetch($options);
		$used_dates = $used_dates[$year][$month];
		
		$date = mktime(12, 0, 0, $month, 1, $year);
		$daysInMonth = date('t', $date);
		
		$offset = date('N', $date);
		$rows = 1;
		$out .= '<div id="photoblog_calendar_month">' . "\n";
			$out.= '<a href="">&laquo;</a>' . "\n";
				$out .= '<span title="offset: ' . $offset . '">' . date('F', $date) . ', ' . $year . '</span>' . "\n";
			$out.= '<a href="">&raquo;</a>' . "\n";
		$out .= '</div>' . "\n";
		$out .= '<table>' . "\n";
		$out .= '<tr><th>M</th><th>T</th><th>O</th><th>T</th><th>F</th><th>L</th><th>S</th></tr>' . "\n";
		$out .= '<tr>';
		for($i = 1; $i < $offset; $i++)
		{
			$out .= '<td></td>' . "\n";
		}
		for($day = 1; $day <= $daysInMonth; $day++)
		{
			$format_day = ($day < 10) ? '0' . $day : $day;
			
			if( ($day + $offset - 2) % 7 == 0 && $day != 1)
			{
				$out .= '</tr><tr>' . "\n";
				$rows++;
			}
			$out .= '<td>' . (isset($used_dates[$format_day]) ? '<a href="#day-' . $year . $month . $day . '">' . $day . '</a>' : $day) . '</td>' . "\n";
		}
		while( ($day + $offset) <= $rows * 7)
		{
			$out .= '<td></td>' . "\n";
			$day++;
		}
		$out .= '</tr>' . "\n";
		$out .= '</table>' . "\n";
		$out .= '<div id="photoblog_calendar_year">' . "\n";
		$out .= '<span class="photoblog_calendar_year_pre">' . ((int)$year - 1) . '</span><span class="photoblog_calendar_year_after">' . ((int)$year + 1) . '</span>' . "\n";
		$out .= '</div>' . "\n";
		return $out;
	}
?>