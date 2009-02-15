<?php

	function poll_fetch($options)
	{
		$options['limit'] = (is_numeric($options['limit'])) ? $options['limit'] : 1;
		
		$query = 'SELECT poll.*';
		$query .= (login_checklogin()) ? ', poll_answers.answer_id' : '';
		$query .= ' FROM poll';
		$query .= (login_checklogin()) ? ' LEFT OUTER JOIN poll_answers ON poll.id = poll_answers.poll_id AND poll_answers.user_id = "' . $_SESSION['login']['id'] . '"' : '';
		$query .= ' WHERE 1';
		$query .= (isset($options['id']) && is_numeric($options['id'])) ? ' AND poll.id = "' . $options['id'] . '"' : '';
		$query .= (isset($options['author'])) ? ' AND poll.author = "' . $options['author'] . '"' : '';
		$query .= (isset($options['handle'])) ? ' AND poll.handle = "' . $options['handle'] . '"' : '';		
		$query .= (isset($options['type'])) ? ' AND poll.type = "' . $options['type'] . '"' : '';
		$query .= ' ORDER BY poll.id DESC';		
		$query .= ' LIMIT ' . $options['limit'];
		
		//if(!login_checklogin())
		if(false)
		{
			$poll = query_cache(array('query' => $query, 'type' => 'poll'));
		}
		else
		{
			$result = mysql_query($query) or report_sql_error($query);
			while($data = mysql_fetch_assoc($result))
			{
				$poll[] = $data;
			}
		}

		foreach($poll AS $id => $current)
		{
			if(login_checklogin())
			{
				$poll[$id]['can_answer'] = ($current['answer_id'] > 0) ? false : true;
			}
			elseif($current['force_logon'] == 0)
			{
				if($_COOKIE['poll_' . $current['id']] == true)
				{
					$poll[$id]['can_answer'] = false;
				}
				else
				{
					$poll[$id]['can_answer'] = true;
				}
			}
			else
			{
				$poll[$id]['can_answer'] = false;
			}
		}
		
		return $poll;
	}

	function poll_render($poll)
	{
		global $survey_chart_colors;
		$output .= '<div class="poll_container">' . "\n";
		
		$img = 'http://www.hamsterpaj.net/dynamic_images/poll_chart.php?';
		for($i = 1; $i <= 7; $i++)
		{
			if(strlen($poll['alt_' . $i]) > 0)
			{
				$img .= '&alt_' . $i . '=' . $poll['alt_' . $i . '_votes'];
				$total_votes += $poll['alt_' . $i . '_votes'];
			}
		}
		$img .= '&cache_prevention=' . rand();
		
		$output .= '<h2>' . $poll['question'] . ' (<span id="poll_' . $poll['id'] . '_vote_count">' . $total_votes . '</span> röster)</h2>' . "\n";
		$output .= '<div class="graph_area">' . "\n";
		if($total_votes > 0)
		{
			$output .= '<img src="' . $img . '" id="poll_' . $poll['id'] . '_chart" />' . "\n";
		}
		else
		{
			$output .= '<img id="poll_' . $poll['id'] . '_chart" style="display: none;"/>' . "\n";
			$output .= '<h3 id="poll_' . $poll['id'] . '_no_votes">Ingen har svarat än</h3>' . "\n";
		}
		$output .= '</div>' . "\n";
		
		$output .= '<form class="poll_form" name="poll_' . $poll['id'] . '" id="poll_form_' . $poll['id'] . '">' . "\n";
		$output .= '<input type="hidden" name="poll_id" value="' . $poll['id'] . '" />' . "\n";
		if(strlen($poll['description']) > 0)
		{
			$output .= '<p>' . $poll['description'] . '</p>' . "\n";
		}
		$disabled = ($poll['can_answer'] == true) ? '' : ' disabled';
		$output .= '<ul>' . "\n";
		for($i = 1; $i <= 7; $i++)
		{
			if(strlen($poll['alt_' . $i]) > 0)
			{
				$output .= '<li>' . "\n";
				$output .= '<div class="poll_color_index" style="background: ' . $survey_chart_colors[$i-1] . ';">' . "\n";
				$output .= '<input type="radio" name="poll_' . $poll['id'] . '_answer" value="' . $i . '" id="poll_' . $poll['id'] . '_input_' . $i . '"' . $disabled . ' />' . "\n";
				$output .= '</div>' . "\n";
				$output .= '<label for="poll_' . $poll['id'] . '_input_' . $i . '"' . $disabled . '>' . $poll['alt_' . $i] . '</label>' . "\n";
				$output .= '</li>' . "\n";
			}
		}
		$output .= '</ul>' . "\n";

		if($poll['can_answer'] == true)
		{
			$output .= '<input type="submit" value="Svara" id="poll_' . $poll['id'] . '_submit" />' . "\n";
		}

		$output .= '</form>' . "\n";
		$output .= '<br style="clear: both;" />' . "\n";
		$output .= '</div>' . "\n";
		
		return $output . "\n";
	}
	
	function poll_form($poll)
	{
		$output .= '<form method="post" action="/poll/ny_poll.php" class="poll_create_form">' . "\n";
		$output .= '<div class="column_1">' . "\n";
		$output .= '<h5>Vilken fråga vill du ställa?</h5>' . "\n";
		$output .= '<input type="text" name="question" class="text_input" />' . "\n";
		$output .= '<h5>Vill du skriva en förklarande text?</h5>' . "\n";
		$output .= '<textarea name="description" class="text_input"></textarea>' . "\n";
		
		$output .= '<h5>Vilka alternativ ska finnas?</h5>' . "\n";
		$output .= '<ol>' . "\n";
		for($i = 1; $i <= 7; $i++)
		{
			$output .= '<li><input type="text" name="alt_' . $i . '" class="text_input" /></li>' . "\n";
		}
		$output .= '</ol>' . "\n";
		$output .= '</div>' . "\n";
		$output .= '<div class="column_2">' . "\n";				
		if(is_privilegied('frontpage_poll_admin'))
		{
			$output .= '<h5>Vad är det för sorts undersökning?</h5>' . "\n";
			$output .= '<select name="type">' . "\n";
			$output .= '<option value="user" onclick="document.getElementById(\'poll_release_control\').style.display = \'none\';">Egen undersökning</option>' . "\n";
			$output .= '<option value="daily" onclick="document.getElementById(\'poll_release_control\').style.display = \'block\';">Dagens fråga på förstasidan</option>' . "\n";
			$output .= '</select>' . "\n";
			
			$output .= '<div id="poll_release_control">' . "\n";
			$output .= '<h5>När skall undersökningen publiceras?</h5>' . "\n";
			$output .= '<input type="text" name="release" value="' . date('Y-m-d H:i', schedule_release_get(array('type' => 'poll'))) . '" />' . "\n";
			$output .= '</div>' . "\n";
		}
		
		$output .= '<h5>Vilka får svara?</h5>' . "\n";
		$output .= '<input name="force_logon" value="1" type="radio" id="poll_form_force_logon_true" />' . "\n";
		$output .= '<label for="poll_form_force_logon_true">Låt bara Hamsterpaj-medlemmar svara</label><br />' . "\n";
		$output .= '<input name="force_logon" value="0" type="radio" id="poll_form_force_logon_false" />' . "\n";
		$output .= '<label for="poll_form_force_logon_false">Låt alla svara, även besökare</label><br />' . "\n";

		$output .= '<input type="submit" value="Skapa undersökning" class="button_140" />' . "\n";
		$output .= '</div>' . "\n";
		$output .= '<br style="clear: both;" />' . "\n";
		$output .= '</form>' . "\n";
		
		return $output;
	}
	
	function poll_create($poll)
	{
		$handle = url_secure_string($poll['question']);
		if(empty($handle))
		{
			$handle = md5(time() . rand(0, 9999));
		}
		
		for($i = 1; $i < 100; $i++)
		{
			$query = 'SELECT id FROM poll WHERE handle LIKE "' . $handle . '" LIMIT 1';
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 0)
			{
				break;
			}
			$handle = url_secure_string($poll['question']) . '_' . $i;
		}
		if ( !is_privilegied('frontpage_poll_admin') && $poll['type'] == 'daily' )
		{
			header('Location: /login/logout.php');
			exit;
			exit;
			exit; // !!!
		}
		$query = 'INSERT INTO poll (handle, question, description, author, type, force_logon, timestamp, alt_1, alt_2, alt_3, alt_4, alt_5, alt_6, alt_7)';
		$query .= ' VALUES("' . $handle . '", "' . $poll['question'] . '", "' . $poll['description'] . '", "' . (($poll['type'] == 'daily') ? 2348 : $_SESSION['login']['id']) . '", "' . $poll['type'] . '", "' . $poll['force_logon'] . '", "' . time() . '"';

		for($i = 1; $i <= 7; $i++)
		{
			$query .= ', "' . $poll['alt_' . $i] . '"';
		}
		$query .= ')';
		
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		return $handle;
	}
	
	function poll_get_action($url)
	{
		if($url == '/poll/ny_poll.php')
		{
			if(strlen($_POST['question']) > 0)
			{
				$return['action'] = 'create';
				$return['poll'] = $_POST;			
			}
			else
			{
				$return['action'] = 'compose';
			}
		}
		elseif(substr($url, -5) == '.html')
		{
			$return['poll_handle'] = substr($url, strrpos($url, '/')+1, -5);
			$poll = poll_fetch(array('handle' => $return['poll_handle']));
			if(count($poll) != 1)
			{
				$return['action'] = 'poll_not_found';
			}
			else
			{
				$return['action'] = 'view_poll';
				$return['poll'] = $poll[0];
			}
		}
		else
		{
			$return['action'] = 'index';
		}
		return $return;
	}

?>