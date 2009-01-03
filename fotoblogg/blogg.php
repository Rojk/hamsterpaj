<?php
		$ui_options['ui_modules']['photoblog_user'] = 'User';
		$ui_options['ui_modules']['photoblog_calendar'] = 'Kalender';
		$ui_options['ui_modules']['photoblog_albums'] = 'Album';
		
		$user_id = $photoblog_user['id'];
		$options = array(
			'user' => $user_id,
			'month' => date('Ym', time())
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
					$out .= '<dt id="photoblog_prevmonth"><a rel="prev-month" title="F&ouml;reg&aring;ende m&aring;nad" href="#">F&ouml;reg&aring;ende m&aring;nad</a></dt>';
					$is_first = true;
					$last_day = array('date' => null, 'formatted' => null);
					if ( ! count($photos) )
					{
						$out .= '<dt>H&auml;r var det tomt...</dt>';
					}
					
					foreach ( $photos as $photo )
					{
						if ( $last_day['date'] != $photo['date'] )
						{
							$last_day['date'] = $photo['date'];
							$last_day['formatted'] = date('j/n', strtotime($photo['date']));
							$out .= '<dt>' . $last_day['formatted'] . '</dt>';
						}
						$out .= '<dd><a title="' . $photo['date'] . '" rel="imageid_' . $photo['id'] . '" ' . ($is_first ? 'class="photoblog_active"' : '') . ' href="#image-' . $photo['id'] . '"><img src="' . IMAGE_URL . 'photos/mini/' . floor($photo['id']/5000) . '/' . $photo['id'] . '.jpg" title="' . $photo['username'] . '" /></a></dd>';
						$is_first = false;
					}
					
					$out .= '<dt id="photoblog_nextmonth"><a rel="next-month" title="N&auml;sta m&aring;nad" href="#">N&auml;sta m&aring;nad</a></dt>';
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
		$out .= '<div id="photoblog_comments">';
			$out .= '<h3>Kommentarer</h3><ul>';
			
				/*	$options['user_id'] = "625058";
						$out .= message_top($options);
						$out .= '<form action="#" method="post">' . "\n";
						$out .= '<p><label><span>Kommentar:</span> <textarea value="Kommentar..." name="comment"></textarea></label><br /> <input class="submit" type="submit" value="Skicka" /></p>' . "\n";
						$out .= '</form>' . "\n";
						$out .= message_bottom();
					$out .= '</ul>' . "\n"; */
				
				$out .= '<div id="photoblog_comments_container">' . "\n";
					$out .= '<ul>' . "\n";
					$comment1 = array('user_id' => 625058, 'username' => 'Lef', 'comment' => 'Din mamma är så fet!');
					$comment2 = array('user_id' => 3, 'answer' => 'Jag bryr mig <del>inte</del> visst! Det gör ont när du säger sånt...', 'username' => 'Johan', 'comment' => 'Din med! :( ');
					$comments = array($comment1, $comment2);
					foreach ($comments as $comment)
					{
						$options['user_id'] = $comment['user_id'];
						$out .= '<li class="photoblog_comment">' . "\n";
						
						$out .= '<div class="photoblog_comment_userinfo">' . "\n";
						$out .= ui_avatar($comment['user_id']);
						$out .= '<a href="/traffa/profile.php?user_id=' . $comment['user_id'] . '">' . $comment['username'] . '</a>' . "\n";
						$out .= '<span>31 December</span>' . "\n";
						$out .= '</div>' . "\n";
						
						$out .= '<div class="photoblog_comment_bubble_pointer">' . "\n";
							$out .= '<div class="photoblog_comment_text">' . "\n";
							$out .= '<p>' . nl2br($comment['comment']) . '</p>' . "\n";
								if(isset($comment['answer']))
								{
									$out .= '<div class="photoblog_comment_answer">' . "\n";
										$out .= '<span>Svar av: ' . $photoblog_user['username'] . '</span>' . "\n";
										$out .= '<p>' . nl2br($comment['answer']) . '</p>' . "\n";
									$out .= '</div>' . "\n";
								}
							$out .= '</div' . "\n";
						$out .= '</div>' . "\n";
						$out .= '<br style="clear: both;" />' . "\n";
						$out .= '</li>' . "\n";
					}
						
						$out .= '
					
					</ul>
				</div>
			';
		$out .= '</div>';
?>