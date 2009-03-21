<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('traeffa', 'galleriet');
	ui_top($ui_options);

	echo '<h1>Galleri</h1>';
	echo '<p class="intro">Här visas de senaste användarna som loggat in. Klicka på en bild för att visa användarens profil.</p>';
	
	echo cache_load('traffa_gallery');

	ui_bottom();	
?>
