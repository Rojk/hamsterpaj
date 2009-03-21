<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'rank.lib.php');
	require(PATHS_LIBRARIES . 'comments.lib.php');
	include($hp_path . 'survey/library.php');

	$ui_options['menu_path'] = array('annat', 'hamburgare', 'testet');
	$ui_options['title'] = 'Gissa Hamburgaren på Hamsterpaj';
	$ui_options['stylesheets'][] = 'burgers.css';
	$ui_options['stylesheets'][] = 'comments.css';
	$ui_options['javascripts'][] = 'comments.js';

	$ui_options['stylesheets'][] = 'survey.css';
	$ui_options['javascripts'][] = 'survey.js';

	$ui_options['menu_addition']['annat']['children']['hamburgare']['children']['test'] = array('label' => 'Gör testet', 'url' => '/hamburgare/test.php');
	$ui_options['menu_addition']['annat']['children']['hamburgare']['children']['alla_burgare'] = array('label' => 'Alla burgare', 'url' => '/hamburgare/alla_burgare.php');
	$ui_options['menu_addition']['annat']['children']['hamburgare']['children']['om_testet'] = array('label' => 'Om testet', 'url' => '/hamburgare/om_testet.php');					
	ui_top($ui_options);
	
	if(!isset($_SESSION['burger']))
	{
		event_log_log('burgers_test_init');
	}
		
	echo '<h1>Gissa Hamburgaren</h1>' . "\n";

	if(isset($_GET['guessed_burger']))
	{
		$query = 'SELECT vendor, name FROM burgers WHERE id = "' . $_SESSION['burger']['current_burger'] . '" LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$data = mysql_fetch_assoc($result);
				
		if($_GET['guessed_burger'] == $_SESSION['burger']['current_burger'])
		{
			rounded_corners('Rätt! Hamburgaren heter ' . $data['name'] . ' och säljs på ' . $data['vendor'], array('color' => 'green'));
			$correct = 1;
		}
		else
		{
			rounded_corners('Fel! Hamburgaren heter ' . $data['name'] . ' och säljs på ' . $data['vendor'], array('color' => 'red'));
			$correct = 0;
		}
		
		if(login_checklogin())
		{
			$query = 'UPDATE burgers SET guesses = guesses + 1, correct_guesses = correct_guesses + ' . $correct . ', correct_ratio = correct_guesses/guesses WHERE id = "' . $_SESSION['burger']['current_burger'] . '"';
			mysql_query($query);
		}
		
		echo '<h2>Hur lik är hamburgaren reklambilden?</h2>' . "\n";
		$labels[0] = 'Oigenkännlig';
		$labels[1] = 'Inte alls lik';
		$labels[2] = 'Igenkännbar';
		$labels[3] = 'Ganska lik';
		$labels[4] = 'Identisk';
		
		for($i = 0; $i < 5; $i++)
		{
			echo '<button value="' . $i . '" class="button_100" style="margin-right: 20px;" onclick="window.location = \'/hamburgare/test.php?burger_vote=' . $i . '&burger=' . $_SESSION['burger']['current_burger'] . '\';">' . $labels[$i] . '</button>' . "\n";
		}
				
		echo '<div id="burger_compare">' . "\n";
		echo '<img src="' . IMAGE_URL . 'hamburgers/' . $_SESSION['burger']['current_burger'] . '_ad.jpg" />' . "\n";
		echo '<img src="' . IMAGE_URL . 'hamburgers/' . $_SESSION['burger']['current_burger'] . '_real.jpg" />' . "\n";
		echo '</div>' . "\n";

		echo '<h2>Kommentera burgaren</h2>' . "\n";
		echo comments_input_draw($_SESSION['burger']['current_burger'], 'burger');

		rounded_corners_top();
		echo comments_list($_SESSION['burger']['current_burger'], 'burger');
		rounded_corners_bottom();
	}
	else
	{
		echo '<p>Går det att känna igen hamburgarna från kedjornas reklambilder? Gissa dig igenom och sätt betyg på 14 hamburgare i vårt hamburger-test!</p>' . "\n";
		
		if(isset($_GET['burger']) && isset($_GET['burger_vote']) && in_array($_GET['burger_vote'], array(0,1,2,3,4)))
		{
			$query = 'UPDATE burgers SET votes = votes + 1, score = score + ' . $_GET['burger_vote'] . ', average_score = score/votes WHERE id = "' . $_GET['burger'] . '"';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}
		
		$query = 'SELECT id FROM burgers WHERE id NOT IN("' . implode('", "', $_SESSION['burger']['seen_burgers']) . '") ORDER BY RAND() LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			$_SESSION['burger']['current_burger'] = $data['id'];
			$_SESSION['burger']['seen_burgers'][] = $data['id'];
			
			echo '<h2>Vilken hamburgare är detta?</h2>' . "\n";
			echo '<img src="' . IMAGE_URL . 'hamburgers/' . $data['id'] . '_real.jpg" id="burger_guess_image" />' . "\n";
			echo '<div id="hamburger_guessing_pane">' . "\n";
			$query = 'SELECT * FROM burgers ORDER BY vendor ASC, name ASC';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			while($burger = mysql_fetch_assoc($result))
			{
				if($burger['vendor'] != $vendor)
				{
					echo '<h3>' . $burger['vendor'] . '</h3>' . "\n";
					$vendor = $burger['vendor'];
				}
				echo '<a href="?guessed_burger=' . $burger['id'] . '">' . $burger['name'] . '</a>' . "\n";
			}
			echo '</div>' . "\n";
		}
		else
		{
			echo '<h1>Nu har du gissat på alla hamburgare</h1>' . "\n";
			echo '<p>Vi hoppas du tyckte det var kul och att du tänker lite mer kritiskt nästa gång det blir dags för en snabb cheeseburgare på donkan. Titta gärna på <a href="alla_burgare.php">översikten med alla hamburgare</a> eller läs mer <a href="om_testet.php">om hur vi gjorde testet</a>.' . "\n";
			$survey = survey_fetch(array('id' => 165));
			echo survey_draw_frame($survey);
			

		}
	}
	
	echo '<h2 style="clear: both; margin-top: 50px;">Tror du att vi har fuskat med bilderna?</h2>' . "\n";
	echo '<p>Det har vi inte, det här testet är helt ärligt gjort. Du kan läsa mer <a href="om_testet.php">om testet</a>, gör gärna det när du har gissat på alla burgare!</p>' . "\n";
	
	event_log_log('burgers_test_pageview');

	ui_bottom();
?>


