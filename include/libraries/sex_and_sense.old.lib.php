<?php
	function sex_sense_request($url)
	{
		$request['action'] = 'index';
		if(!empty($_GET['answer_question']))
		{
			$request['action'] = 'compose_answer';
			$request['question_id'] = $_GET['answer_question'];
		}
		elseif($url == '/sex_och_sinne/ny.php')
		{
			if(isset($_POST['title'], $_POST['question']))
			{
				$request['action'] = 'create';				
			}
			else
			{
				$request['action'] = 'compose';
			}
		}
		elseif(substr($url, -5) == '.html')
		{
			$request['handle'] = substr($url, strrpos($url, '/')+1, -5);
			$request['action'] = 'view_entry';
		}
		elseif($url == '/sex_och_sinne/' || $url == '/sex_och_sinne')
		{
			$request['action'] = 'index';
		}
		elseif ($url == '/sex_och_sinne/nya_fraagor.php')
		{
			$request['action'] = 'answer_index';
		}
		else
		{
			$request['action'] = 'category_index';
			$request['category_handle'] = substr($url, 15, -1);
		}

		return $request;
	}
	function render_sex_sense_question($questions, $opt)
	{
		foreach ($questions AS $question)
		{
			$return .= '<div id="sex_sense_question_' . $question['id'] . '">' . "\n";
			$return .= ($opt['rounded_corners']) ? rounded_corners_top($void, true) : '';
			$return .= '<h3>' . $question['title'] . '</h3>' . "\n";
			$return .= '<p>' . $question['question'] . '</p>' . "\n";
			if ($opt['show_answer_textarea'])
			{
				$return .= '<form action="/sex_och_sinne/nya_fraagor.php" method="post">' . "\n";
				$return .= '<input type="hidden" name="id" value="' . $question['id'] . '" />' . "\n";
				$return .= '<textarea style="width: 550px; height: 200px;" name="answer" id="answer">' . "\n";
				$return .= '</textarea>' . "\n";
				$return .= '<br /><input type="submit" value="Svara" class="button_60" />' . "\n";
				$return .= '</form>' . "\n";
			}
			$return .= ($opt['rounded_corners']) ? rounded_corners_bottom($void, true) : '';
			$return .= '</div>';
		}
		
		return $return;
	}
	
	
	function sex_sense_form($entry)
	{
		$output .= '<h2>' . (isset($entry['id']) ? 'Uppdatera fråga' : 'Lägg in en ny fråga') . '</h2>' . "\n";
		$output .= '<form id="sex_sense_form" method="post">' . "\n";
		$output .= '<label for="sex_sense_title">Rubrik</label>' . "\n";
		$output .= '<input id="sex_sense_title" name="title" type="text" class="sex_sense_title_input" value="' . $entry['title'] . '" />' . "\n";
		$output .= '<label for="sex_sense_question">Fråga</label>' . "\n";
		$output .= '<textarea id="sex_sense_question" name="question">' . $entry['question']. '</textarea>' . "\n";
		$output .= '<label for="sex_sense_answer">Svar</label>' . "\n";
		$output .= '<textarea id="sex_sense_answer" name="answer">' . $entry['answer']. '</textarea>' . "\n";
		
		
		$output .= '<h3>Kategori</h3>' . "\n";
		$output .= '<ul>' . "\n";
		$query = 'SELECT DISTINCT(category) FROM sex_sense ORDER by category ASC';
		$result = mysql_query($query);
		while($data = mysql_fetch_assoc($result))
		{
			$output .= '<li><input type="radio" name="category" value="' . $data['category'] . '" id="sex_sense_category_' . $data['category'] . '" />' . "\n";
			$output .= '<label for="sex_sense_category_' . $data['category'] . '">' . $data['category'] . '</label></li>' . "\n";
		}
		$output .= '<li><input type="radio" name="category" value="freetext" /><input type="text" name="category_freetext" /></li>' . "\n";
		$output .= '</ul>' . "\n";
		
		if(!isset($entry['id']))
		{
			$output .= '<label for="sex_sense_release">Tid för publicering</label><br />' . "\n";
			$output .= '<input id="sex_sense_release" name="release" type="text" value="' . date('Y-m-d H:i', schedule_release_get(array('type' => 'sex_sense'))) . '" /><br />' . "\n";
		}

		$output .= '<input type="submit" value="Spara" class="button_60" />' . "\n";
		$output .= '</form>' . "\n";
		
		return $output;
	}
	
	function sex_sense_fetch($options)
	{
		$query = 'SELECT ss.id, ss.handle, ss.title, ss.question, ss.answer, ss.category, ss.forum_url';
		$query .= ' FROM sex_sense AS ss';
		$query .= ' WHERE 1';
		$query .= (isset($options['handle'])) ? ' AND ss.handle LIKE "' . $options['handle'] . '"' : '';
		$query .= (isset($options['id'])) ? ' AND ss.id = "' . $options['id'] . '"' : '';
		$query .= (isset($options['category'])) ? ' AND ss.category LIKE "' . $options['category'] . '"' : '';
		$query .= ' ORDER BY ss.title ASC';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			$entries[] = $data;
		}
		
		return $entries;
	}
	
	function sex_sense_render($entry)
	{
		$output .= '<h1>'.  $entry['title'] . '</h1>' . "\n";
		$output .= '<p class="sex_sense_question">' . nl2br($entry['question']) . '</p>' . "\n";
		$output .= '<p class="sex_sense_answer"> </p>' . "\n";
		
		if(strlen($entry['forum_url']) > 0)
		{
			$output .= '<p class="sex_sense_forum_link"></a></p>' . "\n";
		}
		
		return $output;
	}

	function sex_sense_list($entries)
	{
		$output .= '<ul class="sex_sense_list">' . "\n";
		foreach($entries AS $entry)
		{
			$url = '/sex_och_sinne/' . $entry['category'] . '/' . $entry['handle'] . '.html';
			$question = (strlen($entry['question']) > 200) ? substr($entry['question'], 0, 197) . '...' : $entry['question'];			
			
			$output .= '<li>' . "\n";
			$output .= '<h3><a href="' . $url . '">' . $entry['title'] . '</a></h3>' . "\n";
			$output .= '<p><a href="' . $url . '">' . $question . '</a></p>' . "\n";
			$output .= '</li>' . "\n";
		}
		$output .= '</ul>' . "\n";
		
		return $output;
	}
	
	function sex_sense_create($entry)
	{
		$handle = url_secure_string($entry['title']);
		for($i = 2; count(sex_sense_fetch(array('handle' => $handle))) > 0; $i++)
		{
			$handle = url_secure_string($entry['title']) . '_' . $i;
		}
		
		$entry['category'] = ($entry['category'] == 'freetext') ? url_secure_string($entry['category_freetext']) : $entry['category'];
		
		$query = 'INSERT INTO sex_sense (title, handle, category, question, answer, forum_url)';
		$query .= ' VALUES("' . $entry['title'] . '", "' . $handle . '", "' . $entry['category'] . '", "' . $entry['question'] . '", "' . $entry['answer'] . '", "")';

		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$entry['id'] = mysql_insert_id();
		/* WTF!?!?!
		$post['content'] = $entry['question'] . "\n\n\n[b]Reyhaneh svarar:[/b]\n" . $entry['answer'] . "\n\n\nDu hittar fler frågor kring Sex & Sinne om du klickar på [Sex och sinne] i menyn!";
		$post['title'] = $entry['title'];
		$post['forum_id'] = 19;
		$post['mode'] = 'new_thread';
		$post['author'] = 823261;
		
		$thread_id = discussion_forum_post_create($post);
		$forum_url = forum_get_url_by_post($thread_id);
		*/
		
		$query = 'UPDATE sex_sense SET forum_url = "' . $forum_url . '" WHERE id = "' . $entry['id'] . '"';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		return $entry['id'];
	}
	
?>
