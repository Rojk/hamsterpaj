<?php
	
	$db_server = '192.168.1.45';
	$db_username = 'hamsterpaj';
	$db_password = 'Wn6ntF09IgmOCdZA';
	$db_database = 'hp';
	
	mysql_connect($db_server, $db_username, $db_password);
	mysql_select_db($db_database);
	
	$query = 'SELECT * FROM music_guess_songs ORDER BY id DESC LIMIT 1';
	$result = mysql_query($query);
	$data = mysql_fetch_assoc($result);
	
	header('Content-type: application/xspf+xml');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<playlist version="0" xmlns="http://xspf.org/ns/0/">

<trackList>

<track>

<location>http://images.hamsterpaj.net/music_guess_mp3/116af4e48e1e148f0168b94875f607dd.mp3</location>

<image>http://images.hamsterpaj.net/mattan/album_pic.jpg</image>

<annotation>senaste gissa laten</annotation>

</track>

</trackList>

</playlist>