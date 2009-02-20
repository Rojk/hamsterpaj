<?php
	try
	{
		require('../include/core/common.php');
		require(PATHS_LIBRARIES . 'radio.lib.php');
		include_once('shoutcast/ShoutcastInfo.class.php');
		require(PATHS_LIBRARIES . 'articles.lib.php');
		$ui_options['stylesheets'][] = 'radio.css';
		$ui_options['javascripts'][] = 'radio.js';
		$ui_options['stylesheets'][] = 'forms.css'; // includes stylesheet for form
		
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
		$out .= '<img src="' . IMAGE_URL . 'radio/logo.png" id="radio_logo" />' . "\n";
		
		/* 
			 ###############################################
				Menu
			 ###############################################
		*/
		$out .= '<ul id="radio_menu">
		<li>
			<a id="radio_menu_01' . (($uri_parts[2] == 'lyssna' || $uri_parts[2] == '') ? '_active"' : '') . '" href="/radio/">Lyssna</a>
		</li>
		<li>
			<a id="radio_menu_02' . ($uri_parts[2] == 'crew' ? '_active"' : '') . '" href="/radio/crew">Crew</a>
		</li>
		<li>
			<a id="radio_menu_03' . ($uri_parts[2] == 'program' ? '_active"' : '') . '" href="/radio/program">Program</a>
		</li>
		<li>
			<a id="radio_menu_04' . ($uri_parts[2] == 'schema' ? '_active"' : '') . '" href="/radio/schema">Schema</a>
		</li>
		<li>
			<a id="radio_menu_05" href="' . radio_chat_url_render() . '">IRC-kanal</a>
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
				$options['order-by'] = 'username';
				$options['order-direction']= 'ASC';
				$radio_djs = radio_djs_fetch($options);
				foreach($radio_djs as $radio_dj)
				{
					$out .= '<div class="radio_crew" id="' . $radio_dj['user_id'] . '">' . "\n";
					$out .= ui_avatar($radio_dj['user_id']) . "\n";
					$out .= '<h2>' . $radio_dj['username'] . '</h2>' . "\n";
					if(is_privilegied('radio_admin'))// Only administrators for the whole radio can edit/remove DJs
					{
						$out .= '<div class="admin_tools">' . "\n";
						// $out .= '<a class="dj_edit_information" href="#" title="Ändra DJ">Ändra</a> | ' . "\n"; // När man klickar edit ska formuläret för att lägga till sändning användas för att ändra sändningen.
						$out .= '<a class="dj_remove" href="/ajax_gateways/radio.php?action=dj_remove&id=' . $radio_dj['user_id'] . '&no_ajax=true" title="Ta bort DJ">Ta bort</a>' . "\n"; // Ajax, popup-accept
						$out .= '</div>' . "\n";
					}
					$out .= '<p>' . $radio_dj['information'] . '</p>' . "\n"; // substr to fitting characters
					$out .= '</div>' . "\n";
				}
				if(is_privilegied('radio_admin')) // Only administrators for the whole radio can edit/add DJs
				{
					$out .= '<div id="form_notice"></div>' . "\n";
					$out .= '<fieldset>' . "\n";
					$out .= '<legend>Lägg till DJ</legend>' . "\n";
					$out .= '<form action="/ajax_gateways/radio.php?action=dj_add" method="post">';
					$out .= '<table class="form">' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="radio_dj_add_name">Användarnamn <strong>*</strong></label></th>' . "\n";
						$out .= '<td><input type="text" name="radio_dj_add_name" id="radio_dj_add_name" /></td>' . "\n";
					$out .= '</tr>' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="radio_dj_add_information">Information <strong>*</strong></label></th>' . "\n"; 
						$out .= '<td><textarea name="radio_dj_add_information" cols="45" rows="5" id="radio_dj_add_information"></textarea></td>' . "\n";
					$out .= '</tr>' . "\n";				
					$out .= '</table>' . "\n";
					$out .= '<input type="submit" id="radio_dj_add_submit" value="Spara" />' . "\n"; // Ajax, privilegiet radio_sender ska ges till personen
					$out .= '</form>';
					$out .= '</fieldset>' . "\n";
				}	
			break;
			
			case 'program':
				$options['order-by']= 'name';
				$options['order-direction']= 'DESC';
				$radio_programs = radio_programs_fetch($options);
				
				$zebra = 'even';
				foreach($radio_programs as $radio_program)
				{
					$out .= '<div id="' . $radio_program['id'] . '" class="' . $zebra . ' radio_program">' . "\n";
						$out .= ui_avatar($radio_program['user_id']) . "\n";
						$out .= '<div class="radio_about">' . "\n";
						$out .= '<h2>' . $radio_program['name'] . '</h2>' . "\n";
						if(is_privilegied('radio_sender')) // Only senders can add/edit programs
						{
							$out .= '<a href="#" class="program_remove">Ta bort</a>' . "\n";
						}
						$out .= '<strong>DJ: ' . $radio_program['username'] . '</strong><br />' . "\n";
						$out .= '<span>' . $radio_program['sendtime'] . '</span>' . "\n";
						$out .= '</div>' . "\n";
					$out .= '</div>' . "\n";
					$zebra = ($zebra == 'even') ? 'uneven' : 'even';
				}

				if(is_privilegied('radio_sender')) // Only senders can add/edit programs
				{
					$radio_djs = radio_djs_fetch(); // Fetches DJ's to the Select list in the form
					
					$out .= '<br style="clear: both;" /><div id="form_notice"></div>' . "\n";
					$out .= '<fieldset>' . "\n";
					$out .= '<legend>Lägg till program</legend>' . "\n";
					$out .= '<form action="/ajax_gateways/radio.php?action=program_add&no_ajax=true" method="post">';
					$out .= '<table class="form">' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="name">Namn <strong>*</strong></label></th>' . "\n";
						$out .= '<td><input id="radio_program_add_name" type="text" name="name" /></td>' . "\n";
					$out .= '</tr>' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="dj">DJ <strong>*</strong></label></th>' . "\n";
						$out .= '<td><select id="radio_program_add_dj" name="dj">' . "\n";
							foreach($radio_djs as $radio_dj)
							{
								$out .= '<option value="' . $radio_dj['user_id'] . '">' . $radio_dj['username'] . '</option>' ."\n";
							}
						$out .= '</select>' . "\n";
						$out .= '</td>' . "\n";
					$out .= '</tr>' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="sendtime">Sändningstid/Övrigt </label></th>' . "\n";
						$out .= '<td><input  id="radio_program_add_sendtime" type="text" name="sendtime" /></td>' . "\n";
					$out .= '</tr>' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="information">Information <strong>*</strong></label></th>' . "\n"; 
						$out .= '<td><textarea  id="radio_program_add_information" name="information" cols="45" rows="10"></textarea></td>' . "\n";
					$out .= '</tr>' . "\n";				
					$out .= '</table>' . "\n";
					$out .= '<input type="submit" id="radio_program_add_submit" value="Spara" />' . "\n";
					$out .= '</form>';
					$out .= '</fieldset>' . "\n";
				}	
			break;
			
			case 'schema':	
				$options['show_from_today'] = true; 
				$options['limit'] = 30; 
				$options['order-direction']= 'ASC'; // We want them in order by which is coming first
				$options['sort-by-day'] = true;
				$radio_events = radio_schedule_fetch($options);
				
				foreach($radio_events as $radio_day => $radio_day_events)
				{
					$radio_day_fix_margin++;
					$out .= '<div class="radio_schedule_day';
					$out .= ' radio_schedule_' . strtolower(date('D', strtotime($radio_day))) . '' . "\n";
					$out .= (is_integer($radio_day_fix_margin / 3) ? ' radio_schedule_day_marginfix' : '') . "\n";
					$out .= '">' . "\n";
					$out .= '<h2>' . date('j/n', strtotime($radio_day)) . '</h2>' . "\n";
					$out .= '<table>' . "\n";
					foreach($radio_day_events as $radio_event)
					{
						$out .= '<tr id="' . $radio_event['id'] . '">' . "\n";
						$out .= '<td class="radio_schedule_program_name">' . $radio_event['name'] . '</td>' . "\n";
						$out .= '<td>' . date('H:i', strtotime($radio_event['starttime'])) . '</td>' . "\n"; // Snygga till datumet så det står: Imorgon 22:00 Eller ngt sådant snyggt
						if(is_privilegied('radio_sender'))
						{
							$out .= '<td><a href="#" class="schedule_remove" title="Ta bort sändning">(x)</a></td>' . "\n"; // Ajax
						}
						$out .= '</tr>' . "\n";
					}
					$out .= '</table>' . "\n";
					$out .= '</div>' . "\n";
				}
				if(is_privilegied('radio_sender'))
				{
					$options['order-by']= 'name';
					$options['order-direction']= 'DESC';
					$radio_programs = radio_programs_fetch($options); // For Select list
					unset($options);
					$out .= '<br style="clear: both;" /><div id="form_notice"></div>' . "\n";
					$out .= '<fieldset>' . "\n";
					$out .= '<legend>Lägg till sändning</legend>' . "\n";
					$out .= '<form action="/ajax_gateways/radio.php?action=schedule_add" method="post">';
					$out .= '<table class="form">' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="program">Program <strong>*</strong></label></th>' . "\n";
						$out .= '<td><select name="program" id="radio_schedule_add_program">' . "\n";
							foreach($radio_programs as $radio_program)
							{
								$out .= '<option value="' . $radio_program['id'] . '">' . $radio_program['name'] . '</option>' ."\n";
							}
						$out .= '</select>' . "\n";
						$out .= '</td>' . "\n";
					$out .= '</tr>' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="starttime">Starttid <strong>*</strong></label></th>' . "\n"; // Jquery calendar?
						$out .= '<td><input type="text" name="starttime" id="radio_schedule_add_starttime" value="' . date( 'Y-m-d') . ' 00:00:00" /></td>' . "\n";
					$out .= '</tr>' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="endtime">Sluttid <strong>*</strong></label></th>' . "\n"; // jquery calendar?
						$out .= '<td><input type="text" name="endtime" id="radio_schedule_add_endtime" value="' . date( 'Y-m-d') . ' 00:00:00" /></td>' . "\n";
					$out .= '</tr>' . "\n";				
					$out .= '</table>' . "\n";
					$out .= '<input type="submit" id="radio_schedule_add_submit" value="Spara" />' . "\n";
					$out .= '</form>';
					$out .= '</fieldset>' . "\n";
				}
			break;
			
			case 'om_radion':		
				$ui_options['stylesheets'][] = 'articles.css'; // Includes stylesheet for article
				$article = articles_fetch(array('id' => '96'));
				$out .= render_full_article($article);
			break;
				
			default:
				$time['hour'] = date('G');
				$time['minute'] = intval(date('G'));
				$time['day'] = intval(date('d'));
				$time['month'] = date('n');
				#$time['hour'] = 21;
				$choose_player_moved = false;
				if (!($time['hour'] > 22) && !($time['minute'] > 30) && $time['hour'] >= 21 && 20 == $time['day'] && 2 == $time['month']) {
					$choose_player_moved = true;
					if ($radioinfo['status'] == 1) // If the server is broadcasting we will show a list of players to listen in
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
					
					$hjh_images_serialized = file_get_contents('http://images.hamsterpaj.net/radio/hardjavlahamster/images.serial');
					$hjh_images = unserialize($hjh_images_serialized);
					$out .= '<div id="hardjavlahamster_header">' . "\n";
					$out .= '<img src="' . $url_images_old . 'fp_ads/hjh-live-just-nu.jpg" alt="Hård Jävla Hamster - LIVE just nu!" />' . "\n";
					$out .= '</div>' . "\n";
					$out .= '<div id="hardjavlahamster_images">' . "\n";
					foreach ($hjh_images as $key => $hjh_image_hash) {
						if (2 == $key) {
							$out .= '<br />' . "\n";
						}
						$out .= '<img class="hardjavlahamster_live_image" src="' . $url_images_old . 'radio/hardjavlahamster/' . $hjh_image_hash . '.jpg" alt="Hårjd Jävla Hamster - LIVE image" />' . "\n";
					}
					$out .= '</div>' . "\n";
				}
				
				$options['broadcasting'] = true; // It should be broadcasting right now
				$options['limit'] = 1; // We only wish to have one
				$options['order-direction']= 'DESC'; // We want the latest
				$radio_sending = radio_schedule_fetch($options);
				if (isset($radio_sending[0]) && $radioinfo['status'] == 1) // If program is sent and server is up
				{
					$out .= '<div id="radio_sending">' . "\n";
						$out .= '<img src="' . IMAGE_URL . 'images/users/thumb/' . $radio_sending[0]['user_id'] . '.jpg" />' . "\n";
						$out .= '<div class="radio_about">' . "\n";
						$out .= '<h2>' . $radio_sending[0]['name'] . '</h2>' . "\n";
						$out .= '<strong>DJ: ' . $radio_sending[0]['username'] . '</strong><br />' . "\n";
						$out .= '<span>' . $radio_sending[0]['sendtime'] . '</span>' . "\n";
						$out .= '</div>' . "\n";
					$out .= '</div>' . "\n";
				}
				elseif (isset($radio_sending[0])) // If it is a program scheduled but no program is up
				{
					$out .= '<div id="radio_sending_inactive">' . "\n"; // Displays "Ingen sändning
					$out .= '</div>' . "\n";
					$radio_server_problems = true;
				}
				elseif ($radioinfo['status'] == 1) // If the server is up but no program scheduled
				{
						$out .= '<div id="radio_sending_slinga">' . "\n"; // Displays "Slingan rullar"
						$out .= '</div>' . "\n";
				}
				else
				{
					$out .= '<div id="radio_sending_inactive">' . "\n"; // Displays "Ingen sändning
					$out .= '</div>' . "\n";
				}
				
				$options['broadcasting'] = false; // It shouldn't be broadcasting right now
				$options['limit'] = 1; // We only want the coming one
				$options['order-direction']= 'ASC'; // We want the coming one
				$radio_next_program = radio_schedule_fetch($options);
				if (isset($radio_next_program[0])) // If there are any next program
				{
					$out .= '<div id="radio_next_program">' . "\n";
						$out .= ui_avatar($radio_next_program[0]['user_id']) . "\n";
						$out .= '<div class="radio_about">' . "\n";
						$out .= '<h2>' . $radio_next_program[0]['name'] . '</h2>' . "\n";
						$out .= '<strong>DJ: ' . $radio_next_program[0]['username'] . '</strong><br />' . "\n";
						$out .= '<span>' . $radio_next_program[0]['sendtime'] . '</span>' . "\n";
						$out .= '</div>' . "\n";
					$out .= '</div>' . "\n";
				}
				else
				{
					$out .= '<div id="radio_next_program_inactive">' . "\n"; // Displays a "Inget inplanerat" box
					$out .= '</div>' . "\n";
				}
				$out .= '<br style="clear: both;" />' . "\n";
				
				if($radio_server_problems === true)
				{
					$out .= '<div class="form_notice_error">Något verkar vara fel med servern, vi jobbar på felet och skyller det på Heggan.</div>' . "\n";
				}
				if (!$choose_player_moved) {
					if ($radioinfo['status'] == 1) // If the server is broadcasting we will show a list of players to listen in
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
				}
				switch ($uri_parts[3])
				{
					case 'pls': // If address is lyssna/pls it will download pls playlist
						header('Content-Type: audio/scpls');
						header('Content-Disposition: attachment;filename="lyssna.pls"');
						echo '[playlist]
NumberOfEntries=2
File1=http://' . RADIO_SERVER . '
Title1=HamsterRadio - Server 1
Length1=-1
File2=http://' . RADIO_SERVER2 . '
Title2=HamsterRadio - Server 2
Length2=-1
Version=2';
						die();
					break;
					case 'asx': // If address is lyssna/asx it will download asx playlist
						header('Content-Type: video/x-ms-asf');
						header('Content-Disposition: attachment;filename="lyssna.asx"');
						echo '<ASX version = "3.0">
<Entry>
<REF HREF="http://' . RADIO_SERVER . '" />
</Entry>
<Entry>
<REF HREF="http://' . RADIO_SERVER2 . '" />
</Entry>
</ASX>';
						die();
					break;
					case 'webbspelare': // If address is lyssna/webbspelaren it will open the webplayer in a popup-window
					
					break;
				}
			break;
		}
	}
	catch (Exception $error)
	{
		$options['type'] = 'error';
    $options['title'] = 'Nu blev det fel här';
    $options['message'] = $error -> getMessage();
    $options['collapse_link'] = 'Visa felsökningsinformation';
    $options['collapse_information'] = preint_r($error, true);
    $out .= ui_server_message($options);
		preint_r($error);
	}
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
