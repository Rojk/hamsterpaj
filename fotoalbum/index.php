<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE . 'libraries/photo.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/comments.lib.php');

	$ui_options['menu_path'] = array('fotoalbum');
	$ui_options['title'] = 'Fotoalbum';

	$ui_options['javascripts'][] = 'prototype.js';
	$ui_options['javascripts'][] = 'scriptaculous.js';
	$ui_options['javascripts'][] = 'photo.js';
	$ui_options['javascripts'][] = 'comments.js';

	$ui_options['stylesheets'][] = 'photo.css';
	$ui_options['stylesheets'][] = 'comments.css';

	$ui_options['header_extra'] = '<script type="text/javascript" language="javascript" src="/cropper.js"></script>';
	
	ui_top($ui_options);
	
	/* Temporary hack to populate the $_GET array even though the URL is mod_rewrited */
	parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?')+1), $_GET);

	$action = photo_get_action();

	switch($action['action'])
	{
		case 'view_album':
		
			break;
		case 'view_photo':
			$image = photo_get_photos(array('external_id' => $action['external_id']));
			$image = array_pop($image);
			photo_display_full(array('image' => $image));
			break;
		case 'scale':
			$images = photo_parse_scale_input();
			$created_albums = array();
			foreach($images AS $image)
			{
				/* If the user has choosen to create a new album, do that.
					 But first, check if the album has been created recently, if so
					 use the ID stored in $created_albums, otherwise create a new album
					 and cache it in $created_albums.
				*/
				if(!is_numeric($image['album']))
				{
					if(isset($created_albums[$image['album']]))
					{
						$image['album'] = $created_albums[$image['album']];
					}
					else
					{
						$album['owner'] = $_SESSION['login']['id'];
						$album['owner_type'] = 'user';
						$album['label'] = $image['album'];
						$created_albums[$image['album']] = photo_create_album($album);
						$image['album'] = $created_albums[$image['album']];
					}
				}
				$image['owner'] = $_SESSION['login']['id'];
				$image['owner_type'] = 'user';
				photo_create_image($image);
			}
			echo '<h1>Dina bilder är uppladdade - såhär blev det</h1>' . "\n";
			photo_list_thumbs(array('images' => $images));
			echo '<p>Nu kan du gå till <a href="#">startsidan i ditt fotoalbum</a>, <a href="#">ändra inställningar för ditt fotoalbum</a> eller <a href="#">ladda upp fler bilder</a></p>' . "\n";
			break;
		case 'upload':
			$filenames = photo_upload_to_temp();
			photo_scale_and_comment_form(array('filenames' => $filenames, 'owner' => $_SESSION['login']['id'], 'owner_type' => 'user'));
			break;
		case 'list_albums':
			echo '<h1>Alla foton user: ' . $action['owner'] . ' laddat upp</h1>' . "\n";
			$albums = photo_get_albums(array('owner' => $action['owner'], 'owner_type' => 'user'));
//			$albums = photo_get_albums(array('owner' => 3, 'owner_type' => 'user'));
			foreach($albums AS $album)
			{
				echo '<h2>' . $album['label'] . '</h2>' . "\n";
				$photos = photo_get_photos(array('album' => $album['id']));
				photo_list_thumbs(array('images' => $photos));
			}
			break;
		case 'list_by_date':
			echo '<h1>Listing photos by date</h1>' . "\n";
			$photos_by_date = photo_count_by_date(array('owner' => $action['owner'], 'owner_type' => $action['owner_type']));
			$list_by_date['dates'] = $photos_by_date;
			$list_by_date['viewing_modes']['day'] = 2;
			$list_by_date['viewing_modes']['week'] = 2;
			$list_by_date['viewing_modes']['month'] = 100;
			
			// Fetch four photos per day for the days that will be listed as "days"
			$count = 0;
			foreach(array_keys($list_by_date['dates']) AS $date)
			{
				$list_by_date['dates'][$date]['photos'] = photo_get_photos(array('photo_taken' => $date, 'owner' => $action['owner'], 'owner_type' => $action['owner_type'], 'limit' => 5));
				$count++;
				if($count == $list_by_date['viewing_modes']['day'])
				{
					break;
				}
			}
			
			photo_list_by_date($list_by_date);
			break;
		case 'list_latest':
			echo '<h1>Senast uppladdade bilderna</h1>' . "\n";
			$photos = photo_get_photos(array('limit' => 100));
			photo_list_thumbs(array('images' => $photos));
			break;
		case 'upload_form':
			photo_upload_form(array('owner_type' => 'user'));		
			break;
		default:
			$photos = photo_get_photos(array('limit' => 5));
			photo_list_thumbs(array('images' => $photos));
			echo '<h1>Oändligt antal bilder, oändligt antal album - gratis</h1>' . "\n";
			echo '<p>Det här är testversionen av Hamsterpajs nya fotoalbum. <a href="/traffa/profile.php?id=3">Johan</a> utvecklar fortfarande funktionen, men du får gärna testa på den!</p>' . "\n";
			echo '<ul>' . "\n";
			echo '<li>Kör igång och <a href="/fotoalbum/uploadform.php">ladda upp bilder</a> på direkten!</li>' . "\n";
			echo '<li>Kolla in <a href="/fotoalbum/senaste.php?owner=3">Johans fotoalbum</a></li>' . "\n";
			echo '<li>Titta i listan över <a href="/fotoalbum/senaste.php">senast uppladdade bilder</a></li>' . "\n";
			echo '</ul>' . "\n";
			break;
	}


	ui_bottom();
?>


