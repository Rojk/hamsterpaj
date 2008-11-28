<?php
	/* OPEN_SOURCE */
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	require(PATHS_INCLUDE . 'libraries/photos.lib.php');
	require(PATHS_INCLUDE . 'libraries/comments.lib.php');
	require(PATHS_INCLUDE . 'libraries/guestbook.lib.php');
	require(PATHS_INCLUDE . 'libraries/userblock.lib.php');
	
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['javascripts'][] = 'photos.js';
	
	$ui_options['stylesheets'][] = 'comments.css';
	$ui_options['javascripts'][] = 'comments.js';
	
	$ui_options['menu_path'] = array('traeffa');
	
	if (isset($_GET['iamgod']))
	{
		header('Location: http://images.hamsterpaj.net/photos/full/' . floor($_GET['id'] / 5000) . '/' . $_GET['id'] . '.jpg');
		echo 'test';
	}
	
	if($_POST['action'] == 'update')
	{
		$photo = photos_fetch(array('id' => $_POST['photo_id']));
		if($photo[0]['user'] == $_SESSION['login']['id'])
		{
			$category = photos_get_categories(array('user' => $_SESSION['login']['id'], 'name' => $_POST['category']));
	
			$query = 'UPDATE user_photos SET description = "' . $_POST['description'] . '", category = "' . $category[0]['id'] . '"';
			$query .= ' WHERE id = "' . $_POST['photo_id'] . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

			$query = 'UPDATE user_photo_categories SET photo_count = photo_count - 1 WHERE id = "' . $photo[0]['category'] . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
			$query = 'UPDATE user_photo_categories SET photo_count = photo_count + 1 WHERE id = "' . $category[0]['id'] . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		}
	}
	if($_POST['action'] == 'delete')
	{
		$photo = photos_fetch(array('id' => $_POST['photo_id']));
		
		if(count($photo) > 0)
		{
			$query = 'UPDATE user_photos SET deleted = 1 WHERE id = "' . $_POST['photo_id'] . '"';
			$query .= ' LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			if(mysql_affected_rows() == 1)
			{
				$query = 'UPDATE user_photo_categories SET photo_count = photo_count - 1 WHERE id = "' . $photo[0]['category'] . '" LIMIT 1';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
				//make ghostcomments go away
				$query = 'UPDATE user_photos SET unread_comments = 0 WHERE id = '.intval($_POST['photo_id']).' LIMIT 1';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			}
		}
	}

	if (isset($_GET['ajax']))
	{
			$out = '<span class="updateviewid" id="'.$_GET['image_id'].'_'.'" style="display:none;"></span>'. "\n";
			$out .= '<div id="img_view">'."\n";
			$out .= '<div id="img_category"></div>'."\n";
			$out .= '<div id="img_full"></div>' . "\n";
			$out .= '</div>' . "\n";
			$output .= $out;
			
			$user_id = $_GET['user_id'];
		
		if(isset($user_id))
		{
			$profile = profile_fetch(array('user_id' => $user_id));
			if (strlen($profile['profile_theme']) > 0)
			{
				$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';
			}
		
			$profile_head .= profile_mini_page($profile);
		}
	}
	else
	{
	if(isset($_GET['id']))
	{
		$query = 'SELECT user FROM user_photos WHERE id = ' . $_GET['id'] . ' LIMIT 1';
		$result = mysql_query($query);
		$data = mysql_fetch_assoc($result);
		if (substr($_SERVER["REQUEST_URI"], 0, 21) != "/traffa/photos.php?ajax")
		{
			header('Location: /traffa/photos.php?ajax&user_id=' . $data['user'] . '&image_id=' . $_GET['id'] . '');
		}
	}
	elseif(isset($_GET['category']))
	{
		$category = photos_get_categories(array('id' => $_GET['category']));
		if(count($category) > 0)
		{
			$output .= '<h1>' . $category[0]['name'] . '</h1>' . "\n";
			$photos = photos_fetch(array('category' => $_GET['category']));
			$user_id = $photos[0]['user'];
			$output .= photos_list($photos);
		}
	}
	elseif(isset($_GET['user']) && is_numeric($_GET['user']))
	{
		$user_id  = $_GET['user'];
	}
	// NEW Variable standards, always use "user_id" when retrieving an user id. /Joar
	elseif(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
	{
		$user_id  = $_GET['user_id'];
	}
	elseif(login_checklogin())
	{
		$user_id = $_SESSION['login']['id'];
	}
	else
	{
		$output .= '<h1>Endast medlemmar</h1>';
	}

	if (userblock_checkblock($user_id))
	{
		ui_top();
		echo '<p class="error">IXΘYΣ! Du har blivit blockad, var snel hest så slipper du sånt ;)<br /><em>Visste du förresten att IXΘYΣ betyder Fisk på grekiska?</em></p>';
		ui_bottom();
		exit;
	}
	
	if(isset($user_id))
	{
		$profile = profile_fetch(array('user_id' => $user_id));
		$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';
	
		$profile_head .= profile_mini_page($profile);
	}
	
	if($_SESSION['login']['id'] == $user_id && login_checklogin())
	{
	$display_successful_message = false;
		for($i = 0; $i < PHOTOS_MAX_UPLOADS; $i++)
		{
			if(is_uploaded_file($_FILES['photo_' . $i]['tmp_name']))
			{
				$options['file'] = $_FILES['photo_' . $i]['tmp_name'];
				$options['user'] = $_SESSION['login']['id'];
				$options['description'] = $_POST['description_' . $i];
				$options['category'] = $_POST['category_' . $i];
				
				$photo_id = photos_upload($options);
				
				$query = 'SELECT id FROM user_action_log WHERE user = "' . $_SESSION['login']['id'] . '" AND timestamp > "' . strtotime(date('Y-m-d')) . '" AND action= "photos" LIMIT 1';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					$query = 'UPDATE user_action_log SET url = "/traffa/photos.php?id=' . $photo_id . '", label = "' . $options['description'] . '", timestamp = "' . time() . '" WHERE id = "' . $data['id'] . '" LIMIT 1';
					mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					
					// Gives friend a notice of the users action
					$options['url'] = '/traffa/photos.php?id=' . $photo_id . '#photo';
					$options['action'] = 'photos';
					$options['label'] = $options['description'];
					friends_actions_insert($options);
				}
				else
				{
					$query = 'INSERT INTO user_action_log (action, timestamp, user, url, label) VALUES("photos", "' . time() . '", "' . $_SESSION['login']['id'] . '", "/traffa/photos.php?id=' . $photo_id . '", "' . $options['description'] . '")';
					mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

					// Gives friend a notice of the users action
					$options['url'] = '/traffa/photos.php?id=' . $photo_id . '#photo';
					$options['action'] = 'photos';
					$options['label'] = $options['description'];
					friends_actions_insert($options);
				}

				$display_successful_message = true;
			}
		}
		if($display_successful_message)
		{
			$upload_form .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
			$upload_form .= 'Bilderna är uppladdade!';
			$upload_form .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
		}
		$upload_form .= photos_upload_form(array('user' => $_SESSION['login']['id']));
	}
	
	if($_SESSION['login']['id'] == $user_id && !isset($_GET['id']) && !isset($_GET['category']))
	{
		$photos = photos_fetch(array('user' => $user_id, 'force_unread_comments' => true));
		if(count($photos) > 0)
		{
			$output .= '<h1>Foton med nya kommentarer</h1>';
			$output .= photos_list($photos);
		}
	}
	
	if(isset($user_id))
	{
		$output .= '<h1 style="clear: both;">Fotoalbum</h1>';
		$categories = photos_get_categories(array('user' => $user_id));
		$output .= photos_render_categories($categories);
	}
	
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
		// Joar är stolt över detta, ge honom en klapp på ryggen.
		$title_start = $profile['username'];
		$title_end = ' fotoalbum - Hamsterpaj.net';
		$title_end = (strtolower(substr($profile['username'], -1)) != "s") ? 's'.$title_end : $title_end;
		$ui_options['title'] = $title_start.$title_end;
	}
}
	
	ui_top($ui_options);
	echo $profile_head;
	echo $upload_form;
	echo $output;
	ui_bottom();
?>
