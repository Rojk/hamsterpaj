<?php

	function photo_get_action_b($uri)
	{
		echo $uri;
		//$uri = $_SERVER['REQUEST_URI'];
		$result = preg_match('/\/fotoalbum\/(\w+)\/(\w+)\/(\w+)\/([\w\d]+)?/', $uri, $matches);

//		preint_r($matches);

		$action['owner_type']	= $matches[1];	// owner type is [user|group|system]
		$action['owner']		= $matches[2];	// owner is user name, group name or a special system name
		$action['action']		= $matches[3];	// action is a view or a post
		$action['item']			= $matches[4];	// an id identifying a photo or an album
		preint_r($action);

		return $action;
	}

	function photo_get_action()
	{		
		if($_SERVER['REQUEST_URI'] == '/fotoalbum/uppladdning.php')
		{
			return array('action' => 'upload');
		}
		if($_SERVER['REQUEST_URI'] == '/fotoalbum/uploadform.php')
		{
			return array('action' => 'upload_form');
		}
		if($_SERVER['REQUEST_URI'] == '/fotoalbum/beskaer.php')
		{
			return array('action' => 'scale');
		}
		if(substr($_SERVER['REQUEST_URI'], 0, 20) == '/fotoalbum/album.php')
		{
			return array('action' => 'list_albums', 'owner' => $_GET['owner'], 'owner_type' => $user);
		}
		if(substr($_SERVER['REQUEST_URI'], 0, 22) == '/fotoalbum/senaste.php')
		{
			if(isset($_GET['owner']))
			{
				return array('action' => 'list_by_date', 'owner' => $_GET['owner'], 'owner_type' => $user);
			}
			else
			{
				return array('action' => 'list_latest');				
			}
		}
		if(substr($_SERVER['REQUEST_URI'], 0, 19) == '/fotoalbum/bild.php')
		{
			return array('action' => 'view_photo', 'external_id' => $_GET['photo']);
		}
		if(substr($_SERVER['REQUEST_URI'], 0, 19) == '/fotoalbum/user.php')
		{
			return array('action' => 'user_index', 'owner_id' => $_GET['id']);
		}
	}
	
	function photo_upload_to_temp()
	{
		for($i = 1; $i <= PHOTO_UPLOAD_MAX_IMAGES; $i++)
		{
			if(is_uploaded_file($_FILES['photo_' . $i]['tmp_name']))
			{
				$uniqid = uniqid();
				$filenames[] = array('tmp_name' => $uniqid, 'original_name' => $_FILES['photo_' . $i]['name']);
				system('convert -resize "' . PHOTO_MAX_WIDTH . '>x' . PHOTO_MAX_HEIGHT . '>" ' . $_FILES['photo_' . $i]['tmp_name'] . ' ' . PHOTO_UPLOAD_TEMP_PATH . $uniqid . '.jpg');
			}
		}
		return $filenames;
	}
	
	function photo_count_by_date($parameters)
	{
		$query = 'SELECT COUNT(*) AS photo_count, photo_taken FROM user_photos WHERE 1';
		$query .= (isset($parameters['owner'])) ? ' AND owner = "' . $parameters['owner'] . '"' : '';
		$query .= (isset($parameters['owner_type'])) ? ' AND owner = "' . $parameters['owner_type'] . '"' : '';
		$query .= ' GROUP BY photo_taken ORDER BY photo_taken DESC';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			$return[$data['photo_taken']] = $data;
		}
		
		return $return;
	}
	
	function photo_scale_and_comment_form($parameters)
	{
		$crop_cords = array('x1', 'x2', 'y1', 'y2');
		$count = 1;
		
		/* Fetch available albums, if album doesn't exist - try to create a default album */
		$albums = photo_get_albums(array('owner_type' => $parameters['owner_type'], 'owner' => $parameters['owner']));
		if(count($albums) == 0)
		{
			photo_create_album(array('owner' => $parameters['owner'], 'owner_type' => $parameters['owner_type'], 'default_category' => 'true'));
			$albums = photo_get_albums(array('owner_type' => $parameters['owner_type'], 'owner' => $parameters['owner']));			
		}
		if(count($albums) == 0)
		{
			die('<h1>Allvarligt internt fel!</h1><p>Inga album för detta mål existerar, försök att skapa album misslyckades. (Typ allt är trasigt, ungefär)</p>');
		}
		
		echo '<form action="/fotoalbum/beskaer.php" method="post">' . "\n";
		foreach($parameters['filenames'] AS $filename)
		{
			rounded_corners_top(array('color' => 'white'));
			echo '<h2>' . $filename['original_name'] . '</h2>' . "\n";
			$img_url = PHOTO_UPLOAD_TEMP_URL . $filename['tmp_name'] . '.jpg';

			foreach($crop_cords AS $field)
			{
				echo '<input type="hidden" name="' . $field . '_' . $filename['tmp_name'] . '" id="' . $field . '_' . $filename['tmp_name'] . '" />' . "\n";
			}
			echo '<script type="text/javascript" charset="utf-8">' . "\n";
			echo 'function onEndCrop_' . $filename['tmp_name'] . '(coords, dimensions )' . "\n";
			echo '{' . "\n";
			foreach($crop_cords AS $field)
			{
				echo 'document.getElementById("' . $field . '_' . $filename['tmp_name'] . '").value = coords.' . $field . ";\n";
			}
			echo '}' . "\n";
			echo 'womAdd("new Cropper.Img(\'crop_' . $filename['tmp_name'] . '\',{onEndCrop: onEndCrop_' . $filename['tmp_name'] . '})");' . "\n"; 
			echo '</script>' . "\n";
			
			echo '<div class="photo_upload_inputs">' . "\n";
			echo '<div class="description_input">' . "\n";
			echo '<h5>Frivillig kommentar / beskrivning</h5>' . "\n";
			echo '<textarea name="description_' . $filename['tmp_name'] . '"></textarea>' . "\n";
			echo '</div>' . "\n";
//			echo '<input type="checkbox" name="maximize_' . $filename['tmp_name'] . '" id="maximize_' . $filename['tmp_name'] . '" value="true" />' . "\n";
//			echo '<label for="maximize_' . $filename['tmp_name'] . '">Förstora markeringen</label>' . "\n";

			echo '<div class="date_and_album_input">' . "\n";

			/* Date select */
			echo '<h5>När togs fotot?</h5>' . "\n";
			echo '<select name="date_' . $filename['tmp_name'] . '">' . "\n";
			echo '<option value="' . date('Y-m-d') . '">Idag</option>' . "\n";
			echo '<option value="' . date('Y-m-d', strtotime('yesterday')) . '">Igår</option>' . "\n";
			for($j = 2; $j < 10; $j++)
			{
				echo '<option value="' . date('Y-m-d', time() - 86400*$j) . '">' . date('D j/n', time() - 86400*$j) . '</option>' . "\n";
			}
			echo '<option value="text_input">Annat datum</option>' . "\n";
			echo '</select>' . "\n";

			/* Album select */
			echo '<h5>Fotoalbum</h5>' . "\n";
			echo '<select name="album_' . $filename['tmp_name'] . '" class="photo_upload_album_select">' . "\n";
			foreach($albums AS $album)
			{
				echo '<option value="' . $album['id'] . '">' . $album['label'] . '</option>' . "\n";
			}
			echo '<option value="text_input">Nytt album</option>' . "\n";			
			echo '</select>' . "\n";

			echo '</div>' . "\n";
			echo '</div>' . "\n";
			
			echo '<h5>Klicka och dra i bilden för att markera det du vill ha med</h5>' . "\n";
			echo '<div class="photo_resize">' . "\n";
			echo '<img src="' . $img_url . '" alt="test image" id="crop_' . $filename['tmp_name'] . '" />' . "\n";
			echo '</div>' . "\n";
			
			$count++;
			rounded_corners_bottom(array('color' => 'white'));
		}
		echo '<input type="submit" value="Beskär och spara" />' . "\n";
		echo '</form>' . "\n";
	}

	function photo_id_create()
	{
		while($id_created != true)
		{
			$external_id = uniqid(rand(), TRUE);
			$external_id = base64_encode($external_id);
			$external_id = substr($external_id, 0, 25);

			$query = 'SELECT internal_id FROM user_photos WHERE external_id = "' . $external_id . '" LIMIT 1';
			$result = mysql_query($query) or hp_sql_error(array('query' => $query));
			if(mysql_num_rows($result) == 0)
			{
				$id_created = true;
				$query = 'INSERT INTO user_photos(external_id) VALUES("' . $external_id . '")';
				mysql_query($query) or hp_sql_error(array('query' => $query));
			}
		}
		return array('external_id' => $external_id, 'internal_id' => mysql_insert_id());
	}

	function photo_get_albums($parameters)
	{
		$query = 'SELECT * FROM photo_categories WHERE 1';
		$query .= (isset($parameters['owner'])) ? ' AND owner = "' . $parameters['owner'] . '"' : '';
		$query .= (isset($parameters['owner_type'])) ? ' AND owner_type = "' . $parameters['owner_type'] . '"' : '';
		$query .= ' ORDER BY label DESC';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

		while($data = mysql_fetch_assoc($result))
		{
			$albums[$data['id']] = $data;
		}
		
		return $albums;
	}
	
	function photo_get_photos($parameters)
	{
		$parameters['order_by'] = (isset($parameters['order_by'])) ? $parameters['order_by'] : 'internal_id';
		$parameters['order_direction'] = (isset($parameters['order_direction'])) ? $parameters['order_direction'] : 'DESC';
		
		$query = 'SELECT * FROM user_photos WHERE 1';
		$query .= (isset($parameters['album'])) ? ' AND album = "' . $parameters['album'] . '"' : '';
		$query .= (isset($parameters['external_id'])) ? ' AND external_id = "' . $parameters['external_id'] . '"' : '';

		$query .= (isset($parameters['internal_id'])) ? ' AND external_id = "' . $parameters['internal_id'] . '"' : '';
		$query .= (isset($parameters['internal_id_min'])) ? ' AND internal_id > "' . $parameters['internal_id_min'] . '"' : '';
		$query .= (isset($parameters['internal_id_max'])) ? ' AND internal_id < "' . $parameters['internal_id_max'] . '"' : '';

		$query .= (isset($parameters['photo_taken'])) ? ' AND photo_taken = "' . $parameters['photo_taken'] . '"' : '';
		
		$query .= ' ORDER BY ' . $parameters['order_by'] . ' ' . $parameters['order_direction'];

		$query .= (isset($parameters['limit'])) ? ' LIMIT ' . $parameters['limit'] : '';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			$photos[] = $data;
		}
		
