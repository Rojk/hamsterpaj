<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/rank.lib.php');
	require(PATHS_INCLUDE . 'libraries/comments.lib.php');
	$ui_options['menu_path'] = array('annat', 'hamburgare', 'alla_burgare');
	$ui_options['title'] = 'Gissa Hamburgaren på Hamsterpaj';
	$ui_options['stylesheets'][] = 'burgers.css';
	$ui_options['stylesheets'][] = 'comments.css';
	$ui_options['javascripts'][] = 'comments.js';
	
	$ui_options['menu_addition']['annat']['children']['hamburgare']['children']['test'] = array('label' => 'Gör testet', 'url' => '/hamburgare/test.php');
	$ui_options['menu_addition']['annat']['children']['hamburgare']['children']['alla_burgare'] = array('label' => 'Alla burgare', 'url' => '/hamburgare/alla_burgare.php');
	$ui_options['menu_addition']['annat']['children']['hamburgare']['children']['om_testet'] = array('label' => 'Om testet', 'url' => '/hamburgare/om_testet.php');					
	ui_top($ui_options);
	
	echo '<h1>Jämför alla burgare</h1>' . "\n";
	echo '<p>' . "\n";
	echo 'Här visas alla burgare vi jämförde. Vi tog även ett gruppfoto som kan laddas ned som <a href="http://images.hamsterpaj.net/hamburgers/gruppfoto.jpg" target="_blank">högupplöst bakgrundsbild</a>.<br />' . "\n";
	echo 'Alla foton har tagits av Dan Lindgren för Hamsterpajs räkning, och vi är tacksamma om du nämner detta när du wärschar bilderna.' . "\n";
	echo '</p>' . "\n";
	
	$query = 'SELECT * FROM burgers ORDER BY vendor, name';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$labels[0] = 'Oigenkännlig';
		$labels[1] = 'Inte alls lik';
		$labels[2] = 'Igenkännbar';
		$labels[3] = 'Ganska lik';
		$labels[4] = 'Identisk';

		echo '<h2>' . $data['name'] . ' från ' . $data['vendor'] . '</h2>' . "\n";
		echo '<p>' . round($data['correct_ratio'] * 100) . '% rätta gissningar, snittbetyg: ' . $labels[round($data['average_score'])] . '</p>' . "\n";
		echo '<div id="burger_compare">' . "\n";
		echo '<img src="' . IMAGE_URL . 'hamburgers/' . $data['id'] . '_ad.jpg" />' . "\n";
		echo '<img src="' . IMAGE_URL . 'hamburgers/' . $data['id'] . '_real.jpg" />' . "\n";
		echo '</div>' . "\n";

		echo '<h4>Kommentarer till ' . $data['name'] . '</h4>' . "\n";

		rounded_corners_top();
		echo comments_list($data['id'], 'burger');
		rounded_corners_bottom();
		echo '<hr style="margin-top: 20px; margin-bottom: 20px;" />';
		
	}
	
	event_log_log('burgers_overview');
	
	ui_bottom();
?>


