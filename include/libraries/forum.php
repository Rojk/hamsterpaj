<?php

	/**
	 * Use this function to set a read only flag on a user. This prohibits the user from posting in the forum.
	 */
	function forum_read_only_set($user_id, $admin_id, $duration, $reason)
	{
		if($_SESSION['login']['userlevel'] > 2)
		{
			$query = 'SELECT * FROM forum_read_only WHERE userid="' . $user_id . '"';
			$query_insert = 'INSERT INTO forum_read_only (userid, expire, reason, admin_userid) VALUES ("' . 
								$user_id . '", "' .
								time() + $duration . '", "' .
								$reason . '", "' .
								$_SESSION['login']['id'] . '")';
			$query_update = 'UPDATE forum_read_only SET' . 
								' expire="' . time() + $duration . '", ' .
								' reason="' . $reason . '", ' .
								' admin_userid="' . $_SESSION['login']['id'] . '"' . 
								' WHERE userid="' . $user_id . '"';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			
			if(mysql_num_rows($result) > 0)
			{
				if($data = mysql_fetch_array($result))
				{
					//Om ny avstängning skall vara längre än den gamla så uppdaterar vi annars låter vi det vara.
					if($data['expire'] < time() + $duration)
					{
						mysql_query($query_update) or die(report_sql_error($query, __FILE__, __LINE__));
					}
				}
			}
			// Om det inte fanns någon avstängning sen tidigare så sätter vi in den nya.
			else
			{
				mysql_query($query_insert) or die(report_sql_error($query_insert, __FILE__, __LINE__));
			}
		}
	}
	
	/**
	 * @return True if the reader is read only, false if not.
	 */
	function forum_read_only_get($user_id)
	{
		$query = 'SELECT * FROM forum_read_only WHERE userid="' . $user_id . '"';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if($data = mysql_fetch_array($result))
		{
			if($data['expire'] > time())
			{
				return true;
			}
			else
			{
				$query = 'DELETE FROM forum_read_only WHERE userid="' . $user_id . '"';
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function forum_fix_title($title)
	{
		return $title;
	}
	
	function get_quality_rank($text)
	{
		return 1;
	}

	function get_spelling_score($text)
	{
		return 1;	
	}
	
	function forum_sub_category_list($main_category)
	{
		global $FORUM_CATEGORIES;
		
		echo '<ul class="forum_sub_category_list">' . "\n";
		foreach($FORUM_CATEGORIES[$main_category]['sub_categories'] AS $handle => $sub_category)
		{
			echo '<li><a href="/forum/' . $main_category . '/' . $handle . '/">' . $sub_category['label'] . '</a></li>' . "\n";
		}
		echo '</ul>' . "\n";
		echo '<br style="clear: both;" />' . "\n";
		echo "\n\n";
	}
	
	function forum_category_list($categories)
	{
		/*
			Recieves an array of category handles
		*/
		global $FORUM_CATEGORIES;
		
		echo '<ul class="forum_sub_category_list">' . "\n";
		foreach($categories AS $handle)
		{
			foreach($FORUM_CATEGORIES AS $main_category => $category_group)
			{
				foreach($category_group['sub_categories'] AS $current_handle => $current_info)
				{
					if($current_handle == $handle)
					{
						$this_parent = $main_category;
						$this_label = $current_info['label'];
					}
				}
			}
			echo '<li><a href="/forum/' . $this_parent . '/' . $handle . '/">' . $this_label . '</a></li>' . "\n";
		}
		echo '</ul>' . "\n";
		echo '<br style="clear: both;" />' . "\n";
		echo "\n\n";
	}
	
	function forum_favourite_category_add($handle)
	{
		$_SESSION['preferences']['forum_favourite_categories'][] = $handle;
		$_SESSION['preferences']['forum_favourite_categories'] = array_unique($_SESSION['preferences']['forum_favourite_categories']);
		
		$db_save['preferences']['forum_favourite_categories'] = mysql_real_escape_string(serialize($_SESSION['preferences']['forum_favourite_categories']));
		login_save_user_data($_SESSION['login']['id'], $db_save);
	}
	
	function forum_favourite_category_remove($handle)
	{
		/* Copy the old data to a new variabel, then empty the session array and copy the old data, skipping the category to remove */
		$old = $_SESSION['preferences']['forum_favourite_categories'];
		$_SESSION['preferences']['forum_favourite_categories'] = array();
	
		foreach($old AS $category)
		{
			if($handle != $category)
			{
				$_SESSION['preferences']['forum_favourite_categories'][] = $category;
			}
		}
		
		$db_save['preferences']['forum_favourite_categories'] = mysql_real_escape_string(serialize($_SESSION['preferences']['forum_favourite_categories']));
		login_save_user_data($_SESSION['login']['id'], $db_save);
	}

	function forum_index()
	{
		echo '<div class="latest_discussions">' . "\n";
		echo '<div class="head">' . "\n";
		echo '<form action="' . $_SERVER['request_uri'] . '" method="post">' . "\n";
		echo '<select name="mode">'. "\n";
		$modes['popular'] = 'Intressanta diskussioner';
		$modes['last_post'] = 'Diskussioner med nya inlägg';
		$modes['few_posts'] = 'Diskussioner med få inlägg';
		$modes['many_posts'] = 'Diskussioner med många inlägg';
		$modes['random'] = 'Slumpade diskussioner';
		$modes['latest'] = 'Nya diskussioner';
		foreach($modes AS $handle => $label)
		{
			echo "\t" . '<option value="' . $handle . '"' . ($_SESSION['preferences']['forum_index_mode'] == $handle ? ' selected="true"' : '') . '>' . $label . '</option>' . "\n";
		}
		echo '</select>' . "\n\n";
		echo '<select name="discussions_read">' . "\n";
		echo '<option value="all" ' . ($_SESSION['preferences']['forum_index_discussions_read'] == 'all' ? ' selected="true"' : '') . ' >Alla diskussioner</option>' . "\n";
		echo '<option value="read" ' . ($_SESSION['preferences']['forum_index_discussions_read'] == 'read' ? ' selected="true"' : '') . ' >Endast tidigare lästa</option>' . "\n";
		echo '<option value="unread" ' . ($_SESSION['preferences']['forum_index_discussions_read'] == 'unread' ? ' selected="true"' : '') . ' >Endast olästa</option>' . "\n";
		echo '</select>' . "\n\n";
		
		echo '<input type="checkbox" name="force_unread" value="true" id="chk_force_unread" ' . (($_SESSION['preferences']['forum_index_force_unread'] == 'true') ? 'checked="true" ' : '') . '/>' . "\n";
		echo '<label for="chk_force_unread">Bara med olästa inlägg</label>' . "\n";
	
		echo '<input type="submit" value="Visa" />' . "\n";
		echo '</form>' . "\n";
/*

SELECT discussion_id
FROM posts
WHERE timestamp > UNIX_TIMESTAMP( ) -86400 *3
GROUP BY discussion_id
ORDER BY COUNT( discussion_id ) DESC
LIMIT 10

*/

		echo '</div>' . "\n";
		echo '</div>' . "\n";
	}


	function forum_get_action($url)
	{
		if(644314 == $_SESSION['login']['id'])
			log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'forum_get_action', $url);
		$actions['ny_diskussion'] = 'discussion_create';
		$actions['ny_post'] = 'post_create';
		$actions['redigera'] = 'post_edit';
		$actions['notiser'] = 'notices_view';
		$actions['instaellningar'] = 'settings';
		$actions['admin_check'] = 'admin_check';
		
		$tab_actions = array('intressanta','laesta','dina_diskussioner','nya','nya_inlaegg','dar_du_har_svarat');
		
		$url = preg_replace('/#.*$/', '', $url);
		$url = preg_replace('/([^(php)(html)\/])$/','$0/',$url);									 
		$return = array('action' => 'unknown');
		$return['modified_url'] = $url;
		//Forum index
		// /forum
		// /forum/
		if(preg_match('/^\/forum\/?$/', $url, $matches))
		{
			$return['action'] = 'index';
		}
		else
		{
			
			if(preg_match('/^\/forum\/((\w+)\/)*(\w+)\.php$/', $url, $matches))
			{
				$return['action'] = $actions[$matches[3]];
			}
			//Action - catch action
			//Index-view
			// /forum/index/tab-handle/
			if(preg_match('/^\/forum\/index\/((\w+)\/)/', $url, $matches))
			{
				$return['debug'] = $matches;
				$return['forum_tab'] = $matches[2];
				$return['action'] = 'index';
			}
			elseif(preg_match('/^\/forum\/((\w+)\.php)/', $url, $matches))
			{
				$return['debug'] = $matches;
				$return['action'] = $actions[$matches[2]];
			}
			elseif(preg_match('/^\/forum\/index\/((\w+)\.php)/', $url, $matches))
			{
				$return['debug'] = $matches;
				$return['action'] = 'index';
				if(in_array($matches[2], $tab_actions))
				{
					$return['forum_tab'] = $matches[2];
				}
				else
				{
					$return['action'] = $matches[2];
				}
			}
			// /forum/action.php
			// /forum/category/action.php
			// /forum/category/sub_category/.../action.php
			elseif(preg_match('/^\/forum\/((\w+)\/)(\w+)\.php$/', $url, $matches))
			{
				$return['debug'] = $matches;
				$return['action'] = $actions[$matches[3]];
				$return['discussion_handle'] = $matches[2];
				if(in_array($matches[3], $tab_actions))
				{
					$return['forum_tab'] = $matches[3];
					$return['action'] = 'category_view';
					$return['category_main'] = $matches[2];
				}
			}
			elseif(preg_match('/^\/forum\/((\w+)\/)((\w+)\/)*(\w+)\.php$/', $url, $matches))
			{
				$return['debug'] = $matches;
				$return['action'] = $actions[$matches[5]];
				$return['discussion_handle'] = $matches[4];
				if(in_array($matches[5], $tab_actions))
				{
					$return['forum_tab'] = $matches[5];
					$return['action'] = 'category_view';
					$return['category_main'] = $matches[2];
					$return['category_sub'] = $matches[4];
				}
			}
			//Discussion view - catch page number
			// /forum/category/sub_category/discussion_handle/sida_3.html
			// /forum/category/sub_category/discussion_handle/discussion_handle2/sida_3.html
			elseif(preg_match('/^\/forum\/\w+\/\w+\/((\w+)\/)+sida_(\d+)\.html$/', $url, $matches))
			{
				$return['debug'] = $matches;
				$return['discussion_handle'] = $matches[2];
				$return['page_number'] = array_pop($matches);
				$return['action'] = 'discussion_view';
			}
			//Discussion view
			// /forum/category/sub_category/discussion_handle/
			// /forum/category/sub_category/discussion_handle/discussion_handle3/
			// /forum/category/sub_category/discussion_handle/discussion_handle3/discussion_handle4/
			elseif(preg_match('/^\/forum\/\w+\/\w+\/((\w+)\/)+$/', $url, $matches))
			{
				$return['debug'] = $matches;
				$return['action'] = 'discussion_view';
				$return['discussion_handle'] = $matches[2];
			}
			//Sub-category view
			// /forum/category/sub_category/
			elseif(preg_match('/^\/forum\/((\w+)\/)((\w+)\/)/', $url, $matches))
			{
				$return['debug'] = $matches;
				$return['category_main'] = $matches[2];
				$return['category_sub'] = $matches[4];
				$return['action'] = 'category_view';
			}
			//Category view
			// /forum/category/
			elseif(preg_match('/^\/forum\/((\w+)\/)/', $url, $matches))
			{
				$return['debug'] = $matches;
				$return['category_main'] = array_pop($matches);
				$return['action'] = 'category_view';
			}
		}
		//preint_r($return);
		return $return;
	}

	function forum_tag_cloud($tags = null)
	{
		if(!isset($tags))
		{
			$query = 'SELECT DISTINCT t.label, t.handle FROM tags AS t, object_tags AS o';
			$query .= ' WHERE t.id = o.tag_id AND o.object_type = "discussion"';
			$query .= ' ORDER BY t.popularity DESC LIMIT 75';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$count = 0;
			while($data = mysql_fetch_assoc($result))
			{
				if(!forum_get_parent_category($data['handle']))
				{
					$tags[] = $data;
					$count++;
				}
			}
			shuffle($tags);
			$tags = array_slice($tags, 0, 25);
			echo '<h3>Populära taggar</h3>' . "\n";
		}

		echo '<div class="forum_tag_cloud">' . "\n";
		echo '<ul>' . "\n";
		$class = 'odd';
		foreach($tags AS $tag)
		{
			echo '<li class="' . $class . '"><a href="/forum/' . $tag['handle'] . '/">' . $tag['label'] . '</a></li>' . "\n";
			$class = ($class == 'odd') ? 'even' : 'odd';
		}
		echo '</ul>' . "\n";
		echo '<br style="clear: both;" />' . "\n";
		echo '</div>' . "\n";
		echo "\n\n";
	}
	
	function forum_get_parent_category($category_sub)
	{
		/* $category subs recieves a handle */
		global $FORUM_CATEGORIES;
		foreach($FORUM_CATEGORIES as $handle => $forum_category)
		{
			if(array_key_exists($category_sub, $forum_category['sub_categories']))
			{
				return $handle;
			}
		}
		return false;
	}
	
	function forum_get_category_label($handle)
	{
		global $FORUM_CATEGORIES;
		return $FORUM_CATEGORIES[$handle]['label'];
	}
	
	function forum_comment_module($options)
	{
		/*
		Please supply a list of:
			fetch_tags
			create_tags
			main_category (this is a category handle and deafults to 'allmaent_om_hamsterpaj')
	
		*/
		$options['main_category'] = isset($options['main_category']) ? $options['main_category'] : 'allmaent_om_hamsterpaj';
	
		$fetch['type'] = 'forum';
		$fetch['tags'] = $options['fetch_tags'];
		$discussions = discussions_fetch($fetch);
		discussions_list($discussions);
		
		if(login_checklogin())
		{
			echo '<button onclick="this.style.display = \'none\'; document.getElementById(\'comment_discussion_create\').style.display = \'block\';">Starta en ny diskussion</button>' . "\n";
			echo '<div id="comment_discussion_create" style="display: none;">' . "\n";
			$post_form['mode'] = 'discussion_create';
			$post_form['action_url'] = '/forum/ny_diskussion.php';
			$post_form['category_handle'] = $options['main_category'];
			$post_form['tags'] = implode(', ', $options['create_tags']);
				
			posts_form($post_form);
			echo '</div>' . "\n";
		}
	}
	
?>
