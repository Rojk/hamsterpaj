<?php
	require_once(PATHS_INCLUDE . 'copy_protection/exif.php');

	function photoalbum_upload_photo($filename, $user, $options = null)
	{
		$category = (isset($options['category'])) ? $options['category'] : 4; /* Default to 8 if category is not set */
		$description = (isset($options['description'])) ? $options['description'] : null;
		
		$query = 'SELECT photos FROM photo_albums WHERE owner = "' . $user . '" AND position = "' . $category . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$data = mysql_fetch_assoc($result);
		$images_string = $data['photos'];

		$existing_images = substr_count($data['photos'], ',');

		if($existing_images == 0)
		{
			$existing_images = (strlen($existing_images) > 0) ? 1 : 0;
		}
		else
		{
			$existing_images = $existing_images + 1;
		}

		if($existing_images > 9)
		{
			return array('status' => 'fail', 'reason' => 'Fotoalbumet är fullt');
		}
		$copy_data = read_copy_protection($filename);
		if ($copy_data['copyright'] == 1 && $_SESSION['login']['id'] != $copy_data['userid'])
		{
			return array('status' => 'fail', 'reason' => 'Bilden du försöker ladda upp är upphovsrättsskyddad, och kunde därför inte laddas upp');
		}

		$query = 'INSERT INTO photos (owner, timestamp, description) VALUES(' . $user . ', ' . time() . ', "' . $description . '")';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$image_id = mysql_insert_id();

		
		$save_path = PATHS_IMAGES . 'photoalbums/images_' . round($image_id / 1000) . '/' . $image_id;
		if(!is_dir(PATHS_IMAGES . 'photoalbums/images_' . round($image_id / 1000)))
		{
			if(!mkdir(PATHS_IMAGES . 'photoalbums/images_' . round($image_id / 1000)))
			{
				to_logfile('error', __FILE__, __LINE__, 'Could not create directory',  PATHS_IMAGES . 'photoalbums/images_' . round($image_id / 1000));
				return array('status' => 'fail', 'reason' => 'could not create directory');
			}
		}
		print_r($options);

		list($info_width, $info_height, $info_type, $info_attr) = getimagesize($filename);
		if($info_width < 400 && $info_height < 300)
		{
			system('convert ' . $filename . ' ' . $save_path . '_full.jpg');
		}
		else
		{
			system('convert ' . $filename . ' -resize 400x300 ' . $save_path . '_full.jpg');
		}
		
		
		system('convert ' . $filename . ' -resize 60x45! ' . $save_path . '_thumb.jpg'); /* ! disregards image proportions */

		/* Copyright info has to be written after resize, since it only works for JPEG files */
		if($options['copyright'] == 'true')
		{
			write_copy_protection($save_path . '_full.jpg', 'Copyrighted Work');
			write_copy_protection($save_path . '_thumb.jpg', 'Copyrighted Work');
		}
		else
		{
			write_copy_protection($save_path . '_full.jpg', '');
			write_copy_protection($save_path . '_thumb.jpg', '');
		}

		unlink($filename);

		$images_string = (strlen($images_string) > 0) ? $images_string . ',' . $image_id : $image_id;
		$query = 'UPDATE photo_albums SET photos = "' . $images_string . '" WHERE owner = "' . $user . '" AND position = "' . $category . '"';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_affected_rows() == 0)
		{
			return array('status' => 'fail', 'reason' => 'photo album does not exist!');
		}

		return array('status' => 'success', 'id' => $image_id);
	}

	function photoalbum_remove_photo($photos, $owner, $photoalbum_information = null, $override = false)
	{
		/* $photos can be a single photo id or an array of ids. 
			For security reasons, the owner of the photos has to,
			be passed in $owner. To disable owner check, just pass
			override instead of the owners id.

			TODO: add support for updating the photoalbums-table...
		*/
		if(!is_array($photos))
		{
			$photos = array($photos);
		}
		if(!is_array($photoalbum_information) && $photoalbum_information != 'iknowwhatido')
		{
			to_logfile('error', __FILE__, __LINE__, 'I was called without information about how table:photo_albums should be updated. Neither wasI given assurance that the script who called me would take care of the updating itself.');
			die('<p class="error">Ett allvarligt fel har inträffats på hamsterpaj. Dina bilder kunde inte tas bort, incidenten har rapporterats till de som arbetar med hamsterpaj</p>');
		}

		if($override != true)
		{
			$query = 'SELECT owner FROM photos WHERE id IN(' . implode(',', $photos) . ') LIMIT 10';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			while($data = mysql_fetch_assoc($result))
			{
				if($data['owner'] != $_SESSION['login']['id'])
				{
					return false;
				}
			}
		}
		$query = 'DELETE photos, comments FROM photos, comments WHERE photos.id IN(' . implode(',', $photos) . ') AND type = "photos" AND comments.item_id = photos.id';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(isset($photoalbum_information))
		{
			$new_photos = array_diff($photoalbum_information['photos'], $photos);
			$new_string = implode(',', $new_photos);
			$query = 'UPDATE photo_albums SET photos = "' . $new_string . '" WHERE (id = "' . $photoalbum_information['id'] . '" or position = "' . $photoalbum_information['category'] . '") && owner = "' . $owner . '" LIMIT 1';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}

		foreach($photos AS $image_id)
		{
			unlink(PATHS_IMAGES . 'photoalbums/images_' . round($image_id / 1000) . '/' . $image_id . '_thumb.jpg');
			unlink(PATHS_IMAGES . 'photoalbums/images_' . round($image_id / 1000) . '/' . $image_id . '_full.jpg');			
		}
		return true;
	}
?>
