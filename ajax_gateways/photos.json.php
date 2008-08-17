<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/photos.lib.php');
	
	$options['user'] = $_GET['user'];
	if(isset($_GET['date']) && strtotime($_GET['date']) > 0)
	{
		$options['date'] = $_GET['date'];
	}
	$photos = photos_fetch($options);	
	echo json_encode($photos);
?>