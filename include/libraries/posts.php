<?php

	define('POSTS_PER_PAGE', 30);
	define('POSTS_DEFAULT_LIMIT', POSTS_PER_PAGE);

function	hpml_parse($text, $context)
{
	return nl2br($text);
}

function posts_delete_check($post)
{
	if($_SESSION['login']['userlevel'] >= 3)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function posts_admin_check($post)
{
	if($_SESSION['login']['userlevel'] >= 3)
	{
		return true;
	}
	else
	{
		return false;
	}
}


function user_avatar($user_id)
{
	return '<img class="user_avatar" src="' . IMAGE_URL . 'images/users/thumb/' . $user_id . '.jpg" alt="" />';
}


function posts_list($posts, $discussions = null, $options)
{
	$options['quality_level'] = (isset($options['quality_level']) ? $options['quality_level'] : 0);
	
	$options['mode'] = (isset($options['mode'])) ? $options['mode'] : 'forum';
    echo '<input type="hidden" id="debug_input" />';
	foreach($posts AS $i => $post)
	{
		/* Shall we display any discussion splits before the post? */
		$display_discussions = array();
		foreach($discussions AS $key => $discussion)
		{
			if($discussion['timestamp'] <= $post['timestamp'])
			{
				$display_discussions[] = $discussion;
				unset($discussions[$key]);
			}
		}
		if(count($display_discussions) > 0)
		{
			discussions_list_splits($display_discussions);
		}
		
		if(in_array('private_gb', $post['flags']))
		{
			echo '<h5 class="private_header">Privat inlägg</h5>' . "\n";
			if($_SESSION['login']['id'] != $post['author'] && $_SESSION['login']['id'] != $options['gb_recipient'])
			{
				continue;
			}
		}
		
		echo '<!-- Post #' . $post['post_id'] . ' at ' . date('Y-m-d H:i:s', $post['timestamp']) . '-->' . "\n";
		echo '<a name="post_' . $post['post_id'] . '_anchor"></a>' . "\n";		
	
		if(in_array('removed', $post['flags']))
		{
			echo '<div class="removed_post">' . "\n";
			echo '<h2>Borttaget inlägg, skrevs ' . fix_time($post['timestamp']) . ' av <a href="/traffa/profile.php?id=' . $post['author'] . '">' . 
						$post['username'] . '</a></h2>' . "\n"; 
			echo '</div>' . "\n";
			$displayed_posts[] = $post['post_id'];
			continue;
		}

// todo!
/*	detta skall användas för Skräpklassade inlägg, inlägg flaggade med junk
		$rounded_corners_color = in_array('junk', $post['flags']) ? 'orange' : 
									((login_checklogin() && strpos( $post['content'], $_SESSION['login']['username'])) ? 'orange' : 'blue');

*/
		$rounded_corners_color = (login_checklogin() && strpos( $post['content'], $_SESSION['login']['username'])) ? 'orange' : 'blue';
		echo rounded_corners_top(array('id' => 'post_' . $post['post_id'], 'color' => $rounded_corners_color));
		echo '<div class="post">' . "\n";
		
		
		/* Head section */
		echo '<div class="head">' . "\n";
		echo '	<span class="date_time">Skrevs ' . fix_time($post['timestamp']) . '</span>' . "\n";
		
		echo '	<a href="/traffa/profile.php?id=' . $post['author'] . '">' . $post['username'] . '</a>' . "\n";
		if($post['gender'] == 'm')
		{
			echo ' kille';
		}
		elseif($post['gender'] == 'f')
		{
			echo ' tjej';
			
		}		
		echo (date_get_age($post['birthday']) > 0) ? ' ' . date_get_age($post['birthday']) . ' år' : '';
		echo (strlen($post['spot']) > 0) ? ' från ' . $post['spot']  . "\n": '';
		$onlinestatus = login_onlinestatus($post['lastaction'], $post['lastrealaction']);
		echo ' <span class="user_label_' . $onlinestatus['handle'] . '">' . $onlinestatus['label'] . '</span>' . "\n";
		echo '</div>' . "\n";

		/* Author pane */
		echo '<div class="author_pane">' . "\n";		
		if($post['image'] == 1 || $post['image'] == 2)
		{
			echo user_avatar($post['author']) . "\n";
		}
		echo '&nbsp;';
		echo birthdaycake($post['birthday']);
		echo '</div>' . "\n";
		
		
		echo '<div id="post_content_' . $post['post_id'] . '" class="content">' . "\n";
		$options_markup['post_id'] = $post['post_id'];
		$options_markup['context'] = 'forum';
		if($post['no_smilies'] != 1)
		{
			echo setsmilies(markup_parse($post['content'], $options_markup));
		}
		else
		{
			echo markup_parse($post['content'], $options_markup);
		}
		if(strlen($post['forum_signature']) > 0)
		{
			echo '<div class="signature">' . $post['forum_signature'] . '</div>' . "\n";
		}
		if(strlen($post['user_status']) > 0)
		{
			echo '<p class="user_status">' . $post['user_status'] . '</p>' . "\n";
		}
		echo '</div>' . "\n";
		
		echo '<div class="controls">' . "\n";
		if(login_checklogin())
		{
			
			$control_buttons['answer'] = '<input type="button" class="post_answer_button" id="post_answer_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Svara" />';
			$control_buttons['comment'] = '<input type="button" class="post_comment_button" id="post_comment_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Kommentera" />';
			$control_buttons['quote'] = '<input type="button" class="post_quote_button" id="post_quote_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Citera" />';
			$control_buttons['history'] = '<input type="button" class="post_history_button" id="post_history_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Historik" />';
			$control_buttons['delete'] = '<input type="button" class="post_delete_button" id="post_delete_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Ta bort" />';
			$control_buttons['censor'] = '<input type="button" class="post_censor_button" id="post_censor_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Censurera" />';
			$control_buttons['addition'] = '<input type="button" class="post_addition_button" id="post_addition_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Tillägg" />';
			$control_buttons['link'] = '<input type="button" class="post_link_button" id="post_link_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Direktlänk" />';
			$control_buttons['report'] = '<input type="button" class="post_report_button" value="Rapport" onclick="abuse_report(\'post\', \'' . $post['post_id'] . '\');" />';
			$control_buttons['edit'] = '<input type="button" class="post_edit_button" id="post_edit_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Ändra"/>';
			$control_buttons['junk'] = '<input type="button" class="post_junk_button" id="post_junk_button_' . $post['post_id'] . '_' . $post['username'] . '" value="Skräp!"/>';

			$control_set['forum'] = array('answer', 'comment', 'quote', 'link');
			$control_set['guestbook'] = array('answer', 'quote', 'history');
//			$control_set['game_comment'] = array();
			$control_set['admin'] = array('delete', 'censor', 'addition'); // todo! Aktivera Skräpknappen när färgspecen är klar, 'junk');
//			$control_set['game_admin'] = array('delete');

			foreach($control_set[$options['mode']] as $button)
			{
				echo $control_buttons[$button] . "\n";
			}
			if(posts_admin_check($post))
			{
				if($options['mode'] == 'game_comment')
				{
					$control_set['admin'] = $control_set['game_admin'];
				}
				foreach($control_set['admin'] as $button)
				{
					echo $control_buttons[$button] . "\n";
				}
			}
			if($post['author'] == $_SESSION['login']['id'])
			{
				if(!posts_admin_check($post))
				{
					echo $control_buttons['addition'] . "\n";
				}
				if(time() < $post['timestamp'] + 20 * 60)
				{
					echo $control_buttons['edit'] . "\n";
				}
			}

			if($_SESSION['login']['userlevel'] == 2)
			{
				echo $control_buttons['report'];
			}
			
		}
		echo '</div>' . "\n";
		
		echo '<div class="post_addition" id="post_addition_' . $post['post_id'] . '">' . "\n";
		echo '	<textarea id="post_addition_content_' . $post['post_id'] . '" rows="3" cols="50"  ></textarea>' . "\n";
		echo '	<button class="button_60" id="post_addition_submit_' . $post['post_id'] . '" value="post_addition_' . $post['post_id'] . '">Spara</button>' . "\n";
		echo '</div>' . "\n";
		echo '<div class="post_link" id="post_link_' . $post['post_id'] . '">' . "\n";
		echo '<h5>Länken nedan går direkt till detta inlägg.</h5>' . "\n";
		echo '<input type="text" class="post_link_input" value="http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '#post_' . $post['post_id'] . '_anchor" />' . "\n";
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		
		echo rounded_corners_bottom(array('color' => $rounded_corners_color));

		echo "\n\n";
		
		$displayed_posts[] = $post['post_id'];
	}
	if(login_checklogin())
	{
		/* Remove all notices and answer notices for read posts */
		$query = 'DELETE FROM notices WHERE post_id IN("' . implode('", "', $displayed_posts) . '") AND user_id = "' . $_SESSION['login']['id'] . '"';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
	
	/* List the remaining discussions */
	if(count($discussions) > 0)
	{
		discussions_list_splits($discussions);
	}
}

function posts_list_old($posts, $discussions = null, $options)
{
	$options['enable_quality_rank'] = ($options['enable_quality_rank'] === false) ? false : true;
	$options['quality_level'] = (isset($options['quality_level']) ? $options['quality_level'] : 0);
	
	$options['mode'] = (isset($options['mode'])) ? $options['mode'] : 'forum';
    echo '<input type="hidden" id="debug_input" />';
	foreach($posts AS $i => $post)
	{
		/* Shall we display any discussion splits before the post? */
		$display_discussions = array();
		foreach($discussions AS $key => $discussion)
		{
			if($discussion['timestamp'] <= $post['timestamp'])
			{
				$display_discussions[] = $discussion;
				unset($discussions[$key]);
			}
		}
		if(count($display_discussions) > 0)
		{
			discussions_list_splits($display_discussions);
		}
		
		/* Shall we hide this post, due to low quality? */
		$low_quality =  $post['quality_level'] >= 0 && (($i > 0 && $options['enable_quality_rank'] && $post['quality_rank'] < ($options['quality_level'] - 0.4 )) ? true : false);
		$low_quality = false;
		
		if(in_array('private_gb', $post['flags']))
		{
			echo '<h5 class="private_header">Privat inlägg</h5>' . "\n";
			if($_SESSION['login']['id'] != $post['author'] && $_SESSION['login']['id'] != $options['gb_recipient'])
			{
				continue;
			}
		}
		
		echo '<!-- Post #' . $post['post_id'] . ' by ' . $post['username'] . ' at ' . date('Y-m-d H:i:s', $post['timestamp']) . '-->' . "\n";
		echo '<a name="post_' . $post['post_id'] . '_anchor"></a>' . "\n";		
	
		if(in_array('removed', $post['flags']))
		{
			echo '<div class="removed_post">' . "\n";
			echo '<h2>Borttaget inlägg, skrevs ' . fix_time($post['timestamp']) . ' av <a href="/traffa/profile.php?id=' . $post['author'] . '">' . 
						$post['username'] . '</a></h2>' . "\n"; 
			echo '</div>' . "\n";
			$displayed_posts[] = $post['post_id'];
			continue;
		}

		if($low_quality)
		{
			echo '<div class="low_quality">';
		}
		echo '<div class="post" id="post_' . $post['post_id'] . '"';
		if(login_checklogin() && strpos( $post['content'], $_SESSION['login']['username']))
		{
			echo ' style="background: #f9cc88;"';
		}
		echo '>' . "\n";
		echo '<div class="author">' . "\n";
		if($low_quality)
		{
			echo 'Detta inlägg har lägre kvalitét än vad skaparen av diskussionen vill ha.';
		}
		elseif($post['image'] == 1 || $post['image'] == 2)
		{
			echo user_avatar($post['author']) . "\n";
		}
		
		echo '</div>' . "\n";
		

		echo '<div class="date_time">Skrevs ' . fix_time($post['timestamp']) . '</div>' . "\n"; 
		echo '<div class="author_text"><a href="/traffa/profile.php?id=' . 
					$post['author'] . '">' . $post['username'] . '</a>';
		echo ($post['gender'] == 'm' ? ', pojke' : ($post['gender'] == 'f' ? ', flicka' : '')) . ' ' . date_get_age($post['birthday']);
		if(strlen($post['spot']) > 0)
		{
			echo ' från ' . $post['spot'];
		}
		echo '</div>';
		if($post['userlevel'] >= 3)
		{
			echo '<img class="badge" src="' . IMAGE_URL . 'forum/ordningsvakt.png" alt="" />';
		}
		if($post['lastaction'] > (time() - 600))
		{
			echo '<img class="badge" src="' . IMAGE_URL . 'forum/online.png" alt="" />';
		}

		
		echo '<div id="post_content_' . $post['post_id'] . '" class="' . ($low_quality ? 'low_quality_content' : 'content') . '">' . "\n";
		$options_markup['post_id'] = $post['post_id'];
		$options_markup['context'] = 'forum';
		echo setsmilies(markup_parse($post['content'], $options_markup));
		if(strlen($post['forum_signature']) > 0)
		{
			echo '<div class="signature">' . $post['forum_signature'] . '</div>' . "\n";
		}
		echo '</div>' . "\n";
		echo '<div class="controls">' . "\n";
		if(login_checklogin())
		{
			
			$control_buttons['answer'] = '<input type="button" class="post_answer_button" id="post_answer_button_' . $post['post_id'] . '_' . $post['username'] . '" value="[Svara]" />';
			$control_buttons['quote'] = '<input type="button" class="post_quote_button" id="post_quote_button_' . $post['post_id'] . '_' . $post['username'] . '" value="[Citera]" />';
			$control_buttons['history'] = '<input type="button" class="post_history_button" id="post_history_button_' . $post['post_id'] . '_' . $post['username'] . '" value="[Historik]" />';
			$control_buttons['delete'] = '<input type="button" class="post_delete_button" id="post_delete_button_' . $post['post_id'] . '_' . $post['username'] . '" value="[Ta bort]" />';
			$control_buttons['censor'] = '<input type="button" class="post_censor_button" id="post_censor_button_' . $post['post_id'] . '_' . $post['username'] . '" value="[Censurera]" />';
			$control_buttons['addition'] = '<input type="button" class="post_addition_button" id="post_addition_button_' . $post['post_id'] . '_' . $post['username'] . '" value="[Tillägg]" />';

			$control_set['forum'] = array('answer', 'quote');
			$control_set['guestbook'] = array('answer', 'quote', 'history');
			$control_set['admin'] = array('delete', 'censor', 'addition');
			
			foreach($control_set[$options['mode']] as $button)
			{
				echo $control_buttons[$button] . "\n";
			}
			if(posts_admin_check($post))
			{
				foreach($control_set['admin'] as $button)
				{
					echo $control_buttons[$button] . "\n";
				}
			}
			elseif($post['author'] == $_SESSION['login']['id'])
			{
				echo $control_buttons['addition'];
			}
			
		}
		echo '</div>' . "\n";
		echo '<div class="post_addition" id="post_addition_' . $post['post_id'] . '">' . "\n";
		echo '	<textarea id="post_addition_content_' . $post['post_id'] . '" rows="3" cols="50"  ></textarea>' . "\n";
		echo '	<button class="button_30" id="post_addition_submit_' . $post['post_id'] . '" value="post_addition_' . $post['post_id'] . '">Spara</button>' . "\n";
		echo '</div>';
		echo '</div>' . "\n";
		
		if($low_quality)
		{
			echo '</div>' . "\n";
		}
		echo "\n\n";
		
		$displayed_posts[] = $post['post_id'];
	}
	if(login_checklogin())
	{
		/* Remove all notices and answer notices for read posts */
		$query = 'DELETE FROM notices WHERE post_id IN("' . implode('", "', $displayed_posts) . '") AND user_id = "' . $_SESSION['login']['id'] . '"';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
	
	/* List the remaining discussions */
	if(count($discussions) > 0)
	{
		discussions_list_splits($discussions);
	}
}


function posts_create($post, $options)
{
	/*
		Required info: 
			content
			discussion_id
			author
		
		Optional info:
			timestamp
	
	*/
/*
	$post['content'] = str_replace('(</p><p>)+', '</p><p>', $post['content']);
	$post['content'] = str_replace('(<br />)+', '<br />', $post['content']);

*/
	if(forum_read_only_get($post['author']))
	{
		echo 'Error: Användaren avstängd från forumet' . "\n";
		return false;
	}

	$post['content'] = trim($post['content']);
	$content = mysql_real_escape_string($post['content']);
	
	$quality_rank = text_quality_rank($post['content']);
	
	$spelling_grammar = text_quality_rank($post['content']);
	$post['timestamp'] = (isset($post['timestamp'])) ? $post['timestamp'] : time();
	
	
	$query = 'INSERT INTO posts (author, length, content, discussion_id, quality_rank, spelling_grammar, timestamp, no_smilies)';
	$query .= ' VALUES("' . $post['author'] . '", "' . strlen($post['content']) . '", "' . $post['content'] . '", "' . $post['discussion_id'];
	$query .= '", "' . $quality_rank . '", "' . $spelling_grammar . '", "' . $post['timestamp'] . '", "';
	$query .= (isset($post['no_smilies']) ? '1' : '0') . '")';

	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

	$post_id = mysql_insert_id();
	
	/* Increase the post counter */
	$query = 'UPDATE discussions SET posts = posts + 1, last_post = "' . $post_id  . '" WHERE id = "' . $post['discussion_id'] . '" LIMIT 1';
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

	if(isset($options['gb_recipient']))
	{
		/* Update the "unread entries" in the remote users session */
		$query = 'SELECT session_id FROM login WHERE id = "' . $options['gb_recipient'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			if(strlen($data['session_id']) > 1)
			{
				$remote_session = session_load($data['session_id']);
				$remote_session['notices']['unread_gb_entries'] += 1;
				session_save($sessid_data['session_id'], $remote_session);				
			}
		}
		
		/* If a private entry has been sent, set the appropriate flag */
		if($options['private_gb'] == true)
		{
			$query = 'INSERT INTO flags (object_id, object_type, flag) VALUES("' . $post_id . '", "post", "private_gb")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
	}
		
	//Parse to find answer tags
	foreach(preg_split('/\n/', $content) as $line)
	{
		// find tags like: [svar:AmarsoLove=3245]
		// 					eller: [svar:Henrik]
		if(preg_match('/\[svar:(\w+)(=\d+)?\]/', $line, $matches))
		{
			//Fetch user_id
			if(strtolower($matches[1]) != 'borttagen')
			{
				$query = 'SELECT id FROM login WHERE username = "' . $matches[1] . '"';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					$receiver_id = $data['id'];
					//Insert response notice
					$query = 'INSERT INTO notices (user_id, post_id, type) VALUES ("' . $receiver_id . '", "' . $post_id . '", "response")';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
			}
		}
	}

	//Send notices to all users listed in $options['notices']
	if(isset($options['notices']))
	{
		foreach($options['notices'] as $receiver)
		{
			if(strtolower($matches[1]) != 'borttagen')
			{
				//Fetch user_id
				$query = 'SELECT id FROM login WHERE username = "' . $receiver . '"';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					$receiver_id = $data['id'];
					//Insert response notice
					$query = 'INSERT INTO notices (user_id, post_id, type) VALUES ("' . $receiver_id . '", "' . $post_id . '", "notice")';
					log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'notiser', $query);
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
			}
		}
	}

	//Update users quality rank
	//todo! This will require some thinking and adjustments in the future
	if(login_checklogin())
	{
		$user_quality_rank = ($_SESSION['userinfo']['forum_quality_rank'] * 9 + $quality_rank) / 10;
		unset($data);
	
		$data['userinfo']['forum_quality_rank'] = $user_quality_rank;
		login_save_user_data($_SESSION['login']['id'], $data);
		session_merge($data);
	
		//Update discussion quality rank
		$query = 'SELECT quality_rank FROM posts WHERE discussion_id ="' . $post['discussion_id'] . '" ORDER BY id DESC LIMIT 30';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		while($data = mysql_fetch_assoc($result))
		{
			$quality_ranks[] = $data['quality_rank'];
		}
		sort($quality_ranks);
		$discussion_quality_rank = $quality_ranks[floor(count($quality_ranks)/2)];
		$query = 'UPDATE discussions SET quality_rank="' . $discussion_quality_rank . '" WHERE id = "' . $post['discussion_id'] . '"';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
	//Update discussion popularity
	$slot = floor(date('G')/3);
	$slot_pre = $slot - 1;
	$date_pre = date('Y-m-d');
	if($slot_pre < 0)
	{
		$slot_pre = 7;
		$date_pre = date('Y-m-d', strtotime('yesterday'));
	}
	$query_update = 'UPDATE discussion_statistics SET posts = posts + 1 WHERE discussion_id = "' . $post['discussion_id'] . '" AND date = "' . date('Y-m-d') . '" AND slot = "' . $slot . '"';
	$query_posts_pre = 'SELECT posts FROM discussion_statistics WHERE discussion_id = "' . $post['discussion_id'] . '" AND date = "' . $date_pre . '" AND slot = "' . $slot_pre . '" LIMIT 1';

//	log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, $post['discussion_id'] . ' ' . $slot . ' ' . $slot_pre, $query_posts_pre);


	// Update the popularity for the tags that the discussion has been tagged with.
	$tags = tag_get_by_item('discussion', $post['discussion_id']);
	
	foreach($tags AS $tag)
	{
		$query = 'UPDATE tags SET popularity = IF(popularity IS NULL, 0.05, popularity + 0.05) WHERE id = "' .$tag['tag_id'] . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query));
	}

	$result = mysql_query($query_posts_pre);
	if($data = mysql_fetch_assoc($result))
	{
//		log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, $data['posts'], $query_posts_pre);
		$posts_pre = $data['posts'];
	}
	$query_insert = 'INSERT INTO discussion_statistics (discussion_id, date, slot, posts, posts_pre) VALUES ("' . $post['discussion_id'] . '", "' . date('Y-m-d') . '", "' . $slot . '", "1",' .
									' "' . $posts_pre . '")';
//	log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'insert', $query_insert);
	if(mysql_query($query_insert))
	{
		log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'Vi körde insert!', $query_insert);
	}
	elseif(mysql_query($query_update))
	{
		log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'Vi körde update!', $query_update);
	}
	else
	{
		log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, $query_update, $query_insert);
		exit;
	}
	
	$_SESSION['posts']['latest'][] = array('timestamp' => $time, 'hash' => md5($post['content']));
	
	
	return $post_id;
}

