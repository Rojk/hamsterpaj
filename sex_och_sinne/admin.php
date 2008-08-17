<?php
	require('../include/core/common.php');
	include_once(PATHS_INCLUDE . 'libraries/sex_sense.lib.php');
	include_once(PATHS_INCLUDE . 'libraries/sex_sense_ui.lib.php');
	include_once(PATHS_INCLUDE . 'libraries/guestbook.lib.php');
	include_once(PATHS_INCLUDE . 'libraries/discussion_forum.lib.php');
	$ui_options['stylesheets'][] = 'sex_sense.css';
	$ui_options['javascripts'][] = 'sex_sense.js';
	$ui_options['title'] = 'Besvara frågor - Sex och Sinne - Hamsterpaj.net';
	$ui_options['menu_path'] = array('sex_sense', 'new_questions');
	
	if (!is_privilegied('sex_sense_admin'))
	{
		die('Fuling, gå till <a href="/sex_och_sinne/">Startsidan</a> istället :)');
	}
	if ($_GET['action'] == 'remove' && !empty($_GET['id']) && is_numeric($_GET['id']))
	{
		$sql .= 'UPDATE sex_questions SET is_removed = 1 WHERE id = ' . $_GET['id'] . ' LIMIT 1';
		mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
		header('Location: /sex_och_sinne/admin.php');
	}
	elseif (isset($_POST['answer'], $_POST['title'], $_POST['answer_to'], $_POST['sex_category'], $_POST['question']) && !empty($_POST['question']) && is_numeric($_POST['sex_category']) && is_numeric($_POST['answer_to']))
	{
		$options['id'] = $_POST['answer_to'];
		$options['ignore_no_posts_found_error'] = true;
		$posts = sex_sense_fetch_posts($options);
		if(count($posts) < 1)
		{
			$options['is_answered'] = 0;
			$posts = sex_sense_fetch_posts($options);
		}
		if(count($posts) != 1)
		{
			die('Error in ' . __FILE__ . ' on line ' . __LINE__);
		}
		$post = array_pop($posts);
		
		if(empty($post['title']) && !empty($_POST['title']))
		{
			sex_sense_update_handle(array(
				'id' => $post['id'],
				'title_to_serialize' => $_POST['title']
			));
		}
		
		if(!empty($_POST['answer']))
		{
			sex_sense_answer_create(array(
				'answer_to' => $post['id'],
				'answer' => $_POST['answer'],
				'user_id' => $_SESSION['login']['id']
			));
		}
		
		$query = 'UPDATE sex_questions SET title = "' . $_POST['title'] . '", question = "' . $_POST['question'] . '", category = ' . $_POST['sex_category'] . ' WHERE id = ' . $post['id'] . ' LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	
		// Schedule release if not schedules yet...
		
		$scheduled_items = schedule_event_fetch(array(
			'fetch_released_and_unreleased' => true,
			'type' => 'sex_sense',
			'item_id' => $post['id']
		));
		
		if(count($scheduled_items) < 1)
		{
			$direct_link = '/sex_och_sinne/';
		  $categories = sex_sense_fetch_categories(array('category_id' => $post['category_id']));
		  foreach($categories as $category_tree)
		  {
		  	$category = array_pop($category_tree);
		  	$direct_link .= $category['category_handle'] . '/';
		  }
		  $direct_link .= $post['handle'] . '.html';
		  
			sex_sense_schedule_add(array('id' => $post['id'], 'title' => $post['title'], 'url' => $direct_link));
			echo 'Scheduling... Done!';
		}
		
		jscript_alert('Fixat!');
		jscript_go_back();
	}
	elseif (empty($_POST['answer']) && !empty($_POST['answer_to']) && !empty($_POST['question']) && !empty($_POST['sex_category']) && is_numeric($_POST['answer_to']) && is_numeric($_POST['sex_category']))
	{
		
		$sql = 'UPDATE sex_questions SET category = ' . $_POST['sex_category'] . ' WHERE id = ' . $_POST['answer_to'] . ' LIMIT 1';
		mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
	}
	elseif (!empty($_GET['id']) && is_numeric($_GET['id']))
	{
		$options['id'] = $_GET['id'];
		$options['is_answered'] = 0;
		$options['ignore_no_posts_found_error'] = true;
		
		$questions = sex_sense_fetch_posts($options);
		if(count($questions) < 1)
		{
			$options['is_answered'] = 1;
			$questions = sex_sense_fetch_posts($options);
		}
		foreach ($questions AS $question)
		{ 
			$out .= sex_sense_bright_container_top();
				$out .= sex_sense_dark_container_top();
					$out .= '<form style="margin-left: 6px;" action="/sex_och_sinne/admin.php" method="post">' . "\n";
					$out .= '<h3>Titel: <input id="title" name="title" type="text" value="' . $question['title'] . '" />';
					/*$out .= ' Kategori: <select name="sex_category" id="sex_category">' . "\n";
						$category_array[] = '<option value="X"><strong>Välj kategori</strong></option>' . "\n";
						$category_array[] = '<optgroup label="Sex">' . "\n";
						$category_array[] = '<option value="4">Oralsex</option>' . "\n";
						$category_array[] = '<option value="5">Analsex</option>' . "\n";
						$category_array[] = '<option value="6">Vaginalsex</option>' . "\n";
						$category_array[] = '<option value="7">Homosex</option>' . "\n";
						$category_array[] = '<option value="8">Ställningar</option>' . "\n";
						$category_array[] = '<option value="9">Könssjukdomar</option>' . "\n";
						$category_array[] = '<option value="10">Förspel</option>' . "\n";
						$category_array[] = '<option value="11">Preventivmedel</option>' . "\n";
						$category_array[] = '<option value="12">Övrigt</option>' . "\n";
						$category_array[] = '</optgoup>' . "\n";
						$category_array[] = '<optgroup label="Kroppen"></option>' . "\n";
						$category_array[] = '<option value="14">Killens kropp</option>' . "\n";
						$category_array[] = '<option value="15">Tjejens kropp</option>' . "\n";
						$category_array[] = '<option value="16">Övrigt</option>' . "\n";
						$category_array[] = '</optgoup>' . "\n";
						$category_array[] = '<optgroup label="Relationer">' . "\n";
						$category_array[] = '<option value="18">Kärlek</option>' . "\n";
						$category_array[] = '<option value="19">Förhållanden</option>' . "\n";
						$category_array[] = '<option value="20">Övrigt</option>' . "\n";
						$category_array[] = '</optgoup>' . "\n";
						foreach ($category_array AS $cat)
						{
							if (stristr($cat, '<option value="' . $question['category'] . '">'))
							{
								$part_1 = substr($cat, 0, 6);
								$part_2 = substr($cat, 7);
								$out .= $part_1 . ' selected="selected"' . $part_2;
							}
							else
							{
								$out .= $cat;
							}
						}
					$out .= '</select>';*/
					
					$out .= '<select name="sex_category" id="sex_category">' . "\n";
					
					$categories = array_pop(sex_sense_fetch_categories(array('parent_category' => 0)));
					foreach($categories as $category)
					{
						$out .= '<optgroup label="' . $category['category_title'] . '">' . "\n";
						
						$child_categories = array_pop(sex_sense_fetch_categories(array('parent_category' => $category['category_id'])));
						foreach($child_categories as $child_category)
						{
							$selected = ($question['category'] == $child_category['category_id']) ? ' selected="selected"' : '';
							$out .= "\t" . '<option value="' . $child_category['category_id'] . '"' . $selected . '>' . $child_category['category_title'] . '</option>' . "\n";
						}
						
						$out .= '</optgroup>' . "\n";
					}
					$out .= '</select>';
					
					$out .= '</h3>' . "\n";
				$out .= sex_sense_dark_container_bottom();
				$out .= '<textarea name="question" style="width: 550px; height: 200px;">' . $question['question'] . '</textarea><br style="clear: both;" />' . "\n";
				foreach ($question['answers'] AS $answer)
				{
					$out .= ui_avatar($answer['user_id']);
			  	$out .= sex_sense_bubble_top();
			  	$out .= '<h4>' . $answer['username'] . ' svarar:</h4>' . "\n";
			  	$out .= '<p>' . nl2br($answer['answer']) . '</p>' . "\n";
			  	$out .= sex_sense_bubble_bottom();
				}
				$out .= '<input type="hidden" name="answer_to" value="' . $question['id'] . '" />' . "\n";
				$out .= '<label for="answer">Svara på frågan: <strong>Detta fält måste fyllas i, annars går saker sönder.</strong></label>' . "\n";
				$out .= '<textarea name="answer" style="width: 550px; height: 200px;">' . "\n";
				$out .= '</textarea>' . "\n";
				$out .= '<br /><input type="submit" value="Skicka" onclick="return checkChosenCategory()" class="button_60" />' . "\n";
				$out .= '<a onclick="checkChosenCategory()">LOOOL</a>' . "\n";
				$out .= '</form>' . "\n";
			  $out .= '<div style="clear: both; height: 5px;"></div>' . "\n";
			$out .= sex_sense_bright_container_bottom();
		}
	}
	else
	{
		$options['order'] = 'DESC';
		$options['order_by'] = 'timestamp';
		$options['is_answered'] = 0;
		$options['ignore_no_posts_found_error'] = true;
		$question = sex_sense_fetch_posts($options);
		foreach ($question AS $question)
		{ 
			$out .= sex_sense_bright_container_top();
				$out .= sex_sense_dark_container_top();
					$out .= '<h3>' . $question['title'] . '</h3>' . "\n";
				$out .= sex_sense_dark_container_bottom();
				$out .= '<p>' . $question['question'];
			  $out .= '<br /><a style="cursor: pointer" onclick="sex_sense_confirm_removal(' . $question['id'] . ')">Ta bort</a> <a href="/sex_och_sinne/admin.php?id=' . $question['id'] . '">Svara &raquo;</a>' . "\n";
			  $out .= '</p>' . "\n";
				/*foreach ($question['answers'] AS $answer)
				{
					$out .= ui_avatar($answer['user_id']);
			  	$out .= bubble_top();
			  	$out .= '<h4>' . $answer['username'] . ' svarar:</h4>' . "\n";
			  	$out .= '<p>' . $answer['answer'] . '</p>' . "\n";
			  	$out .= bubble_bottom();
				}*/
			  $out .= '<div style="clear: both; height: 5px;"></div>' . "\n";
			$out .= sex_sense_bright_container_bottom();
		}
	}
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>