//		echo '<p>' . $query . '</p>';
		
		return $photos;
	}
	
	function photo_create_album($parameters)
	{
		$parameters['default_category'] = ($parameters['default_category'] == true) ? 'true' : 'false';
		$parameters['access_level'] = (isset($parameters['access_level'])) ? $parameters['access_level'] : 'all';
		$parameters['label'] = (isset($parameters['label'])) ? $parameters['label'] : 'Osorterat';
		$query = 'INSERT INTO photo_categories (owner, owner_type, label, default_category, created)';
		$query .= ' VALUES("' . $parameters['owner'] . '", "' . $parameters['owner_type'] . '", "' . $parameters['label'];
		$query .= '", "' . $parameters['default_category'] . '", "' . time() . '")';

		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		return mysql_insert_id();
	}
	
	function photo_parse_scale_input()
	{
		foreach($_POST AS $key => $value)
		{
			$explosion = explode('_', $key);
			$images[$explosion[1]][$explosion[0]] = $value;
			$images[$explosion[1]]['temp_filename'] = $explosion[1];
		}
		foreach($images AS $temp_filename => $image)
		{
			$created_id = photo_id_create();
			$images[$temp_filename]['external_id'] = $created_id['external_id'];
			$images[$temp_filename]['internal_id'] = $created_id['internal_id'];
		}
		return $images;
	}
	
	function photo_create_image($image)
	{
		event_log_log('photo_create');
		/* Create fullsize image */
		$crop_command = '-crop ' . ($image['x2'] - $image['x1']) . 'x' . ($image['y2'] - $image['y1']) . '+' . $image['x1'] . '+' . $image['y1'];
		$source = PHOTO_UPLOAD_TEMP_PATH . $image['temp_filename'] . '.jpg';
		$target = PHOTO_FULL_IMAGE_PATH . floor($image['internal_id']/5000) . '/' . $image['external_id'] . '.jpg';
		if(!is_dir(PHOTO_FULL_IMAGE_PATH . floor($image['internal_id']/5000)))
		{
			mkdir(PHOTO_FULL_IMAGE_PATH . floor($image['internal_id']/5000));
		}

		system('convert ' . $crop_command . ' ' . $source . ' ' . $target);

		if($image['maximize'] == true)
		{
			system('convert -scale 600x600 ' . $target . ' ' . $target);
		}

		/* Create the thumbnail */
		system('convert -scale 90x90 ' . $target . ' ' . $source);
		$thumb_target = PHOTO_THUMB_IMAGE_PATH . floor($image['internal_id']/5000) . '/' . $image['external_id'] . '.png';
		if(!is_dir(PHOTO_THUMB_IMAGE_PATH . floor($image['internal_id']/5000)))
		{
			mkdir(PHOTO_THUMB_IMAGE_PATH . floor($image['internal_id']/5000));
		}

//		system('convert ' . $source . ' -bordercolor white  -border 6 -bordercolor grey60 -border 1 -background  none -rotate 2 -background  black  \( +clone -shadow 60x4+4+4 \) +swap -background  none   -flatten -depth 8  -quality 95 ' . $thumb_target);
		system('convert ' . $source . ' -bordercolor snow  -background black -polaroid 2 ' . $thumb_target);
		/* Insert to database */
		$query = 'UPDATE user_photos SET owner = "' . $image['owner'] . '", owner_type = "' . $image['owner_type'] . '", timestamp = "' . time() . '"';
		$query .= ', photo_taken = "' . $image['date'] . '", description = "' . $image['description'] . '", album = "' . $image['album'] . '"';
		$query .= ' WHERE external_id = "' . $image['external_id'] . '"';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	}
	
	function photo_display_full($parameters)
	{
		event_log_log('photo_display');
		$next_by_day = photo_get_photos(array('limit' => 1, 'photo_taken' => $parameters['image']['photo_taken'], 'order_direction' => 'ASC', 'internal_id_min' => $parameters['image']['internal_id']));;
		$next_by_album = photo_get_photos(array('limit' => 1, 'album' => $parameters['image']['album'], 'order_direction' => 'ASC', 'internal_id_min' => $parameters['image']['internal_id']));;

		$previous_by_day = photo_get_photos(array('limit' => 1, 'photo_taken' => $parameters['image']['photo_taken'], 'order_direction' => 'DESC', 'internal_id_max' => $parameters['image']['internal_id']));;
		$previous_by_album = photo_get_photos(array('limit' => 1, 'album' => $parameters['image']['album'], 'order_direction' => 'DESC', 'internal_id_max' => $parameters['image']['internal_id']));;
		
		echo '<div class="photo_full">' . "\n";
		echo '<div class="head">' . "\n";
		echo '<span class="date">' . $parameters['image']['photo_taken'] . '</span>' . "\n";
		if(strlen($parameters['image']['description']) > 0)
		{
			echo '<p class="photo_description">' . "\n";
			echo $parameters['image']['description'] . "\n";
			echo '</p>' . "\n";
		}
		echo '</div>' . "\n";

		// Photo with passepartout
		echo '<div class="photo_passepartout_outer">' . "\n";
		echo '<div class="photo_passepartout_inner">' . "\n";
		echo '<img src="' . PHOTO_FULL_IMAGE_URL . floor($parameters['image']['internal_id']/5000) . '/' .$parameters['image']['external_id'] . '.jpg" />' . "\n";
		echo '</div>' . "\n";
		echo '</div>' . "\n";

		echo '<div class="foot">' . "\n";		
		echo '<div class="next">' . "\n";
		if($next_by_day[0]['internal_id'] > 0)		
		{
			echo '<a href="/fotoalbum/bild.php?photo=' . $next_by_day[0]['external_id'] . '"><button class="button_110">Samma dag &raquo;</button></a>' . "\n";
		}
		if($next_by_album[0]['internal_id'] > 0)		
		{
			echo '<a href="/fotoalbum/bild.php?photo=' . $next_by_album[0]['external_id'] . '"><button class="button_110">Samma album &raquo;</button></a>' . "\n";
		}
		echo '</div>' . "\n";	

		echo '<div class="previous">' . "\n";
		if($previous_by_day[0]['internal_id'] > 0)		
		{
			echo '<a href="/fotoalbum/bild.php?photo=' . $previous_by_day[0]['external_id'] . '"><button class="button_110">&laquo; Samma dag</button></a>' . "\n";
		}
		if($previous_by_album[0]['internal_id'] > 0)		
		{
			echo '<a href="/fotoalbum/bild.php?photo=' . $previous_by_album[0]['external_id'] . '"><button class="button_110">&laquo; Samma album</button></a>' . "\n";
		}	
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		
		echo '</div>' . "\n";
		
		$query = 'UPDATE photos SET unread_comments = 0 WHERE internal_id = "' . $parameters['image']['internal_id'] . '" LIMIT 1';
		mysql_query($query);

		echo '<h2>Kommentarer</h2>' . "\n";
		echo '<h5>Här kan du lämna din kommentar</h5>' . "\n";
		echo comments_input_draw($parameters['image']['internal_id'], 'photo');

		rounded_corners_top();
		echo comments_list($parameters['image']['internal_id'], 'photo');
		rounded_corners_bottom();

		// Count views
		if(!in_array($parameters['image']['internal_id'], $_SESSION['photos']['viewed_photos']))
		{
			$_SESSION['photos']['viewed_photos'][] = $parameters['image']['internal_id'];
			$query = 'UPDATE photos SET view_count = view_count + 1 WHERE internal_id = "' . $parameters['image']['internal_id'] . '" LIMIT 1';
			mysql_query($query);
		}

	}

	function photo_list_thumbs($parameters)
	{
		$parameters['url'] = (isset($parameters['url'])) ? $parameters['url'] : '/fotoalbum/bild.php?photo=%EXTERNAL_ID%';
		echo '<div class="photo_thumbs_list">' . "\n";
		$i = 0;
		foreach($parameters['images'] AS $image)
		{
			$i++;
			if($i == 6)
			{
				echo '<br />';
				$i = 1;
			}
			echo '<div class="thumb">' . "\n";
			echo '<a href="' . str_replace('%EXTERNAL_ID%', $image['external_id'], $parameters['url']) . '">' . "\n";
			echo '<img src="' . PHOTO_THUMB_IMAGE_URL . floor($image['internal_id']/5000) . '/' . $image['external_id'] . '.png" />' . "\n";
			echo '</a>' . "\n";
			echo '</div>' . "\n";
		}
		echo '</div>' . "\n";
	}
		
	function photo_list_by_date($parameters)
	{
		$earliest_photo = reset(array_reverse($parameters['dates']));
		$earliest_photo = strtotime($earliest_photo['photo_taken']);

		foreach($parameters['viewing_modes'] AS $viewing_mode => $view_mode_limit)
		{
			$rendered_lists = 0;
			$date = new DateTime('today');
			while($date->format('U') >= $earliest_photo)
			{
				switch($viewing_mode)
				{
					case 'day':
						if(isset($parameters['dates'][$date->format('Y-m-d')]))
						{
							echo '<h3 class="photo_list_heading">' . $date->format('Y-m-d') . '</h3>' . "\n";
							photo_list_thumbs(array('images' => $parameters['dates'][$date->format('Y-m-d')]['photos']));
							echo '<a href="#" class="photo_by_day_link">Visa alla bilder</a>' . "\n";
							$rendered_lists++;
						}
						$date->modify('1 day ago');
						break;
					case 'week':
						$html = '';
						$display_list = false;
						
						$day_in_week = $date->format('N');
						$temp_date = new DateTime($date->format('Y-m-d'));
						$temp_date->modify(($day_in_week-1) . ' days ago');
						$html = '<h3 class="photo_list_heading">Vecka ' . $date->format('W Y') . '</h3>' . "\n";
						$html .= '<ul class="photo_week_list">' . "\n";
						for($i = 0; $i < 7; $i++)
						{
							$class = (isset($parameters['dates'][$temp_date->format('Y-m-d')])) ? ' class="clickable"' : '';
							$html .= (isset($parameters['dates'][$temp_date->format('Y-m-d')])) ? '<a href="#">' : '';

							$html .= '<li' . $class . '>' . "\n";
							$html .= '<span class="weekday">' .  $temp_date->format('D') . '</span>' . "\n";
							$photo_count = ($parameters['dates'][$temp_date->format('Y-m-d')]['photo_count'] > 0) ? $parameters['dates'][$temp_date->format('Y-m-d')]['photo_count'] : 0;
							$html .= '<span class="date">' .  $temp_date->format('j/n') . '</span>' . "\n";
							$html .= (isset($parameters['dates'][$temp_date->format('Y-m-d')])) ? '<span class="photo_count">' .  $photo_count . ' bilder</span>' . "\n" : '';
							$html .= '</li>' . "\n";
							$html .= (isset($parameters['dates'][$temp_date->format('Y-m-d')])) ? '</a>' : '';

							$temp_date->modify('1 day');
							if(isset($parameters['dates'][$temp_date->format('Y-m-d')]))
							{
								$display_list = true;
							}
						}
						$html .= '</ul>' . "\n";

						if($display_list)
						{
							echo $html;
							$rendered_lists++;
						}

						$date->modify('1 week ago');
						break;
					case 'month':
					
						$html = '<h3 class="photo_list_heading">' . $date->format('F Y') . '</h3>' . "\n";
						$html .= '<ul class="photo_month_list">' . "\n";
						for($day = 1; $day <= $date->format('t'); $day++)
						{
							$day_zerofill = ($day < 10) ? '0' . $day : $day;
						
							$class = (isset($parameters['dates'][$date->format('Y-m-') . $day_zerofill])) ? ' class="clickable"' : '';
							$html .= '<li' . $class .'>'  . "\n";
							$html .= (isset($parameters['dates'][$date->format('Y-m-') . $day_zerofill])) ? '<a href="#"> ' . $day . '</a>' : $day;
							
							$html .= '</li>' . "\n";
							if(isset($parameters['dates'][$date->format('Y-m-') . $day_zerofill]))
							{
								$display_list = true;
							}
						}
						$html .= '</ul>' . "\n";
						
						if($display_list)
						{
							echo $html;
							$rendered_lists++;
						}
						
						$date->modify('1 month ago');				
						break;
					default:
						echo '<h1>Unknown list type!</h1>' . "\n";
						break 2;
				}
				if($rendered_lists == $view_mode_limit)
				{
					break;
				}
			}
		}
	}

	function photo_upload_form($parameters)
	{
		echo '<h1>Här laddar du upp bilder till fotoalbumet på Hamsterpaj!</h1>' . "\n";
		
		echo '<p>Du kan ladda upp hur många bilder du vill, men bara fem åt gången. Vi accepterar de flesta bildfiler, beskär och beskriver bilderna gör du på nästa sida!</p>' . "\n";
		
		echo '<img src="' . IMAGE_URL . 'photos/upload_illustration.png" style="float: right;" />' . "\n";
		
		$parameters['owner'] = (isset($parameters['owner'])) ? $parameters['owner'] : $_SESSION['login']['id'];
		
		echo '<form action="/fotoalbum/uppladdning.php" method="post" enctype="multipart/form-data">' . "\n";
		for($i = 1; $i <= PHOTO_UPLOAD_MAX_IMAGES; $i++)
		{
			echo '<div class="photo_upload_entry">' . "\n";
			echo '<input type="file" name="photo_' . $i . '" />' . "\n";
			echo '</div>' . "\n";
		}
		echo '<input type="submit" value="Ladda upp" class="button_80" />' . "\n";
		echo '</form>' . "\n";

		echo '<div class="tips">' . "\n";
		echo '<h3>Tips!</h3>' . "\n";
		echo '<p>När du har tryckt på "Bläddra" kan du ofta högerklicka i filväljaren och välja <strong>Visa -> Miniatyrer</strong>. På det sättet ser du vilka bilder du laddar upp!</p>' . "\n";
		echo '</div>' . "\n";
		
		echo '<div class="warning">' . "\n";
		echo '<h3>Du förlorar kontrollen över bilder du laddar upp!</h3>' . "\n";
		echo '<p>Bilder som en gång laddats upp till Internet kan kopieras och skickas vidare i all evighet. Det gäller på Hamsterpaj såväl som på alla andra webbsajter.</p>' . "\n";
		echo '<h3>Är du en blond tjej med stora tuttar eller kille med brunt hår och slingor? </h3>' . "\n";
		echo '<p>Den där blyge typen med fula glasögon och som luktade äckligt i din klass i mellanstadiet kommer förr eller senare stjäla din bild för att ragga på Lunarstorm, Hamsterpaj, PlayAhead och andra communities. När det händer så kontaktar du en ordningsvakt så löser vi det!</p>' . "\n";

		echo '<h3>Hamsterpaj är ingen porrsajt, Goatse är äckligt och hitlerhälsningar olagliga</h3>' . "\n";
		echo '<p>Snälla låt bli porr och goatse här, tänk på att barn besöker den här sajten!</p>' . "\n";
		echo '<em>Brottsbalkens sextonde kapitel, paragraf åtta</em><br />' . "\n";
		echo '<p>8 § Den som i uttalande eller i annat meddelande som sprids hotar eller uttrycker missaktning för folkgrupp eller annan sådan grupp av personer med anspelning på ras, hudfärg, nationellt eller etniskt ursprung, trosbekännelse eller sexuell läggning, döms för hets mot folkgrupp till fängelse i högst två år eller om brottet är ringa, till böter.</p>' . "\n";
		echo '</div>' . "\n";
	}

?>