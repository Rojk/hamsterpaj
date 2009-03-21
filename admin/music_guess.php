<?php
	require('../include/core/common.php');
	include(PATHS_LIBRARIES . 'schedule.lib.php');
	
	$ui_options['menu_path'] = array('administration', 'music_guess');
	ui_top($ui_options);
	
	define('MP3_PATH', '/mnt/amuse/music_guess/');
	
	// If users experiences problems with the is_privilegied() below, try
	// to change their privilegie value from 1 to 0 (0=root). /Joel
	if(is_privilegied('music_guess_admin'))
	{
		if(isset($_GET['filename']))
		{
			$file_id = rand(1, 100000000);
			echo '<h1>' . utf8_encode($_GET['filename']) . '</h1>';
			echo filesize(MP3_PATH . $_GET['filename']) . ' byte';
			
			echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n";
			echo '<input type="hidden" name="file_id" value="' . $file_id . '" />' . "\n";
			echo '<input type="hidden" name="original_filename" value="' . $_GET['filename'] . '" />' . "\n";
			
			$times = array('0.1 0.5', '0.1 0.10', '0.30 0.35', '0.40 0.42', '0.45 0.46', '0.50 0.53', '1.2 1.4', '1.5 1.7', '2.1 2.4');
			
			for($i = 0; $i < count($times); $i++)
			{
				passthru('mp3splt ' . escapeshellarg(MP3_PATH . stripslashes($_GET['filename'])) . ' -n -q -d /mnt/amuse/music_guess_mp3 -o ' . $file_id . '_' . $i . '.mp3 ' . $times[$i]);
				
				echo '<li style="margin-bottom: 20px;"><input type="radio" value="' . $i . '" name="file_version" /> ' . $times[$i] . "\n";
				$swf_player = 'http://hamsterpaj.net/swfs/xspf_player_slim.swf';
				$swf_song = '?song_url=http://amuse.hamsterpaj.net/music_guess_mp3/' . $file_id . '_' . $i . '.mp3';
				$swf_song_title = '&song_title=' . $_GET['filename'];
				$swf_player_title = '&player_title=' . $_GET['filename'];
				
				$swf_url = $swf_player . $swf_song . $swf_title;
				
				$swf_w = '500';
				$swf_h = '15';
				echo '<br />
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

				echo '</li>' . "\n";
			}
			
			echo '<div style="float: left; width: 300px;">' . "\n";
			echo '<h3>Artist</h3>' . "\n";
			echo '<input type="text" name="artist" />' . "\n";
			echo '<h3>Alternativa stavningar</h3>' . "\n";
			echo '<input type="text" name="artist1" /><br />' . "\n";
			echo '<input type="text" name="artist2" /><br />' . "\n";
			echo '<input type="text" name="artist3" /><br />' . "\n";
			echo '</div>' . "\n";
			
			echo '<div style="width: 300px; float: left;">' . "\n";
			echo '<h3>Låtnamn</h3>' . "\n";
			echo '<input type="text" name="song" />' . "\n";
			echo '<h3>Alternativa stavningar</h3>' . "\n";
			echo '<input type="text" name="song1" /><br />' . "\n";
			echo '<input type="text" name="song2" /><br />' . "\n";
			echo '<input type="text" name="song3" /><br />' . "\n";			
			echo '</div>' . "\n";
			
			echo '<br style="clear: both;" />' . "\n";
			
			echo '<ul><li><input type="checkbox" name="artist_score" value="1" checked="checked" id="artist_score" /><label for="artist_score">Ge poäng för rätt artist</label></li>' . "\n";
			echo '<li><input type="checkbox" name="song_score" value="1" checked="checked" id="song_score" /><label for="song_score">Ge poäng för rätt låttitel</label></li></ul>' . "\n";

			echo '<input type="submit" value="Spara" />' . "\n";
			echo '</form>' . "\n";
		}
		elseif(isset($_POST['artist']))
		{
			if(!isset($_POST['file_version']))
			{
				preint_r($_POST);
				die('Ljudklipp ej valt, backa webbläsaren och välj vilket ljudklipp som skall användas!');
			}
			$data['artist'] = $_POST['artist'];
			$data['song'] = $_POST['song'];
			$data['secret_id'] = md5(rand(0, 999999) .  $_POST['artist'] . $_POST['song'] . rand(0, 999999));
			$data['artist_score'] = $_POST['artist_score'];
			$data['song_score'] = $_POST['song_score'];
		
			
			if(strlen($_POST['artist1']) > 0) { $data['alternate_spellings']['artist'] = $_POST['artist1']; }
			if(strlen($_POST['artist2']) > 0) { $data['alternate_spellings']['artist'] = $_POST['artist2']; }
			if(strlen($_POST['artist3']) > 0) { $data['alternate_spellings']['artist'] = $_POST['artist3']; }

			if(strlen($_POST['song1']) > 0) { $data['alternate_spellings']['artist'] = $_POST['song1']; }
			if(strlen($_POST['song2']) > 0) { $data['alternate_spellings']['artist'] = $_POST['song2']; }
			if(strlen($_POST['song3']) > 0) { $data['alternate_spellings']['artist'] = $_POST['song3']; }

			copy('/mnt/amuse/music_guess_mp3/' . $_POST['file_id'] . '_' . $_POST['file_version'] . '.mp3', '/mnt/images/music_guess_mp3/' . $data['secret_id'] . '.mp3');
			
			$schedule['type'] = 'music_guess';
			$schedule['data'] = serialize($data);
			$schedule['release'] = schedule_release_get(array('type' => 'music_guess'));
			schedule_event_add($schedule);
			
			rename('/mnt/amuse/music_guess/' . $_POST['original_filename'], '/mnt/amuse/music_guess/_KLAR_' . $_POST['original_filename']);

			echo '<h1>Sparat! <a href="/admin/music_guess.php">Lägg in en ny låt</a></h1>' . "\n";
		}
		else
		{
			echo '<ul style="list-style-type: none;">' . "\n";
			$dir = opendir('/mnt/amuse/music_guess/');
			while($filename = readdir($dir))
			{
				if($filename != '.' && $filename != '..')
				{
					$filenames[] = $filename;
				}			
			}
			sort($filenames);
			foreach($filenames AS $filename)
			{
				echo '<li style="line-height: 25px;"><a href="?filename=' . urlencode($filename) . '">' . utf8_encode($filename) . '</a></li>' . "\n";
			}
			echo '<ul>' . "\n";
		}
	}
	
	echo '<h1>Redan inlagda låtar</h1>' . "\n";
	$query = 'SELECT * FROM scheduled_events WHERE type = "music_guess"';
	$result = mysql_query($query);
	while($data = mysql_fetch_assoc($result))
	{
		$song = unserialize($data['data']);
		$songs[] = $song;
	}
	sort($songs);
	
	echo '<ul>' . "\n";
	foreach($songs AS $song)
	{
		echo '<li>' . $song['artist'] . ' - ' . $song['song'] . '</li>' . "\n";
	}
	echo '</ul>' . "\n";
	
	ui_bottom();
?>