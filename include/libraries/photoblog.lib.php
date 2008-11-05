<?php
	function photoblog_upload_upload($options)
	{
		if(!isset($options['user']))
		{
			throw new Exception('You must specify an user id.');
		}
		
		if(!isset($options['file_temp_path']))
		{
			throw new Exception('Missing parameter: file_temp_path');
		}
		
		$query = 'INSERT INTO user_photos (user, upload_complete, date)';
		$query .= ' VALUES("' . $options['user'] . '", 0, "' . date('Y-m-d') . '")';
		if(!mysql_query($query))
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
?>