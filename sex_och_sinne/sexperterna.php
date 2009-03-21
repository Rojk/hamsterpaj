<?php
	require('../include/core/common.php');
	include_once(PATHS_LIBRARIES . 'sex_sense.lib.php');
	include_once(PATHS_LIBRARIES . 'articles.lib.php');
	include_once(PATHS_LIBRARIES . 'sex_sense_ui.lib.php');
	$ui_options['stylesheets'][] = 'sex_sense.css';
	$ui_options['title'] = 'Sex och Sinne - Hamsterpaj.net';
	$ui_options['menu_path'] = array('sex_sense', 'sexpretterna');
	
	$out .= '<img id="sex_and_sense_top" src="http://images.hamsterpaj.net/sex_and_sense/sex_and_sense_top.png" alt="Sex och sinne" />' . "\n";
	$article = articles_fetch(array('id' => '82'));
	$out .= sex_sense_bright_container_top();
	$out .= render_full_article($article);
	$out .= sex_sense_bright_container_bottom();
	//event_log_log('sex_sense_index');
	ui_top($ui_options);
	echo $out;
	ui_bottom();

?>