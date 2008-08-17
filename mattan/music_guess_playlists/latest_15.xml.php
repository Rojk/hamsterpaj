<?php
	include('../../include/core/common.php');

	$query = 'SELECT * FROM music_guess_songs ORDER BY id DESC LIMIT 1, 15';
	$songs = query_cache(array('query' => $query));
	
	header('Content-type: application/xml; charset=utf-8');
	
	//header('Content-type: text/plain');

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	
	echo '<playlist version="0" xmlns="http://xspf.org/ns/0/">' . "\n";
	
	echo '<trackList>' . "\n";
		
	foreach($songs AS $song)
	{
		$song['song'] = strtolower($song['song']);
		$song['artist'] = strtolower($song['artist']);
		$song['timestamp'] = strtolower(fix_time($song['timestamp']));
		
		$search = array('å', 'ä', 'ö', '\'', '"');
		$replace = array('a', 'a', 'o', '', '');
		
		$song['song'] = str_replace($search, $replace, $song['song']);
		$song['artist'] = str_replace($search, $replace, $song['artist']);
		$song['timestamp'] = str_replace($search, $replace, $song['timestamp']);
		/*
		$song['song'] = htmlentities($song['song']);
		$song['artist'] = htmlentities($song['artist']);
		$song['timestamp'] = htmlentities($song['timestamp']);
		*/
		echo '<track>' . "\n";
		
		echo '<location>http://images.hamsterpaj.net/music_guess_mp3/' . $song['secret_id'] . '.mp3</location>' . "\n";
		
		echo '<image>http://images.hamsterpaj.net/mattan/album_pic.jpg</image>' . "\n";
		
		echo '<annotation>' . $song['timestamp'] . ', ' . $song['artist'] . ' - ' . $song['song'] . '</annotation>' . "\n";
		
		echo '</track>' . "\n";
	}
		
	echo '</trackList>' . "\n";
	
	echo '</playlist>' . "\n";
?>