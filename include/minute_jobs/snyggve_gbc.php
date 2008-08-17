<?php
	/*  Snyggve garbage collection process
			Removes old images (600s or older) from the Snyggve temp directory
	*/

	define('SNYGGVE_TEMP_PATH', $hp_path . 'snyggve_temp/');
	
	$thumbs = opendir(SNYGGVE_TEMP_PATH . 'thumb/');
	echo SNYGGVE_TEMP_PATH . 'thumb/';
	while($filename = readdir($thumbs))
	{
		if($filename != '.' && $filename != '..')
		{
			if(filemtime(SNYGGVE_TEMP_PATH . 'thumb/' . $filename) < (time()-600))
			{
				unlink(SNYGGVE_TEMP_PATH . 'thumb/' . $filename);
			}
		}
	}
	
	$fulls = opendir(SNYGGVE_TEMP_PATH . 'full/');
	while($filename = readdir($fulls))
	{
		if($filename != '.' && $filename != '..')
		{
			if(filemtime(SNYGGVE_TEMP_PATH . 'full/' . $filename) < (time()-600))
			{
				unlink(SNYGGVE_TEMP_PATH . 'full/' . $filename);
			}
		}
	}
?>
