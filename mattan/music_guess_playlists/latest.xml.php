<?php
	include('../../include/core/common.php');
	
	$query = 'SELECT * FROM music_guess_songs ORDER BY id DESC LIMIT 1';
	$result = mysql_query($query) or die(mysql_error());
	$data = mysql_fetch_assoc($result);
	
	header('Content-type: application/xml; charset=utf-8');
	echo '<?xml version="1.0" encoding="UTF-8"?>';

echo '<playlist version="0" xmlns="http://xspf.org/ns/0/">

<trackList>

<track>

<location>http://images.hamsterpaj.net/music_guess_mp3/' . $data['secret_id'] . '.mp3</location>

<image>http://images.hamsterpaj.net/mattan/album_pic.jpg</image>

<annotation>senaste gissa laten</annotation>

</track>

</trackList>

</playlist>';

?>