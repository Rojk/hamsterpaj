<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE . 'libraries/sex_and_sense.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/schedule.lib.php');

	$ui_options['menu_path'] = array('sex_sense');
	$ui_options['title'] = 'Sex och sinne';
	$ui_options['stylesheets'][] = 'sex_sense.css';

	$query = 'SELECT DISTINCT(category) FROM sex_sense ORDER BY category ASC';
	$categories = query_cache(array('query' => $query, 'max_delay' => 1));
	foreach($categories AS $category)
	{
		$ui_options['menu_addition']['sex_sense']['children'][$category['category']] = array('label' => $SEX_SENSE[$category['category']]['label'], 'url' => '/sex_och_sinne/' . $category['category'] . '/');
	}

	$request = sex_sense_request($_SERVER['REQUEST_URI']);
	
	switch($request['action'])
	{
		case 'answer_index':
			if(!is_privilegied('sex_sense_admin'))
			{
				die("FULHAXX!");
			}
			if (!empty($_POST['answer']))
			{
				$sql = 'UPDATE sex_questions SET answer = "' . $_POST['answer'] . '" WHERE id = ' . $_POST['id'] . ' LIMIT 1';
				if (mysql_query($sql) or report_sql_query($sql, __FILE__, __LINE__))
				{
					jscript_alert('Svaret tillagt');
				}
			}
			$ui_options['menu_path'] = array('sex_sense', 'new_questions');
			$output .= '<h2>Här kan du svara på frågor om sex och sinne ;)</h2>' . "\n";
			$sql = 'SELECT * FROM sex_questions ORDER BY timestamp DESC';
			$result = mysql_query($sql);
			while ($data = mysql_fetch_assoc($result))
			{
				$out_while .= '<div style="clear: both;"></div>' . "\n";
				$out_while .= '<h3>' . $data['title'] . '</h3>' . "\n";
				$out_while .= '<p>' . $data['question'] . '</p>' . "\n";
				$out_while .= '<a href="/sex_och_sinne/?answer_question=' . $data['id'] . '">Svara&raquo;</a>' . "\n";
				$output .= rounded_corners($out_while, $nothing, true);
				$out_while = '';
			}
			$output .= '' . "\n";
			$output .= '' . "\n";
			$output .= '' . "\n";
			$output .= '' . "\n";
			$output .= '' . "\n";
			$output .= '' . "\n";
			$output .= '' . "\n";
			
			break;
		case 'category_index':
			$ui_options['menu_path'] = array('sex_sense', $request['category_handle']);
			$output .= '<h1><a href="/sex_och_sinne/">Sex och sinne</a> &raquo; ' . $SEX_SENSE[$request['category_handle']]['label'] . '</h2>' . "\n";
			$entries = sex_sense_fetch(array('category' => $request['category_handle']));
			$output .= sex_sense_list($entries);
			
			break;
		case 'compose_answer':
		
			$sql = 'SELECT * FROM sex_questions WHERE id = ' . $request['question_id'] . ' LIMIT 1';
			$result = mysql_query($sql);
			while ($data = mysql_fetch_assoc($result))
			{
				$questions[] = $data;
			}
			$options['show_answer_textarea'] = true;
			$options['rounded_corners'] = true;
			$output .= render_sex_sense_question($questions, $options);
			
			break;
		case 'compose':
			$output .= sex_sense_form();
			break;
		case 'edit':
		
			break;
		case 'create':
			if (login_checklogin())
			{
				$secue_url = url_secure_string($_POST['title']);
				$sql = 'SELECT handle FROM sex_questions WHERE handle = "' . $_POST['title'] . '" LIMIT 1';
				$result = mysql_query($sql);
				$data = mysql_fetch_assoc($result);
				if (strlen($data['handle']) > 0)
				{
					$handle_exists_already = true;
				}
			}
			break;
		case 'update':
		
			break;		
		case 'index':
		default:
			$ui_options['menu_path'] = array('sex_sense');
			
			$output .= '<img src="http://images.hamsterpaj.net/sexosinne.png" />' . "\n";
			$void['color'] = 'orange_deluxe';
			$info = '<h2 style="margin-top: 0px;">Info</h2>';
			$info .= 'Den här sidan är bara öppen för <a href="/traffa/profile.php?user_id=534434">SheDevil</a>, <a href="/traffa/profile.php?user_id=643392">Entrero</a> och <a href="/traffa/profile.php?user_id=774586">Joar</a> än så länge (alla kommer åt den, men det är bara vi som ser den i menyn.)';
			$output .= rounded_corners($info, $void, true);
			$output .= '<h1>Sex och sinne</h1>' . "\n";
			$output .= '<p><a href="/traffa/profile.php?user_id=643392">Entrero</a> och <a href="/traffa/profile.php?user_id=534434">SheDevil</a> svarar på dina funderingar om sex, kärlek, kroppen och annat som hör tonåren till. Vill du ställa en fråga? Välj "Ställ en fråga" i menyn till höger!</p>' . "\n";
			$output .= '<h2>Börja med att välja en kategori</h2>' . "\n";
			
			$query = 'SELECT DISTINCT(category) FROM sex_sense ORDER BY category ASC';
			$categories = query_cache(array('query' => $query, 'max_delay' => 1));
			
			
			$output .= '<ul class="sex_sense_categories">' . "\n";
			foreach($categories AS $category)
			{
				$output .= '<li><h3><a href="/sex_och_sinne/' . $category['category'] . '/">' . $SEX_SENSE[$category['category']]['label'] . '</a></h3>' . "\n";
				$output .= '<p><a href="/sex_och_sinne/' . $category['category'] . '/">' . $SEX_SENSE[$category['category']]['description'] . '</a></p>' . "\n";
				$output .= '</li>' . "\n";
			}
			$output .= '</ul>' . "\n";

			if(login_checklogin())
			{
				$void['color'] = 'blue_deluxe';
				$output .= '</div>';
				$output .= '<h2 style="margin-top: 0px; padding-top: 2px;">Ställ en fråga, anonymt givetvis ;)</h2>' . "\n";
				$output .= rounded_corners_top($void, true);
				$output .= '<form action="/sex_och_sinne/ny.php" method="post">' . "\n";
				$output .= '<label for="title">Ämne</label><br />' . "\n";
				$output .= '<input type="text" name="title" /><br />' . "\n";
				$output .= '<label for="question">Fråga</label><br />' . "\n";
				$output .= '<textarea style="width: 500px; height: 100px;" name="question"></textarea><br />' . "\n";
				$output .= '<input type="submit" value="Skicka" class="button_60" />' . "\n";
				$output .= '</form>' . "\n";
				$output .= rounded_corners_bottom($void, true);
			}

			//event_log_log('sex_sense_index');

			break;
	}
	
	ui_top($ui_options);
	echo $output;
	ui_bottom();
?>
