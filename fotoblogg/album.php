<?php
	$ui_options['ui_modules']['photoblog_user'] = 'User';
	$ui_options['ui_modules']['photoblog_albums'] = 'Album';
	$ui_options['ui_modules']['photoblog_calendar'] = 'Kalender';
	
		if ( isset($uri_parts[4]) && preg_match('/^[a-zA-Z0-9-_]+$/', $uri_parts[4]) )
		{
			$albumname = $uri_parts[4];	
			$out .= '<h2>Visar album ' . $albumname . 's foton.</h2>';
			global $photoblog_user;
			$options['name'] = $albumname;
			$photoblog_album = photoblog_categories_fetch($options);
			
			$user_id = $photoblog_user['id'];
			$options = array(
				'category' => $photoblog_album[0]['id'],
				'user_id' => $user_id
			);
			
			$out .= '<script type="text/javascript">';
				$out .= 'hp.photoblog.current_user = {';
					$out .= 'id: ' . $user_id;
				$out .= '};';
			$out .= '</script>';
			
			$photos = photoblog_photos_fetch($options);
			$out .= '<div id="photoblog_thumbs">';
				$out .= '<div id="photoblog_thumbs_container">';
					$out .= '<div id="photoblog_thumbs_inner">';
						$out .= '<dl>';
						$is_first = true;
					
					$out .= '<dt id="photoblog_prevmonth"><a rel="prev-month" title="F&ouml;reg&aring;ende m&aring;nad" href="#" style="display: none;">F&ouml;reg&aring;ende m&aring;nad</a></dt>';
						if ( ! count($photos) )
						{
							$out .= '<dt>H&auml;r var det tomt...</dt>';
						}
						
						foreach ( $photos as $photo )
						{
							$out .= '<dd><a title="' . $photo['date'] . '" rel="imageid_' . $photo['id'] . '" ' . ($is_first ? 'class="photoblog_active"' : '') . ' href="#image-' . $photo['id'] . '"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" title="' . $photo['username'] . '" /></a></dd>';
							$is_first = false;
						}
						
						$out .= '<dt id="photoblog_nextmonth"><a rel="next-month" title="N&auml;sta m&aring;nad" href="#" style="display: none;">N&auml;sta m&aring;nad</a></dt>';
						$out .= '</dl>';
					$out .= '</div>';
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
			
			// Some test-data
			$comment1 = array('user_id' => 625058, 'username' => 'Lef', 'comment' => 'Din mamma är så fet!');
			$comment2 = array('user_id' => 3, 'answer' => 'Jag bryr mig <del>inte</del> visst! Det gör ont när du säger sånt...', 'username' => 'Johan', 'comment' => 'Din med! :( ');
			$comments = array($comment1, $comment2);
			
			$out .= photoblog_comments_form($options);
			$out .= photoblog_comments_list($comments);
		}
		else
		{		
				$out .= 'Listar ' . $photoblog_user['username'] . 's album.';
				global $photoblog_user;
				$options['user'] = $photoblog_user['id'];
				$photoblog_albums = photoblog_categories_fetch($options);
				
				foreach($photoblog_albums as $photoblog_album)
				{
					if(count($photoblog_album['photos']) >= 1)
					{
						$out .= '<a href="/fotoblogg/' . $photoblog_user['username'] . '/album/' . $photoblog_album['name'] . '" />' . "\n";
						$out .= '<img src="http://images.hamsterpaj.net/photos/full/' . floor($photoblog_album['photos']['id'][0]/5000) . '/' . $photoblog_album['photos']['id'][0] . '.jpg" />' . "\n";
						$out .= '<h3>' . $photoblog_album['name'] . '</h3>' . "\n";
						$out .= '</a>' . "\n";
					}
				}
		}
		
		
		
?>