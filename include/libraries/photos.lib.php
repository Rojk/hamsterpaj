<?php
	/* OPEN_SOURCE */
	function photos_upload_form($options)
	{
		$categories = photos_get_categories(array('user' => $options['user']));
		
		$output .= rounded_corners_top(array('color' => 'blue_deluxe'));
		$output .= '<button id="photos_upload_form_toggle">Ladda upp bilder</button>' . "\n";
		$output .= '<form method="post" enctype="multipart/form-data" id="photos_upload_form">' . "\n";
		$output .= '<span class="file_upload_label">Välj fil</span>' . "\n";
		$output .= '<span class="description_label">Beskriv din bild</span>' . "\n";
		$output .= '<span class="category_label">Välj kategori</span>' . "\n";
		$output .= '<ul>' . "\n";
		for($i = 0; $i < 5; $i++)
		{
			$output .= '<li>' . "\n";
			$output .= '<input class="file_upload" type="file" name="photo_' . $i . '" />' . "\n";
			$output .= '<input class="description" type="text" name="description_' . $i . '" />' . "\n";
			$output .= '<select name="category_' . $i . '" class="photo_category_selector">' . "\n";
			if(count($categories) == 0)
			{
				$output .= '<option value="Övriga bilder">Övriga bilder</option>' . "\n";
			}
			else
			{
				foreach($categories AS $category)
				{
					$output .= '<option value="' . $category['name'] . '">' . $category['name'] . '</option>' . "\n";					
				}
			}
			$output .= '<option value="new_category">Nytt album</option>' . "\n";
			$output .= '</select>' . "\n";
			$output .= '</li>' . "\n";
		}
		$output .= '</ul>' . "\n";
		$output .= '<input type="submit" value="Ladda upp" class="photo_upload_submit" />' . "\n";
		$output .= '</form>' . "\n";
		$output .= rounded_corners_bottom();
		
		return $output;
	}

	function photos_get_categories($options)
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

	function photos_upload($options)
	{
		if(!login_checklogin())
		{
			return false;
		}
		
		$category = photos_get_categories(array('user' => $options['user'], 'name' => $options['category'], 'create_if_not_found' => true));

		$category = array_pop($category);
	
		
		$query = 'INSERT INTO user_photos (user, description, category, date)';
		$query .= ' VALUES("' . $options['user'] . '", "' . $options['description'] . '", "' . $category['id'] . '", "' . date('Y-m-d') . '")';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		$id = mysql_insert_id();
		$folder = floor($id / 5000);

		$query = 'UPDATE user_photo_categories SET photo_count = photo_count + 1 WHERE id = "' . $category['id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		

		
		// Check if folders exists, otherwise, create it
		foreach(array('mini', 'thumb', 'full') AS $format)
		{
			if(!is_dir(PHOTOS_PATH . $format . '/' . $folder))
			{
				mkdir(PHOTOS_PATH . $format . '/' . $folder);
			}
		}

		$image_size = getimagesize($options['file']);
		
		$square = min($image_size[0], $image_size[1]);
		$width = round($square * 0.9);
		$height = ($width / 4) * 3;
		
		$mini = 'convert ' . $options['file'] . ' -gravity center -crop ' . $width . 'x' . $height . '+0+0 -resize 50x38! ' . PHOTOS_PATH . 'mini/' . $folder . '/' . $id . '.jpg';
		$thumb = 'convert ' . $options['file'] . ' -gravity center -crop ' . $width . 'x' . $height . '+0+0 -resize 150x112! ' . PHOTOS_PATH . 'thumb/' . $folder . '/' . $id . '.jpg';
		$full = 'convert -resize "630x630>" ' . $options['file'] . ' ' . PHOTOS_PATH . 'full/' . $folder . '/' . $id . '.jpg';

		system($mini);
		system($thumb);
		system($full);
		
		return $id;
	}

	function photos_fetch($options)
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
		
		$options['order-by'] = (in_array($options['order-by'], array('up.id'))) ? $options['order-by'] : 'up.id';
		$options['order-direction'] = (in_array($options['order-direction'], array('ASC', 'DESC'))) ? $options['order-direction'] : 'ASC';
		$options['offset'] = (isset($options['offset']) && is_numeric($options['offset'])) ? $options['offset'] : 0;
		$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 9999;
		
		$query = 'SELECT up.*, l.username';
		$query .= ' FROM user_photos AS up, login AS l';
		$query .= ' WHERE l.id = up.user';
		$query .= ' AND up.deleted = 0';
		$query .= ($_GET['slutatabort'] && is_privilegied('use_ghosting_tools')) ? '' : ' AND l.is_removed = 0';
		$query .= (isset($options['id'])) ? ' AND up.id IN("' . implode('", "', $options['id']) . '")' : '';
		$query .= (isset($options['user'])) ? ' AND up.user  = "' . $options['user'] . '"' : '';
		$query .= (isset($options['date'])) ? ' AND up.date IN("' . implode('", "', $options['date']) . '")' : '';
		$query .= (isset($options['category'])) ? ' AND up.category IN("' . implode('", "', $options['category']) . '")' : '';
		$query .= ($options['force_unread_comments'] == true) ? ' AND up.unread_comments > 0' : '';
		$query .= ' ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			$data['description'] = (strlen($data['description']) > 0) ? $data['description'] : 'namnlös';
			$photos[] = $data;
			$found_something = true;
		}
		
		return $photos;
	}
	
	function photos_fetch_next_id($options)
	{
		$options['offset'] = 0;
		$options['limit'] = 1;
		$photos = photos_fetch($options);
		$photos = $photos[0];

		$id_where = $options['direction'] == 'left' ? '<' : '>';
		$sort = $options['direction'] == 'left' ? 'DESC' : 'ASC';
		$query = 'SELECT id FROM user_photos AS up WHERE id '.$id_where.' '.$options['id'].' AND user = '.$photos['user'].' AND category = '.$photos['category'].' AND deleted = 0 ORDER BY id '.$sort.' LIMIT 1';
		//echo $query;
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$data = mysql_fetch_assoc($result);
		return $data;
	}

	function photos_render_categories($categories)
	{
		$out .= '<ol class="photo_categories_list">' . "\n";
		foreach($categories AS $category)
		{
			if(strlen($category['photos']) > 0)
			{
				$photos = explode(',', $category['photos']);
				$out .= '<li>' . "\n";
				$out .= '<a href="/traffa/photos.php?category=' . $category['id'] . '">' . "\n";
				for($i = 0; $i < 9; $i++)
				{
					if(isset($photos[$i]))
					{
						$out .= '<img src="' . IMAGE_URL . 'photos/mini/' . floor($photos[$i]/5000) . '/' . $photos[$i] . '.jpg" />';
					}
					else
					{
						$out .= '<img src="' . IMAGE_URL . 'photos/mini/no_photo.png" />';					
					}
				}
				$out .= '</a>' . "\n";
				$out .= '<p><a href="/traffa/photos.php?category=' . $category['id'] . '">' . $category['name'] . ' (' . $category['photo_count'] . ')</a></p>' . "\n";
				$out .= '</li>' . "\n";
			}
		}
		$out .= '<ol>';
		
		return $out;
	}

	function photos_list($photos)
	{
		$output .= '<ul class="photos_list">' . "\n";
		foreach($photos AS $photo)
		{
			$photo['description'] = (mb_strlen($photo['description'], 'UTF8') > 19) ? mb_substr($photo['description'], 0, 17, 'UTF8') . '...' : $photo['description'];

			#$photo['description'] = (strlen($photo['description']) > 19) ? substr($photo['description'], 0, 17) . '...' : $photo['description'];
			$output .= '<li>' . "\n";
			$output .= '<a href="/traffa/photos.php?id=' . $photo['id'] . '#photo"><img src="' . IMAGE_URL . 'photos/thumb/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" title="' . $photo['username'] . '" /></a>';
			$output .= '<p><a href="/traffa/photos.php?id=' . $photo['id'] . '#photo">' . $photo['description'] . '</a>';
			$output .= ($photo['user'] == $_SESSION['login']['id'] && $photo['unread_comments'] > 0) ? '<strong>(' . $photo['unread_comments'] . ')</strong>' : '';
			$output .= '</p>' . "\n";
			$output .= '</li>' . "\n";
		}
		$output .= '</ul>' . "\n";
		
		return $output;
	}
	
	function photos_list_mini($photos)
	{
		$output .= '<ul class="photos_list_mini">' . "\n";
		foreach($photos AS $photo)
		{
			$output .= '<li>' . "\n";
			$output .= '<a href="/traffa/photos.php?id=' . $photo['id'] . '#photo" id="'.$photo['id'].'"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" /></a>';
			$output .= '</li>' . "\n";
		}
		$output .= '</ul>' . "\n";
		
		return $output;
	}

	
	function photos_browse($photos, $fastload)
	{
		$fastload = ($fastload === true) ? true : false;
		$output .= '<ul class="photos_list_mini">' . "\n";
		foreach($photos AS $photo)
		{
			if ($fastload)
			{
				if($photo['id'] == $_GET['id'])
				{
					$output .= '<li style="padding: 2px;" class="current_photo">' . "\n";
					$output .= '<a style="cursor: pointer;" id="updateviewid_' . $photo['id'] . '"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" /></a>';
					$output .= '</li>' . "\n";
				}
				else
				{
					$output .= '<li style="padding: 2px;border: 4px solid #fff;">' . "\n";
					$output .= '<a style="cursor: pointer;" id="updateviewid_' . $photo['id'] . '"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" /></a>';
					$output .= '</li>' . "\n";
				}
			}
			else
			{
				if($photo['id'] == $_GET['id'])
				{
					$output .= '<li style="background: #fcc52c; padding: 2px;">' . "\n";
					$output .= '<a href="?id=' . $photo['id'] . '#photo"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" /></a>';
					$output .= '</li>' . "\n";
				}
				else
				{
					$output .= '<li style="padding: 2px;border: 4px solid #fff;">' . "\n";
					$output .= '<a href="?id=' . $photo['id'] . '#photo"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" /></a>';
					$output .= '</li>' . "\n";
				}
			}
		}
		$output .= '</ul>' . "\n";
		
		return $output;
	}

	function photos_display($photos, $wait_for_image_to_load)
	{
		foreach($photos AS $photo)
		{
			$output .= '<div class="photo_full">' . "\n";
			$output .= '<div class="passepartout">' . "\n";
			$output .= '<p>' . $photo['date'] . ': <a href="/traffa/photos.php?ajax&user_id='.$photo['user'].'&image_id='.$photo['id'].'#photo" title="Länk till bilden">'.$photo['description'].'</a>';
			if ($_SESSION['login']['id'] == 774586)
			{
				$output .= ' <img src="http://images.hamsterpaj.net/abuse.png" alt="Din mamma ;)" onclick=" /><br style="clear:both;" />';
			}
			$output .= '</p>' . "\n";
			$output .= '<span class="loading" id="loading"></span>' . "\n";
			$output .= '<img id="tha_image" src="' . IMAGE_URL . 'photos/full/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" />';
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
			
			$comment_list_options = array();
			$comment_list_options['photo_owner'] = $photo['user'];

			if($_SESSION['login']['id'] == $photo['user'])
			{
				//$comment_list_options['show_admin_controls'] = true;
			
				$categories = photos_get_categories(array('user' => $photo['user']));
				
				$output .= rounded_corners_top(array('color' => 'blue_deluxe'));
				$output .= '<form class="photo_edit" method="post">' . "\n";
				$output .= '<input type="hidden" name="action" value="update" />' . "\n";
				$output .= '<input type="hidden" name="photo_id" value="' . $photo['id'] . '" />' . "\n";
				$output .= '<input type="text" name="description" value="' . addslashes($photo['description']) . '" class="textbox" />' . "\n";
				$output .= '<select name="category" class="photo_category_selector">' . "\n";
				foreach($categories AS $category)
				{
					$selected = ($photo['category'] == $category['id']) ? ' selected="selected"' : '';
					$output .= '<option value="' . $category['name'] . '"' . $selected . '>' . $category['name'] . '</option>' . "\n";					
				}
				$output .= '<option value="new_category">Ny kategori</option>' . "\n";
				$output .= '</select>' . "\n";
				$output .= '<input type="submit" value="Uppdatera" class="button_80" />' . "\n";
				$output .= '</form>' . "\n";
				$output .= '<form class="photo_delete" method="post" action="/traffa/photos.php">' . "\n";
				$output .= '<input type="hidden" name="action" value="delete" />' . "\n";
				$output .= '<input type="hidden" name="photo_id" value="' . $photo['id'] . '" />' . "\n";
				$output .= '<input type="submit" value="Radera" class="photo_delete" />' . "\n";
				$output .= '</form>' . "\n";				
				$output .= rounded_corners_bottom();
				// KOMMENTERING FÖR ÄGAREN
				$output .= rounded_corners_top(array('color' => 'blue_deluxe'));
				$output .= 'Kommentera: ';
				$output .= comments_input_draw($photo['id'], 'photos');
				$output .= rounded_corners_bottom();
				
				// BILDTÄVLINGEN
				//$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
				//$output .= '<span style="">Rita av en Sysop i paint! kolla in tävlingen <a href="/tavling.php">HÄR</a></span><span style="float: right; margin-top: -14px;">Bild-id: '.$photo['id'].'</span>';
				//$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
				// END
				
			
				$query = 'UPDATE user_photos SET unread_comments = 0 WHERE id = "' . $photo['id'] . '" LIMIT 1';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
				cache_update_photo_comments();
				
			}
			else
			{
				
				if(is_privilegied('remove_photo'))
				{
					
					$output .= '<form class="photo_delete" method="post" action="/traffa/photos.php">' . "\n";
					$output .= '<input type="hidden" name="action" value="delete" />' . "\n";
					$output .= '<input type="hidden" name="photo_id" value="' . $photo['id'] . '" />' . "\n";
					$output .= '<input type="submit" value="Ta bort bild" class="photos_remove" />' . "\n";
					$output .= '</form>' . "\n";
					/*
					$output .= '';
					$output .= '<button class="button_90">Ta bort bild</button>' . "\n";
					*/
				}
				
				
				$output .= rounded_corners_top(array('color' => 'blue_deluxe'));
				$output .= 'Kommentera: ';
				$output .= comments_input_draw($photo['id'], 'photos');
				$output .= rounded_corners_bottom();
			}
			
			$output .= comments_list($photo['id'], 'photos', $comment_list_options);
		}
		
		return $output;
	}
	
	function photos_date_bar($options)
	{
		$query = 'SELECT DISTINCT(date) FROM user_photos WHERE user = "' . $options['user'] . '" ORDER BY date ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		while($data = mysql_fetch_assoc($result))
		{
			$months[date('Y-m', strtotime($data['date']))][date('j', strtotime($data['date']))] = $data['date'];
		}
		
		$out .= rounded_corners_top(array('color' => 'blue_deluxe'));
		$out .= '<select id="photos_date_scroller_dropdown">'  . "\n";
		foreach(array_keys($months) AS $month)
		{
			$out .= '<option>' . $month . '</option>' . "\n";
		}
		$out .= '</select>';
		
		$out .= '<div class="photos_date_scroller">' . "\n";
		foreach($months AS $month => $days)
		{
			$out .= '<ol>' . "\n";
			for($i = 1; $i <= date('t', strtotime($month)); $i++)
			{
				if(isset($days[$i]))
				{
					$out .= '<li><a href="#" class="photo_date_link" id="photos_' . $month . '-' . $i . '_' . $options['user'] . '">' . $i . '</a></li>' . "\n";
				}
				else
				{
					$out .= '<li>' . $i . '</li>' . "\n";
				}
			}
			$out .= '</ol>' . "\n";
		}
		$out .= '</div>' . "\n";
		
		$out .= '<div id="photos_date_previews">' . "\n";
		
		$photos = photos_fetch(array('user' => $options['user'], 'date' => $options['date']));
		if(count($photos) > 0)
		{
			$out .= photos_list_mini($photos);
			$out .= '<br style="clear: both;" />' . "\n";
		}
		$out .= '</div>' . "\n";
		
		$out .= rounded_corners_bottom();
		
		return $out;
	}
?>