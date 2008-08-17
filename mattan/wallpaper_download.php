<?php
	require('../include/core/common.php');
	define('WALLPAPER_URL', IMAGE_URL . 'wallpapers/');
	
	
	if(!in_array($_SESSION['downloaded_wallpapers'], $_GET['id']))
	{
		$query = 'UPDATE wallpapers SET downloads = downloads + 1 WHERE id = "' . $_GET['id'] . '" LIMIT 1';
		mysql_query($query);
		$_SESSION['downloaded_wallpapers'][] = $_GET['id'];
	}
	
	echo '<h1>Högerklicka på bilden och välj "Använd som skrivbordsbakgrund"</h1>' . "\n";
	echo '<img src="' . WALLPAPER_URL . $_GET['id'] . '_' . $_GET['resolution'] . '.jpg" />';
?>