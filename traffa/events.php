<?php
	require('../include/core/common.php');
	
	require_once(PATHS_LIBRARIES . 'photos.lib.php');
	require_once(PATHS_LIBRARIES . 'comments.lib.php');
	require_once(PATHS_LIBRARIES . 'guestbook.lib.php');
	
	
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['javascripts'][] = 'photos.js';
	
	$ui_options['stylesheets'][] = 'comments.css';
	$ui_options['javascripts'][] = 'comments.js';
	
	$ui_options['title'] = 'Dina nya händelser - Hamsterpaj.net';
	$ui_options['menu_path'] = array('traeffa', 'haendelser');
	
	ui_top($ui_options);
	
	if(login_checklogin())
	{
		echo '<h1>Nya händelser</h1>' . "\n";
		
		echo '<h2>Nya fotokommentarer</h2>' . "\n";
		$photos = photos_fetch(array('user' => $_SESSION['login']['id'], 'force_unread_comments' => true));
		if(count($photos) > 0)
		{
			echo 'Wosch! Nya kommentarer att besvara!<br />' . "\n";
			echo photos_list($photos);
		}
		else
		{
			echo '<italic>Du har inga oläsa fotokommentarer.</italic>';
		}
		
		echo '<h2>Gamla PM-systemet är borttaget.</h2>' . "\n";
		echo '<italic>Vi har tagit bort gamla PM-systemet. Mer info lär komma inom kort.</italic>';
	}
	else
	{
		echo '<h1>Du måste vara inloggad</h1>' . "\n";
		echo 'Du måste vara inloggad för att komma åt den här sidan.' . "\n";
	}
	
	ui_bottom();
?>