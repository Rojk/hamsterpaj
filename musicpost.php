<?php
	require('include/core/common.php');
	
	// De extra kollarna i slutet är pga att henriks md5:a blev stulen i samband med att alla användare blev henrik 2007-08-14
	if(is_numeric($_POST['userid']) && md5($_POST['userid'] . 'gullejo') == $_POST['hash'] && md5($_POST['userid'] . 'gullejo') != md5(644314 . 'gullejo') || $_POST['hash'] == cc79de6bd84c982a8475290fcf13d79a)
	{
		$artist = str_replace('_', ' ', $_POST['Artist1']);
		$query = 'SELECT id FROM artists WHERE name LIKE "' . $artist . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			$artist_id = $data['id'];
		}
		else
		{
			$query = 'INSERT INTO artists (name) VALUES("' . $artist . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$artist_id = mysql_insert_id();
		}
		
		$song = str_replace('_', ' ', $_POST['Title1']);
		$query = 'SELECT id FROM songs WHERE artist = "' . $artist_id . '" AND title LIKE "' . $song . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			$song_id = $data['id'];
			$query = 'UPDATE songs SET popularity = popularity + 1 WHERE id = "' . $song_id . '" ';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
		else
		{
			$query = 'INSERT INTO songs (artist, title, popularity) VALUES("' . $artist_id . '", "' . $song . '", 1)';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$song_id = mysql_insert_id();
		}			
		
		$query = 'INSERT INTO nowplaying (user, timestamp, artist, song) VALUES("' . $_POST['userid'] . '"';
		$query .= ', ' . time() . ', "' . $artist_id . '", "' . $song_id . '")';
		
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		/* Update the users session, so that the right module displays the right song */
		$query = 'SELECT session_id, lastaction FROM login WHERE id = "' . $_POST['userid'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$data = mysql_fetch_assoc($result);
		if($data['lastaction'] > (time()-900))
		{
			$remote_session = session_load($data['session_id']);
			$remote_session['nowplaying']['artist'] = $artist;
			$remote_session['nowplaying']['song'] = $song;
			$remote_session['nowplaying']['updated'] = time();
		
			session_save($data['session_id'], $remote_session);
		}
	}
?>