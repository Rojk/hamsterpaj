<?php
	require('../include/core/common.php');

	$music['Beastie Boys']['tracks'][] = array('title' => 'Now Get Busy');

	$music['Cirkus Miramar']['tracks'][] = array('title' => 'Fattiga barn med hjärna');	
	$music['Cirkus Miramar']['tracks'][] = array('title' => 'kärleken i klor');	
	$music['Cirkus Miramar']['tracks'][] = array('title' => 'Kung ingenting');	
	$music['Cirkus Miramar']['tracks'][] = array('title' => 'Schlager från Moskva');
	$music['Cirkus Miramar']['tracks'][]= array('title' => 'Stjärnan och matrosen');	

	$music['CK']['tracks'][]= array('title' => 'På Rosa Moln');	

	$music['Dan The Automator']['tracks'][]= array('title' => 'Relaxation Spa Treatment');
	
	$music['Dia Psalma']['tracks'][]= array('title' => 'Luft');
	$music['Dia Psalma']['tracks'][]= array('title' => 'Vemodsvals');
	$music['Dia Psalma']['tracks'][]= array('title' => 'Mördarvals');
	$music['Dia Psalma']['tracks'][]= array('title' => 'Hundra kilo kärlek');
	$music['Dia Psalma']['tracks'][]= array('title' => 'Requiem');
	$music['Dia Psalma']['tracks'][]= array('title' => 'Alla älskar dig');
	$music['Dia Psalma']['tracks'][]= array('title' => 'Hon får');
	$music['Dia Psalma']['tracks'][]= array('title' => 'Tro rätt, tro fel');
	$music['Dia Psalma']['tracks'][]= array('title' => 'Den som spar');	


	$music['Dubmood']['text'] = 'Dubmood är bla bla.';
	$music['Dubmood']['miscellaneous'] = 'Webbsidor: <a href="http://www.razor1911.com/dubmood/">Officiell hemsida</a> <a href="http://www.myspace.com/dubmoodst">Officiell myspace</a> <a href="http://dubmood.wordpress.com/">Officiell blog</a>';
	$music['Dubmood']['tracks'][]= array('title' => '0xx00');
	$music['Dubmood']['tracks'][]= array('title' => 'Chiphop #3');
	$music['Dubmood']['tracks'][]= array('title' => 'Idas Haschkaka');
	$music['Dubmood']['tracks'][]= array('title' => 'Monkey Island');
	$music['Dubmood']['tracks'][]= array('title' => 'ST Style');
	$music['Dubmood']['tracks'][]= array('title' => 'Vodka Dance #1');
	$music['Dubmood']['tracks'][]= array('title' => 'YM 2149');



	$music['Fronda']['tracks'][]= array('title' => 'Det andra svaret');	
	$music['Fronda']['tracks'][]= array('title' => 'En spark i ansiktet');	
	$music['Fronda']['tracks'][]= array('title' => 'Håll dig långt bort');	
	$music['Fronda']['tracks'][]= array('title' => 'Rullar fram');	
	$music['Fronda']['tracks'][]= array('title' => 'Sitter i min barstol');	

	$music['Peaches']['tracks'][] = array('title' => 'Dansa Nu', 'url' => 'http://www.peaches.se/audio/DansaNu.wma');
	$music['Peaches']['tracks'][] = array('title' => 'Hallå Hallå', 'url' => 'http://www.peaches.se/audio/HallaHalla.wma');
	$music['Peaches']['tracks'][] = array('title' => 'Jag Ser Dig', 'url' => 'http://www.peaches.se/audio/Rymdraket.wma');
	$music['Peaches']['tracks'][] = array('title' => 'Jag Vill Inte Gå Hit', 'url' => 'http://www.peaches.se/audio/JagVillInteGaHit.wma');
	$music['Peaches']['tracks'][] = array('title' => 'Rosa helikopter', 'url' => 'http://www.peaches.se/audio/RosaHelikopter.wma');
	$music['Peaches']['tracks'][] = array('title' => 'Rymdraket', 'url' => 'http://www.peaches.se/audio/Rymdraket.wma');
	$music['Peaches']['tracks'][] = array('title' => 'Skateboard', 'url' => 'http://www.peaches.se/audio/Skateboard.wma');
	$music['Peaches']['tracks'][] = array('title' => 'Stockholm', 'url' => 'http://www.peaches.se/audio/Stockholm.wma');
	$music['Peaches']['tracks'][] = array('title' => 'Vi Två', 'url' => 'http://www.peaches.se/audio/ViTva.wma');
	$music['Peaches']['tracks'][] = array('title' => 'Vi Går På Disco', 'url' => 'http://www.peaches.se/audio/Disco.wma');

	$music['T-röd']['tracks'][]= array('title' => 'En tönt som spelar Counter-Strike');		

	$music['Virtual Voices']['tracks'][]= array('title' => 'Aceton-Sture');
	$music['Virtual Voices']['tracks'][]= array('title' => 'Min fula fru');
	$music['Virtual Voices']['tracks'][]= array('title' => 'Prao på Systembolaget');		


	// javascript:hp.music_library.fetch_artist_data('Dubmood')
	
	if(isset($_GET['ajax']) && isset($_GET['artist']) && array_key_exists(base64_decode($_GET['artist']), $music))
	{
		$_GET['artist'] = base64_decode($_GET['artist']);
		
		$artist = $music[$_GET['artist']];
		
		$_GET['artist'] = htmlspecialchars($_GET['artist']);
		
		?>
			{
				"title": "<?php echo addslashes($_GET['artist']); ?>",
				"text": "<?php echo addslashes($artist['text']); ?>",
				"miscellaneous": "<?php echo addslashes($artist['miscellaneous']); ?>",
				"playlist":
				[
					<?php
						$track_list = array();
						foreach($artist['tracks'] as $track)
						{
							$url = isset($track['url']) ? addslashes($track['url']) : addslashes('http://music.t67.se/mp3/' . url_secure_string($_GET['artist']) . '_-_' . url_secure_string($track['title']) . '.mp3');
							$track_list[] = '{ "title": "' . addslashes($track['title']) . '", "url": "' . $url . '" }';
						}
						
						echo implode(', ', $track_list);
					?>
				]
			}
		<?php
	}
	else
	{
		$ui_options['menu_path'] = array('mattan', 'gratis_musik');
		$ui_options['title'] = 'Gratis musik på Hamsterpaj!';
		$ui_options['stylesheets'][] = 'music_library.css';
		$ui_options['javascripts'][] = 'music_library.js';
		ui_top($ui_options);
		
		echo '<h1>Gratis musik på Hamsterpaj!</h1>' . "\n";
		echo '<p>Vi bjuder på gratis musik helt lagligt. Bara högerklicka på låtarna och ta "download"</p>' . "\n";
	
		echo '<div id="music_library_player">' . "\n";
		?>
		<div class="left">
			<h2 class="artist_title" id="music_library_player_title">TITEL</h2>
			<div class="artist_text" id="music_library_player_text">TEXT</div>
			<div class="artist_miscellaneous" id="music_library_player_miscellaneous">MISC</div>
		</div>
		
		<div class="right">
			<div class="player_playlist" id="music_library_player_playlist">
				PLAYLIST
			</div>
			<div class="player_player" id="music_library_player_player">PLAYER</div>
		</div>
		<?php
		echo '</div>' . "\n";
	
		foreach($music AS $artist_name => $artist)
		{
			echo '<a name="' . url_secure_string($artist_name) . '"></a>' . "\n";
			echo '<h2 style="margin-top: 20px;">' . $artist_name . '</h2>' . "\n";
			echo '<ul>' . "\n";
			foreach($artist['tracks'] AS $track)
			{
				$url = (isset($track['url'])) ? $track['url'] : 'http://music.t67.se/mp3/' . url_secure_string($artist_name) . '_-_' . url_secure_string($track['title']) . '.mp3';

				echo '<li>';
				if(isset($_GET['fisk']))
				{
					echo ' <a href="' . $url . '" onclick="hp.music_library.fetch_artist_data(\'' . base64_encode($artist_name) . '\', this.href);return false;">' . $track['title'] . '</a>';
				}
				else
				{
					echo '<a href="' . $url . '">' . $track['title'] . '</a>';
				}			
				echo '</li>' . "\n";
			}
			echo '</ul>' . "\n";
		}
		
		ui_bottom();
	}
?>


