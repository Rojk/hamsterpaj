<?php
	try
	{
		require('../include/core/common.php');
		require(PATHS_LIBRARIES . 'radio.lib.php');
		include_once('shoutcast/ShoutcastInfo.class.php');
		require(PATHS_LIBRARIES . 'articles.lib.php');
		$ui_options['stylesheets'][] = 'radio.css';
		$ui_options['javascripts'][] = 'radio.js';
		
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
			case 'post_settings':
				if (is_privilegied('radio_dj'))
				{
					switch ($uri_parts[3])
					{	
						case 'schedule_add':
							
							if(!isset($_POST['program'], $_POST['starttime'], $_POST['endtime']))
							{
								throw new Exception('Getmjölk i soppan?');
							}
							
							if(strlen($_POST['starttime']) < 0 || !is_numeric($_POST['program']) || strlen($_POST['endtime']) < 0)
							{
								throw new Exception('Något fält var ju INTE korrekt ifyllt säger jag ju då.');
							}
							
							radio_schedule_add(array(
								'program_id' => $_POST['program'],
								'starttime' => $_POST['starttime'],
								'endtime' => $_POST['endtime']
							));
						break;
					}
				}
				else
				{
					throw new Exception('Haxx0r!');
				}
			break;
			
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
					$ui_options['stylesheets'][] = 'forms.css'; // Includes stylesheet for form.
					
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
					$ui_options['stylesheets'][] = 'forms.css'; // Inkluderar stilmall för formuläret
					
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
				$options['show_sent'] = false; 
				$options['limit'] = 30; 
				$options['order-direction']= 'DESC'; // We want them in order by which is coming first
				$radio_events = radio_schedule_fetch($options);
				$out .= '<table style="width: 638px;">' . "\n";
				foreach($radio_events as $radio_event)
				{
					$out .= '<tr>' . "\n";
					$out .= '<td>' . $radio_event['name'] . '</td>' . "\n";
					$out .= '<td>' . $radio_event['username'] . '</td>' . "\n";
					$out .= '<td>' . $radio_event['starttime'] . '</td>' . "\n"; // Snygga till datumet så det står: Imorgon 22:00 Eller ngt sådant snyggt
					if(is_privilegied('radio_sender'))
					{
						$out .= '<td><a href="#" title="Ändra sändning">Ändra</a></td>' . "\n"; // När man klickar edit ska formuläret för att lägga till sändning användas för att ändra sändningen.
						$out .= '<td><a href="#" title="Ta bort sändning">Ta bort</a></td>' . "\n"; // Ajax
					}
					$out .= '</tr>' . "\n";
				}
				$out .= '</table>' . "\n";
				if(is_privilegied('radio_sender'))
				{
					$ui_options['stylesheets'][] = 'forms.css'; // includes stylesheet for form
					
					$options['order-by']= 'name';
					$options['order-direction']= 'DESC';
					$radio_programs = radio_programs_fetch($options); // For Select list
					unset($options);
					
					$out .= '<fieldset>' . "\n";
					$out .= '<legend>Lägg till sändning</legend>' . "\n";
					$out .= '<form action="/radio/post_settings/schedule_add" method="post">';
					$out .= '<table class="form">' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="program">Program <strong>*</strong></label></th>' . "\n";
						$out .= '<td><select name="program">' . "\n";
							foreach($radio_programs as $radio_program)
							{
								$out .= '<option value="' . $radio_program['id'] . '">' . $radio_program['name'] . '</option>' ."\n";
							}
						$out .= '</select>' . "\n";
						$out .= '</td>' . "\n";
					$out .= '</tr>' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="starttime">Starttid <strong>*</strong></label></th>' . "\n"; // Jquery calendar?
						$out .= '<td><input type="text" name="starttime" value="' . date( 'Y-m-d') . ' 00:00:00" /></td>' . "\n";
					$out .= '</tr>' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th><label for="endtime">Sluttid <strong>*</strong></label></th>' . "\n"; // jquery calendar?
						$out .= '<td><input type="text" name="endtime" value="' . date( 'Y-m-d') . ' 00:00:00" /></td>' . "\n";
					$out .= '</tr>' . "\n";				
					$out .= '</table>' . "\n";
					$out .= '<input type="submit" id="submit" value="Spara" />' . "\n"; // Ajax
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
				else
				{
					if ($radioinfo['status'] == 1) // If the server is up but no program scheduled
					{
						$out .= '<div id="radio_sending_slinga">' . "\n"; // Displays "Slingan rullar"
						$out .= '</div>' . "\n";
					}
					else
					{
						$out .= '<div id="radio_sending_inactive">' . "\n"; // Displays "Ingen sändning
						$out .= '</div>' . "\n";
					}
				}
				
				$options['broadcasting'] = false; // It shouldn't be broadcasting right now
				$options['limit'] = 1; // We only want the coming one
				$options['order-direcion']= 'DESC'; // We want the coming one
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