function posts_fetch($options)
{
	/*
		Parameter        Data-type            Array-support   Comment
	X	post_id          int                  yes             -
	X	limit            int                  no              Maximum number of rows to return
	X	offset           int                  no
	X	order            array                yes             May contain multiple keys holding arrays with both field and direction, ex: (field => id direction => desc)
	  flags         	 ENUM-array           yes             array('removed' => 'ok', 'private' => 'force'). Values: exclude, ok, force
	X	freetext_search  text                 no              Search string
	X	discussion_id    int                  yes             fetch posts from one of theese discussions
	X	author           int                  yes             fetch posts written by one of theeese authors
	  time_min         int                  no
	  time_max         int                  no
		id_max          int                  no             -
		id_max          int                  no             -
	*/
		
	if(isset($options['time_min']))
	{
		log_to_file('johan', LOGLEVEL_DEBUG, __FILE__, __LINE__, print_r(debug_backtrace(), true));
		return false;
	}
	
	$options['order'] = is_array($options['order']) ? $options['order'] : array(array('field' => 'post_id', 'direction' => 'asc'));
	$options['limit'] = is_numeric($options['limit']) ? $options['limit'] : POSTS_DEFAULT_LIMIT;
	$options['offset'] = is_numeric($options['offset']) ? $options['offset'] : 0;

	/* Make sure that some entries are hidden by default */
	$default_exclude_flags = array('private_gb');
	foreach($default_exclude_flags AS $flag)
	{
		if(!isset($options['flags'][$flag]))
		{
			$options['flags'][$flag] = 'exclude';
		}
	}

	if(isset($options['post_id']))
	{
		$options['post_id'] = is_array($options['post_id']) ? $options['post_id'] : array($options['post_id']);
	}
	if(isset($options['author']))
	{
		$options['author'] = is_array($options['author']) ? $options['author'] : array($options['author']);
	}
	if(isset($options['tag']))
	{
		$options['tag'] = is_array($options['tag']) ? $options['tag'] : array($options['tag']);
	}
	
		
	$query = 'SELECT p.id AS post_id, p.timestamp, p.author, p.length, p.content, p.discussion_id, p.quality_rank, p.spelling_grammar, p.no_smilies';
	$query .= ', l.username, l.lastaction, l.lastrealaction, l.userlevel, u.image';
	$query .= ', u.gender, u.birthday, u.zip_code, u.forum_signature, u.user_status';
	$query .= ', z.spot, z.x_rt90, z.y_rt90, GROUP_CONCAT(flag) as flags';

	$query .= ' FROM login AS l, userinfo AS u, zip_codes AS z, posts AS p';

	$query .= ' LEFT OUTER JOIN flags ON p.id = flags.object_id';
	$query .= ' WHERE l.id = p.author';
	$query .= ' AND u.userid = p.author';
	$query .= ' AND z.zip_code = u.zip_code';
	foreach($options['flags'] as $flag => $action)
	{
		$query .= ' AND ' . ($action == 'force' ? 'EXISTS' : 'NOT EXISTS') . ' (SELECT * FROM flags AS fs';
		$query .= ' WHERE p.id = fs.object_id';
		$query .= ' AND fs.flag = "' . $flag . '"';
		$query .= ' AND fs.object_type = "post")';
	}

	if(isset($options['discussion_id']) && !is_numeric($options['discussion_id']))
	{
		log_to_file('forum', LOGLEVEL_ERROR, __FILE__, __LINE__, 'post_fetch() called with non numeric discussion_id set', print_r(debug_backtrace(), true));
	}
	if(!isset($options['discussion_id']))
	{
		log_to_file('forum', LOGLEVEL_INFO, __FILE__, __LINE__, 'post_fetch() called without discussion_id', print_r(debug_backtrace(), true));
	}
	$query .= (isset($options['discussion_id'])) ? ' AND p.discussion_id = "' . $options['discussion_id'] . '"' : '';
	$query .= (isset($options['post_id'])) ? ' AND p.id IN("' . implode('", "', $options['post_id']) . '")' : '';
	$query .= (isset($options['author'])) ? ' AND p.author IN("' . implode('", "', $options['author']) . '")' : '';
	$query .= (isset($options['freetext_search'])) ? ' MATCH(p.content) AGAINST("' . $options['freetext_search'] . '")' : '';

	$query .= (isset($options['time_min'])) ? ' AND p.timestamp >= "' . $options['time_min'] . '"' : '';
	$query .= (isset($options['time_max'])) ? ' AND p.timestamp <= "' . $options['time_max'] . '"' : '';

	$query .= (isset($options['id_min'])) ? ' AND p.id >= "' . $options['id_min'] . '"' : '';
	$query .= (isset($options['id_max'])) ? ' AND p.id <= "' . $options['id_max'] . '"' : '';


	$query .= "\n";
		
	$query .= ' GROUP BY post_id';
		
	$query .= ' ORDER BY';
	for($i = 0; $current = array_shift($options['order']); $i++)
	{
		$query .= ($i != 0) ? ',' : '';
		$query .= ' ' . $current['field'] . ' ' . $current['direction'];
	}

	$query .= "\n";

	$query .= ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
	
	$query .= "\n";
	log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'posts_fetch_query', $query);
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

	while($data = mysql_fetch_assoc($result))
	{
		$return[$data['post_id']] = $data;
		$return[$data['post_id']]['flags'] = explode(',', $return[$data['post_id']]['flags']);
	}

