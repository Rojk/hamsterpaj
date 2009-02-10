<?php
	// Note: 'vuxenpoaeng.html' is turned off on line 350.
	include('../include/core/common.php');
	require(PATHS_LIBRARIES . 'rank.lib.php'); // The comment library needs this for some reason..
	require(PATHS_LIBRARIES . 'comments.lib.php'); // Stylesheets ans javascripts are loaded in tests_calculate_score().
	
	$ui_options['title'] = 'Tester, på Hamsterpaj';
	$ui_options['menu_path'] = array('traeffa', 'tester');
	//$ui_options['menu_addition']['traeffa']['children']['tester']['children']['skapa_test'] = array('label' => 'Skapa test', 'url' => '/tests/create.html');

	
	// .htaccess fix
	if(!isset($_GET['page']) && isset($_SERVER['REQUEST_URI']) && substr($_SERVER['REQUEST_URI'], 0, 7) == '/tests/')
	{
	 $uri = substr($_SERVER['REQUEST_URI'], 7);
	 $uri_parts = explode('?', $uri, 2);
	 $_GET['page'] = mysql_real_escape_string($uri_parts[0]);
	}
	
	$page = (isset($_GET['page']) && !empty($_GET['page'])) ? $_GET['page'] : 'index.html';
	$page = (substr($page, -1, 1) == '/') ? substr($page, 0, -1) : $page;
	
	switch($page)
	{
		case 'index.html':
		case 'index.php':
			tests_index();
			break;

		case 'create.html':
			tests_create_test();
			break;
			
		case 'save.html':
			event_log_log('test_create');
			tests_save_test();
			break;
			
		case 'list_my_tests.html':
			tests_list_my_tests();
			break;

		default:
			if(substr($page, -5, 5) == '.html' && preg_match('/^([a-z0-9_])+$/', substr($page, 0, -5)))
			{
				event_log_log('test_view');
				tests_do_test(array('handle' => substr($page, 0, -5)));
			}
			
			elseif(substr($page, -5, 5) == '.html' && preg_match('/^([a-z0-9_]+)\/user_answers\/([0-9]+)\.html$/', $page, $matches))
			{
				tests_view_user_answers(array('handle' => $matches[1], 'user_id' => (int) $matches[2]));
			}
			
			elseif(substr($page, -10, 10) == '/save.html' && preg_match('/^([a-z0-9_])+$/', substr($page, 0, -10)))
			{
				event_log_log('test_complete');
				tests_calculate_score(array('handle' => substr($page, 0, -10)));
			}
			
			
			elseif(substr($page, -16, 16) == '/list_users.html' && preg_match('/^([a-z0-9_])+$/', substr($page, 0, -16)))
			{
				tests_list_users(array('handle' => substr($page, 0, -16)));
			}
			
			
			else
			{
				tests_index(array('error_message' => 'Sådär trixar vi inte!'));
			}
	}
	
	function tests_index($options = array())
	{
		/*
			create_ui <-- If we should run ui_top, or not. Default: true
			error_message
		*/
		if(isset($options['create_ui']) || $options['create_ui'] == false)
		{
			global $ui_options;
			$ui_options['stylesheets'][] = 'tests.css';
			ui_top($ui_options);
		}
		
		echo '<h1>Tester</h1>';

		if(isset($options['error_message']) && $options['error_message'] == 0)
		{
			echo rounded_corners_top(array('color' => 'orange'));
				echo 'Fel: ' . $options['error_message'];
			echo rounded_corners_bottom(array('color' => 'orange'));
		}
		
		echo '<p>';
		echo '<a href="/tests/create.html"><img src="' . IMAGE_URL . 'tests/create_ad.png?destroy_cache" alt="Skapa test!" /></a>';
		echo '</p>';
		
		echo '<br />';
		
		echo '<h2>De fem senaste testerna</h2>';
		echo '<p>';
		
		$query  = 'SELECT l.id AS userid, l.username, t.title, t.description, t.handle';
		$query .= ' FROM tests AS t, login AS l';
		$query .= ' WHERE t.author = l.id';
		$query .= ' ORDER BY t.id DESC';
		$query .= ' LIMIT 5';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		while($data = mysql_fetch_assoc($result))
		{
			
			
			echo rounded_corners_top();
			
			echo '<br />';
			echo '<a href="/tests/' . $data['handle'] . '.html" class="tests_index_latest_link">' . $data['title'] . '</a><br />' . "\n";
			
			echo 'Skapat av <a href="/traffa/profile.php?id=' . $data['userid'] . '">' . $data['username'] . '</a><br />' . "\n";
			echo 'Beskrivning: ' . $data['description'] . "\n";
			
			echo '<br /><br />';
			
			echo rounded_corners_bottom();
		}
		
		echo '</p>';	
	}
	
	function tests_create_test()
	{
		global $ui_options;
		$ui_options['javascripts'][] = 'tests.js';
		$ui_options['stylesheets'][] = 'tests.css';
		ui_top($ui_options);
		
		echo rounded_corners_top(array('color' => 'green'));
		echo 'Du kan också <a href="/tests/index.php">gå till testernas förstasida</a>.';
		echo rounded_corners_bottom(array('color' => 'green'));
		
		if(login_checklogin())
		{
			//echo 'Running tests_create_test()...' . serialize(array(array('A 1', 1), array('A 2', 2), array('A 3', 3)));
			echo '<h2>Skapa test</h2>' . "\n";
			echo rounded_corners_top(array('color' => 'white'));
	
			echo '<div id="tests_create_title_label_holder"><label for="tests_create_title" id="tests_create_title_label">Namn:</label></div>';
			echo '<input type="text" id="tests_create_title" maxlength="80" />' . "\n";
			
			echo '<div id="tests_create_author_label">Skapare:</div><div id="tests_create_author">' . $_SESSION['login']['username'] . '</div>' . "\n";

			echo '<div id="tests_create_description_label_holder"><label for="tests_create_description">Beskrivning:</label></div>' . "\n";
			echo '<textarea id="tests_create_description"></textarea>' . "\n";
			
			echo '<br style="clear: both" />';
	
			echo rounded_corners_bottom(array('color' => 'white'));

			echo '<div id="tests_create_actions">&nbsp;</div><br />' . "\n";
			echo '<button id="tests_create_add_question" class="button_110">Lägg till fråga...</button><br />' . "\n";
			echo '<button id="tests_create_save" class="button_60">Spara</button>';
		
		echo '<br /><br />';
			
		echo rounded_corners_top();
		echo '<br /><h2>Hur fungrar det här nu då?</h2>';
		echo 'Börja med att lägga till en fråga genom att trycka på [Lägg till fråga...]-knappen. Det bör dyka upp en ruta. I den kan man sedan fylla i en fråga och ett antal svar. Med <button class="button_20">X</button>-knapparna tar du bort frågor eller svar.<br /><br />';
		echo 'Efter det är det dags att välja om man skall kunna svara med ett (<input type="radio" />) eller flera (<input type="checkbox" />) svar.<br />';
		echo 'Till sist skall du poängsätta dina svar. Om en person sedan väljer ett svar med 5 poäng, läggs 5 poäng på till slutpoängen personen får när han/hon gjort testet.<br /><br />';
		echo rounded_corners_bottom();
			
		}
		else
		{
			echo 'Om du <a href="/register.php">blir medlem</a> kan du också skapa tester!';
		}
		
		if(isset($_GET['johans_haxx']))
		{
			echo '<div class="score_meter"><div class="color_1" style="width: 60px">&nbsp;</div></div><br />';
			echo '<div class="score_meter"><div class="color_2" style="width: 120px">&nbsp;</div></div><br />';
			echo '<div class="score_meter"><div class="color_3" style="width: 180px">&nbsp;</div></div><br />';
			echo '<div class="score_meter"><div class="color_4" style="width: 240px">&nbsp;</div></div><br />';
			echo '<div class="score_meter"><div class="color_5" style="width: 300px">&nbsp;</div></div><br />';
		}
		
	}
	
	function tests_save_test($options)
	{		
		$options['source'] = isset($options['source']) ? $options['source']  : $_POST;
		$_S = $options['source'];
		
		//preint_r($_S);
		
		if(!isset($options['author']) && !login_checklogin()){
			/*tests_index(array('error_message' => */ die('Du måste vara inloggad för att kunna skapa tester!');/*));return;*/
		}
		else
		{
			$options['author'] = (isset($options['author']) ? $options['author'] : $_SESSION['login']['id']);
		}
		
		if(!isset($_S['title'], $_S['description'], $_S['questions_length'])){ /*tests_index(array('error_message' => */ die('Kunde ej identifiera titel/beskrivning/antal frågor.');/*));return;*/ }
		if(!is_numeric($_S['questions_length']) || empty($_S['title']) || (int) $_S['questions_length'] < 0 || (int) $_S['questions_length'] > 60){ /*tests_index(array('error_message' => '*/ die('Ursäkta, men är Ni möjligtvis en hacker?');/*));return;*/ }

		$questions = array();
		for($question = 0; $question < (int) $_S['questions_length']; $question++)
		{
			if(isset($_S['question_' . $question . '_label'], $_S['question_' . $question . '_answer_type'], $_S['question_' . $question . '_answer_length']) && (int) $_S['question_' . $question . '_answer_length'] > 0 && (int) $_S['question_' . $question . '_answer_length'] < 31 && in_array($_S['question_' . $question . '_answer_type'], array('single_answer', 'multiple_answers')))
			{
				$questions[$question] = array('answers' => array(), 'label' => $_S['question_' . $question . '_label'], 'answer_type' => $_S['question_' . $question . '_answer_type']);
				for($answer = 0; $answer < (int) $_S['question_' . $question . '_answer_length']; $answer++){
					$questions[$question]['answers'][$answer] = array('answer' => $_S['question_' . $question . '_answer_' . $answer . '_answer'], 'score' => (int) $_S['question_' . $question . '_answer_' . $answer . '_score']);
				}
			}
			else
			{
			}
		}
		
		// Make sure we at least have one valid question:
		if(count($questions[0]['answers']) > 0)
		{
			$handle = tests_generate_test_handler($_S['title']);
			
			$query  = 'INSERT INTO tests (author, handle, title, description) VALUES (' . $options['author'];
			$query .= ', "' . $handle . '"';
			$query .= ', "' . $_S['title'] . '"';
			$query .= ', "' .  $_S['description']. '")';
			if(mysql_query($query))
			{
				$test_id = mysql_insert_id();
				$questions_to_insert = array();
				
				foreach($questions as $question_index => $question)
				{
					if(count($question['answers']) > 0)
					{
						$answer_save_array = array();
						foreach($question['answers'] as $answer){
							// Format: [0] = answer, [1] = points (score).
							$answer_save_array[] = array(0 => $answer['answer'], 1 => $answer['score']);
						}
						$answer_save_string = serialize($answer_save_array);

						// Format: test_id, question, answers (phpserialized), answer_type
						$questions_to_insert[] = '(' . $test_id . ', "' . $question['label'] . '", "' . mysql_real_escape_string($answer_save_string) . '", "' . $question['answer_type'] . '")';
					}
				}
				
				$query = 'INSERT INTO tests_questions (test_id, question, answers, answer_type) VALUES ' . implode(', ', $questions_to_insert);

				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
				//global $ui_options;
				//ui_top($ui_options);
				
				die('success:Testet skapades utan problem!');
				
				//jscript_alert('Testet skapat!');
				//jscript_location('/tests/');
			}
			else
			{
				//global $ui_options;
				//ui_top($ui_options);
				
				die('Tryck F5 i din webbläsare. Om den här texten kommer upp igen så skriv en rad i bugg-forumet. (Handle error)');
						
				//report_sql_error($query, __FILE__, __LINE__);
			}
			

		}
		else
		{		
			/*tests_index(array('error_message' => */ die('Förmodligen så hackade du, för du slapp igenom första kontrollen men inte andra!'); /*));return; */
		}
		die(); // !!!
	}
	
	function tests_generate_test_handler($title)
	{
		// In case of escaped HTML and so on...
		if(strlen($title) > 50)
		{
			$title = substr($title, 0, 50);
		}
	
		$secured_string = url_secure_string($title);
		$handle = $secured_string;
		$query = 'SELECT id FROM tests WHERE handle LIKE "' . $handle . '" LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		for($i = 2; mysql_num_rows($result) > 0; $i++)
		{
			$handle = $secured_string . '_' . $i;
			$query = 'SELECT id FROM tests WHERE handle LIKE "' . $handle . '" LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);			
		}
		return $handle;
	}
	
	
	
	function tests_do_test($options)
	{
		/*
			handle
		*/
		if(!isset($options['handle'])){ tests_index(array('error_message' => 'Hittade inget "handle" i funktionsanroppet.'));return; }
		if($options['handle'] == 'vuxenpoaeng'){ tests_index(array('error_message' => 'Detta test är stängt.'));return; }
		
		$query  = 'SELECT t.id, t.handle, t.title, t.description, t.author AS author_userid, l.username AS author_username';
		$query .= ' FROM tests AS t, login AS l';
		$query .= ' WHERE t.handle = "' . $options['handle'] . '" AND t.author = l.id';
		$query .= ' LIMIT 1';
		
		$result = mysql_query($query) or report_sql_error($query);//, __FILE__, __LINE__);
		if(mysql_num_rows($result) == 0){ tests_index(array('error_message' => 'Kunde inte hitta testet.'));return; }

		$test_info = mysql_fetch_assoc($result);
		
		$query  = 'SELECT question_id, question, answers, answer_type';
		$query .= ' FROM tests_questions';
		$query .= ' WHERE test_id = ' . $test_info['id'];
		
		$result = mysql_query($query) or report_sql_error($query);//, __FILE__, __LINE__);
		
		$test_questions = array();
		while($data = mysql_fetch_assoc($result))
		{
			$test_questions[] = $data;
		}
		
		/*
			$test_info = information about the test
			$test_questions = all questions and a serialized version of the answers and their points.
		*/
		
		global $ui_options;
		ui_top($ui_options);
		
		echo rounded_corners_top(array('color' => 'green'));
		echo 'Du kan också <a href="/tests/index.php">gå till testernas förstasida</a> eller <a href="/tests/create.html">skapa ett eget test</a>.';
		echo rounded_corners_bottom(array('color' => 'green'));
		
		echo '<h1>Gör test</h1>';
		
		
		echo rounded_corners_top(array('color' => 'blue'));
			echo 'Namn: ' . $test_info['title'] . "<br />\n";
			echo 'Skapare: <a href="/traffa/profile.php?id=' . $test_info['author_userid'] . '">' . $test_info['author_username'] . '</a>' . "<br />\n";
			echo 'Länk: http://www.hamsterpaj.net/tests/' . $test_info['handle'] . '.html' . "<br />\n";
			echo 'Beskrivinig: ' . $test_info['description'];
		echo rounded_corners_bottom(array('color' => 'blue'));
		
		echo '<form method="post" action="/tests/' . $test_info['handle'] . '/save.html" id="tests_save_answer_form">' . "\n";
		
		foreach($test_questions as $question)
		{
			echo '<h2>' . $question['question'] . '</h2>' . "\n";
			foreach(unserialize($question['answers']) as $answer_id => $answer)
			{
				/*
					$answer[0] = Answer
					$answer[1] = Points
					---
					Note: Each answer (including answers with answer_type=single_answer) is sent as an array (i.e. name="tests_save_answer_Q-ID[]").
				*/
				$answer_type_html = ($question['answer_type'] == 'single_answer') ? 'radio' : 'checkbox';
					echo '<input type="' . $answer_type_html . '" name="tests_save_answer_' . $question['question_id'] . ($answer_type_html == 'radio' ? '_0' : '_' . $answer_id) . '" value="' . $answer_id . '"' . (($answer_type_html == 'radio' && $answer_id == 0) ? ' checked="checked"' : '') . ' id="tests_save_answer_id_' . $question['question_id'] . '_' . $answer_id . '" />';
					echo ' <label for="tests_save_answer_id_' . $question['question_id'] . '_' . $answer_id . '">' . $answer[0] . '</label><br />' . "\n";
			}
			echo '<br />';
		}
		
		echo '<input type="submit" name="tests_save_submit" class="button_60" value="Spara" style="float: right;" />' . "\n";
		echo '</form>' . "\n";
	}

	function tests_calculate_score($options){
		$options['source'] = isset($options['source']) ? $options['source']  : $_POST;
		if(!isset($options['handle'])){ tests_index(array('error_message' => 'Inget handle skickat till tests_calculate_score()!'));return; }
		
		$answers = array();
		$answers_to_fetch = array();
		foreach($_POST as $post_variable_key => $post_variable)
		{
			if(preg_match('/^tests_save_answer_([0-9]+)_([0-9r]+)$/', $post_variable_key, $matches))
			{
				// $matches[1] = question id
				$answers[$matches[1]][] = $post_variable;//$answers[question_id][x] = answer
				$unique_questions_answered[$matches[1]] = $matches[1];
			}
		}
		
		$answers_points = array();
		$answers_to_fetch_sql = '';
		foreach($answers_to_fetch as $answer)
		{
			$answers_to_fetch_sql .= ' OR q.question_id = ' . $answer;
		}

		if(count($answers) > 0)
		{
			$query  = 'SELECT id, custom_css, custom_score_phrases';
			$query .= ' FROM tests';
			$query .= ' WHERE handle = "' . $options['handle'] . '"';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
			if(mysql_num_rows($result) != 1)
			{
				tests_index(array('error_message' => 'Testet du postade till finns inte eller fanns två gånger!'));return;
			}
			
			$data = mysql_fetch_assoc($result);
			
			$test_custom_css = $data['custom_css'];
			$test_custom_score_phrases = $data['custom_score_phrases'];
			$test_id = $data['id'];
			
			$query  = 'SELECT q.answers, q.answer_type, q.question_id';
			$query .= ' FROM tests_questions AS q, tests AS t';
			$query .= ' WHERE t.handle = "' . $options['handle'] . '" AND t.id = q.test_id';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
			$total_score = 0;
			$max_score = 0;
			$answers_to_save_sql = array();
			while($data = mysql_fetch_assoc($result))
			{
				$question_answers = unserialize(stripslashes($data['answers']));
				$this_question_max_score = 0;
				foreach($question_answers as $question_answer_key => $question_answer)
				{
					/* question_answer[0] = answer, question_answer[1] = points/score (<--That's what we're going to use!). */
					if(in_array($question_answer_key, $answers[$data['question_id']]))
					{
						if(login_checklogin())
						{
							// %TESTID% is replaced with the tests ID below...
							// (user_id, test_id, question_id, answer_id)
							$answers_to_save_sql[] = '(' . $_SESSION['login']['id'] . ', %TESTID%, ' . $data['question_id'] . ', ' . $question_answer_key . ')';
						}
						$total_score = $total_score + $question_answer[1];
					}
										
					if($data['answer_type'] == 'single_answer')
					{
						if($this_question_max_score < $question_answer[1])
						{
							$this_question_max_score = $question_answer[1];
						}
					}
					elseif($data['answer_type'] == 'multiple_answers' && $question_answer[1] > 0)
					{
						$this_question_max_score = $this_question_max_score + $question_answer[1];
					}
				}
				
				$max_score = $max_score + $this_question_max_score;
			}
			
			if($max_score > 0 && $total_score > 0)
			{
				$score_percents_right = round(($total_score / $max_score) * 100);
				if($max_score == $total_score)
				{
					$score_text_index = 'all';
				}
				else
				{
					$score_text_index = round(($total_score / $max_score) * 4) + 1; // 1-5
				}
				$score_color_index = round(($total_score / $max_score) * 4) + 1;// 1-5
			}
			else
			{
				$percents_right = 0;
				$score_color_index = 1;
				$score_text_index = 'zero';
				/*if($total_score < 0)
				{
					$score_text_index = 'below_zero';
				}*/
			}
			
			$score_texts = array();
			//$score_texts['below_zero'] = 'Men du, det här gick inte så bra alls! Minuspoäng! %TOTAL_SCORE% av %MAX_SCORE%.';
			$score_texts['zero'] = 'Men du... Du fick noll poäng, dags att öva?';
			$score_texts['all'] = 'Gratulerar! Du fick alla rätt! (%MAX_SCORE% var max).';
			
			$score_texts[1] = 'Nä, det här var du inte så bra på. Du fick bara %TOTAL_SCORE% av %MAX_SCORE% poäng.';
			$score_texts[2] = 'Det gick väl sådär, du fick %TOTAL_SCORE% poäng av %MAX_SCORE% möjliga.';
			$score_texts[3] = 'Bättre kan du! Av %MAX_SCORE% fick du %TOTAL_SCORE%, alltså ungefär hälften.';
			$score_texts[4] = 'Du fick hyfast bra poäng, men ändå inte jättebra. Om du övar lite så kanske dina %TOTAL_SCORE% poäng blir maxpoäng (%MAX_SCORE%p).';
			$score_texts[5] = 'Gratulerar! Du fick %TOTAL_SCORE% poäng, vilket är nästan alla rätt. På det här testet kunde man som mest få %MAX_SCORE%.';

			if(!empty($custom_score_phrases))
			{
				$score_texts = unserialize(stripslashes($custom_score_phrases));
			}
	
	
			$score_text = str_replace(array('%TOTAL_SCORE%', '%MAX_SCORE%'), array($total_score, $max_score), $score_texts[$score_text_index]);	
			
			global $ui_options;
			$ui_options['stylesheets'][] = 'tests.css';
			$ui_options['stylesheets'][] = 'comments.css';
			$ui_options['javascripts'][] = 'comments.js';
			if($custom_css == 'yes')
			{
				$ui_options['stylesheets'][] = $options['handle'];
			}
			ui_top($ui_options);
			
			//echo '<h1>Resultat av test</h1>';
			
			echo '<h2>' . $score_text . '</h2>';
			echo '<div class="score_meter"><div class="color_' . $score_color_index . '" style="width: ' . ($score_percents_right * 3) . 'px">&nbsp;</div></div>';
			echo '<div class="score_meter_right">Din poäng: ' . $total_score  . ' (' . $score_percents_right . '% av max)<br />Maxpoäng: ' . $max_score . '</div>';
			echo '<br style="clear: both" />';
			if(login_checklogin())
			{
				if(count($answers_to_save_sql) > 0)
				{
					$query = 'SELECT id FROM tests WHERE handle = "' . $options['handle'] . '" LIMIT 1';
					$result = mysql_query($query) or report_sql_error($query);
					$data = mysql_fetch_assoc($result);
					$test_id = $data['id'];
					
					$query  = 'INSERT INTO tests_user_score (user_id, test_id, score, timestamp) VALUES';
					$query .= ' (' . $_SESSION['login']['id'] . ', ' . $test_id . ', ' . $total_score . ', ' . time() . ')';
					$result = @mysql_query($query);// ...

					if($result)// ...The sql query was succesfull - which means that there were no rows in tests_user_score for the given user_is and test_id.
					{
						$query  = 'INSERT INTO tests_user_answers (user_id, test_id, question_id, answer_id) VALUES';
						$query .= ' ' . str_replace('%TESTID%', $test_id, implode(', ', $answers_to_save_sql));
					}
					else
					{
						echo 'Poängen sparades inte eftersom att du redan gjort testet en gång.';
					}
				}
				else
				{
					echo 'Du måste svara på någon av frågorna!';
				}
			}
			else
			{
				echo 'Om du loggar in så kan vi spara dina poäng också!';
			}
			echo '</p><br /><br />' . "\n";
			
			echo '<h2>Folk som gjort testet</h2>';
			echo rounded_corners_top();
			echo tests_get_last_test_completers(array('test_id' => $test_id, 'limit' => 4));
			echo '<br style="clear: both" />';
			echo rounded_corners_bottom();
				
			echo rounded_corners_top();
				echo 'Kommentera testet: ' . comments_input_draw($test_id, 'tests');
				echo comments_list($test_id, 'tests');
			echo rounded_corners_bottom();
			
			echo '<p>';
			echo '<a href="/tests/">Tillbaka till testernas förstasida.</a>';
			echo '</p>';
		}
		else
		{
			global $ui_options;
			ui_top($ui_options);
			jscript_go_back();
		}
	}
	
	function tests_list_my_tests()
	{
		if(!login_checklogin())
		{
			global $ui_options;
			ui_top($ui_options);
			echo 'Du måste logga in för att kunna se dina tester!';
			return;
		}
		
		$query  = 'SELECT t.title, t.description, t.handle';
		$query .= ' FROM tests AS t';
		$query .= ' WHERE t.author = ' . $_SESSION['login']['id'];
		$query .= ' ORDER BY t.id DESC';
		$query .= ' LIMIT 100';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		global $ui_options;
		$ui_options['stylesheets'][] = 'tests.css';
		ui_top($ui_options);
		
		echo '<h1>Dina tester</h1>';
		while($data = mysql_fetch_assoc($result))
		{
			echo rounded_corners_top();
			echo '<br />';
			echo '<a href="/tests/' . $data['handle'] . '.html" class="tests_list_my_test_link">' . $data['title'] . '</a> - <a href="/tests/' . $data['handle'] . '/list_users.html">Visa folk som gjort testet och deras poäng.</a><br />' . "\n";
			echo 'Beskrivning: ' . $data['description'] . "\n";
			echo '<br /><br />';
			echo rounded_corners_bottom();
		}
	}
	
	function tests_list_users($options)
	{
		if(!isset($options['handle'])){ die('Kunde inte lista användare av test, parametern handle ej medskickad!'); }
		
		$query  = 'SELECT l.id AS userid, l.username AS username, tus.score AS score';
		$query .= ' FROM tests AS t, tests_user_score as tus, login AS l';
		$query .= ' WHERE t.handle = "' . $options['handle'] . '" AND tus.user_id = l.id AND tus.test_id = t.id';
		$query .= ' ORDER BY tus.score DESC';
		$query .= ' LIMIT 100';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

		global $ui_options;		
		ui_top($ui_options);
		
		echo '<h1>Visar folk som gjort ' . htmlspecialchars($options['handle']) . '.</h1>' . "\n";
		
		echo '<table>' . "\n";
		echo '<tr><td>Användare</td><td>Poäng</td><td></td></tr>' . "\n";
		while($data = mysql_fetch_assoc($result))
		{
			echo '<tr>' . "\n";
			
			echo "\t" . '<td><a href="/traffa/profile.php?id=' . $data['userid'] . '">' . $data['username'] . '</a></td>' . "\n";
			echo "\t" . '<td>' . $data['score'] . '</td>' . "\n";
			echo "\t" . '<td><a href="/tests/' . htmlspecialchars($options['handle']) . '/user_answers/' . $data['userid'] . '.html">Se svar</a></td>' . "\n";
			
			echo '</tr>' . "\n\n";
		}
		echo '</table>' . "\n";		
	}
	
	function tests_view_user_answers($options)
	{
		$query  = 'SELECT';
		$query .= '';
		
		global $ui_options;
		ui_top($ui_options);
	}
	
	function tests_get_last_test_completers($options) // Värry god englich... 
	{
		if(!isset($options['test_id'], $options['limit'])){ return 'tests_get_last_test_completers - Not enough parameters...'; }
		if(!is_numeric($options['test_id']) || !is_numeric($options['limit'])){ return 'Wrong parameter format for tests_get_last_test_completers.'; }
		
		$query  = 'SELECT tus.score AS score, l.id AS userid, l.username AS username, u.image AS image';
		$query .= ' FROM tests_user_score AS tus, login AS l, userinfo AS u';
		$query .= ' WHERE tus.test_id = ' . $options['test_id'] . ' AND tus.user_id = l.id AND u.userid = l.id';
		$query .= ' ORDER BY tus.timestamp DESC';
		$query .= ' LIMIT ' . $options['limit'];
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		$return = (mysql_num_rows($result) > 0) ? '' : 'Ingen inloggad användare har gjort testet ännu!';
		
		while($data = mysql_fetch_assoc($result))
		{
			$return .= '<div class="tests_completer">';
			$return .= '<h3>' . $data['score'] . ' poäng</h3>' . "\n";
			$return .= '<a href="/traffa/profile.php?id=' . $data['userid'] . '">' . ucfirst($data['username']) . '</a><br />' . "\n";
			$return .= ui_avatar($data['userid'], array('style' => 'border: 1px solid #cccccc')) . '<br />' . "\n";
			$return .= '</div>';
		}
		
		return $return;
	}

	ui_bottom();
?>
