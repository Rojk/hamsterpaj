<?php
	require('../include/core/common.php');
	$ui_options['title'] = 'Pedofilpolicy';

	$ui_options['menu_path'] = array('hamsterpaj', 'pedofilpolicy');
	
	require(PATHS_INCLUDE . 'libraries/articles.lib.php');
	$ui_options['stylesheets'][] = 'articles.css';
	
	$article = articles_fetch(array('id' => '64'));
	$out .= render_full_article($article);
	
	ui_top($ui_options);
		echo $out;
	ui_bottom();
?>


