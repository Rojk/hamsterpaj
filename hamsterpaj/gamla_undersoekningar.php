<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . '../survey/library.php');
	$ui_options['menu_path'] = array('hamsterpaj', 'gamla_undersoekningar');
	$ui_options['title'] = 'Gamla undersökningar som körts på Hamsterpaj';
	$ui_options['stylesheets'][] = 'survey.css';
	$ui_options['javascripts'][] = 'survey.js';
	ui_top($ui_options);
	
	echo '<div id="old_surveys">' . "\n";
	
	if(isset($_GET['survey_id']) && is_numeric($_GET['survey_id']))
	{
		$survey = survey_fetch(array('type' => 'front_page', 'id' => $_GET['survey_id']));
		echo survey_draw_frame($survey);
	}

	
	echo '<h1>Gamla undersökningar på Hamsterpaj</h1>' . "\n";

		$surveys = survey_fetch(array('type' => 'front_page'), array('limit' => 999999, 'order-by' => 'start_time'));

		foreach($surveys AS $survey)
		{
			if($current_month != date('Y_m', $survey['start_time']))
			{
				if(isset($current_month))
				{
					echo '</ul>' . "\n";
				}
				echo '<h2>' . date('F', $survey['start_time']) . ' -' . date('y', $survey['start_time']) . '</h2>' . "\n";
				$current_month = date('Y_m', $survey['start_time']);
				echo '<ul>' . "\n";
			}
			echo "\n" . '<li><span class="date">' . date('d/m', $survey['start_time']) . '</span> <a href="?survey_id=' . $survey['id'] . '" class="question">' . $survey['question'] . '</a></li>' . "\n";
		}
		echo '</ul>' . "\n";
	
	echo '</div>' . "\n";

	ui_bottom();
?>