/*	$query = 'SELECT object_id, flag FROM flags WHERE object_id IN("' . implode(array_keys($return), '", "') . '") AND object_type = "post"';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

	while($data = mysql_fetch_assoc($result))
	{
		if(strlen($data['flag']) > 0)
		{
			$return[$data['object_id']]['flags'][] = $data['flag'];	
		}	
	}
*/
	return $return;
}

function posts_form($parameters)
{
	if(!login_checklogin())
	{
		echo 'Nu gjorde ett script fel här, men det gör inte så mycket.';
		return false;
	}
	elseif(forum_read_only_get($_SESSION['login']['id']))
	{
		echo '<h5>Du är anvstängt från forumet för tillfället.</h5>' . "\n";
		return false;
	}
	$parameters['mode'] = (in_array($parameters['mode'], array('post_may_split', 'post_no_split', 'discussion_create'))) ? $parameters['mode'] : 'post_may_split';
	$parameters['private_check'] = ($parameters['private_check'] === true) ? true : false;
	$parameters['submit_button'] = (isset($parameters['submit_button'])) ? $parameters['submit_button'] : 'Spara &raquo;';
	$parameters['preview_button'] = isset($parameters['preview_button']) ? $parameters['preview_button'] : 'Förhandsgranska &raquo;';
	$parameters['action_url'] = (isset($parameters['action_url'])) ? $parameters['action_url'] : $_SERVER['PHP_SELF'];
	$parameters['notices_show'] = ($parameters['notices_show'] === false) ? false : true;
	$parameters['anonymous_allowed'] = ($parameters['anonymous_allowed'] === true) ? true : false;

	
	echo '<!-- Post form, sending POST data to ' . $parameters['action_url'] . ' -->' . "\n";
	echo '<a name="post_form"></a>' . "\n";
		
	echo '<div class="post_form">' . "\n";
	echo '<input type="hidden" id="desired_quality_value" value ="' . $parameters['discussion']['desired_quality'] . '" />';
	echo '<input type="hidden" id="quality_rank_value" value ="' . $parameters['discussion']['quality_rank'] . '" />';
	echo '<h1>' . $parameters['headline'] . '</h1>';
	echo '	<form id="post_form" action="' . $parameters['action_url'] . '" method="post">' . "\n";
	if(isset($parameters['extra_html']))
	{
		echo $parameters['extra_html'];
	}
	foreach($parameters['hidden_inputs'] AS $name => $value)
	{
		echo '		<input type="hidden" name="' . $name . '" value="' . $value . '" />' . "\n";
	}

	echo '	<div class="input_main">' . "\n";
	
	if($parameters['mode'] == 'post_may_split')
	{
//		echo '<h5>Tillhör ditt inlägg diskussionen "<em>' . $parameters['discussion_title'] . '</em>"?</h5>' . "\n";
        echo '<select id="discussion_split_select" name="discussion_split_select" >';
		echo '<option value="continue_discussion" id="discussion_radio_continue" selected="selected" >';
		echo 'Mitt inlägg tillhör den här diskussionen. (standard)</option>' . "\n";
		echo '<option value="discussion_split" id="discussion_radio_split" />';
		echo 'Jag vill starta en ny underdiskussion.</option>' . "\n";
		echo '<option value="new_discussion" id="discussion_radio_new" />';
		echo 'Jag vill starta en ny diskussion utan anknytning till den här diskussionen.</option>' . "\n";
		echo '</select>';
	}
	echo '	</div>';

    


	if(in_array($parameters['mode'], array('post_may_split', 'discussion_create')))
	{
		echo '	<!-- New discussion dialog, hidden div -->' . "\n";
		echo '	<div id="new_discussion" ' . ($parameters['mode'] == 'discussion_create' ? 'style="display: block;"' : '') . '>' . "\n";
		echo '		<div class="input_left" >';
		echo '			<h5>Välj en rubrik till din nya diskussion</h5>' . "\n";
		echo '			<input id="post_form_subject" type="text" name="title" />' . "\n";
		echo '		</div>';

		//Category choice
		echo '		<div class="input_right" >';
		echo '			<h5>I vilken kategori vill du lägga din diskussion?</h5>' . "\n";
		
		echo '			<select name="category" >' . "\n";
		global $FORUM_CATEGORIES;
		foreach($FORUM_CATEGORIES AS $main_category)
		{
			echo '				<optgroup label="' . $main_category['label'] . '">' . "\n";
			foreach($main_category['sub_categories'] AS $handle => $sub_category)
			{
				echo '					<option value="' . $sub_category['label'] . '"';
				if($parameters['category_handle'] == $handle)
				{
					echo ' selected="selected"';
				}
				elseif(!isset($parameters['category_handle']) && $handle == 'mellan_himmel_och_jord')
				{
					echo ' selected="selected"';
				}
				echo '>' . $sub_category['label'] . '</option>' . "\n";
			}
			echo '				</optgroup>' . "\n";
		}
		echo '			</select>' . "\n\n";
		echo '		</div>';

//		echo '		<br />';

		//Tags
		echo '<div id="tag_div_button" class="input_left">' . "\n";
		echo '<button class="button_200" onclick="javascript: return posts_show_tag_div();" id="post_form_tag_button">Klicka här för att lägga till nyckelord</button>' . "\n";
		echo '</div>' . "\n";
		
		echo '		<div id="tag_div_normal"  class="input_left" style="display: none;">';
		echo '			<h5>Tagga din diskussion med nyckelord</h5>' . "\n";
		echo '			<textarea name="tags" rows="" cols=""  >' . $parameters['tags'] . '</textarea>' . "\n";
		echo '		</div>';

		//Rules
		/*
			echo '		<div class="input_right">';
			echo '			<h5>Hur seriös är din diskussion?</h5>' . "\n";
			echo '			<select name="desired_quality" >' . "\n";
			echo '				<option value="1">Väldigt seriös</option>' . "\n";
			echo '				<option value="0.5">Ganska seriös</option>' . "\n";
			echo '				<option value="0">Normalseriös</option>' . "\n";
			echo '				<option value="-0.5" selected="selected">Ganska oseriös</option>' . "\n";
			echo '				<option value="-1">Totalt oseriös</option>' . "\n";
			echo '			</select>' . "\n";
			echo '		</div>';
		*/

		echo '		<div class="input_right">';

		echo '		</div>';

		//Tags help
/*		echo '		<div class="input_right">';
		echo '			<p>' . "\n";
		echo '				Taggar är nyckelord som gör det lättare att hitta intressanta diskussioner. Skriv dina taggar med kommatecken mellan ';
		echo '				varje ord, vill du diskutera nya 911:an kan du exempelvis skriva <br /><em>porsche, bilar, sportbilar, hästkrafter</em>' . "\n";
		echo '			</p>' . "\n";
		echo '		</div>';
		echo '		</div>';
*/
		echo '		<br />';

		echo '	</div>';
	}

	echo '<div id="post_content_header" class="input_left">' . "\n";
	echo ($parameters['mode'] == 'discussion_create') ? '	<h5>Skriv diskussionens första inlägg här</h5>' . "\n" : '<h5>Skriv ditt inlägg här</h5>' . "\n";
	//Discussion watch
	echo '</div>';
	echo '<div id="post_checkboxes" class="input_right">' . "\n";
	$watched = false;
	if(login_checklogin())
	{
		$query = 'SELECT * FROM discussion_watches WHERE user_id = ' . $_SESSION['login']['id'] . ' AND discussion_id = ' . $parameters['discussion']['id'];
		$result = mysql_query($query);
		if(mysql_fetch_assoc($result))
		{
			$watched = true;
		}
	}

	echo '<input id="forum_discussion_watch_form" name="discussion_watch" type="checkbox" ' . ( $watched ? 'checked="checked"' : '') . ' />' . "\n";
	echo '<label for="forum_discussion_watch_form">Bevaka den här diskussionen</label>' . "\n";
	echo '<input id="forum_post_no_smilies" name="no_smilies" type="checkbox" />' . "\n";
	echo '<label for="forum_post_no_smilies">Stäng av smilies</label>' . "\n";
	echo '</div>' . "\n";
	echo '<br />' . "\n";

	echo '<div class="input_module">' . "\n";
	echo '<div class="content_control">';
/*
	echo '<select id="content_control_select">' . "\n";
	echo '<option value="smilies">Smilies</option>' . "\n";
	echo '<option value="markup">Formattering</option>' . "\n";
	echo '<option value="notices">Skicka notiser</option>' . "\n";
	echo '</select>' . "\n";
*/	
/*
	echo '<div class="control_item" id="control_item_smilies">' . "\n";
	echo listSmilies('document.getElementById(\'post_form_content\')');
	echo '</div>' . "\n";
*/

	echo '<h4>Notiser</h4>';
	echo '<div class="control_item" id="control_item_notices">' . "\n";
	for($i = 1; $i <= 5; $i++)
	{
		echo '<h5>Användarnamn #' . $i . '</h5>' . "\n";
		echo '<input type="text" name="notice_' . $i . '" />' . "\n";
	}
	echo '</div>' . "\n";

	echo '<h4>Bilder</h4>';
	echo '<div class="control_item" id="control_item_markup">' . "\n";
	echo '!http://sajt.se/bild.jpg!<br />';
	echo '<img src="http://images.hamsterpaj.net/forum_image_example.png" alt="" />';

	echo '</div>' . "\n";
	echo '</div>' . "\n";
	echo '</div>';



	echo '<div class="input_main">' . "\n";
	echo '<textarea name="content" id="post_form_content" rows="" cols="" >' . "\n";
	echo $parameters['content'];
	echo '</textarea>' . "\n";
	echo '</div>';
	if($parameters['discussion']['desired_quality'] >= 0.5 && isset($parameters['discussion']['desired_quality']))
	{
		echo '<div class="input_main" id="quality_warning"><h5>Tänk på att skaparen av den här diskussionen vill ha en seriös diskussion -' .
				' inga tramsinlägg eller chattande. Ta tid på dig att formulera dig och granska' .
				' gärna ditt inlägg innan du sparar.</h5></div>';
	}
	
	echo '	<div class="input_main">' . "\n";
	echo '<div class="smilies">' . "\n";
	echo listSmilies();
	echo '</div>' . "\n";
	echo '		<button class="button_150" id="preview_button" ' . /*($parameters['mode'] == 'post_may_split' ? 'disabled="disabled"' : '') . */
					' value="' . $parameters['preview_button'] . '" >' . $parameters['preview_button'] . '</button>' . "\n";
	if($parameters['private_check'])
	{
		echo '		<input type="checkbox" name="private" value="true" id="gb_private_check" class="checkbox"/>' . "\n";
		echo '		<label for="gb_private_check">Gör inlägget privat</label>' . "\n";
	}
	echo '	</div>';
 
	//Preview
	//This div will only be shown after click on preview button
	echo '	<div id="preview">';
	echo '	</div>';
	echo '	<div id="submit_button_div" class="input_main">';
	echo '		<input id="submit_button" type="submit" value="' . $parameters['submit_button'] . '" class="button_60" />' . "\n";
	echo '	</div>';
	echo '	<br style="clear: both;" />' . "\n";
	
	echo '	</form>' . "\n";
	echo '</div>' . "\n\n";
// preint_r($parameters);
}

