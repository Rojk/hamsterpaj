<?php
	require('include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	require(PATHS_INCLUDE . 'libraries/fp_modules.lib.php');

	$ui_options['javascripts'][] = 'start.js';

	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'start.css';

	$ui_options['title'] = 'Startsidan på Hamsterpaj';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';

	$fp_modules = fp_modules_fetch(array('removal_min' => time(), 'launch_max' => time()));

	$o .= '<ol id="fp_module_list">' . "\n";
	foreach($fp_modules AS $module)
	{
		$o .= '<li>' . "\n";
		if($module['code_mode'] == 'php')
		{
			include(PATHS_INCLUDE . 'fp_modules/' . $module['id'] . '.php');
		}
		else
		{
			$o .= file_get_contents(PATHS_INCLUDE . 'fp_modules/' . $module['id'] . '.php');
		}

		$o .= '<div class="controls">' . "\n";
		if($module['published'] == 'true')
		{
			$o .= '<p>Upplagd ' . date('Y-m-d', $module['launch']) . '</p>' . "\n";
		}
		if($module['commenting'] == 'true')
		{
			$o .= '<a href="' . forum_get_url_by_post($module['thread_id']) . '">Kommentera</a>' . "\n";
		}
		if($module['grading'] == 'true')
		{
			$o .= '<img src="http://images.hamsterpaj.net/discussion_forum/thread_voting_plus.png" />' . "\n";
			$o .= '<img src="http://images.hamsterpaj.net/discussion_forum/thread_voting_minus.png" />' . "\n";
			$o .= '<p>' . cute_number($module['score']) . 'p</p>' . "\n";
		}
		if($module['clicks'] > 0)
		{
			$o .= '<p>' . cute_number($module['clicks']) . ' klick</p>' . "\n";
		}
		$o .= '</div>' . "\n";
		$o .= '</li>' . "\n";
	}
	$o .= '</ol>' . "\n";
	
	ui_top($ui_options);
	echo $o;
	ui_bottom();
	?>
