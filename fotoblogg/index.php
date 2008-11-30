<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	
	$ui_options['stylesheets'][] = 'photoblog.css.php';
	$ui_options['javascripts'][] = 'jquery-ui-slider.js';
	$ui_options['javascripts'][] = 'jquery-ui-datepicker.js';
	$ui_options['javascripts'][] = 'photoblog.js';
	
	$uri_parts = explode('/', $_SERVER['REQUEST_URI']);
	
	$out .= '<ul>
	<li>
		<a href="/fotoblogg/">Min dagbok</a>
	</li>
	<li>
		<a href="/fotoblogg/Lef/">Lefs dagbok</a>
	</li>
	<li>
		<a href="/fotoblogg/ladda_upp/">Ladda upp</a>
	</li>
	<li>
		<a href="/fotoblogg/instaellningar/">Inställningar</a>
	</li>
	</ul>
	' . "\n";
	
	switch ($uri_parts[2])
	{
		case 'instaellningar':
			$ui_options['stylesheets'][] = 'colorpicker.css';
			$ui_options['stylesheets'][] = 'colorpicker_layout.css';
			$ui_options['javascripts'][] = 'colorpicker.js';
			$ui_options['javascripts'][] = 'photoblog_settings.js';
			$ui_options['javascripts'][] = 'colorpicker_eye.js';
			$ui_options['javascripts'][] = 'colorpicker_layout.js';
			$ui_options['javascripts'][] = 'colorpicker_utils.js';
			
			$params['photoblog_settings_detail_color'] = 'FF00FF';
			$params['photoblog_settings_main_color'] = 'FFFF00';
			
			$out .= '<h2>INSTÄLLNINGAR</h2>' . "\n";
			$out .= '<div id="test">test</div>' . "\n";
			$out .= '<form id="photoblog_settings_form" action="/fotoblogg/instaellningar/post_settings.php" method="post">' . "\n";
			$out .= '<label for="photoblog_is_private">Vill du stänga din fotoblogg för allmänheten och endast visa för inloggade medlemmar?</label>' . "\n";
			$out .= '<input type="checkbox" name="photoblog_is_private" id="photoblog_is_private" /><br />' . "\n";
			$save_to_later = $out;
				$out = '<h2 style="margin-top: 0px;">Färginställningar</h2>';
				$out .= '<table id="photoblog_settings_color_table">' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<th>' . "\n";
							$out .= 'Detaljfärg' . "\n";
						$out .= '</th>' . "\n";
						$out .= '<th>' . "\n";
							$out .= 'Bakgrund på element' . "\n";
						$out .= '</th>' . "\n";
					$out .= '</tr>' . "\n";
					$out .= '<tr>' . "\n";
						$out .= '<td>' . "\n";
							$out .= '<div class="colorSelector" id="photoblog_settings_detail_color_div"><div style="background-color: ' . $params['photoblog_settings_detail_color'] . ';"/></div></div>' . "\n";
							$out .= '<input type="hidden" name="photoblog_settings_detail_color" id="photoblog_settings_detail_color" value="' . $params['photoblog_settings_detail_color'] . '" />' . "\n";
						$out .= '</td>' . "\n";
						$out .= '<td>' . "\n";
							$out .= '<div class="colorSelector" id="photoblog_settings_main_color_div"><div style="background-color: ' . $params['photoblog_settings_main_color'] . ';"/></div></div>' . "\n";
							$out .= '<input type="hidden" name="photoblog_settings_main_color" id="photoblog_settings_main_color" value="' . $params['photoblog_settings_main_color'] . '" />' . "\n";
						$out .= '</td>' . "\n";
					$out .= '</tr>' . "\n";
				$out .= '</table>' . "\n";
			$save_to_later .= rounded_corners($out, $params, true);
			$out = $save_to_later;
			$out .= '<input type="submit" value="Spara inställningar" class="button_130" />' . "\n";
			$out .= '</form>' . "\n";
			switch ($uri_parts[3])
			{
				case 'post_settings.php':
					$out .= preint_r($_POST);
				break;
			}
		break;
		
		case 'ladda_upp':		
			switch(isset($uri_parts[3]) ? $uri_parts[3] : '')
			{
				default:
					$ui_options['stylesheets'][] = 'datepicker.css';
					/*	
						########################################################
							Page title
						########################################################
					*/
					$out .= '<h1>Välkommen att ladda upp bilder i din fotoblogg</h1>' . "\n";
													
					/*	
						########################################################
							How to upload photos
						########################################################
					*/			   
		    	$options['type'] = 'notification';
		    	$options['title'] = '';
		    	$options['message'] = '<p>Du kan ladda upp flera bilder samtidigt genom att markera flera när du väljer bilder.<br /> Men tänk på att det tar en del tid att ladda upp bilderna, och du bör kanske inte ladda upp så många i taget.</p><p>Fungerar inte bilduppladdningen? Klicka <a href="#">här</a> för att använda en enklare version av uppladdningen</a>';
		    	$out .= ui_server_message($options);  
					
					/*	
						########################################################
							Upload form
						########################################################
					*/
					$upload_ticket = md5(uniqid(rand()));
					$_SESSION['photoblog']['upload']['upload_tickets'][$upload_ticket] = array();
					
					$out .= '<div id="photoblog_upload_wrapper">' . "\n";
						$out .= '<div id="photoblog_upload_upload_flash_objectarea">&nbsp;</div>' . "\n";
						$out .= '<script type="text/javascript">
				   		var so = new SWFObject("../upload.swf", "photoblog_upload_flash_upload", "100", "20", "8", "#ffffff");
				 		  so.addParam("wmode", "transparent");
				 		  so.addParam("flashVars", "PHPSESSID=" + document.cookie.split("PHPSESSID=")[1].split("&")[0] + "&upload_ticket=' . $upload_ticket . '");
				 		  so.write("photoblog_upload_upload_flash_objectarea");
						</script>' . "\n"; 
					$out .= '</div>' . "\n";
		    						
					/*	
						########################################################
							Uploaded photos setting
						########################################################
					*/
					
					$out .= '<form action="/fotoblogg/ladda_upp/sortering" method="post">' . "\n";
					$out .= '<input type="hidden" value="' . $upload_ticket . '" id="photoblog_upload_ticket" name="photoblog_upload_ticket" />';				
					$out .= '<div id="photoblog_photo_properties_container">&nbsp;</div>' . "\n";
					$out .= '<input type="submit" value="Vidare &raquo;" class="button_80" id="photoblog_photo_properties_save" />' . "\n";
					$out .= '</form>' . "\n";
		
		    	/*	
						########################################################
							Rules of what to upload
						########################################################
					*/
					$options['type'] = 'warning';
					$options['id'] = 'photoblog_upload_rules';
					$options['title'] = 'Att tänka på innan du laddar upp bilder';
					$options['message'] = '<h3>Du förlorar kontrollen över bilder du laddar upp!</h3>' . "\n";
					$options['message'] .= '<p>Bilder som en gång laddats upp till Internet kan kopieras och skickas vidare i all evighet. Det gäller på Hamsterpaj såväl som på alla andra webbsajter.</p>' . "\n";
					$options['message'] .= '<h3>Är du en blond tjej med stora tuttar eller kille med brunt hår och slingor? </h3>' . "\n";
					$options['message'] .= '<p>Den där blyge typen med fula glasögon och som luktade äckligt i din klass i mellanstadiet kommer förr eller senare stjäla din bild för att ragga på Lunarstorm, Hamsterpaj, PlayAhead och andra communities. När det händer så kontaktar du en ordningsvakt så löser vi det!</p>' . "\n";
					$options['message'] .= '<h3>Hamsterpaj är ingen porrsajt, Goatse är äckligt och hitlerhälsningar olagliga</h3>' . "\n";
					$options['message'] .= '<p>Snälla låt bli porr och goatse här, tänk på att barn besöker den här sajten!</p>' . "\n";
					$options['message'] .= '<em>Brottsbalkens sextonde kapitel, paragraf åtta</em><br />' . "\n";
					$options['message'] .= '<p>8 § Den som i uttalande eller i annat meddelande som sprids hotar eller uttrycker missaktning för folkgrupp eller annan sådan grupp av personer med anspelning på ras, hudfärg, nationellt eller etniskt ursprung, trosbekännelse eller sexuell läggning, döms för hets mot folkgrupp till fängelse i högst två år eller om brottet är ringa, till böter.</p>' . "\n";
					$out .= ui_server_message($options);
				break;	
				
				case 'sortering':
					$photo_ids = array();
					
					if(isset($_POST['photoblog_upload_ticket']) && isset($_SESSION['photoblog']['upload']['upload_tickets'][$_POST['photoblog_upload_ticket']]))
					{
						foreach($_POST as $key => $value)
						{
							if(preg_match('/^photoblog_photo_properties_(\d+)_description$/', $key, $matches))
							{
								$matches['photo_id'] = $matches[1];
								if(isset($_POST['photoblog_photo_properties_' . $matches['photo_id'] . '_autodate']))
								{
									$data['date'] = ('Y-m-d');
								}
								elseif(isset($_POST['photoblog_photo_properties_' . $matches['photo_id'] . '_date']) && strtolower($_POST['photoblog_photo_properties_' . $matches['photo_id'] . '_date']) == 'idag')
								{
									$data['date'] = date('Y-m-d');
								}
								elseif(isset($_POST['photoblog_photo_properties_' . $matches['photo_id'] . '_date']) && preg_match('/^20(\d{2})-(\d{1,2})-(\d{1,2})$/', $_POST['photoblog_photo_properties_' . $matches['photo_id'] . '_date']))
								{
									$data['date'] = $_POST['photoblog_photo_properties_' . $matches['photo_id'] . '_date'];
								}
								else
								{
									throw new Exception('Invalid date!');
								}
								
								if(isset($_SESSION['photoblog']['upload']['upload_tickets'][$_POST['photoblog_upload_ticket']][$matches['photo_id']]))
								{
									$data['id'] = $_SESSION['photoblog']['upload']['upload_tickets'][$_POST['photoblog_upload_ticket']][$matches['photo_id']];
								}
								else
								{
									throw new Exception('Photo does not exist in upload ticket!');
								}
								
								$data['description'] = $_POST['photoblog_photo_properties_' . $matches['photo_id'] . '_description'];
								
								photoblog_photos_update($data);
								$photo_ids[] = $data['id'];
							}
						}
					}
					else
					{
						throw new Exception('No ticket id specified or ticket id expired.');
					}
					
					if(empty($photo_ids))
					{
						$out .= 'Något gick lite snett, vi hittade inga av dina foton du just laddade upp.';
						throw new Exception('No photos found when counting uploaded ids before fetching them.');
					}
					else
					{
						$out .= '<h2>Här kan du sortera dina foton</h2>';
						$out .= 'Att sortera sina foton är självklart frivilligt, men kan vara bra så att man kan hålla koll på dem. Klicka och dra fotona dit du vill ha dem eller spara. Annars kan du <a href="/fotoalbum">gå direkt till ditt album och se bilderna du laddade upp</a>.';
						
						$photos = photoblog_photos_fetch(array('id' => $photo_ids), array('save_path' => '/fotoblogg/ladda_upp/spara_sortering'));
						
						$out .= photoblog_sort_module($photos);
					}
				break;
			}
		break;
			
		default:
			if ( isset($uri_parts[2]) && preg_match('/^[a-zA-Z0-9-_]+$/', $uri_parts[2]) )
			{
				$username = $uri_parts[2];	
			}
			elseif ( login_checklogin() )
			{
				$username = $_SESSION['login']['username'];
			}
			else
			{
				$username = 'iphone';
			}
			
			// this should probably be added to som .lib
			$query .= 'SELECT l.id FROM login AS l';
			$query .= ' WHERE l.username = "' . $username . '"';
			$query .= ' LIMIT 1';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$user = mysql_fetch_assoc($result);
			
			$user_id = $user['id'];
			$options = array(
				'user' => $user_id
			);
			
			$photos = photoblog_photos_fetch($options);			
					
			/*$out .= 'Välkommen till ' . "\n";
			$out .= preg_match('/s$/', $uri_parts[2]) ? $uri_parts[2] : $uri_parts[2] . 's';
			$out .= ' fotoblogg!';*/
			$out .= '<h2 id="photoblog_header">' . $username . ' - 2008 September</h2>';
			$out .= '<div id="photoblog_thumbs">';
				$out .= '<div id="photoblog_thumbs_container">';
					$out .= '<dl>';
					$out .= '<dt><a href="#">F&ouml;reg&aring;ende m&aring;nad</a></dt>';
					//$out .= '<dt>23/11</dt>';
					$is_first = true;
					$last_day = array('date' => null, 'formatted' => null);
					foreach ( $photos as $photo )
					{
						if ( $last_day['date'] != $photo['date'] )
						{
							$last_day['date'] = $photo['date'];
							$last_day['formatted'] = date('j/n', strtotime($photo['date']));
							$out .= '<dt>' . $last_day['formatted'] . '</dt>';
						}
						$out .= '<dd><a rel="imageid_' . $photo['id'] . '" ' . ($is_first ? 'class="photoblog_active"' : '') . ' href="#image-' . $photo['id'] . '"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" title="' . $photo['username'] . '" /></a></dd>';
						$is_first = false;
					}
					
					$out .= '<dt id="photoblog_nextmonth"><a href="#">N&auml;sta m&aring;nad</a></dt>';
					$out .= '</dl>';
				$out .= '</div>';
			$out .= '</div>';
			$out .= '<div id="photoblog_image">';
			$first_photo = $photos[0];
			$out .= '<p><img src="http://images.hamsterpaj.net/photos/full/' . floor($first_photo['id'] / 5000) . '/' . $first_photo['id'] . '.jpg" alt="" /></p>';
			$out .= '</div>';
			$out .= '<div id="photoblog_description">';
			$out .= '<div id="photoblog_description_text">';
				$out .= '<p>Jag tänkte att jag skulle kunna äta upp dig.';
				$out .= '<br />';
				$out .= '<br />';
				$out .= 'Not.</p>';
				$out .= '</div>';
			$out .= '</div>';
			$out .= '<div id="photoblog_comments">';
				$out .= '<h3>Kommentarer</h3>';
			$out .= '</div>';
		break;
	}
	//$out .= '<br /><br />' . preint_r($uri_parts);
	/*
	/fotoblogg/user/
	/fotoblogg/instaellningar
	/fotoblogg/ladda_upp
	/fotoblogg/ladda_upp/sortering
	/fotoblogg <- sin egen
	*/
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
