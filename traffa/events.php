<?php
	require('../include/core/common.php');
	
	require_once(PATHS_INCLUDE . 'libraries/photos.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/comments.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/guestbook.lib.php');
	
	require_once($hp_includepath . 'message-functions.php');
	require_once($hp_includepath . 'traffa-functions.php');
	
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
		
		echo '<h2>Nya PM i gamla PM-systemet (<a href="/traffa/messages.php">gå till &raquo;</a>)</h2>' . "\n";
		/* This is a straight copy-paste from message-functions.php... */
		$query = 'SELECT m.id, m.timestamp, CONCAT(mm.title, m.title) AS title, m.recipient_status, SUBSTRING(CONCAT(mm.message, m.message), 1, 150) AS message';
		$query .= ' , m.sender AS sender_id, slogin.username AS sender_username, sinfo.birthday AS sender_birthday';
		$query .= ' , sinfo.gender AS sender_gender, sinfo.image AS sender_image';
		$query .= ' FROM messages_new AS m, mass_messages AS mm, login AS slogin, userinfo AS sinfo';
		$query .= ' WHERE mm.id = m.mass_message_id AND slogin.id = m.sender AND sinfo.userid = m.sender AND m.recipient = "' . $_SESSION['login']['id'] . '"';
		$query .= ' AND m.recipient_status = 0';
		$query .= ' ORDER BY m.id DESC';
		$query .= ' LIMIT 0, 25';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		if(mysql_num_rows($result) > 0)
		{
			echo '<ul>' . "\n";
			while($data = mysql_fetch_assoc($result))
			{
				echo '<li><a href="/traffa/messages.php?action=read&message_id=' . $data['id'] . '">' . ((strlen($data['title']) == 0) ? '[Rubrik saknas]' : $data['title']) . '</a></li>' . "\n";
			}
			echo '</ul>' . "\n";
		}
		else
		{
			echo '<italic>Du har inga oläsa PM.</italic>';
		}
	}
	else
	{
		echo '<h1>Du måste vara inloggad</h1>' . "\n";
		echo 'Du måste vara inloggad för att komma åt den här sidan.' . "\n";
	}
	
	ui_bottom();
?>