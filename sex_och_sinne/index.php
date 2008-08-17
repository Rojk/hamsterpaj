<?php
	require('../include/core/common.php');
	include_once(PATHS_INCLUDE . 'libraries/sex_sense.lib.php');
	include_once(PATHS_INCLUDE . 'libraries/sex_sense_ui.lib.php');
	$ui_options['stylesheets'][] = 'sex_sense.css';
	$ui_options['javascripts'][] = 'sex_sense.js';
	$ui_options['title'] = 'Sex och Sinne - Hamsterpaj.net';
	
	$out .= '<img id="sex_and_sense_top" src="http://images.hamsterpaj.net/sex_and_sense/sex_and_sense_top.png" alt="Sex och sinne" />' . "\n";
	
	try
	{
		$request = sex_sense_parse_request(array('URI' => $_SERVER['REQUEST_URI']));
		switch($request['category'])
		{
			case 'view':
				$options['limit'] = 1;
				$options['handle'] = $request['question_handle'];
				$question = sex_sense_fetch_posts($options);
				$out .= sex_sense_render_posts($question, array('unhide_content' => true));
			break;
			
			case 'view_category':
				$ui_options['menu_path'] = array('sex_sense', 'view_category');
				
				$category = array_pop(sex_sense_fetch_categories(array('parent_category' => 0)));
				$out .= sex_sense_render_category($category);
				
				$data = array_pop(array_pop(sex_sense_fetch_categories(array('category_handle' => $request['category_handle']))));

				$out .= '<h1>Senaste frågorna i kategorin ' . $data['category_title'] . '</h1>';

				$options['category_id'] = $data['category_id'];
				$options['order'] = 'DESC';
				$options['order_by'] = 'last_answer';
				$options['is_answered'] = 1;
				$options['is_released'] = 1;
				$options['ignore_no_posts_found_error'] = true;
				$questions = sex_sense_fetch_posts($options);
				$out .= sex_sense_render_posts($questions);
				
				if(empty($questions))
				{
					$out .= '<div class="warning">' . "\n";
					$out .= '<h2>Det hittades inga frågor i kategorin :(</h2>' . "\n";
					$out .= '<p>Troligtvis beror det på att ingen av våra sexperter svarat på en fråga för den här kategorin. 
											Men misströsta inte, de kommer snart finnas frågor även här :)
									</p>' . "\n";
					$out .= '</div>' . "\n";
				}
				
			break;
				
			case 'list_categories':
				$ui_options['menu_path'] = array('sex_sense', 'view_category');
				
				$category = array_pop(sex_sense_fetch_categories(array('parent_category' => 0)));
				$out .= sex_sense_render_category($category);
			break;
			
			default:
			case 'index':
				$ui_options['menu_path'] = array('sex_sense');
		
				$out .= sex_sense_bright_container_top();
					$out .= '<p>Entrero och SheDevil svarar på dina funderingar om sex, kärlek, kroppen 
					och annat som hör tonåren till. Vill du ställa en fråga? Välj "Ställ en fråga" i 
					menyn till höger! Givetvis är du helt anonym om du så önskar. 
					Våra sexperter får inte se vem som ställt frågan.</p>' . "\n";
				$out .= sex_sense_bright_container_bottom();
				
				$options['limit'] = isset($_GET['viewall']) ? 100 : 1;
				$options['order'] = 'DESC';
				$options['order_by'] = 'last_answer';
				$options['is_answered'] = 1;
				$options['is_released'] = 1;
				
				$questions = sex_sense_fetch_posts($options);
				$out .= '<h2>Senast besvarade frågan</h2>' . "\n";
				$out .= sex_sense_render_posts($questions);
				
				$category = array_pop(sex_sense_fetch_categories(array('parent_category' => 0)));
				$out .= '<h2>Välj en kategori att kika runt i</h2>' . "\n";
				$out .= sex_sense_render_category($category);
			break;
			
			case 'new_question':
				$ui_options['menu_path'] = array('sex_sense', 'question');
	
				if(login_checklogin())
				{
					$out .= sex_sense_new_question_form();
				}
			break;
			
			case 'save_new_question':
				if(login_checklogin())
				{
					sex_sense_new_question_create(array(
						'user_id' => $_SESSION['login']['id'],
						'question' => $_POST['question']
					));
					jscript_alert('Tack för din fråga! Du kommer att få ett privat gästboksinlägg så fort din fråga är besvarad :)');
					jscript_location('/sex_och_sinne/');
					exit;
				}
				else
				{
					$out .= '<h2>Din fråga kunde inte skapas!</h2><pre>' . $_POST['question'] . '</pre>';
					throw new Exception('Du måste vara inloggad för att skapa nya frågor.');
				}
			break;
			
			case 'latest':
				$ui_options['menu_path'] = array('sex_sense', 'latest');
			
				$category = array_pop(sex_sense_fetch_categories(array('parent_category' => 0)));
				$out .= sex_sense_render_category($category);
				
				$out .= '<h2>Senast besvarade frågorna</h2>';
				
				$options['order'] = 'DESC';
				$options['order_by'] = 'last_answer';
				$options['is_answered'] = 1;
				$options['is_released'] = 1;
				$question = sex_sense_fetch_posts($options);
				$out .= sex_sense_render_posts($question);
			
			break;
			
			case 'admin':
				
			break;
		}
	}
	catch(Exception $error)
	{
		$out .= '<div class="warning">' . "\n";
		$out .= '<h2>Ett systemfel har inträffat!</h2>' . "\n";
		$out .= '<p>' . "\n";
		$out .= $error -> getMessage() . '<br />';
		$out .= 'Felsökingsinformation: Felet orsakades på rad ' . $error -> getLine() . ' i filen ' . $error -> getFile();
		$out .= '</p>' . "\n";
		$out .= '</div>' . "\n";
	}

	//event_log_log('sex_sense_index');
	
	if(!isset($ui_options['menu_path']))
	{
		$ui_options['menu_path'] = array('sex_sense');
	}
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();

?>