function posts_page_list($posts, $url, $page, $options)
{
	if(!isset($page))
	{
		$page = 1;
	}
	$pages = ceil($posts / POSTS_PER_PAGE);
	echo '<ol class="posts_page_list">' . "\n";
	if($page > 1)
	{
		echo '<li><a href="' . $url . 'sida_' . ($page - 1) . '.html">Föregående sida</a></li>' . "\n";
	}
	if($page - 2 > 1)
	{
		echo ' ... ';
	}
	for($i = max($page - 2, 1); $i <= $pages && $i <= $page + 2; $i++)
	{
		echo '<li>';
		if($i == $page)
		{
			echo '<strong>[ ' . $i . ' ]</strong>';
		}
		else
		{
			if(isset($options['get_mode']))
			{
				echo '<a href="' . $url . '&sida=' . $i . '">' . $i . '</a>';
			}
			else
			{
				echo '<a href="' . $url . 'sida_' . $i . '.html">' . $i . '</a>';
			}
		}
	 	echo '</li>' . "\n";
	}
	if($page + 2 < $pages)
	{
		echo ' ... ';
	}
	if($page < $pages)
	{
		echo '<li><a href="' . $url . 'sida_' . ($page + 1) . '.html">Nästa sida</a></li>' . "\n";
	}
	echo '</ol>' . "\n";
}



