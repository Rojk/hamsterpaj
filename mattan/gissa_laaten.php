<?php
	require('../include/core/common.php');

	$ui_options['menu_path'] = array('mattan', 'gissa_laaten');
	$ui_options['title'] = 'Gissa låten på Hamsterpaj - kan du känna igen vilken låt vi valt ut?';
	$ui_options['stylesheets'][] = 'music_guess.css';

	ui_top($ui_options);
	
	echo '<h1>Gissa låten - testversion</h1>' . "\n";
	
	event_log_log('music_guess_impression');
	
	$query = 'SELECT * FROM music_guess_songs ORDER BY id DESC LIMIT 1';
	$result = mysql_query($query) or trace('sql_error', $query);
	if(mysql_num_rows($result) != 1)
	{
		echo '<p>Ett fel tycks ha uppstått, ingen låt hittades.</p>' . "\n";
	}
	else
	{
		$data = mysql_fetch_assoc($result);
		rounded_corners_top(array('color' => 'orange_deluxe'));
		$swf_url = 'http://hamsterpaj.net/xspf/xspf_player_slim.swf?playlist_url=/mattan/music_guess_playlists/latest.xml.php&player_title=nyaste+gissa+laten';
		$swf_w = '500';
		$swf_h = '15';
		echo '
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
			codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" 
			width="' . $swf_w . '" 
			height="15" 
			id="xspf_player" 
			align="middle">
			<param name="movie" value="' . $swf_url . '" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#e6e6e6" />
			<embed src="' . $swf_url . '" 
			quality="high" 
			bgcolor="#e6e6e6" 
			width="' . $swf_w . '" 
			height="' . $swf_h . '" 
			name="xspf_player" 
			type="application/x-shockwave-flash" 
			pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object>';
		
		echo '<a href="http://images.hamsterpaj.net/music_guess_mp3/' . $data['secret_id']. '.mp3"><button class="button_50">MP3-fil</button></a>' . "\n";
		rounded_corners_bottom(array('color' => 'orange_deluxe'));
		
		if(login_checklogin())
		{
			$query = 'SELECT song FROM music_guess_guesses WHERE user = "' . $_SESSION['login']['id'] . '" ORDER BY song DESC LIMIT 1';
			$result = mysql_query($query);
			$last_guess = mysql_fetch_assoc($result);
			if($last_guess['song'] == $data['id'])
			{
				echo '<h2>Tack för din gissning!</h2>' . "\n";
			}
			else
			{
				if(isset($_POST['artist']))
				{
					$artist_score = 0;
					$song_score = 0;
					
					$alternate_spellings = unserialize($data['alternate_spellings']);
	
					if($data['artist_score'] == 1)
					{
						$alternatives = array(strtolower($data['artist']));
						foreach($alternate_spellings['artist'] AS $artist_spelling)
						{
							$alternatives[] = strtolower($artist_spelling);			
						}
						foreach($alternatives AS $alternative)
						{
							similar_text(strtolower($_POST['artist']), $alternative, $percentage);
							if($percentage > 90)
							{
								$artist_score = 1;
							}
						}
					}
	
					if($data['song_score'] == 1)
					{
						$alternatives = array(strtolower($data['song']));
						foreach($alternate_spellings['song'] AS $song_spelling)
						{
							$alternatives[] = strtolower($song_spelling);	
						}
						foreach($alternatives AS $alternative)
						{
							similar_text(strtolower($_POST['song']), $alternative, $percentage);
							if($percentage > 90)
							{
								$song_score = 1;
							}
						}
					}
					
					$query = 'INSERT INTO music_guess_guesses (user, song, artist_score, song_score)';
					$query .= ' VALUES("' . $_SESSION['login']['id'] . '", "' . $data['id'] . '", "' . $artist_score . '", "' . $song_score . '")';
					
					mysql_query($query);
					
					/* Total */
					$insertquery = 'INSERT INTO music_guess_scoring (user, score, period_type, period) VALUES("' . $_SESSION['login']['id'] . '", "' . ($artist_score+$song_score) . '", "total", 0)';
					$updatequery = 'UPDATE music_guess_scoring SET score = score + "' . ($artist_score + $song_score) . '" WHERE user = "' . $_SESSION['login']['id'] . '" AND period_type = "total" LIMIT 1';
					mysql_query($insertquery) or mysql_query($updatequery) or report_sql_error($updatequery, __FILE__, __LINE__);
	
					/* This week */
					$insertquery = 'INSERT INTO music_guess_scoring (user, score, period_type, period) VALUES("' . $_SESSION['login']['id'] . '", "' . ($artist_score+$song_score) . '", "week", "' . date('YW') . '")';
					$updatequery = 'UPDATE music_guess_scoring SET score = score + "' . ($artist_score + $song_score) . '" WHERE user = "' . $_SESSION['login']['id'] . '" AND period_type = "week" AND period = "' . date('YW') . '" LIMIT 1';
					mysql_query($insertquery) or mysql_query($updatequery) or report_sql_error($updatequery, __FILE__, __LINE__);
									
					/* This month */
					$insertquery = 'INSERT INTO music_guess_scoring (user, score, period_type, period) VALUES("' . $_SESSION['login']['id'] . '", "' . ($artist_score+$song_score) . '", "month", "' . date('Ym') . '")';
					$updatequery = 'UPDATE music_guess_scoring SET score = score + "' . ($artist_score + $song_score) . '" WHERE user = "' . $_SESSION['login']['id'] . '" AND period_type = "month" AND period = "' . date('Ym') . '" LIMIT 1';
					mysql_query($insertquery) or mysql_query($updatequery) or report_sql_error($updatequery, __FILE__, __LINE__);
	
					$query = 'UPDATE music_guess_songs SET guesses = guesses + 1, correct = correct + "' . ($artist_score + $song_score) . '" WHERE id = "' . $data['id'] . '" LIMIT 1';
					mysql_query($query);
					event_log_log('musig_guess_guess');

					echo '<h2>Tack för din gissning!</h2>' . "\n";
				}
				else
				{
					echo '<h2>Vet du vilken låt det är?</h2>' . "\n";
					echo '<form id="music_guess_form" method="post"><label for="music_guess_artist">Artist</label><label for="music_guess_song">Låtnamn</label><br />' . "\n";
					echo '<input type="text" name="artist" class="textbox" /><input type="text" name="song" class="textbox" /><input type="submit" value="Skicka &raquo;" class="button_60" /></form>' . "\n";
				}
			}
		}
		else
		{
			echo '<h2><a href="/register.php">Bli medlem</a> och var med i tävlingen du också!</h2>' . "\n";
		}
	}
	
	
		$swf_url = 'http://hamsterpaj.net/xspf/xspf_player.swf?playlist_url=/mattan/music_guess_playlists/latest_15.xml.php&player_title=aldre+gissa+laten';
		$swf_w = '638';
		$swf_h = '215';
		echo '
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
			codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" 
			width="' . $swf_w . '" 
			height="' . $swf_h . '" 
			id="xspf_player" 
			align="middle">
			<param name="movie" value="' . $swf_url . '" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#e6e6e6" />
			<embed src="' . $swf_url . '" 
			quality="high" 
			bgcolor="#e6e6e6" 
			width="' . $swf_w . '" 
			height="' . $swf_h . '" 
			name="xspf_player" 
			type="application/x-shockwave-flash" 
			pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object>';
	/*
	echo '<div id="music_guess_earlier">' . "\n";
	echo '<h2>Tidigare låtar</h2>' . "\n";
	$query = 'SELECT * FROM music_guess_songs ORDER BY id DESC LIMIT 1, 15';
	$songs = query_cache(array('query' => $query));
	foreach($songs AS $song)
	{
		echo '<li>' . "\n";
		echo '<h3>' . fix_time($song['timestamp']) . '</h3>' . "\n";
		echo '<div id="music_guess_' . $song['secret_id'] . '" style="display: inline;"><a href="http://www.macromedia.com/go/getflashplayer">Installera Flash Player</a>.</div>
					<script type="text/javascript">
					var s1 = new SWFObject("/entertain/flvplayer.swf","single","200","20","7");
					s1.addParam("allowfullscreen","true");
					s1.addVariable("file","http://images.hamsterpaj.net/music_guess_mp3/' . $song['secret_id'] . '.mp3");
					s1.addVariable("width","200");
					s1.addVariable("height","20");
					s1.write("music_guess_' . $song['secret_id'] . '");
					</script><br />';
		echo $song['artist'] . ' - ' . $song['song'];
		echo '</li>' . "\n";
	}
	echo '</div>' . "\n";
	
	*/
	
	echo '<div id="music_guess_toplists">' . "\n";
	echo '<h2>Topplista - denna vecka</h2>' . "\n";
	$query = 'SELECT mgs.score, mgs.user, l.username FROM music_guess_scoring AS mgs, login AS l WHERE l.id = mgs.user AND mgs.period_type = "week" AND mgs.period = "' . date('YW') . '" ORDER BY mgs.score DESC, l.username ASC LIMIT 10';
	$result = mysql_query($query);
	echo '<ol>' . "\n";
	while($user = mysql_fetch_assoc($result))
	{
		echo '<li><a href="/traffa/profile.php?id=' . $user['user'] . '">' . $user['username'] . '</a> ' . $user['score'] . 'p</li>' . "\n";
	}
	echo '</ol>' . "\n";
	/* Fetch your own score */
	$query = 'SELECT score FROM music_guess_scoring WHERE user = "' . $_SESSION['login']['id'] . '" AND period_type = "week" AND period = "' . date('YW') . '"';
	$result = mysql_query($query);
	$data = mysql_fetch_assoc($result);

	/* Fetch yout own rank */
	$query = 'SELECT count(score) as TOTALFOUND FROM music_guess_scoring WHERE score > "' . $data['score'] . '" AND period_type = "week" AND period = "' . date('YW') . '"';
	$result = mysql_query($query);
	$rank = mysql_fetch_assoc($result);
	
	echo '<em class="music_guess_yourrank_divider">...</em><br />', "\n";
	echo '<em class="music_guess_yourrank">' . $rank['TOTALFOUND'] . '.  Din score: ' . ($data['score'] > 0 ? $data['score'] : 0) . 'p</em>'. "\n";		

	echo '<h2>Topplista - denna månad</h2>' . "\n";
	$query = 'SELECT mgs.score, mgs.user, l.username FROM music_guess_scoring AS mgs, login AS l WHERE l.id = mgs.user AND mgs.period_type = "month" AND mgs.period = "' . date('Ym') . '" ORDER BY mgs.score DESC, l.username ASC LIMIT 10';
	$result = mysql_query($query);
	echo '<ol>' . "\n";
	while($user = mysql_fetch_assoc($result))
	{
		echo '<li><a href="/traffa/profile.php?id=' . $user['user'] . '">' . $user['username'] . '</a> ' . $user['score'] . 'p</li>' . "\n";
	}
	echo '</ol>' . "\n";
	/* Fetch your own score */
	$query = 'SELECT score FROM music_guess_scoring WHERE user = "' . $_SESSION['login']['id'] . '" AND period_type = "month" AND period = "' . date('Ym') . '"';
	$result = mysql_query($query);
	$data = mysql_fetch_assoc($result);

	/* Fetch yout own rank */
	$query = 'SELECT count(score) as TOTALFOUND FROM music_guess_scoring WHERE score > "' . $data['score'] . '" AND period_type = "month" AND period = "' . date('Ym') . '"';
	$result = mysql_query($query);
	$rank = mysql_fetch_assoc($result);
	
	echo '<em class="music_guess_yourrank_divider">...</em><br />', "\n";
	echo '<em class="music_guess_yourrank">' . $rank['TOTALFOUND'] . '.  Din score: ' . ($data['score'] > 0 ? $data['score'] : 0) . 'p</em>'. "\n";		
		
	echo '<h2>Topplista - totalt</h2>' . "\n";
	$query = 'SELECT mgs.score, mgs.user, l.username FROM music_guess_scoring AS mgs, login AS l WHERE l.id = mgs.user AND mgs.period_type = "total" ORDER BY mgs.score DESC, l.username ASC LIMIT 10';
	$result = mysql_query($query);
	echo '<ol>' . "\n";
	while($user = mysql_fetch_assoc($result))
	{
		echo '<li><a href="/traffa/profile.php?id=' . $user['user'] . '">' . $user['username'] . '</a> ' . $user['score'] . 'p</li>' . "\n";
	}
	echo '</ol>' . "\n";
	/* Fetch your own score */
	$query = 'SELECT score FROM music_guess_scoring WHERE user = "' . $_SESSION['login']['id'] . '" AND period_type = "total"';
	$result = mysql_query($query);
	$data = mysql_fetch_assoc($result);

	/* Fetch yout own rank */
	$query = 'SELECT count(score) as TOTALFOUND FROM music_guess_scoring WHERE score > "' . $data['score'] . '" AND period_type = "total"';
	$result = mysql_query($query);
	$rank = mysql_fetch_assoc($result);

	echo '<em class="music_guess_yourrank_divider">...</em><br />', "\n";
	echo '<em class="music_guess_yourrank">' . $rank['TOTALFOUND'] . '.  Din score: ' . ($data['score'] > 0 ? $data['score'] : 0) . 'p</em>'. "\n";		
	
	
	echo '</div>' . "\n";

	ui_bottom();
?>