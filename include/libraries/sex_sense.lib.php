<?php

	require_once(PATHS_LIBRARIES . 'schedule.lib.php');

	function sex_sense_parse_request($options)
	{
		$options['URI'] = (substr($options['URI'], 0, 1) == '/') ? substr($options['URI'], 1) : $options['URI'];
		$options['URI'] = (substr($options['URI'], -1) == '/') ? substr($options['URI'], 0, -1) : $options['URI'];

		$uri_parts = explode('/', $options['URI']);
		$last_uri_part_index = count($uri_parts) - 1;
		$second_last_uri_part_index = count($uri_parts) - 2;
		
		// Index (list categories)
		if(!isset($uri_parts[1]) || in_array($uri_parts[1], array('', 'index', 'index.php', 'start', 'index.html')))
		{
			return array('category' => 'index');
		}
		
		// Categories
		if($uri_parts[1] == 'kategorier.html')
		{
				return array('category' => 'list_categories');
		}
		
		// new question
		if($uri_parts[1] == 'ny_fraaga.html')
		{
				return array('category' => 'new_question');
		}
		
		// Save new question
		if($uri_parts[1] == 'spara_ny_fraaga.html')
		{
				return array('category' => 'save_new_question');
		}
		
		// Latest questions
		if($uri_parts[1] == 'senaste_fraagorna.html')
		{
				return array('category' => 'latest');
		}
		
		// fetch question
		if(substr($uri_parts[$last_uri_part_index], -5) == '.html' && preg_match('/^([a-zA-Z0-9_]+)$/', substr($uri_parts[$last_uri_part_index], 0, -5)))
		{
			return array('category' => 'view', 'question_handle' => substr($uri_parts[$last_uri_part_index], 0, -5));
		}
		
		// list questions in category
		if(preg_match('/^([a-zA-Z0-9_]+)$/', $uri_parts[$last_uri_part_index]))
		{
			return array('category' => 'view_category', 'category_id' => $uri_parts[$last_uri_part_index], 'category_handle' => $uri_parts[$last_uri_part_index]);
		}
		
		// Else throw an error
		throw new Exception('Adressen till sidan är felaktig, om du kopierade adressen kan det kanske vara så att glömt det sista tecknet eller något sånt. :(');
	}

	function sex_sense_fetch_posts($options)
	{
		$query = 'SELECT q.*, c.*';
		$query .= ' FROM sex_categories AS c, sex_questions AS q';

		$query .= ' WHERE q.category = c.category_id';
		$query .= (isset($options['is_answered']) && $options['is_answered'] == 0) ? '' : ' AND c.category_id != 0';
		$query .= ' AND q.is_removed = 0';

		$query .= (isset($options['is_answered']) && in_array($options['is_answered'], array(0, 1))) ? ' AND q.is_answered = ' . $options['is_answered'] : ' AND q.is_answered = 1';
		$query .= (isset($options['is_released']) && in_array($options['is_released'], array(0, 1))) ? ' AND q.is_released = ' . $options['is_released'] : '';
		$query .= (isset($options['id']) && is_numeric($options['id'])) ? ' AND q.id = ' . $options['id'] : '';
		$query .= isset($options['handle']) ? ' AND q.handle LIKE "' . str_replace('_', '\\_', $options['handle']) . '"' : '';
		$query .= isset($options['match_against']) ? ' AND MATCH (' . implode(', ', $options['match_against']['match']) . ') AGAINST ("' . $options['match_against']['against'] . '")' : '';

		$query .= isset($options['category_handle']) ? ' AND c.category_handle LIKE "' . str_replace('_', '\\_', $options['category_handle']) . '"' : '';
		$query .= isset($options['category_id']) ? ' AND c.category_id = "' . $options['category_id'] . '"' : '';

		$query .= (isset($options['order_by']) && isset($options['order']) && in_array($options['order'], array('ASC', 'DESC'))) ? ' ORDER BY ' . $options['order_by'] . ' ' . $options['order'] : '';
		$query .= (isset($options['limit']) && is_numeric($options['limit'])) ? ' LIMIT ' . $options['limit'] : '';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(!isset($options['ignore_no_posts_found_error']) && mysql_num_rows($result) < 1)
		{
			throw new Exception('Den frågan finns inte i systemet!');
		}
		
		while($data = mysql_fetch_assoc($result))
		{
			if ($data['is_answered'] == 1)
			{
				$query = 'SELECT sa.*, l.username FROM sex_answers AS sa, login AS l WHERE l.id = sa.user_id AND sa.answer_to = ' . $data['id'] . ' ORDER BY sa.timestamp ASC';
				$result_2 = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				while ($data_2 = mysql_fetch_assoc($result_2))
				{
					$data['answers'][] = $data_2;
				}
			}
			$return[] = $data;
		}
		
		return $return;
	}
	
	function sex_sense_render_posts($posts, $options = array())
	{
		foreach($posts AS $post)
		{ 
			$out .= sex_sense_bright_container_top();
				$out .= sex_sense_dark_container_top();
				$out .= '<h3 class="sex_sense_post_header" id="header_' . $post['id'] . '">' . $post['title'] . '</h3>' . "\n";
				$out .= sex_sense_dark_container_bottom();
					
				$out .= '<div class="' . ((isset($options['unhide_content']) && $options['unhide_content'] == true) ? 'content' : 'hidden_content') . '" id="content_' . $post['id'] . '">';
				$out .= '<p>' . nl2br($post['question']) . '</p>' . "\n";
				
				foreach($post['answers'] as $answer)
				{
					$out .= ui_avatar($answer['user_id']);
			  	$out .= sex_sense_bubble_top();
			  	$out .= '<h4>' . $answer['username'] . ' svarar:</h4>' . "\n";
			  	$out .= '<p>' . nl2br($answer['answer']);
			  	$out .= is_privilegied('sex_sense_admin') ? '<br /><a href="/sex_och_sinne/aendra_svar.php?id=' . $answer['id'] . '">Ändra svar.</a>' : '';
			  	$out .= '</p>' . "\n";
			  	$out .= sex_sense_bubble_bottom();
				}
			  
			  $direct_link = '/sex_och_sinne/';
			  
			  $categories = sex_sense_fetch_categories(array('category_id' => $post['category_id']));
			  foreach($categories as $category_tree)
			  {
			  	$category = array_pop($category_tree);
			  	$direct_link .= $category['category_handle'] . '/';
			  }
			  $direct_link .= $post['handle'] . '.html';
			  
			  $out .= '<button class="button_80" onclick="window.location=\'' . $direct_link . '\';">Direktlänk</button>' . "\n";			  
			  if($post['forum_post_id'] != 0)
			  {
			  	$thread_url = forum_get_url_by_post($post['forum_post_id']);
			  	$out .= '<button class="button_140" onclick="window.location=\'' . $thread_url . '\';">Diskutera i forumet</button>' . "\n";
			  }			
			
			  $out .= (is_privilegied('sex_sense_admin')) ? '<br /><a href="/sex_och_sinne/admin.php?id=' . $post['id'] . '">Ändra fråga/lägg till svar &raquo;</a>' : '';
			  
			  
				$out .= '<div style="clear: both; height: 5px;"></div>' . "\n";
				$out .= '</div>';
			$out .= sex_sense_bright_container_bottom();
			$closed_content++;
		}
		
		return $out;
	}
	
	function sex_sense_render_category($category)
	{
		$out .= sex_sense_bright_container_top();
		$out .= '<div class="category_list" />' . "\n";
		foreach($category as $category_data)
		{
			$out .= '<div class="category">' . "\n";
			$out .= '<h2>' . $category_data['category_title'] . '</h2>';
			$child_categories = array_pop(sex_sense_fetch_categories(array('parent_category' => $category_data['category_id'], 'skip_recursion' => true)));
			$out .= '<div class="category_children">' . "\n";
			foreach ($child_categories as $child_category)
			{
				$out .= '<h3><a href="/sex_och_sinne/' . $category_data['category_handle'] . '/' . $child_category['category_handle'] . '">' . $child_category['category_title'] . '</a> | </h3>';
			}
			$out .= '</div>' . "\n";
			$out .= '</div>' . "\n";
		}
		$out .= '<div style="clear: both;"></div>' . "\n";
		$out .= '</div>' . "\n";
		
		$out .= sex_sense_bright_container_bottom();
		
		return $out;
	}

	function sex_sense_fetch_categories($options, $category_tree = array())
	{
		$query = 'SELECT * FROM sex_categories WHERE 1';
		$query .= (isset($options['parent_category']) && is_numeric($options['parent_category'])) ? ' AND parent_category = ' . $options['parent_category'] : '';
		$query .= (isset($options['category_id']) && is_numeric($options['category_id'])) ? ' AND category_id = ' . $options['category_id'] : '';
		$query .= (isset($options['category_handle'])) ? ' AND category_handle LIKE "' . str_replace('_', '\\_', $options['category_handle']) . '"' : '';
		$query .= ' ORDER BY category_id ASC';
		
		$result = query_cache(array(
			'category' => 'sex_sense',
			'query' => $query
		));
		
		if(count($result) < 1)
		{
			throw new Exception('Kategorin existerar inte.');
		}
		
		$child_nodes = array();
		foreach($result as $data)
		{
			$child_nodes[] = $data;
		}
		
		array_unshift($category_tree, $child_nodes);
		
		return ($options['parent_category'] == 0 || (isset($options['skip_recursion']) && $options['skip_recursion'] == true)) ? $category_tree : sex_sense_fetch_categories(array('parent_category' => $category_tree[0]['id']), $category_tree);
	}
	
	function sex_sense_new_question_form()
	{
		$out .= sex_sense_bright_container_top();
		
		$out .= sex_sense_dark_container_top();
		$out .= '<h3>Regler</h3>' . "\n";
		$out .= sex_sense_dark_container_bottom();
		
		$out .= '<p>';
		$out .= 'Man får fråga om allt möjligt, det är bara att fråga på så svarar Entrero, Shedevil eller RoadGunner.';
		$out .= ' Det finns vissa regler för det här och de är egentligen rätt enkla.';
		$out .= '</p>';
		
		$out .= '<p>';
		$out .= 'Det ska vara seriösa frågor som ställs, och man får inte skriva massor av skräp och spam.';
		$out .= 'Sexperterna besvarar helst inte sådant som redan har besvarats så se gärna efter';
		$out .= 'om frågan inte redan har besvarats i en eller annan form genom att använda sökfunktionen';
		$out .= ' Enkelt, inte sant? :)';
		$out .= '</p>';

		$out .= '<p>';
		$out .= 'Hamsterpaj förbehåller sig rätt att ändra texten i frågorna så att det blir rättstavat och sådant.';
		$out .= ' Och du, <strong>det kan ta ett litet tag ibland innan man får svar på sin fråga</strong>.';
		$out .= '</p>';
		
		$out .= sex_sense_bright_container_bottom();
		
		$out .= sex_sense_bright_container_top();
		
		$out .= sex_sense_dark_container_top();
		$out .= '<h3>Ställ en fråga</h3>' . "\n";
		$out .= sex_sense_dark_container_bottom();
		
		$out .= '<div id="sex_and_sense_question_form">' . "\n";
		$out .= '<form action="/sex_och_sinne/spara_ny_fraaga.html" method="post">' . "\n";
		
		$out .= '<textarea style="width: 500px; height: 100px;" name="question"></textarea><br />' . "\n";
		$out .= '<input type="submit" value="Skicka" class="button_60" />' . "\n";
		
		$out .= '</form>' . "\n";
		$out .= '</div>' . "\n";
		
		$out .= sex_sense_bright_container_bottom();
		
		return $out;
	}
	
	function sex_sense_new_question_create($options)
	{
		if(isset($options['user_id']) && is_numeric($options['user_id']))
		{
			if(isset($options['question']))
			{
				if(strlen($options['question']) >= 20)
				{
					// A temporary handle...
					$handle = md5(uniqid(rand() + $_SESSION['login']['id']));
					$query = 'INSERT INTO sex_questions (user_id, timestamp, question, handle)';
					$query .= ' VALUES (' . $_SESSION['login']['id'] . ', ' . time() . ', "' . $_POST['question'] . '", "' . $handle . '")';
					
					mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					
					return mysql_insert_id();
				}
				else
				{
					throw new Exception('Din fråga var för kort. Alla frågor måste bestå av minst 20 tecken!');
				}
			}
			else
			{
				throw new Exception('Ingen fråga skcikad till sex_sense_new_question_create()!');
			}
		}
		else
		{
			throw new Exception('Inget user ID skickat till sex_sense_new_question_create()!');
		}
	}
	
	function sex_sense_schedule_add($options)
	{
		if(!isset($options['id']))
		{
			throw new Exception('Missing parameter id for sex_sense_schedule_add()!');
		}
		
		$release = schedule_release_get(array('type' => 'sex_sense'));
		$data = serialize(array(
			'fetch_item_options' => array('id' => $options['id'], 'ignore_no_posts_found_error' => true, 'is_released' => 0),
			'url' => $options['url'],
			'title' => $options['title']
		));

		schedule_event_add(array(
			'data' => $data,
			'type' => 'sex_sense',
			'release' => $release,
			'item_id' => $options['id']
		));
	}
	
	function sex_sense_answer_create($options)
	{
		// Fetch post to answer to
		$options['id'] = $options['answer_to'];
		$options['ignore_no_posts_found_error'] = true;
		$posts = sex_sense_fetch_posts($options);
		if(count($posts) < 1)
		{
			$options['is_answered'] = 0;
			$posts = sex_sense_fetch_posts($options);
		}
		$post = array_pop($posts);
		
		// Create answer in database
		$query = 'INSERT INTO sex_answers';
		$query .= ' (answer_to, timestamp, user_id, answer)';
		$query .= ' VALUES';
		$query .= ' (' . $options['answer_to'] . ', ' . time() . ', ' . $options['user_id'] . ', "' . $options['answer'] . '")';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		$answer_id = mysql_insert_id();
		
		
		$post['is_answered'] = 1;
		$query = 'UPDATE sex_questions SET is_answered = 1, last_answer = ' . time() . ' WHERE id = ' . $post['id'];
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		// Distribute answer if released, if not, tell schedule how to do that later :P
		if($post['is_released'] == 1)
		{
			sex_sense_answer_distribute(array('post_id' => $post['id'], 'answer_id' => $answer_id));
		}
	}
	
	function sex_sense_answer_distribute($options)
	{
		$options['id'] = $options['post_id'];
		$options['ignore_no_posts_found_error'] = true;
		$posts = sex_sense_fetch_posts($options);
		if(count($posts) < 1)
		{
			$options['is_answered'] = 0;
			$posts = sex_sense_fetch_posts($options);
		}
		$post = array_pop($posts);
		
		if($post['forum_post_id'] == 0)
		{
			return false;
		}
		
		foreach($post['answers'] as $answer)
		{
			if((isset($options['answer_id']) && $answer['id'] == $options['answer_id']) || !isset($options['answer_id']))
			{
				$forum_post['forum_id'] = '102';
				$forum_post['author'] = $answer['user_id'];
				$forum_post['parent_post'] = $post['forum_post_id'];
				$forum_post['content'] = $answer['answer'];
				discussion_forum_post_create($forum_post);
				
				$direct_link = '/sex_och_sinne/';
			  $categories = sex_sense_fetch_categories(array('category_id' => $post['category_id']));
			  foreach($categories as $category_tree)
			  {
			  	$category = array_pop($category_tree);
			  	$direct_link .= $category['category_handle'] . '/';
			  }
			  $direct_link .= $post['handle'] . '.html';
				
				$entry['recipient'] = $post['user_id'];
				$entry['sender'] = 2348;
				$entry['is_private'] = 1;
				$message = 'En av dina frågor i Sex och sinne är besvarad.' . "\n"; 
				$message .= 'Klicka här för att komma till frågan :) ' . "\n";
				$message .= '<a href="' . $direct_link . '">http://hamsterpaj.net' . $direct_link . '</a>' . "\n";
				$entry['message'] = mysql_real_escape_string($message);
				guestbook_insert($entry);
			}
		}
	}
	
	function sex_sense_update_handle($options)
	{
		for($i = 0; ($i > 150 || !($result = @mysql_query('UPDATE sex_questions SET handle = "' . url_secure_string($options['title_to_serialize']) . (($i != 0) ? '_' . $i : '' ) . '" WHERE id = ' . $options['id']))); $i++){ }
	}
?>