function posts_set_property()
{

}

function posts_flood_check()
{
	$flood[4] = 1;
	$flood[600] = 20;
	$flood[86400] = 600;
	
	foreach($flood AS $interval => $limit)
	{
		$count = 0;
		foreach($_SESSION['posts']['latest'] AS $post)
		{
			if($post['timestamp'] <= time() - $interval)
			{
				$count++;
			}	
		}
		if($count > $interval)
		{
			$return['status'] = 'fail';
			$return['message'] = 'Du får inte skriva fler än ' . $limit. ' inlägg på ' . $interval . ' sekunder!';
			return $return;
		}
	}
	
	foreach($_SESSION['posts']['latest'] AS $post)
	{
		if($post['hash'] == $last_hash)
		{
			$return['status'] = 'fail';
			$return['message'] = 'Du får inte skriva samma inlägg fler gånger i rad!';
			return $return;
		}
		$last_hash = $post['hash'];
	}
	
		$return['status'] = 'success';
		return $return;	
}

function posts_url_get($post_id)
{
	$query = 'SELECT p1.*, (count(p2.discussion_id) - 1) as number, d.handle as discussion_handle, t.handle as category_handle, d.category_tag as category_tag_id';
	$query .= ' FROM posts as p1, posts as p2, discussions as d, tags as t';
	$query .= ' WHERE p1.id = ' . $post_id . ' AND p2.discussion_id = p1.discussion_id AND p2.id <= ' . $post_id . ' AND d.id = p1.discussion_id AND d.category_tag = t.id'; 
	$query .= ' GROUP BY p2.discussion_id';
	$result = mysql_query($query) or die(report_sql_error($query));
	if($data = mysql_fetch_assoc($result))
	{
		$page = ceil($data['number'] / POSTS_PER_PAGE);
		$main_category = forum_get_parent_category($data['category_handle']);
		return '/forum/' . $main_category . '/' . $data['category_handle'] . '/' . $data['discussion_handle'] . '/sida_' . $page . '.html#post_' . $post_id . '_anchor';
	}
	else
	{
		return '/forum/';
	}
}
?>
