<?php

	require('../include/core/common.php');

	require(PATHS_INCLUDE.'libraries/wallpaper_admin.lib.php');

	if(!is_privilegied('backgrounds_admin'))
	{
		die('Does not compute');
	}

	if($_FILES['uploaded_image']['error'] == 4)
	{
		echo 'Ingen fil laddades upp';
	}
	elseif($_FILES['uploaded_image']['error'] == 0)
	{
		$out = '';
	
		$paper = wallpaper_add_uploaded_image();
		if(is_array($paper['errors']) && isset($paper['errors']) && count($paper['errors']) > 0)
		{
			foreach ($paper['errors'] as $error) {
				$out .= 'Fel: '.$error.'<br />';
			}
		}
		else
		{	
			list($w, $h) = getimagesize(UPLOAD_PATH.$paper['filename']);
			$out = 'Filename::::::'.$paper['filename'].'::::::'.$paper['id'].'::::::'.$w.'::::::'.$h.'::::::';
		}
	
		echo $out;
	}
	?>