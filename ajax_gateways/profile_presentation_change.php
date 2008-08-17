<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/photos.lib.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');

	if(!login_checklogin()){ echo 'Men gå och gräv ner dig...'; }

	$action = isset($_GET['action']) ? $_GET['action'] : '';
	
	switch($action)
	{
		case 'markup_properties':
			switch(isset($_GET['markup_handler']) ? $_GET['markup_handler'] : '')
			{
				case 'image':
					$html_parameters[] = '{"type": "input_hidden", "id": "choosen_photo_id"}';
					
					$photos = photos_fetch( array('user' => $_SESSION['login']['id'], 'order-by' => 'up.id', 'order-direction' => 'DESC') );
					$onclick_handlers = array();
					
					$html_data .= '<h2 style="margin: 0px">Välj en bild ifrån ditt fotoalbum</h2>';
					$html_data .= 'Klicka på en bild ifrån ditt fotoalbum för att infoga den i presentationstexten. <input type="button" class="button_120" value="Ladda upp bilder" id="select_photos_upload" /><br style="clear: both" />';
					$onclick_handlers[] = '{"id": "select_photos_upload", "call": "hp.profile.presentation.change.markup_properties.property_onevent.image_upload()"}';
					
					$html_data .= '<div id="profile_presentation_change_markup_properties_image">';
					foreach($photos as $photo)
					{
						$onclick_handlers[] = '{"id": "select_photo_photos_' . $photo['id'] . '", "call": "hp.profile.presentation.change.markup_properties.property_onevent.image_select(' . $photo['id'] . ')"}';
						$html_data .= '<img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" alt="" id="select_photo_photos_' . $photo['id'] . '" />';
					}
					$html_data .= '</div>';
					
					$html_data .= '<input type="hidden" id="choosen_photo_id" />';
					
					$html_data .= '<br style="clear: both;" />';
					break;
					
				case 'link':
					$html_parameters[] = '{"type": "select", "id": "link_type"}';
					$html_parameters[] = '{"type": "input_text", "id": "link_href"}';
					
					$onchange_handlers[] = '{"id": "link_type", "call": "hp.profile.presentation.change.markup_properties.property_onevent.link_change_type(this)"}';
					$onclick_handlers[] = '{"id": "link_insert", "call": "hp.profile.presentation.change.markup_properties.property_onevent.link_save()"}';
					
					$link_types['profile'] = 'Användare';
					$link_types['webb'] = 'Webbadress';
					$link_types['photos'] = 'Mitt fotoalbum';
					$link_types['guestbook'] = 'Min gästbok';
					
					
					$html_data .= 'Länk till: ';
					$html_data .= '<select id="link_type">';
					foreach($link_types as $handle => $text)
					{
						$html_data .= '<option value="' . $handle . '">' . $text . '</option>';
					}
					$html_data .= '</select>';
					
					$html_data .= '<div id="profile_presentation_change_markup_properties_link_properties">Användare: <input type="text" id="link_href" /></div>';
					
					$html_data .= '<input type="button" id="link_insert" class="button_60" value="Infoga" />';
					
					break;
					
				case 'header':
					$html_parameters[] = '{"type": "select", "id": "header_size"}';
					$onclick_handlers[] = '{"id": "header_insert", "call": "hp.profile.presentation.change.markup_properties.save()"}';
					
					$header_sizes['rubrik'] =      'Rubrik';
					$header_sizes['underrubrik'] = 'Underrubrik';
					$header_sizes['minirubrik'] =  'Minirubrik';
					
					$html_data .= 'Storlek på rubriken: ';
					$html_data .= '<select id="header_size">';
					foreach($header_sizes as $handle => $text)
					{
						$html_data .= '<option value="' . $handle . '">' . $text . '</option>';
					}
					$html_data .= '</select>';
					
					$html_data .= '<input type="button" id="header_insert" class="button_60" value="Infoga" />';
					
					break;
					
				case 'poll':
					$html_parameters[] = '{"type": "input_hidden", "id": "choosen_poll_id"}';
					
					$polls = poll_fetch( array('author' => $_SESSION['login']['id'], 'limit' => 999) );
					$onclick_handlers = array();
					
					$html_data .= '<h2 style="margin: 0px">Välj en av dina omröstningar</h2>';
					$html_data .= 'Klicka på en omröstning för att infoga den i din presentationstext.<br style="clear: both" />';
					
					$html_data .= '<ul id="profile_presentation_change_markup_properties_poll">';
					foreach($polls as $poll)
					{
						$onclick_handlers[] = '{"id": "select_poll_' . $poll['id'] . '", "call": "hp.profile.presentation.change.markup_properties.property_onevent.poll_select(' . $poll['id'] . ')"}';
						$html_data .= '<li id="select_poll_' . $poll['id'] . '">' . $poll['question'] . '</li>';
					}
					$html_data .= '</ul>';
					
					$html_data .= '<input type="hidden" id="choosen_poll_id" />';
					
					$html_data .= '<br style="clear: both;" />';
					break;
			}
			
			$output .= '{ "html": "' . addslashes($html_data) . '", "html_parameters": [' . implode(', ', $html_parameters) . '], "onclick_handlers": [' . implode(', ', $onclick_handlers) . '], "onchange_handlers": [' . implode(', ', $onchange_handlers) . '] }';
			break;
			
		case 'preview':
			if(isset($_POST['data']))
			{
				echo stripslashes(profile_presentation_parse( array('presentation_text' => $_POST['data'], 'user_id' => $_SESSION['login']['id']) ));
			}
			else
			{
				echo 'No post data sent.';
			}
			break;
	}
	
	echo $output;
?>