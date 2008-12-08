<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/radio.lib.php');
	include_once('shoutcast/ShoutcastInfo.class.php');
	require(PATHS_INCLUDE . 'libraries/articles.lib.php');
	$ui_options['stylesheets'][] = 'articles.css';
	$ui_options['stylesheets'][] = 'radio.css';
	
	$uri_parts = explode('/', $_SERVER['REQUEST_URI']);
	
	// Get information from server
	$radioinfo = radio_shoutcast_fetch();
	
	/* 
		 ###############################################
			What's playing
		 ###############################################
	*/
	if ($radioinfo['status'] == 1) // If the server is broadcasting
	{
		$out .= '<div id="radio_playing">
							<span>' . $radioinfo['track'] . '</span>
						 </div>
						' . "\n";
	}
	/* 
		 ###############################################
			Logo
		 ###############################################
	*/
	$out .= '<img src="http://images.hamsterpaj.net/radio/logo.png" id="radio_logo" />' . "\n";
	
	/* 
		 ###############################################
			Menu
		 ###############################################
	*/
	$out .= '<ul id="radio_menu">
	<li>
		<a id="radio_menu_01' . ($uri_parts[2] == '' ? '_active"' : '') . '" href="/radio/">Lyssna</a>
	</li>
	<li>
		<a id="radio_menu_02' . ($uri_parts[2] == 'crew' ? '_active"' : '') . '" href="#" class="radio_inactive_link" title="Kommer så småningom">Crew</a>
	</li>
	<li>
		<a id="radio_menu_03' . ($uri_parts[2] == 'program' ? '_active"' : '') . '" href="#" class="radio_inactive_link" title="Kommer så småningom">Program</a>
	</li>
	<li>
		<a id="radio_menu_04' . ($uri_parts[2] == 'schema' ? '_active"' : '') . '" href="#" class="radio_inactive_link" title="Kommer så småningom">Kalender</a>
	</li>
	<li>
		<a id="radio_menu_05" href="/chat/">IRC-kanal</a>
	</li>
	<li>
		<a id="radio_menu_06" href="/diskussionsforum/hamsterradio/">Radioforum</a>
	</li>
	<li>
		<a id="radio_menu_07' . ($uri_parts[2] == 'om_radion' ? '_active"' : '') . '" href="/radio/om_radion/">Om radion</a>
	</li>
	</ul>
	' . "\n";
	
	switch ($uri_parts[2])
	{
		case 'crew':
		break;
		
		case 'program':		
		break;
		
		case 'schema':		
		break;
		
		case 'om_radion':		
			$article = articles_fetch(array('id' => '96'));
			$out .= render_full_article($article);
		break;
			
		default:
			// $radio_sending = radio_sending_fetch();
			//$radio_sending['dj'] = 'lef';
			if (isset($radio_sending) && $radioinfo['status'] == 1) // If the program is sent when watching
			{
				$out .= '<div id="radio_sending">' . "\n";
					$out .= '<img src="http://images.hamsterpaj.net/images/users/thumb/772209.jpg" />' . "\n";
					$out .= '<div class="radio_about">' . "\n";
					$out .= '<h2>Stöna med fnas</h2>' . "\n";
					$out .= '<strong>DJ: Fnas</strong><br />' . "\n";
					$out .= '<span>Varje torsdag 20-22</span>' . "\n";
					$out .= '</div>' . "\n";
				$out .= '</div>' . "\n";
			}
			else
			{
				if ($radioinfo['status'] == 1)
				{
					$out .= '<div id="radio_sending_slinga">' . "\n";
					$out .= '</div>' . "\n";
				}
				else
				{
					$out .= '<div id="radio_sending_inactive">' . "\n";
					$out .= '</div>' . "\n";
				}
			}
			
			// $options['broadcasting'] = false; // Det ska inte sändas just nu
			// $options['limit'] = 1; // Vi vill bara se det kommande
			// $options['order']= 'DESC'; // Vi vill ha det senaste
			// $radio_next_program = radio_schedule_fetch($options);
			// $radio_next_program['dj'] = 'lef';
			if (isset($radio_next_program)) // If there are any next program
			{
				$out .= '<div id="radio_next_program">' . "\n";
					$out .= '<img src="http://images.hamsterpaj.net/images/users/thumb/625058.jpg" />' . "\n";
					$out .= '<div class="radio_about">' . "\n";
					$out .= '<h2>Kodarsnack</h2>' . "\n";
					$out .= '<strong>DJ: Lef</strong><br />' . "\n";
					$out .= '<span>Varje fredag 02-03</span>' . "\n";
					$out .= '</div>' . "\n";
				$out .= '</div>' . "\n";
			}
			else
			{
				$out .= '<div id="radio_next_program_inactive">' . "\n";
				$out .= '</div>' . "\n";
			}
			
			if ($radioinfo['status'] == 1) // If the server is broadcasting
			{
				$out .= '<ul id="choose_player">
									<li>
										<a id="choose_player_01" href="/radio/lyssna/pls" title="Den här länken fungerar i de flesta spelare. Exempelvis: iTunes, Real player, Winamp, VLC, foobar.">Spela upp radio i normala spelare</a>
									</li>
									<li>
										<a id="choose_player_02" href="/radio/lyssna/asx" title="">Spela upp radio i Windows Media Player</a>
									</li>
									<li>
										<a id="choose_player_03" href="/radio/lyssna/webbspelare" title="">Spela upp radio i webbspelaren</a>
									</li>
								</ul>' . "\n";
			}
			switch ($uri_parts[3])
			{
				case 'pls':
					header('Content-Type: audio/scpls');
					header('Content-Disposition: attachment;filename="lyssna.pls"');
					$fp=fopen('playlists/lyssna.pls','r');
					fpassthru($fp);
					fclose($fp);
				break;
				case 'asx':
					header('Content-Type: video/x-ms-asf');
					header('Content-Disposition: attachment;filename="lyssna.asx"');
					$fp=fopen('playlists/lyssna.asx','r');
					fpassthru($fp);
					fclose($fp);
				break;
				case 'webbspelare':
				
				break;
			}
		break;
	}
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>