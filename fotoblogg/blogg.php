<?php
		$ui_options['ui_modules']['photoblog_user'] = 'User';
		$ui_options['ui_modules']['photoblog_calendar'] = 'Kalender';
		$ui_options['ui_modules']['photoblog_albums'] = 'Album';
		$ui_options['javascripts'][] = 'jquery.protect-image.js';
		
		if ( ! isset($highest_date) || $highest_date == 0 )
		{
			$date = date('Ym', time());
		}
		else
		{
			$date = $highest_date;
		}
		
		$user_id = $photoblog_user['id'];		
		$options = array(
			'user' => $user_id,
			'month' => $date
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
					$out .= '<dt id="photoblog_prevmonth"><a id="prevmonth" title="F&ouml;reg&aring;ende m&aring;nad" href="#prev-month">F&ouml;reg&aring;ende m&aring;nad</a></dt>';
					$is_first = true;
					$last_day = array('date' => null, 'formatted' => null);
					if ( ! count($photos) )
					{
						$out .= '<dt>Här var det tomt...</dt>';
					}
					
					$photos_last_index = count($photos) - 1;
					
					foreach ( $photos as $key => $photo )
					{
						if ( $last_day['date'] != $photo['date'] )
						{
							$last_day['date'] = $photo['date'];
							$last_day['formatted'] = date('j/n', strtotime($photo['date']));
							$out .= '<dt>' . $last_day['formatted'] . '</dt>';
						}
						$class = '';
						if ( $key == 0 ) $class = ' class="first-image"';
						elseif ( $key == $photos_last_index ) $class = ' class="last-image"';
						$out .= '<dd' . $class . '><a title="' . $photo['date'] . '" ' . ($key == $photos_last_index ? 'class="photoblog_active"' : '') . ' href="#image-' . $photo['id'] . '"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" title="' . $photo['username'] . '" /></a></dd>';
					}
					
					$out .= '<dt id="photoblog_nextmonth"><a id="nextmonth" title="N&auml;sta m&aring;nad" href="#next-month">N&auml;sta m&aring;nad</a></dt>';
					$out .= '</dl>';
				$out .= '</div>';
			$out .= '</div>';
		$out .= '</div>';
		$out .= '<div id="photoblog_image">';
		$last_photo = $photos[$photos_last_index];
		$out .= '<p><img src="' . IMAGE_URL . 'photos/full/' . floor($last_photo['id'] / 5000) . '/' . $last_photo['id'] . '.jpg" alt="" /></p>';
		$out .= '</div>';
		$out .= '<div id="photoblog_description">';
			$out .= '<div id="photoblog_description_text">';
				$out .= $last_photo['description'];
				$out .= '</div>';
		$out .= '</div>';
		
		// Some test-data
		$comment1 = array('user_id' => 625058, 'username' => 'Lef', 'comment' => 'Din mamma är så fet!');
		$comment2 = array('user_id' => 3, 'answer' => 'Jag bryr mig <del>inte</del> visst! Det gör ont när du säger sånt...', 'username' => 'Johan', 'comment' => 'Din med! :( ');
		$comments = array($comment1, $comment2);
		
		$out .= photoblog_comments_form($options);
		$out .= photoblog_comments_list($comments);
?>