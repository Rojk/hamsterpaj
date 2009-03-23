<?php
	/* OPEN_SOURCE */

	function discussion_forum_post_fetch($options)
	{
		if($options['threads_only'] == true && !isset($options['order-by']) && !isset($options['order-direction']))
		{
			$options['order-by'] = 'p.last_post';
			$options['order-direction'] = 'DESC';
		}
		
		if(isset($options['match']['in_columns']) && !is_array($options['match']['in_columns']))
		{
			$options['match']['in_columns'] = array($options['match']['in_columns']);
		}

		$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 30;
		$options['offset'] = (isset($options['offset']) && is_numeric($options['offset']) && $options['offset'] >= 0) ? $options['offset'] : 0;
		$options['offset'] = (isset($options['page_offset']) && is_numeric($options['page_offset']) && $options['page_offset'] >= 0) ? $options['page_offset'] * $options['limit'] : $options['offset'];
		$options['mode'] = ($options['mode'] == 'index') ? 'index' : 'thread';
		$options['order-by'] = (isset($options['order-by'])) ? $options['order-by'] : 'p.id';
		$options['order-direction'] = (isset($options['order-direction'])) ? $options['order-direction'] : 'ASC';
		
		$query = 'SELECT p.*, l.username, l.lastaction, l.userlevel, l.regtimestamp, u.last_warning, u.gender, u.user_status, u.forum_userlabel, u.forum_posts AS author_post_count, u.forum_spam AS author_spam_count, u.birthday, u.image AS author_image, z.spot';
		$query .= (!isset($options['disable_forum_lookup'])) ? ', pf.quality_level' : '';
		if(login_checklogin() && !isset($options['thread_id']))
		{
			$query .= ', rp.posts AS read_posts, rp.has_voted, child_count - rp.posts AS unread_posts';
		}
		$query .= ' FROM forum_posts AS p ';
		$query .= (login_checklogin() && !isset($options['thread_id'])) ? ' LEFT OUTER JOIN forum_read_posts AS rp ON p.id = rp.thread_id AND rp.user_id = "' . $_SESSION['login']['id'] . '"' : '';
		$query .= ', login AS l, userinfo AS u, zip_codes AS z';
		$query .= (!isset($options['disable_forum_lookup'])) ? ', public_forums AS pf' : '';
		
		$query .= ' WHERE';
		$query .= ' l.id = p.author AND u.userid = l.id AND z.zip_code = u.zip_code';
		$query .= (!isset($options['disable_forum_lookup'])) ? ' AND pf.id = p.forum_id' : '';
		$query .= (isset($options['post_id']) && is_numeric($options['post_id'])) ? ' AND p.id = "' . $options['post_id'] . '"' : '';
		$query .= ($options['thread_id'] > 0) ? ' AND ((p.id = "' . $options['thread_id'] . '" AND p.child_count > 0) OR p.parent_post = "' . $options['thread_id'] . '")' : '';
		$query .= ($options['threads_only'] == true) ? ' AND p.child_count > 0' : '';
		$query .= ($options['threads_only'] == true) ? ' AND p.removed = 0' : '';
		$query .= (isset($options['min_quality_level'])) ? ' AND pf.quality_level >= ' . $options['min_quality_level'] : '';
		$query .= (isset($options['max_quality_level'])) ? ' AND pf.quality_level <= ' . $options['max_quality_level'] : '';
		$query .= (strlen($options['thread_handle']) >= 1) ? ' AND p.handle LIKE "' . str_replace('_', '\\_', $options['thread_handle']) . '"' : '';
		$query .= (isset($options['forum_id'])) ? ' AND p.forum_id = "' . $options['forum_id'] . '"' : '';
		$query .= ($options['only_subscriptions'] == true) ? ' AND rp.subscribing = "true"' : '';
		$query .= ($options['force_unread_posts'] == true) ? ' AND rp.posts < p.child_count' : '';
		$query .= (isset($options['author']) && is_numeric($options['author'])) ? ' AND p.author = "' . $options['author'] . '"' : '';
		$query .= (isset($options['min_userlevel_read']) && is_numeric($options['min_userlevel_read'])) ? ' AND pf.userlevel_read >= "' . $options['min_userlevel_read'] . '"' : '';
		$query .= (isset($options['max_userlevel_read']) && is_numeric($options['max_userlevel_read'])) ? ' AND pf.userlevel_read <= "' . $options['max_userlevel_read'] . '"' : '';
		$query .= (isset($options['match']['against'], $options['match']['in_columns'])) ? ' AND MATCH(' . implode(', ', $options['match']['in_columns']) . ') AGAINST("' . $options['match']['against'] . '")' : '';
		
		$query .= ' ORDER BY';
		$query .= isset($options['order_by_sticky']) ? ' sticky DESC,' : '';
		$query .= ' ' . $options['order-by'] . ' ' . $options['order-direction'];

		$query .= ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		while($data = mysql_fetch_assoc($result))
		{
			if($options['url_lookup'] == true)
			{
				$category_path = discussion_forum_path_to_category(array('id' => $data['forum_id']));
				$category = array_pop($category_path);
				$data['title'] = (strlen(trim($data['title'])) == 0) ? 'Rubrik saknas' : $data['title'];
				$data['url'] = $category['url'] . $data['handle'] . '/sida_1.php#post_' . $data['id'];
				$data['last_post_url'] = $category['url'] . $data['handle'] . '/sida_' . (floor($data['child_count'] / FORUM_POSTS_PER_PAGE)+1) . '.php#post_' . $data['last_post'];
				$data['category_url'] = $category['url'];
				$data['category_title'] = $category['title'];
			}
			
			$posts[] = $data;
		}


		return $posts;
	}
	
	function discussion_forum_post_handle($title)
	{
		$secured_string = url_secure_string($title);
		$handle = $secured_string;
		$query = 'SELECT id FROM forum_posts WHERE handle LIKE "' . $handle . '" LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		for($i = 2; mysql_num_rows($result) > 0; $i++)
		{
			$handle = $secured_string . '_' . $i;
			$query = 'SELECT id FROM forum_posts WHERE handle LIKE "' . $handle . '" LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);			
		}
		return $handle;
	}
	
	function discussion_forum_category_handle($title)
	{
		$secured_string = url_secure_string($title);
		$handle = $secured_string;
		$query = 'SELECT id FROM public_forums WHERE handle LIKE "' . $handle . '" LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		for($i = 2; mysql_num_rows($result) > 0; $i++)
		{
			$handle = $secured_string . '_' . $i;
			$query = 'SELECT id FROM public_forums WHERE handle LIKE "' . $handle . '" LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);			
		}
		return $handle;
	}
	
	function discussion_forum_post_create($post, $options)
	{
		$post['author'] = (isset($post['author'])) ? $post['author'] : $_SESSION['login']['id'];
		if($post['author'] < 1)
		{
			die('Fatal error at line #' . __LINE__ . ', no author set');
		}
		if($post['mode'] == 'new_thread' && strlen($post['title']) == 0)
		{
			die('Fatal error at line #' . __LINE__ . ', no title set');
		}
		if(strlen($post['content']) <= 3) {
			die('Mer än så där får du allt skriva!');
		}
		if(content_check($post['content']) != 1)
		{
			die(content_check($post['content']));
		}
		
		$post['timestamp'] = (isset($post['timestamp'])) ? $post['timestamp'] : time();
		$post['handle'] = (isset($post['title'])) ? discussion_forum_post_handle($post['title']) : '';
		$post['forum_type'] = (isset($post['forum_type'])) ? $post['forum_type'] : 'public_forum';
		
		$post['child_count'] = ($post['mode'] == 'new_thread') ? 1 : 0;
		$post['anonymous'] = ($post['anonymous'] == 1) ? 1 : 0;

		$post['fp_module_id'] = (isset($post['fp_module_id'])) ? $post['fp_module_id'] : 0;
		
		$query = 'INSERT INTO forum_posts (handle, author, timestamp, parent_post, forum_id, forum_type';
		$query .= ', title, content, child_count, anonymous, fp_module_id)';
		$query .= ' VALUES("' . $post['handle'] . '", "' . $post['author'] . '", "' . $post['timestamp'] . '"';
		$query .= ', "' . $post['parent_post'] . '", "' . $post['forum_id'] . '", "' . $post['forum_type'] . '"';
		$query .= ', "' . $post['title'] . '", "' . $post['content'] . '", "' . $post['child_count'] . '"';
		$query .= ', "' . $post['anonymous'] . '", "' . $post['fp_module_id'] . '")';
		
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		$post_id = mysql_insert_id();
		
		if($post['parent_post'] > 0)
		{
			$query = 'UPDATE forum_posts SET child_count = child_count + 1, last_post = "' . $post_id . '", last_post_timestamp = "' . time() . '" WHERE id = "' . $post['parent_post'] . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
		}
		if($post['mode'] == 'new_thread')
		{
			$query = 'UPDATE public_forums SET thread_count = thread_count + 1, post_count = post_count + 1, last_thread = "' . $post_id . '" WHERE id = "' . $post['forum_id'] . '"';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);			

			$query = 'UPDATE forum_posts SET last_post = "' . $post_id . '", parent_post = "' . $post_id . '", last_post_timestamp = "' . time() . '" WHERE id = "' . $post_id . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);			
		}
		else
		{
			$query = 'UPDATE public_forums SET post_count = post_count + 1, last_post = "' . $post_id . '" WHERE id = "' . $post['forum_id'] . '"';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}
		
		discussion_forum_parse_input(array('text' => $post['content'], 'post_id' => $post_id, 'author' => $post['author'], 'title' => $post['title']));
		
		if($post['mode'] == 'new_thread')
		{
			forum_thread_cache_latest_threads();
		}
		else
		{
			forum_latest_posts_cache();
		}
		if($post['mode'] == 'new_thread' && $post['forum_id'] == 82)
		{
			forum_thread_cache_latest_open_source_threads();
		}

		

		$query = 'SELECT quality_level FROM public_forums WHERE id = "' . $post['forum_id'] . '" LIMIT 1';
		$data = query_cache(array('category' => 'forum_categories', 'query' => $query, 'max_delay' => 3600));
		if($data[0]['quality_level'] == 1)
		{
			$query = 'UPDATE userinfo SET forum_spam = forum_spam + 1 WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}
		else
		{
			$query = 'UPDATE userinfo SET forum_posts = forum_posts + 1 WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}
		
		$_SESSION['forum']['last_post_timestamp'] = time();
		
		return $post_id;
	}
	
	function discussion_forum_category_create($options)
	{
		$category['handle'] = (isset($options['title'])) ? discussion_forum_category_handle($options['title']) : '';
		$category['parent'] = (isset($options['parent'])) ? $options['parent'] : '0';
		$category['quality_level'] = (isset($options['quality_level'])) ? $options['quality_level'] : '0';
		
		$query = 'INSERT INTO public_forums (handle, parent';
		$query .= ', title, description, quality_level)';
		$query .= ' VALUES("' . $category['handle'] . '", "' . $category['parent'] . '", "' . $options['title'] . '"';
		$query .= ', "' . $options['description'] . '", "' . $category['quality_level'] . '")';
		
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		return mysql_insert_id();
	}

	function discussion_forum_post_render($data, $parent_post = array(), $options = array())
	{
		$options['post'] = $data;
		/*
			[id] => 2 [handle] => normal_vikt [author] => 3 [timestamp] => 1200341971 [parent_post] => 0 [forum_id] => 0
			[forum_type] => public_forum [title] => Normal vikt? [content] => Denna post har skapats av en funktion! [score] => 0 
			[verified] => 0 [removed] => 0 [removal_comment] => [remover] => 0 [child_count] => 1 [locked] => 0 [sticky] => 0
			[last_post] => 0 [anonymous] => 0 [username] => Johan [birthday] => 1988-09-10 [gender] => m [spot] => GÃƒÆ’Ã‚Â¶teborg

		*/

		$warned = 0;
		
		$options['show_post_controls'] = isset($options['show_post_controls']) ? $options['show_post_controls'] : true;
		$options['show_removed_posts_content'] = isset($options['show_removed_posts_content']) ? $options['show_removed_posts_content'] : false;

		
		$return = '<a name="post_' . $data['id'] . '"></a>' . "\n";
		$return .= '<input type="hidden" id="post_' . $data['id'] . '_author_username" value="' . $data['username'] . '" />' . "\n";
		$return .= '<div class="forum_post_container">' . "\n";
		if($data['removed'] == 1 && $options['show_removed_posts_content'] != true)
		{
			$return .= '<div class="forum_post_removed">Inlägget har tagits bort' . "\n";
			$return .= ($options['show_post_controls'] == true && forum_security(array('action' => 'unremove_post'))) ? '<button value="' . $data['id'] . '" class="forum_unremove_button">trob At</button>, Borttaget av <a href="/traffa/profile.php?id=' . $data['remover'] . '">' . $data['remover'] . '</a>.' . "\n" : '';
			$return .= '</div>';
		} 
		else 
		{	
			$return .= (strpos(strtolower($data['content']), strtolower($_SESSION['login']['username']))) ? '<div class="highlight">' . "\n" : '';
			$return .= '<div class="forum_post_top"></div>' . "\n";
			$return .= '<div class="forum_post" id="forum_post_' . $data['id'] . '">' . "\n";
			$return .= '<div class="author">' . "\n";
			if (strtolower($data['username']) == 'vit_seger')
			{
				$data['username'] = 'Vit_Neger';
			}
			$return .= '<a class="username" href="/traffa/profile.php?id=' . $data['author'] . '">' . $data['username'];
			$return .= '</a>' . "\n";
			$return .= ($data['author'] == 2) ? '&nbsp;<a href="/diskussionsforum/hamsterpaj/veckans_ros/" title="Veckans ros"><img src="http://images.hamsterpaj.net/ros.png" alt="Ros" style="width: 11px; height: 17px;border:0;" /></a><br style="clear: both;" />' . "\n" : '';
			$return .= '<div class="passepartout">' . "\n";
			$return .= ui_avatar($data['author']);
			$return .= '</div>' . "\n";
			$return .= '<span class="gender">' .  str_replace(array('m', 'f', 'u'), array('P', 'F', ''), $data['gender'] ) . '</span>' . "\n";
			if($data['birthday'] != '0000-00-00')
			{
				$return .= '<span class="age">' . date_get_age($data['birthday']) . '</span>' . "\n";
			}
			if(strlen($data['spot']) > 0)
			{
				$return .= ' <span class="location">' . $data['spot'] . '</span>' . "\n";				
			}

			
			if($data['last_warning'] > time() - 604800)
			{
				$return .= '<span class="warned">Varnad '/* . fix_time($data['last_warning']) */. '</span>' . "\n";	
			}
			elseif(strlen($data['forum_userlabel']) > 0)
			{
				$return .= '<span class="userlevel">' . $data['forum_userlabel'] . '</span>' . "\n";
			}
			/* The "Saknar liv"-status is now gone, due to posthunting and "spamania".
			elseif($data['author_spam_count'] > 999)
			{
				$return .= '<span class="userlevel">Saknar liv</span>' . "\n";
			}
			*/
			elseif($data['regtimestamp'] < time() - 86400 * 356 * 2.5)
			{
				$return .= '<span class="userlevel">Veteran</span>' . "\n";
			}
			elseif($data['regtimestamp'] < time() - 86400 * 356)
			{
				$return .= '<span class="userlevel">Stammis</span>' . "\n";				
			}
			elseif($data['regtimestamp'] > time() - 86400 * 7)
			{
				$return .= '<span class="userlevel">Nykomling</span>' . "\n";				
			}

			if($data['quality_level'] == 1)
			{
				$return .= '<span class="post_count">' . cute_number($data['author_spam_count']) . ' spam</span>' . "\n";				
			}
			else
			{
				if($data['author'] == 15)
				{
					$author_post_count = 'Några osöta';
				}
				elseif($data['author'] == 87926)
				{
					$author_post_count = 'Många söta';
				}
				elseif($data['author'] == 774586)
				{
					$author_post_count = 'Inte många';
				}
				elseif($data['author'] == 787082)
				{
					$author_post_count = 'Många schmarta';
				}
				elseif($data['author'] == 891711)
				{
					$author_post_count = '';
				}
				elseif($data['author'] == 299825)
				{
					$author_post_count = '666 hatiska inlägg';
				}
				else
				{
			 		$author_post_count = cute_number($data['author_post_count']);
				}
				$return .= '<span class="post_count">' . $author_post_count . ' inlägg</span>' . "\n";
			}
			if($data['lastaction'] > time() - 600)
			{
				$return .= '<span class="online_status">Online</span>' . "\n";				
			}
			$return .= ui_birthday_cake($data['birthday']) . "\n";
			$return .= '</div>' . "\n";

			$return .= '<div class="post_info">' . "\n";
			$return .= '<span class="post_timestamp">' . fix_time($data['timestamp']) . '</span>' . "\n";			
			$return .= ($data['parent_post'] == 0) ? '<h3>' . $data['title'] . '</h3>' : '';
			$return .= '</div>' . "\n";

			$return .= '<div class="post_content">' . "\n";
			
			$return .= ($data['removed'] == 1) ? '<strong>Inlägget är borttaget!</strong><br />' . "\n": '';
			$return .= discussion_forum_parse_output($data['content'], $options);

			if(strlen($data['user_status']) > 0)
			{
				$return .= '<p class="user_status">' . $data['user_status'] . '</p>' . "\n";
			}

			$return .= '</div>' . "\n";

			$return .= '<div class="controls">' . "\n";
			
			if($options['show_post_controls'] == true)
			{
				$return .= '<input type="text" class="forum_direct_link_input" id="forum_direct_link_input_' . $data['id'] . '" />';
				$return .= '<button id="forum_direct_link_button_' . $data['id'] . '" class="forum_direct_link_button">Länk</button>' . "\n";
				$return .= (forum_security(array('action' => 'remove_post'))) ? '<button value="' . $data['id'] . '" class="forum_remove_button">Ta bort</button>' . "\n" : '';
				$return .= (forum_security(array('action' => 'edit_post', 'post' => $data)) || forum_security(array('action' => 'post_addition', 'post' => $data))) ? '<button id="forum_edit_button_' . $data['id'] . '" class="forum_edit_button">Ändra</button>' . "\n" : '';
				$return .= (forum_security()) ? '<button id="post_reply_' . $data['id'] . '" class="forum_reply_button">Citera</button>' . "\n" : '';
				$return .= (forum_security(array('action' => 'reply', 'post' => $parent_post)) === true) ? '<button id="post_reply_' . $data['id'] . '" class="forum_reply_button">Svara</button>' . "\n" : '';
				$return .= (login_checklogin()) ? '<button id="post_comment_' . $data['author'] . '" class="forum_comment_button" value="' . $data['id'] . '">Gästbok</button>' . "\n" : '';
				if(forum_security())
				{
					$return .= '<input type="checkbox" class="post_move_check" name="post[]" value="' . $data['id'] . '" id="forum_move_check_' . $data['id'] . '" />';
					$return .= '<label for="forum_move_check_' . $data['id'] . '">Flytta</label>' . "\n";
				}
				$return .= (forum_security(array('action' => 'user_ro'))) ? '<button value="' . $data['username'] . '" class="forum_user_ro">QL</button>' . "\n" : '';
				$return .= (forum_security(array('action' => 'report'))) ? '<a href="/hamsterpaj/abuse.php?report_type=forum_post&reference_id=' . $data['id'] . '" class="abuse_button"><img src="http://images.hamsterpaj.net/abuse.png" /></a>' . "\n" : '';
				$return .= guestbook_form(array('recipient' => $data['author'], 'form_id' => 'forum_comment_' . $data['id']));
			}
			else
			{
				$return .= '<small>Knappar har inaktiverats</small>';
			}
			
			$return .= '</div>' . "\n";
			$return .= '</div>' . "\n";
			$return .= '<div class="forum_post_bottom"> </div>' . "\n";
			$return .= (strpos(strtolower($data['content']), strtolower($_SESSION['login']['username']))) ? '</div>' . "\n" : '';
		}
		
		$return .= '</div>' . "\n";
		return $return;
	}

	function discussion_forum_remove_post($options)
	{
		$removal_comment = isset($options['removal_comment']) ? $options['removal_comment'] : '';
		$new_value = ($options['mode'] == 'unremove') ? 0 : 1;
		$remover = isset($options['remover']) ? $options['remover'] : (isset($_SESSION['login']['id']) ? $_SESSION['login']['id'] : 0);
		$query = 'UPDATE forum_posts SET';
		$query .= ' removed = ' . $new_value;
		$query .= ', remover = ' . $remover;
		$query .= ', removal_comment = "' . $removal_comment . '"';
		//$query .= (strlen($removal_comment) > 0) ? ', removal_comment = "' . $removal_comment . '"' : '';
		$query .= ' WHERE id = "' . $options['post_id'] . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		$query = 'SELECT fp.author, u.forum_posts FROM forum_posts AS fp, userinfo AS u WHERE fp.id = "' . $options['post_id'] . '" AND u.userid = fp.author LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$data = mysql_fetch_assoc($result);
	
		if($data['forum_posts'] > 5)
		{
			$new_posts = ($options['mode'] == 'unremove') ? $data['forum_posts'] + 5 : $data['forum_posts'] - 5;
		}

		$query = 'UPDATE userinfo SET forum_posts = "' . $new_posts . '" WHERE userid = "' . $data['author'] . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));	
	}
	
	function forum_security($options)
	{
		switch($options['action'])
		{
			case 'move_thread':
				if(login_checklogin())
				{
					if(is_privilegied('discussion_forum_move_thread'))// || $_SESSION['login']['id'] == $options['thread']['author'])
					{
						return true;
					}
				}
				break;
			
			case 'thread_sticky':
				return is_privilegied('discussion_forum_sticky_threads');
			break;
				
			case 'thread_lock':
				return is_privilegied('discussion_forum_lock_threads');
			break;
				
			case 'thread_rename':
				return is_privilegied('discussion_forum_rename_threads');
			break;
				
			case 'remove_post':
			case 'unremove_post':
				return is_privilegied('discussion_forum_remove_posts');
			break;
			
			case 'user_ro':
				return is_privilegied('user_management_admin');				
			break;
			
			case 'report':
				return login_checklogin();
			break;
			
			case 'edit_post':
				if(is_privilegied('discussion_forum_edit_posts'))
				{
					return true;
				}
				if($options['post']['timestamp'] > time() - 600 && $options['post']['author'] == $_SESSION['login']['id'])
				{
					return true;
				}
			break;
			
			case 'post_addition':
				if(is_privilegied('discussion_forum_post_addition') || $options['post']['author'] == $_SESSION['login']['id'])
				{
					return true;
				}
				break;
			case 'reply':
				if(login_checklogin())
				{
					if(isset($options['post']) && !empty($options['post']) && $options['post']['locked'] == 1)
					{
						$return .= '<h2>Diskussionen är låst!</h2>' . "\n";
						$return .= '<p>Av en eller annan anledning har någon utav våra ordningsvakter låst diskussionen. Att diskussioner blir låsta beror oftast på att de har spårat ur, eller om de går för mycket ifrån ämnet.</p>' . "\n";
						return $return;
					}
					return true;
				}
				else
				{
					$return .= '<h2>Inte inloggad</h2>' . "\n";
					$return .= '<p>Du kan inte skriva i forumet om du inte är inloggad. Att <a href="/register.php">bli medlem</a> är både gratis och vi frågar inte några jobbiga frågor om varken din epost eller ditt personnummer.</p>' . "\n";
					return $return;
				}
				break;
			case 'discussion_create':
				$category_tree = discussion_forum_categories_fetch(array('id' => $options['forum_id']));

				if(count($category_tree) < 1)
				{
					$return .= '<h2>Hittade inte forumet</h2>' . "\n";
					$return .= '<p>Vi kunde inte starta en diskussion i det forumet, för det finns inget forum med id #' . $options['forum_id'] . '.</p>' . "\n";
					$return .= '<pre>' . $options['content'] . '</pre>' . "\n";	
					return $return;
					break;
				}
				
				$category = array_pop($category_tree);
				
				if($category['userlevel_create'] > $options['userlevel'] && !is_privilegied('ov_forum'))
				{
					$return .= '<h2>Aja baja, så får man inte göra!</h2>' . "\n";
					$return .= '<p>Nu försökte du starta en diskussion i en kategori du inte får posta i. Kanske var det någon som lekte hacker?</p>' . "\n";
					$return .= '<pre>' . $options['content'] . '</pre>' . "\n";	
					return $return;
					break;
				}
				
				if($category['quality_level'] > $_SESSION['login']['quality_level'] && $_SESSION['login']['quality_level_expire'] > time())
				{
					$return .= '<h2>Här får du inte skriva</h2>' . "\n";
					$return .= '<p>Tjockis, du är ju QL:ad! Sluta hacka, fetknåpp.</p>' . "\n";
					return $return;
					break;
				}
				
				return true;
				break;
				
			case 'new_post':
				$first_post = array_shift(discussion_forum_post_fetch(array('post_id' => $options['parent_post'], 'limit' => 1, 'disable_forum_lookup' => true)));

				if($first_post == NULL)
				{
					$return .= '<h2>Hittade inte diskussionen</h2>' . "\n";
					$return .= '<p>Vi hittade inte diskussionen i vårat system, kanske är den borttagen? Här är ditt inlägg:</p>' . "\n";
					$return .= '<pre>' . $options['content'] . '</pre>' . "\n";			
					return $return;
					break;
				}
				
				if($first_post['locked'] == 1)
				{
					$return .= '<h2>Diskussionen är låst!</h2>' . "\n";
					$return .= '<p>Av en eller annan anledning har någon utav våra ordningsvakter låst diskussionen. Att diskussioner blir låsta beror oftast på att de har spårat ur, eller om de går för mycket ifrån ämnet. Här är ditt inlägg:</p>' . "\n";
					$return .= '<pre>' . $options['content'] . '</pre>' . "\n";			
					return $return;
					break;
				}
			
				$category_tree = discussion_forum_categories_fetch(array('id' => $options['forum_id']));

				if(count($category_tree) < 1)
				{
					$return .= '<h2>Hittade inte forumet</h2>' . "\n";
					$return .= '<p>Vi hittade inte forumet du försökte posta inlägget i. Här är ditt inlägg:</p>' . "\n";
					$return .= '<pre>' . $options['content'] . '</pre>' . "\n";			
					return $return;
					break;
				}
				
				$category = array_pop($category_tree);
				
				if($category['userlevel_post'] > $options['userlevel'] && !is_privilegied('ov_forum'))
				{
					$return .= '<h2>Här får du inte skriva</h2>' . "\n";
					$return .= '<p>Du får inte posta i den här forumdelen. Ditt inlägg sparades inte. Här är ditt inlägg:</p>' . "\n";
					$return .= '<pre>' . $options['content'] . '</pre>' . "\n";	
					return $return;
					break;
				}
				
				if($category['quality_level'] > $_SESSION['login']['quality_level'] && $_SESSION['login']['quality_level_expire'] > time())
				{
					$return .= '<h2>Här får du inte skriva</h2>' . "\n";
					$return .= '<p>Tjockis, du är ju QL:ad! Sluta hacka, fetknåpp.</p>' . "\n";
					return $return;
					break;
				}
				
				return true;
				break;
				
			case 'view_category':
				if($options['category']['userlevel_read'] > $options['userlevel'] && !is_privilegied('ov_forum'))
				{
					$return .= '<h2>För låg användarnivå för kategori</h2>' . "\n";
					$return .= '<p>Nae, den kategorin får inte du titta i.</p>' . "\n";
					return $return;
					break;
				}
				
				return true;
				break;
				
			case 'view_thread':
				$category_tree = discussion_forum_categories_fetch(array('id' => $options['forum_id']));

				if(count($category_tree) < 1)
				{
					trace('forum_bug', 'In ' . __FILE__ . ' on line ' . __LINE__);
					return true;
					break;
				}
				
				$category = array_pop($category_tree);
				
				if($category['userlevel_read'] > $options['userlevel'] && !is_privilegied('ov_forum'))
				{
					$return .= '<h2>Den här diskussionen får du inte läsa!</h2>' . "\n";
					$return .= '<p>Hemligt, hemligt...</p>' . "\n";
					return $return;
					break;
				}
				
				return true;
				break;
		}
		return false;
	}
	
	function forum_update_category_session($options)
	{
		if(!login_checklogin())
		{
			return false;
		}
		$_SESSION['forum']['categories'][$options['category']['id']]['last_thread_count'] = $options['category']['thread_count'];
		foreach($options['threads'] AS $thread)
		{
			$_SESSION['forum']['categories'][$options['category']['id']]['last_thread_id'] = ($thread['id'] > $_SESSION['forum']['categories'][$options['category']['id']]['last_thread_id']) ? $thread['id'] : $_SESSION['forum']['categories'][$options['category']['id']]['last_thread_id'];
		}
		
		$updatequery = 'UPDATE forum_category_visits SET last_thread_count = "' . $options['category']['thread_count'] . '", last_thread_id = "' . $_SESSION['forum']['categories'][$options['category']['id']]['last_thread_id'] . '" WHERE category_id = "' . $options['category']['id'] . '" AND user_id = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		$insertquery = 'INSERT INTO forum_category_visits (category_id, user_id, last_thread_count, last_thread_id)';
		$insertquery .= ' VALUES("' . $options['category']['id'] . '", "' . $_SESSION['login']['id'] . '", "' . $options['category']['thread_count'] . '", "' . $_SESSION['forum']['categories'][$options['category']['id']]['last_thread_id'] . '")';
		
		mysql_query($insertquery) or mysql_query($updatequery) or report_sql_error($updatequery, __FILE__, __LINE__);
	}
	
	function discussion_forum_thread_info($thread)
	{
		
		$output .= '<div class="forum_thread_info">';
		$output .= '<div class="score_controls">' . "\n";
		if($thread['has_voted'] == 0 && login_checklogin())
		{
			$output .= '<img src="' . IMAGE_URL . 'discussion_forum/thread_voting_plus.png" id="forum_thread_vote_plus"/><img src="' . IMAGE_URL . 'discussion_forum/thread_voting_minus.png" id="forum_thread_vote_minus"/>' . "\n";
		}
		else
		{
			$output .= '<img src="' . IMAGE_URL . 'discussion_forum/thread_voting_plus_grey.png" /><img src="' . IMAGE_URL . 'discussion_forum/thread_voting_minus_grey.png" style="margin-top: 3px;" />' . "\n";			
		}
		$output .= '</div>' . "\n";
		$output .= '<div class="scoring">Poäng: <span id="thread_score">' . $thread['score'] . '</span></div>' . "\n";
		$output .= '<div class="started_at">Startad: ' . date('Y-m-d', $thread['timestamp']) . ' av <a href="/traffa/profile.php?id=' . $thread['author'] . '">' . $thread['username'] . '</a></div>' . "\n";
		
		$checked = (isset($_SESSION['forum']['subscriptions'][$thread['id']])) ?  'checked="checked"' : '';
		$output .= '<div class="thread_subscribing">';
		if(login_checklogin())
		{
				$output .= '<input type="checkbox" class="thread_subscription_control"' . $checked . ' id="thread_' . $thread['id'] . '_subscription_control" /><label for="thread_' . $thread['id'] . '_subscription_control">Bevaka tråd</label>';
		}
		$output .= '</div>' . "\n";
		$output .= '<div class="view_count">Visningar: ' . $thread['views'] . '</div>' . "\n";
		$output .= '<div class="post_count">Inlägg: ' . $thread['child_count'] . '</div>' . "\n";
		$output .= '<input type="hidden" id="thread_id" value="' . $thread['id'] . '" />' . "\n";
		$output .= '</div>' . "\n";
		
		if(forum_security(array('action' => 'thread_sticky')))
		{
			if($thread['sticky'] == 1)
			{
				$output .= '<button class="forum_sticky_control" id="sticky_control_' . $thread['id'] . '">Avklistra</button>' . "\n";				
			}
			else
			{
				$output .= '<button class="forum_sticky_control" id="sticky_control_' . $thread['id'] . '">Klistra</button>' . "\n";
			}
		}
		
		if(forum_security(array('action' => 'thread_lock')))
		{	
			if($thread['locked'] == 1)
			{
				$output .= '<button class="forum_thread_lock_control" id="thread_lock_control_' . $thread['id'] . '">Lås upp tråd</button>' . "\n";
			}
			else
			{
				$output .= '<button class="forum_thread_lock_control" id="thread_lock_control_' . $thread['id'] . '">Lås tråd</button>' . "\n";
			}
			
		}
		
		if(forum_security(array('action' => 'thread_rename')))
		{
			
			$output .= '<input type="text" id="forum_thread_rename_input_' . $thread['id'] . '" value="' . $thread['title'] . '" />';
			$output .= '<button class="forum_thread_rename_button" value="' . $thread['id'] . '">Byt namn</button>' . "\n";
		}
		if(forum_security(array('action' => 'move_thread', 'thread' => $thread)))
		{
			$output .= '<form id="lock_thread" action="/diskussionsforum/flytta_traad.php" method="post">' . "\n";
			$output .= '<input type="hidden" name="thread_id" value="' . $thread['id'] . '" />' . "\n";
			$categories = discussion_forum_categories_fetch();
			$output .= '<select name="new_category">' . "\n";
			$output .= '<option value="none">Flytta tråd</option>' . "\n";
			foreach($categories AS $category)
			{
				$output .= '<option value="' . $category['id'] . '">' . $category['title'] . '</option>' . "\n";
			}
			$output .= '</select>' . "\n";
			$output .= '<button id="move_thread_' . $thread['id'] . '_button" class="move_thread_button">Flytta</button>' . "\n";
			$output .= '</form>' . "\n";
		}
		return $output;
	}
	
	function discussion_forum_post_form($options)
	{
		$target_forum = array_pop(query_cache(array('category' => 'forum_categories', 'query' => 'SELECT quality_level FROM public_forums WHERE id = "' . $options['forum_id'] . '"')));

		$options['form_id'] = (isset($options['form_id'])) ? $options['form_id'] : 'forum_form_' . substr(md5(rand()), 0, 7);
		$return .= '<a name="post_form"></a>' . "\n";
		if($target_forum['quality_level'] > $_SESSION['login']['quality_level'] && $_SESSION['login']['quality_level_expire'] > time())
		{
			$return .= '<h2>Du kan inte skriva i detta forum</h2>' . "\n";
			$return .= '<p>Eftersom du misskött dig (eller pajen buggat ur) har du blivit avstängd från forum med en högre seriositetsnivå än <strong>' . $_SESSION['login']['quality_level'] . '</strong>.' . "\n";
			$return .= ' Denna begränsning gäller fram tills <strong>' . date('Y-m-d H:i', $_SESSION['login']['quality_level_expire']) . '</strong></p>' . "\n";
			return $return;
		}
		
		if($target_forum['quality_level'] != 3)
		{
			$return .= '<div class="server_message_notification">' . "\n";
			$options['quality_level'] = $target_forum['quality_level'];
			$return .= discussion_forum_fetch_moderation_info($options);
			$return .= '</div>' ."\n";
		}
		
		$return .= '<a name="new_thread"></a>' . "\n";
		$return .= '<h2 class="forum_post_form_heading">' . (isset($options['thread_id']) ? 'Skriv ett nytt inlägg' : 'Starta en ny tråd') . '</h2>' . "\n";
		$return .= '<form action="/diskussionsforum/nytt_inlaegg.php" method="post" class="forum_post_form" id="forum_post_form">' . "\n";
		$return .= '<input type="hidden" name="parent" value="' . $options['thread_id'] . '" />' . "\n";
		$return .= '<input type="hidden" name="mode" value="' . $options['mode'] . '" />' . "\n";
		$return .= '<input type="hidden" name="forum_id" value="' . $options['forum_id'] . '" />' . "\n";

		$return .= '<div class="column_1">' . "\n";

		$return .= '<div class="heading">' . "\n";
		$return .= '<h5>Rubrik</h5>' . "\n";
		$return .= '<input type="text" name="title" value="' . $options['title'] . '" tabindex="1" />' . "\n";
		$return .= '</div>' . "\n";

/*
		$return .= '<div class="reply_mode">' . "\n";
		$return .= '<h5>Spara svaret</h5>' . "\n";
		$return .= '<ul>' . "\n";
		$return .= '<li><input type="radio" name="reply_mode" id="reply_mode_inline" value="inline" checked="checked" /><label for="reply_mode_inline">I tråden</label></li>' . "\n";
		$return .= '<li><input type="radio" name="reply_mode" id="reply_mode_child_discussion" value="child_discussion" /><label for="reply_mode_child_discussion">I en undertråd</label></li>' . "\n";
		$return .= '</ul>' . "\n";
		$return .= '</div>' . "\n";
*/	
		$return .= '<h5 class="content_heading">Skriv ditt inlägg här</h5>' . "\n";
		$return .= '<div class="markup_controls">' . "\n";
		$return .= '	<button id="forum_form_bold_control">Fet</button>' . "\n";
		$return .= '	<button id="forum_form_italic_control">Kursiv</button>' . "\n";
		$return .= '	<button id="forum_form_spoiler_control">Spoiler</button>' . "\n";
		$return .= '	<button id="forum_form_image_control">Bild</button>' . "\n";
		$return .= '	<button id="forum_form_code_control">Kod</button>' . "\n";
		$return .= '	<button id="forum_form_poll_control">Undersökning</button>' . "\n";
		$return .= '</div>' . "\n";
		
		$return .= '<textarea name="content" id="forum_post_form_content" tabindex="2"></textarea>' . "\n";
		$return .= '<br/>' . "\n";
		$return .= '<button id="forum_form_preview_control">Förhandsgranska</button>' . "\n";
		$submit_value = ($_SESSION['forum']['last_post_timestamp'] > time() - FORUM_MIN_POST_DELAY) ? 'Vänta: ' . ($_SESSION['forum']['last_post_timestamp'] + FORUM_MIN_POST_DELAY - time()) : 'Spara';
		$return .= '<input type="submit" value="' . $submit_value . '" tabindex="3" class="forum_form_submit" id="' . $options['form_id'] . '_submit" />' . "\n";

		$return .= '</div>' . "\n";
		
		$return .= '<div class="column_2">' . "\n";
		$return .= '<div class="forum_post_form_help_area">' . "\n";
		$return .= '<h5>Hjälp</h5>' . "\n";
		$return .= '<select id="forum_post_form_help_selector">' . "\n";
	
		$return .= '<optgroup label="Formatering">' . "\n";
		$return .= '<option value="bold">Fetstil</option>' . "\n";
		$return .= '<option value="italic">Kursiv</option>' . "\n";
		$return .= '</optgroup>' . "\n";
		
		$return .= '<optgroup label="Regler">' . "\n";
		$return .= '<option value="nazi">Rasism</option>' . "\n";
		$return .= '<option value="images">Bilder</option>' . "\n";
		$return .= '</optgroup>' . "\n";

		$return .= '<optgroup label="Specialfunktioner">' . "\n";
		$return .= '<option value="spoiler">Spoiler</option>' . "\n";
		$return .= '<option value="poll">Undersökning</option>' . "\n";
		$return .= '<option value="Image">Infoga bild</option>' . "\n";
		$return .= '</optgroup>' . "\n";

		$return .= '<optgroup label="Om forumet">' . "\n";
		$return .= '<option value="help" selected="selected">Hjälprutan</option>' . "\n";		
		$return .= '<option value="reply">Svar</option>' . "\n";
		$return .= '</optgroup>' . "\n";

		$return .= '</select>' . "\n";
		$return .= '<div id="forum_form_help_content">' . "\n";
		$return .= '<h5>Det här är en hjälpruta</h5>' . "\n";
		$return .= '<p>Här får du korta tips och förklaringar om forumet. Välj kapitel i rullningslisten här ovanför.</p>' . "\n";
		$return .= '<h5>Rutan uppdateras automagiskt</h5>' . "\n";
		$return .= '<p>När du använder funktioner i forumet så visas bra tips här.</p>' . "\n";
		$return .= '</div>' . "\n";
		$return .= '</div>' . "\n";
		$return .= '</div>' . "\n";
		$return .= '<br style="clear: both;" />' . "\n";
		$return .= '</form>' . "\n";

		$return .= '<a name="forum_preview"></a>' . "\n";		
		$return .= '<div id="forum_preview_area">' . "\n";
		$return .= '</div>' . "\n";
		
		return $return;
	}

	function forum_get_url_by_post($post_id)
	{
		$query = 'SELECT t.forum_id, t.handle, t.id FROM forum_posts AS t, forum_posts AS p';
		$query .= ' WHERE p.id = "' . $post_id . '" AND t.id = p.parent_post';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		if(mysql_num_rows($result) != 1)
		{
			return false;
		}
		
		$data = mysql_fetch_assoc($result);
		
		$thread_handle = $data['handle'];
		$thread_id = $data['id'];
		$category_id = $data['forum_id'];

		/* Find out how many posts that exists before the one we're looking at */
		$query = 'SELECT COUNT(*) AS posts FROM forum_posts WHERE parent_post = "' . $thread_id . '" AND id < "' . $post_id . '"';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$data = mysql_fetch_assoc($result);
		
		// Ceil will return zero if there's only one post
		$page_num = floor($data['posts'] / FORUM_POSTS_PER_PAGE)+1;

		$url = '/sida_' . $page_num . '.php';
		$url = '/' . $thread_handle . $url;

		for($i = 0; $i < 50; $i++)
		{
			$query = 'SELECT handle, parent FROM public_forums WHERE id = "' . $category_id . '"';
			$data = query_cache(array('query' => $query, 'category' => 'forum_categories'));
			
			if(count($data) == 1)
			{
				$data = array_pop($data);
				$category_id = $data['parent'];
				$url = '/' . $data['handle'] . $url;
			}
			else
			{
				break;
			}
		}

		$url = '/diskussionsforum' . $url;
		
		$url .= '#post_' . $post_id;
		
		return $url;
	}
	
	function discussion_forum_post_list($posts, $parent_post = array())
	{
		foreach($posts AS $post)
		{
			$return .= discussion_forum_post_render($post, $parent_post);
			$seen_posts[] = $post['id'];
		}
		
		if(login_checklogin())
		{
			$query = 'DELETE FROM forum_notices WHERE post_id IN("' . implode($seen_posts, '", "') . '") AND user = "' . $_SESSION['login']['id'] . '"';
			mysql_query($query);
		}
		
		return $return;
	}
	
	function discussion_forum_post_list_search($posts)
	{
		$o = '<ul class="forum_search_results">' . "\n";
		foreach($posts AS $post)
		{
			$o .= '<li><h3>Av: ' . $post['username'] . ' i: <a href="' . forum_get_url_by_post($post['id']) . '">tråden ' . $post['title'] . '</a> vid:' . date('Y-m-d H:i', $post['timestamp']) . '</h3>';
			$o .= '<p>' . substr($post['content'], 0, 250) . '</p>' . "\n";
			$o .= '</li>';
		}
		$o .= '</ul>' . "\n";
		
		return $o;
	}
	
	function forum_thread_cache_latest_threads()
	{
		$threads = discussion_forum_post_fetch(array('threads_only' => 'true', 'limit' => 8, 'url_lookup' => true, 'order-by' => 'p.id', 'order-direction' => 'DESC', 'min_quality_level' => 2, 'max_userlevel_read' => 1));
		cache_save('latest_forum_threads', $threads);

		$spam = discussion_forum_post_fetch(array('threads_only' => 'true', 'limit' => 8, 'url_lookup' => true, 'order-by' => 'p.id', 'order-direction' => 'DESC', 'max_quality_level' => 1));
		cache_save('latest_forum_spam', $spam);
	}
	
	function forum_thread_cache_latest_open_source_threads()
	{
		$threads = discussion_forum_post_fetch(array('threads_only' => 'true', 'limit' => 8, 'url_lookup' => true, 'order-by' => 'p.id', 'order-direction' => 'DESC', 'forum_id' => 82));
		cache_save('latest_forum_open_source_threads', $threads);
	}
	
	function forum_latest_posts_cache($options = array())
	{
		$posts = discussion_forum_post_fetch(array('threads_only' => 'true', 'limit' => 10, 'url_lookup' => true, 'order-by' => 'p.last_post', 'order-direction' => 'DESC',  'min_quality_level' => 2, 'max_userlevel_read' => 1));
		cache_save('latest_forum_posts', $posts);		
	}
	
	function discussion_forum_count_views($thread)
	{
		if(!in_array($thread['id'], $_SESSION['forum']['seen_threads']))
		{
			$_SESSION['forum']['seen_threads'][] = $thread['id'];
			$query = 'UPDATE forum_posts SET views = views + 1 WHERE id = "' . $thread['id'] . '" LIMIT 1';
			mysql_query($query);
		}
		
		if(login_checklogin())
		{
			$insertquery = 'INSERT INTO forum_read_posts (user_id, thread_id, posts) VALUES("' . $_SESSION['login']['id'] . '", "' . $thread['id'] . '", "' . $thread['child_count'] . '")';
			$updatequery = 'UPDATE forum_read_posts SET posts = "' . $thread['child_count'] . '" WHERE user_id = "' . $_SESSION['login']['id'] . '" AND thread_id = "' . $thread['id'] . '" LIMIT 1';
			mysql_query($insertquery) or mysql_query($updatequery);
		}
	}
	
	function discussion_forum_category_head($options)
	{
		$output .=  '<p class="category_description">' . $options['category']['description'] . '</p>' . "\n";
		
		if(login_checklogin())
		{
			$checked = ($_SESSION['forum']['categories'][$options['category']['id']]['subscribing'] == 1) ? ' checked="checked"' : '';
			$output .= '<input type="checkbox" value="' . $options['category']['id'] . '"' . $checked . ' class="category_subscribtion_control" id="category_' . $options['category']['id'] . '_subscribe_control" />' . "\n";
			$output .= '<label for="category_' . $options['category']['id'] . '_subscribe_control">Prenumerera på nya trådar i "' . $options['category']['title'] . '"</label>' . "\n";
		}
		return $output;
	}
	
	function discussion_forum_thread_list($threads, $options)
	{
		$output .= '<table class="forum_thread_list">' . "\n";
		$output .= '<tr class="headings"><th></th><th>Rubrik</th><th>Skapare</th><th>Inlägg</th><th>Olästa</th><th>Poäng</th></tr>' . "\n";
		$zebra = 'odd';
		foreach($threads AS $thread)
		{
			$flags = ($thread['sticky'] == 1) ? ' <img src="' . IMAGE_URL . 'discussion_forum/thread_sticky_icon.png" alt="Klistrad" />' : '';
			$flags .= ($thread['locked'] == 1) ? ' L' : '';
			$href = (isset($thread['url'])) ? $thread['url'] : $thread['handle'] . '/sida_1.php';
			$thread['unread_posts'] = ($thread['unread_posts'] > 0) ? '<strong>' . $thread['unread_posts'] . '</strong>' : '';
			
			$output .= '<tr class="' . $zebra . '" id="' . $thread['id'] . '">' . "\n";
			if($options['notice_listing'] == true && isset($options['notice_listing']))
			{
				$output .= '	<td class="remove_subscribtion_listed"><a class="remove_subscribtion_listed" href="/ajax_gateways/discussion_forum.php?action=remove_thread_subscription&thread_id=' . $thread['id'] . '" title="Sluta bevaka tråd"><img src="' . IMAGE_URL . 'famfamfam_icons/eye.png" alt="x" /></a></td>' . "\n";
			}
			$output .= '	<td class="main_info">' . "\n";
			$output .= '		' . (empty($flags) ? '' : '&laquo;' . $flags . ' &raquo;') . ' <a href="' . $href . '">' . $thread['title'] . '</a>' . "\n";
			$output .= '	</td>' . "\n";
			$output .= '	<td class="author"><a href="/traffa/profile.php?id=' . $thread['author'] . '">' . $thread['username'] . '</a></td>' . "\n";			
			$output .= '	<td class="post_count">' . $thread['child_count'] . '</td>' . "\n";
			$output .= '	<td class="unread_posts">' . $thread['unread_posts'] . '</td>' . "\n";
			$output .= '	<td class="score">' . $thread['score'] . '</td>' . "\n";
			$output .= '</tr>' . "\n";

			$zebra = ($zebra == 'odd') ? 'even' : 'odd';
		}
		$output .= '</table>' . "\n";

		return $output;
	}
	
	function discussion_forum_categories_fetch($options)
	{
		$options['url_prefix'] = (isset($options['url_prefix'])) ? $options['url_prefix'] : '/diskussionsforum/';
		if(isset($options['id']) && !is_array($options['id']))
		{
			$options['id'] = array($options['id']);
		}
		
		$query = 'SELECT pf.*, t.title AS last_thread_title, t.handle AS last_thread_handle, l.username AS last_thread_username, l.id AS last_thread_author';
		$query .= ' FROM public_forums AS pf, forum_posts AS t, login AS l WHERE 1';
//		$query .= ($options['recursive']) ? ' AND pf.parent IS NULL' : '';
		$query .= (isset($options['parent'])) ? ' AND pf.parent = "' . $options['parent'] . '"' : '';
		$query .= (isset($options['forum_id'])) ? ' AND pf.id = "' . $options['forum_id'] . '"' : ''; // This exists, I know. But it didn't work, so I made my own
		$query .= (isset($options['id'])) ? ' AND pf.id IN("' . implode('", "', $options['id']) . '")' : '';
		$query .= (isset($options['handle'])) ? ' AND pf.handle LIKE "' . $options['handle'] . '"' : '';
		$query .= ' AND t.id = pf.last_thread';
		$query .= ' AND l.id = t.author';
		$query .= ' ORDER BY pf.priority DESC, pf.handle ASC';
				
				
		if(!isset($options['disable_query_caching']))
		{
			$max_delay = 120;
			$data_rows = query_cache(array('query' => $query, 'category' => 'forum_categories', 'max_delay' => $max_delay));
		}
		else
		{
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			while($data = mysql_fetch_assoc($result))
			{
				$data_rows[] = $data;
			}
		}
		
		foreach($data_rows as $data)
		{
			/* It's a bit smarter to check this after the query (query_cache...) */
			if(isset($options['viewers_userlevel']) && forum_security(array('action' => 'view_category', 'category' => $data, 'userlevel' => $options['viewers_userlevel'])) !== true)
			{
				continue;
			}
			
			if(!isset($options['max_levels']) || $options['max_levels'] > 0)
			{
				$recursive_options = $options;
				$recursive_options['parent'] = $data['id'];
				if(isset($options['max_levels']))
				{
					$recursive_options['max_levels'] = $options['max_levels'] - 1;
				}
				$recursive_options['url_prefix'] = $options['url_prefix'] . $data['handle'] . '/';
				$children = discussion_forum_categories_fetch($recursive_options);
			}
			if(count($children) > 0)
			{
				$data['children'] = $children;
			}
			$data['url'] = $options['url_prefix'] . $data['handle'] . '/';
			
			$categories[] = $data;
		}
		
		return $categories;
	}
	
	function discussion_forum_categories_list($categories)
	{
		$zebra = 'even';
		
		$table_open = false;
		foreach($categories AS $category)
		{
			if(is_array($category['children']) && count($category['children']) > 0)
			{
				if($table_open == true)
				{
					$output .= '</table>' . "\n";
					$table_open = false;
				}
				$output .= '<h2 class="forum_category_list_parent_heading"><a href="' . $category['url'] . '">' . $category['title'] . '</a></h2>' . "\n";
				$temp = $category;
				unset($temp['children']);
				$temp['title'] = 'Övrigt, ' . strtolower($temp['title']);
				$category['children'][] = $temp;
				$output .= discussion_forum_categories_list($category['children']);
			}
			else
			{
				if($table_open == false)
				{
					$output .= '<table class="forum_category_list">' . "\n";
					$table_open = true;
				}
				
				$output .= '<tr class="' . $zebra . '">' . "\n";
				$output .= '	<td class="name"><a href="' . $category['url'] . '" class="category_name">' . $category['title'] . '</a><br />' . "\n";
				$category['last_thread_title'] = (strlen($category['last_thread_title']) > 45) ? substr($category['last_thread_title'], 0, 35) . '...' : $category['last_thread_title'];
				$output .= '	Senaste tråden <a href="' . $category['url'] . $category['last_thread_handle'] . '/sida_1.php">' . $category['last_thread_title'] . '</a> av <a href="/traffa/profile.php?id=' . $category['last_thread_author'] . '">' . $category['last_thread_username'] . '</a><br />' . "\n";
				// Listing moderators in forum category
				if(isset($category['ovs']))
				{
					$output .= '<em>Ansvariga ordningsvakter:</em> ' . "\n";
					foreach($category['ovs'] as $ov)
					{
						if($ov['lastaction'] > time() - 600)
						{
							$output .= '<a href="/traffa/profile.php?user_id=' . $ov['id'] . '><strong>' . $ov['username'] . '</strong></a> ' . "\n";
						}
						else
						{
							$output .= '<a href="/traffa/profile.php?user_id=' . $ov['id'] . '><span>' . $ov['username'] . '</span></a> ' . "\n";
						}
					}
				$output .= '</td>' . "\n";
				}
				$output .= '	<td class="thread_count">' . $category['thread_count'] . ' trådar</td>' . "\n";
				if(login_checklogin())
				{
					$new_threads = $category['thread_count'] - $_SESSION['forum']['categories'][$category['id']]['last_thread_count'];
					$text = ($new_threads < 1) ? '' : '<strong>' . $new_threads . '</strong> nya';
					$text = ($new_threads == 1) ? '<strong>1</strong> ny' : $text;
					$output .= '	<td class="new_threads">' . $text . '</td>' . "\n";
				}
				$output .= '</tr>' . "\n";
				$zebra = ($zebra == 'even') ? 'uneven' : 'even';
			}
		}

		if($table_open == true)
		{
			$output .= '</table>' . "\n";
		}
				
		return $output;
	}
	
	function forum_thread_paging($options)
	{
		// Ceil will return zero if there's only one post
		$pages = floor($options['post_count'] / FORUM_POSTS_PER_PAGE)+1;		
		if($pages > 1)
		{
			$output .= ($pages > 10) ? '<br />(' : ' (';
			$output .= ($options['label'] == true) ? 'Sida: ' : '';
			for($i = 1; $i <= $pages; $i++)
			{
				if($i == $options['current_page'])
				{
					$output .= ' <strong>' . $i . '</strong>' . "\n";
				}
				else
				{
					$output .= ' <a href="' . $options['category_url'] . $options['thread_handle'] . '/sida_' . $i . '.php">' . $i . '</a>';
				}
			}
			$output .= ' )';
		}
		return $output;
	}
	
	function discussion_forum_locator($options)
	{
		$output .= '<div class="forum_locator">' . "\n";
		$output .= '<a href="/diskussionsforum/">Forum</a>';
		if($options['page']  == 'notices')
		{
			$output .= ' &raquo; <a href="/diskussionsforum/notiser.php">Notiser</a>';
		}
		foreach($options['categories'] AS $category)
		{
			$output .= ' &raquo; <a href="' . $category['url'] . '">' . str_replace(' ', '&nbsp;', $category['title']) . '</a>';
			$options['category_url'] = $category['url'];
		}
		if(isset($options['thread_handle']))
		{
			$output .= ' &raquo; <a href="' . $options['category_url'] . $options['thread_handle'] . '/sida_1.php">' . str_replace(' ', '&nbsp;', $options['thread_title']) . '</a>';
		}		

		if(isset($options['post_count']))
		{
			$output .= forum_thread_paging($options);
		}
				
		$output .= '</div>' . "\n";
		$output .= '<div class="forum_locator_ovs">' . "\n";

		// Listing moderators in forum category
		$last_category = array_pop($options['categories']);
		if(!empty($last_category['handle']))
		{
			$query = 'SELECT l.id AS user_id, l.username AS username, l.lastaction AS lastaction FROM privilegies AS p, login AS l, public_forums AS pf WHERE l.id = p.user AND pf.handle = p.value AND p.value = "' . $last_category['handle'] . '" AND p.privilegie = "discussion_forum_category_admin"';
			$result = query_cache(array('query' => $query, 'category' => 'forum_categories'));
			
			$output .= '<em>Ansvariga ordningsvakter:</em> ' . "\n";
			foreach($result as $ov)
			{
				if($ov['lastaction'] > time() - 600)
				{
					$output .= '<a href="/traffa/profile.php?user_id=' . $ov['user_id'] . '"><strong>' . $ov['username'] . '</strong></a> ' . "\n";
				}
				else
				{
					$output .= '<a href="/traffa/profile.php?user_id=' . $ov['user_id'] . '"><span>' . $ov['username'] . '</span></a> ' . "\n";
				}
			}
		}
		$output .= '</div>' . "\n";
		
		return $output;
	}
	
	function discussion_forum_path_to_category($options)
	{
		$query = 'SELECT handle, title, id, parent FROM public_forums WHERE 1';
		$query .= (isset($options['id'])) ? ' AND id = "' . $options['id'] . '"' : '';
		$query .= (isset($options['handle'])) ? ' AND id = "' . $options['handle'] . '"' : '';
		$query .= ' LIMIT 1';

		$data = array_pop(query_cache(array('category' => 'forum_categories', 'query' => $query)));
		$categories[] = $data;
	
		if($data['parent'] > 0)
		{
			$categories[] = array_pop(discussion_forum_path_to_category(array('id' => $data['parent'])));
		}
	
		$categories = array_reverse($categories);
	
		$url = '/diskussionsforum/';
		foreach($categories AS $level => $category)
		{
			$url .= $category['handle'] . '/';
			$categories[$level]['url'] = $url;
		}
		
		return $categories;
	}
	
	function discussion_forum_reload_all()
	{
		discussion_forum_reload_subscriptions();
		discussion_forum_reload_notices();
		discussion_forum_reload_category_subscriptions();
		$_SESSION['forum']['new_notices'] = count($_SESSION['forum']['notices']);
		foreach($_SESSION['forum']['subscriptions'] AS $thread)
		{
			$_SESSION['forum']['new_notices'] += $thread['unread_posts'];
		}
		$_SESSION['forum']['new_notices'] += $_SESSION['forum']['new_threads_count'];
	}
	
	function discussion_forum_reload_notices()
	{
		if(!login_checklogin())
		{
			return false;
		}

		$_SESSION['forum']['notices'] = array();

		$query = 'SELECT n.post_id, n.type, p.author, p.timestamp, l.username, t.handle, t.title, t.forum_id, t.id AS thread_id';
		$query .= ' FROM forum_notices AS n, login AS l, forum_posts AS p, forum_posts AS t';
		$query .= ' WHERE n.user = "' . $_SESSION['login']['id'] . '" AND l.id = p.author AND p.id = n.post_id AND t.id = p.parent_post ORDER BY p.id DESC';

		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			$data['title'] = (strlen(trim($data['title'])) == 0) ? 'Rubrik saknas' : $data['title'];
			$_SESSION['forum']['notices'][] = $data;
		}
	}
	
	function discussion_forum_reload_subscriptions()
	{
		if(!login_checklogin())
		{
			return false;
		}
		unset($_SESSION['forum']['subscriptions']);
		$threads = discussion_forum_post_fetch(array('only_subscriptions' => true, 'order-by' => 'unread_posts', 'order-direction' => 'DESC', 'url_lookup' => true));
		
		/* The fetch function gives us a lot of unused info, such as post content
			 Make sure to save only relevant info */
		$info_to_store = array('id', 'read_posts', 'has_voted', 'url', 'handle', 'title', 'score', 'child_count', 'username', 'author', 'unread_posts', 'forum_id');
		foreach($threads AS $thread)
		{
			foreach($info_to_store AS $key)
			{
				$_SESSION['forum']['subscriptions'][$thread['id']][$key] = $thread[$key];
			}
		}
	}

	function discussion_forum_reload_category_subscriptions()
	{
		foreach($_SESSION['forum']['categories'] AS $id => $category)
		{
			if($category['subscribing'] == 1)
			{
				$categories[] = $id;
			}
		}
		$_SESSION['forum']['new_threads_count'] = 0;
		$query = 'SELECT id, thread_count FROM public_forums WHERE id IN ("' . implode('", "', $categories) . '")';
		$query .= ' AND userlevel_read <= ' . (login_checklogin() ? $_SESSION['login']['userlevel'] : 0);
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			if($data['thread_count'] > $_SESSION['forum']['categories'][$data['id']]['last_thread_count'])
			{
				$_SESSION['forum']['new_threads_count'] += $data['thread_count'] - $_SESSION['forum']['categories'][$data['id']]['last_thread_count'];
			}
		}
	}
	
	function discussion_forum_list_notices()
	{
		if(count($_SESSION['forum']['notices']) > 0)
		{
			$output .= '<h2>Svar till dig</h2>' . "\n";
			$output .= '<table>' . "\n";
			foreach($_SESSION['forum']['notices'] AS $notice)
			{
				/* Find out how many posts that exists before the one we're looking at */
				$query = 'SELECT COUNT(*) AS posts FROM forum_posts WHERE parent_post = "' . $notice['thread_id'] . '" AND id < "' . $notice['post_id'] . '"';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				$data = mysql_fetch_assoc($result);
				
				$output .= '<tr>' . "\n";
				$category_path = discussion_forum_path_to_category(array('id' => $notice['forum_id']));
				$category = array_pop($category_path);
				$url = $category['url'] . $notice['handle'] . '/sida_' . (floor($data['posts'] / FORUM_POSTS_PER_PAGE)+1) . '.php#post_' . $notice['post_id'];
				
				$output .= '<td class="remove_answer_notice_listed"><a id="' . $notice['post_id'] . '" class="remove_answer_notice_listed" href="/ajax_gateways/discussion_forum.php?action=remove_answer_notice&post_id=' . $notice['post_id'] . '" title="Flagga inlägget som läst"><img src="' . IMAGE_URL . 'famfamfam_icons/eye.png" alt="x" /></a></td>' . "\n";
				$output .= '<td>' . fix_time($notice['timestamp']) . '</td>' . "\n";		
				$output .= '<td><a href="' . $url . '">' . $notice['title'] . '</a></td>';
				$output .= '<td><a href="/traffa/profile.php?id=' . $notice['author'] . '">' . $notice['username'] . '</a></td>';
			}
			$output .= '</table>' . "\n";
		}
		
		return $output;
	}
	
	function discussion_forum_parse_request($url)
	{
		$url = strtolower($url);
		
		$url_query_parts = explode('?', $url);
		$url_anchor_parts = explode('#', $url_query_parts[0]);
		$url = $url_anchor_parts[0];
		
		if(substr($url, -1) == '/') //Remove the last slash (trailing slash)
		{
			$url = substr($url, 0, -1);
		}
		
		if($url == '/diskussionsforum')
		{
			$request['action'] = 'index';
		}
		elseif($url == '/diskussionsforum/flytta_traad.php' && is_numeric($_POST['new_category']))
		{
			$request['action'] = 'move_thread';
			$request['thread'] = array_pop(discussion_forum_post_fetch(array('post_id' => $_POST['thread_id'])));
			$request['new_category'] = array_pop(discussion_forum_categories_fetch(array('id' => $_POST['new_category'])));
		}
		elseif($url == '/diskussionsforum/nytt_inlaegg.php')
		{
			$request['action'] = 'new_post';
		}
		elseif($url == '/diskussionsforum/soek.php')
		{
			$request['action'] = 'search';
			$request['freetext'] = substr($url_query_parts[1], 9);
		}
		elseif($url == '/diskussionsforum/nya_traadar.php')
		{
			$request['action'] = 'latest_threads';
		}
		elseif($url == '/diskussionsforum/dina_traadar.php')
		{
			$request['action'] = 'threads_by_user';
			$request['user_id'] = $_SESSION['login']['id'];
		}
		elseif($url == '/diskussionsforum/notiser.php')
		{
			$request['action'] = 'view_notices';
		}
		elseif($url == '/diskussionsforum/notiser.new.php')
		{
			$request['action'] = 'view_new_notices';
		}
		elseif(substr($url, -4) == '.php' && substr($url, strrpos($url, '/'), 11) != '/traadsida_')
		{
			// Note: This does NOT have to be a thread, it could also be a help-page such as regler.php
			// AND, the url might be fomatted like this: /discussionsforum/traad_handtag/sida_1.php
			
			$explosion = explode('/', $url);
			
			$page_count_start = strrpos($url, '/sida_')+6;
			$page_count_end = strrpos($url, '.');
			$page_count_length = $page_count_end - $page_count_start;
			$request['page_num'] = substr($url, $page_count_start, $page_count_length);

			$request['action'] = 'view_thread';
			$request['thread_handle'] = $explosion[count($explosion)-2];

			/*//trace('datamirk', print_r($explosion, true));
			$request['category'] = array_pop(discussion_forum_categories_fetch(array('handle' => $explosion[count($explosion)-3])));
			if(!in_array($explosion[count($explosion)-3], array('hamsterpaj', 'nyheter', 'buggar_och_fel', 'spel_och_film', 'mellan_himmel_och_jord', 'mat', 'klaeder_och_utseende', 'djur_och_husdjur', 'historier_och_skaemt', 'mobiltelefoner', 'tonaaring', 'relationer', 'kropp_och_pubertet', 'sex', 'familjen', 'vaenner', 'skola', 'pengar', 'fritid', 'sport', 'traening', 'foereningsliv', 'motor', 'musik', 'film_och_tv', 'foto', 'litteratur', 'spel', 'datorspel', 'counter_strike', 'world_of_warcraft', 'xbox', 'playstation', 'nintendo', 'retrokonsoller', 'lajv_och_rollspel', 'datorer', 'support_och_hjaelp', 'haardvara', 'mjukvara', 'operativsystem', 'lan_och_naetverk', 'programmering', 'php_scripting', 'webbdesign', 'min_hemsida', 'vetenskap', 'fysik', 'kemi', 'biologi', 'matematik', 'teknik', 'elektronik', 'astronomi', 'psykologi', 'debatt', 'politik', 'religion', 'jaemstaelldhet', 'miljoe', 'filosofi', 'alkohol_tobak_droger', 'historia', 'oevrigt', 'koep_och_saelj', 'efterlysningar', 'forumlekar', 'listor_omroestningar', 'skraep_och_spam', 'teknik', 'kaerlek', 'open_source', 'presentationsteman', 'forum_error', 'ordningsvaktsforum')))
			{
				if($explosion[count($explosion)-3] != 'diskussionsforum')
				{
					trace('extreme_datamirk', $_SESSION['login']['username'] . ':' . $explosion[count($explosion)-3] . ' = ' . print_r($explosion, true));
				}
			}*/
		}
		else
		{
			$request['action'] = 'view_category';
			$explosion = explode('/', $url);
			$last_piece = $explosion[count($explosion)-1];
			
			if(substr($last_piece, 0, 10) == 'traadsida_' && substr($last_piece, -4) == '.php' && is_numeric(substr($last_piece, 10, -4)) && intval(substr($last_piece, 10, -4)) > 0)
			{
				$request['page_offset'] = intval(substr(array_pop($explosion), 10, -4)) - 1;
			}
			else
			{
				$request['page_offset'] = 0;
			}
			
				
			$handle = array_pop($explosion);
			
			if($handle == url_secure_string($handle))
			{
				$viewers_userlevel = login_checklogin() ? $_SESSION['login']['userlevel'] : 0;
				$request['category'] = array_pop(discussion_forum_categories_fetch(array(
					'handle' => $handle,
					'viewers_userlevel' => $viewers_userlevel,
					'disable_query_caching' => true // Forum_not_found-thing is killing our query_cache...
					)));
				$request['category_handle'] = $handle;
				if(count($request['category']) < 1)
				{
					$request['action'] = 'forum_not_found';
				}
			}
			else
			{
				$request['action'] = 'forum_not_found';
			}
		}
		return $request;
	}
	
	function discussion_forum_insert_poll($matches)
	{
		$poll = poll_fetch(array('id' => $matches[1]));
		if(count($poll) == 1)
		{
			return poll_render($poll[0]);
		}
	}
	
	function discussion_forum_tillagg_callback($matches)
	{
		$output .= '<div class="post_addition">Tillägg av <a href="/traffa/quicksearch.php?username=' . $matches[1] . '">' . $matches[1] . '</a>';
		$output .= ' ' . strtolower(fix_time($matches[2])) . '<br />' . $matches[3] . '</div>' . "\n";

		return $output;
	}
	
	function discussion_forum_code_tag_callback($matches)
	{
		$highlighted_code = $matches[2];
		
		$output  = '<div class="code_tag">';
		$output .= '<strong>Ett stycke ' . (($matches[1] != '') ? $matches[1] . '-kod:' : 'kod:') . '</strong><br />';
		$output .= '<pre>' . $highlighted_code . '</pre>';
		$output .= '</div>';
		
		return $output;
	}
	
	// Parse markup, such as bold, quotation, smilies and nl2br
	function discussion_forum_parse_output($text, $options = array())
	{
		// BBcode part
		$bbcode_ruleset = array(
			''=>     array('type' => BBCODE_TYPE_ROOT),
			'i'=>    array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<i>', 'close_tag' => '</i>'),
			'img'=>  array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<img src="', 'close_tag' => '" class="forum_post_image" />', 'childs'=>''),
			'b'=>    array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<b>', 'close_tag'=>'</b>'),
			'spoiler'=>  array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<div class="spoiler"><span>Spoiler: <button class="spoiler_control">Visa</button></span><span class="spoiler_content">', 'close_tag' => '</span></div>', 'childs'=>''),
		);
		
		$text = clickable_links($text);
		
		if(isset($options['search_highlight']))
		{
			$options['search_highlight'] = is_array($options['search_highlight']) ? $options['search_highlight'] : explode(' ', $options['search_highlight']);
			
			$safe_wordlist = array_map('preg_quote', $options['search_highlight']);

			$pattern = '/(' . str_replace('/', '\\/', implode('|', $safe_wordlist)) . ')/';
			echo $pattern . "\n";
			$replacement = '<strong class="search_highlight">$1</strong>';
			$text = preg_replace($pattern, $replacement, $text);
		}
		
		$text = nl2br($text);
		$text = str_replace(array("\n", "\r"), '', $text);

		$bbcode_handler = bbcode_create($bbcode_ruleset);
		$text =  bbcode_parse($bbcode_handler, $text);

		$pattern = '/\[svar:(.+?):([0-9]+?)\](.*?)(\[\/svar\])/';
		$replacement = '<div class="answer" id="forum_answer_to_$2"><strong>Svar till <a href="/traffa/quicksearch.php?username=$1">$1</a></strong> [<a href="/diskussionsforum/gaa_till_post.php?post_id=$2">Gå till post</a>]: $3</div>';
		$text =  preg_replace($pattern, $replacement, $text);
		
		$pattern = '/\[svar:(.+?)\](.+?)(\[\/svar\])/';
		$replacement = '<div class="answer"><a href="?$1">$1</a>: $2</div>';
		$text =  preg_replace($pattern, $replacement, $text);

		$pattern = '/\[poll:(.+?)\]/';
		$text =  preg_replace_callback($pattern, 'discussion_forum_insert_poll', $text);
		
		$pattern = '/\@(.+?)\@/';
		$replacement = '<span class="forum_answer" id="$1">Svar till $1</span>';
		$text =  preg_replace($pattern, $replacement, $text);

		$pattern = '/\[tillagg:(.+?):(.+?)\](.*?)(\[\/tillagg\])/';
		$replacement = '<div class="answer" id="forum_answer_to_$2">Tilläg av <a href="/traffa/quicksearch.php?username=$1">$1</a> klockan $2<br />$3</div>';
		$text =  preg_replace_callback($pattern, 'discussion_forum_tillagg_callback', $text);
		
		$code_languages = array('php', 'html', 'javascript', 'css', 'C#', 'asp', 'joar');
		// Note: The pattern modifier /i makes the pattern case-insensitive.
		$pattern = '/\[code:(' . implode('|', $code_languages) . ')\](.*?)(\[\/code\])/i';
		$text =  preg_replace_callback($pattern, 'discussion_forum_code_tag_callback', $text);

		// Note: See above for explanation on /i, /e evaluates (an escaped version) of the search string.
		$pattern = '/\[code\](.*?)\[\/code\]/ie';
		//$text = preg_replace($pattern, 'discussion_forum_code_tag_callback(array(1 => "", 2 => "$1"))', $text);

//		$text = ($_SESSION['preferences']['forum_enable_smilies'] == 1) ? setSmilies($text) : $text;
		
		if(isset($options['post']['quality_level']) && $options['post']['quality_level'] <= 3 && $_SESSION['preferences']['forum_enable_smilies'] == 1)
		{
			$text = setSmilies($text);
		}
		
		
		return $text;
	}
	
	// Send notices for answers and quotations. Mabye do some auto-reporting if detecting foul words
	function discussion_forum_parse_input($options)
	{
		// Send reply-notifications
		$pattern = '/\[svar:(.+?)(:(.+?))?\]|\@(.+?)\@/';
		preg_match_all($pattern, strtolower($options['text']), $matches);
		foreach($matches[1] AS $username)
		{
			if(strlen($username) > 0)
			{
				$usernames[] = $username;
			}
		}
		foreach($matches[4] AS $username)
		{
			if(strlen($username) > 0)
			{
				$usernames[] = $username;
			}
		}

		foreach($usernames AS $username)
		{
			$query = 'SELECT l.id, u.msnbot_msn, l.username FROM login as l, userinfo as u WHERE l.id = u.userid AND username LIKE "' . $username . '" LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

			if(mysql_num_rows($result) == 1)
			{
				$data = mysql_fetch_assoc($result);
				
				if($data['msnbot_msn'] != '')
				{
					msnbot_queue_add(array(
						'user_id' => $data['id'],
						'msn' => $data['msnbot_msn'],
						'message' => (($options['author'] == $_SESSION['login']['id']) ? $_SESSION['login']['username'] : 'Någon') . ' svarade just på ett av dina inlägg i forumet på Hamsterpaj.net. Klicka på den här länken för att läsa svaret:' . "\r\n" . 'http://www.hamsterpaj.net/diskussionsforum/gaa_till_post.php?post_id=' . $options['post_id']
					));
				}
				
				$query = 'INSERT INTO forum_notices (post_id, user, type) VALUES("' . $options['post_id'] . '", "' . $data['id'] . '", "reply")';
				mysql_query($query); //No error-handling, since that would trigger when the same user has multiple replies in one post
			}
		}
	}
	
	function discussion_forum_fetch_moderation_info($options)
	{
		global $DISCUSSION_FORUM_QUALITY_LEVEL_INFO;
		global $DISCUSSION_FORUM_QUALITY_LEVEL_FULL_INFO;
		global $DISCUSSION_FORUM_QUALITY_LEVEL_HEADER;
		$quality_level = $options['quality_level'];
		$output = '<h2>Modereringsnivå: ' . $DISCUSSION_FORUM_QUALITY_LEVEL_HEADER[$quality_level] . '</h1>' ."\n";
		$output .= '<p>' . $DISCUSSION_FORUM_QUALITY_LEVEL_INFO[$quality_level] . '</p>' . "\n";
		
		$output .= '<h3 class="server_message_collapse_header" id="server_message_collapse_header_moderationinfo">Visa mer information</h3>' . "\n";
		$output .= '<div class="server_message_collapsed_information" id="server_message_collapse_information_moderationinfo">' . "\n";
 		$output .= $DISCUSSION_FORUM_QUALITY_LEVEL_FULL_INFO[$quality_level];
 		$output .= '</div>' . "\n";
		return $output;
	}
	
	
	/* Todo: forum_notices_get and forum_notices_count does not conform to the naming standards yet...
	   They should really not be used, but is that in the forum cache update-function in login.lib.php.
	*/
	
		/*	This function return an array of discussion ids along with the title
			and number of unread posts. The field 'types' in $options is an array
			of the kinds of notices that should be returned. Insert the types that you
			want receive, 'watches', 'responses', 'notices' and 'subscriptions'.
			*/
	function forum_notices_get($options)
	{
		$query['watches'] = 'SELECT id, (a.posts - b.posts) AS unread FROM';
		$query['watches'] .= ' ( (SELECT id, posts';
		$query['watches'] .= ' FROM discussion_watches w, discussions d';
		$query['watches'] .= ' WHERE w.user_id = ' . $_SESSION['login']['id'];
		$query['watches'] .= ' AND w.discussion_id = d.id';
		$query['watches'] .= ' AND d.deleted != 1) AS a';
		$query['watches'] .= ' LEFT OUTER JOIN';
		$query['watches'] .= ' (SELECT discussion_id, posts';
		$query['watches'] .= ' FROM posts_read';
		$query['watches'] .= ' WHERE user_id = ' . $_SESSION['login']['id'] . ') AS b';
		$query['watches'] .= ' ON';
		$query['watches'] .= ' a.id = b.discussion_id)';
		$query['watches'] .= ' WHERE (a.posts - b.posts) > 0';

		$query['responses'] = 'SELECT p.id post_id, d.id as id, title, count(post_id) unread FROM discussions d, posts p, notices n';
		$query['responses'] .= ' WHERE n.user_id = ' . $_SESSION['login']['id'] . ' AND p.id = n.post_id';
		$query['responses'] .= ' AND d.id = p.discussion_id AND n.type = "response"';
		$query['responses'] .= ' AND d.deleted != 1';
		$query['responses'] .= ' GROUP BY d.id ORDER BY p.id ASC';

		$query['notices'] = 'SELECT p.id post_id, d.id as id, title, count(post_id) unread FROM discussions d, posts p, notices n';
		$query['notices'] .= ' WHERE n.user_id = ' . $_SESSION['login']['id'] . ' AND p.id = n.post_id';
		$query['notices'] .= ' AND d.id = p.discussion_id AND n.type = "notice"';
		$query['notices'] .= ' AND d.deleted != 1';
		$query['notices'] .= ' GROUP BY d.id ORDER BY p.id ASC';
		
		
		$query['subscriptions'] = 'SELECT id, title, label, (a.posts - b.posts) as unread';
		$query['subscriptions'] .= ' FROM';
		$query['subscriptions'] .= ' ((SELECT d.id AS id, d.title AS title, t.label AS label, d.posts AS posts';
		$query['subscriptions'] .= ' FROM discussions d, object_tags ot, tags t, discussion_subscriptions s';
		$query['subscriptions'] .= ' WHERE s.user_id = ' . $_SESSION['login']['id'];
		$query['subscriptions'] .= ' AND s.tag_id = ot.tag_id';
		$query['subscriptions'] .= ' AND ot.object_type = "discussion"';
		$query['subscriptions'] .= ' AND ot.tag_id = t.id';
		$query['subscriptions'] .= ' AND ot.reference_id = d.id';
		$query['subscriptions'] .= ' AND d.deleted != 1';
		$query['subscriptions'] .= ' GROUP BY d.id) AS a';
		$query['subscriptions'] .= ' LEFT OUTER JOIN';
		$query['subscriptions'] .= ' (SELECT discussion_id, posts';
		$query['subscriptions'] .= ' FROM posts_read';
		$query['subscriptions'] .= ' WHERE user_id = ' . $_SESSION['login']['id'] . ') AS b';
		$query['subscriptions'] .= ' ON a.id = b.discussion_id)';
		$query['subscriptions'] .= ' WHERE (a.posts - b.posts) > 0';
								
		//Set user id to logged in user if not set
		if(isset($options['user_id']))
		{
			$user_id = $options['user_id'];
		}
		else
		{
			$user_id = $_SESSION['login']['id'];
		}

		$return = array();
		foreach($options['types'] as $type)
		{
			/*	Send query and fetch result in the same way for all kinds of notices
					This require that all the sql queries above deliver the same
					column names in the response, that is: 'id', 'title' and 'unread'
			*/
/*
			if(5 == $_SESSION['login']['userlevel'])
			{
				echo '<p>' . $query[$type] . '</p>';
			}

*/
			$result = mysql_query($query[$type]) or die(report_sql_error($query));
	 		while($data = mysql_fetch_assoc($result))
			{
				$return[$type]['discussions'][$data['id']]['id']		= $data['id'];
				$return[$type]['discussions'][$data['id']]['post_id']	= $data['post_id'];
				$return[$type]['unread'] += $data['unread'];
			}
		}
/*
		if(in_array($_SESSION['login']['id'], array(685862, 644314)))
		{
			preint_r($return);
		}
*/
		return $return;
	}
	
	function forum_notices_count($user)
	{
		return 0;
		$fetch['types'] = array('watches', 'responses', 'notices', 'subscriptions');
		$notices = forum_notices_get($fetch);
		return $notices['watches']['unread'] + $notices['responses']['unread'] + $notices['notices']['unread'] + $notices['subscriptions']['unread'];
	}
	
	
	function discussion_forum_search_form()
	{
		$o = '<form class="discussion_forum_search_form" action="/diskussionsforum/soek.php" method="get">' . "\n";
		$o .= '<input type="text" name="freetext" style="font-size: 18px; padding: 5px;" />' . "\n";
		$o .= '<input type="submit" value="Sök" style="font-size: 18px; padding: 5px;" />' . "\n";
		$o .= '</form>' . "\n";
		
		return $o;
	}

?>
