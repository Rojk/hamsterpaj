<?php
	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'articles.lib.php');
	
	$ui_options['menu_path'] = array('hamsterpaj', 'annonsera');
	$ui_options['title'] = 'Annonsera på Hamsterpaj.net';	
	$ui_options['stylesheets'][] = 'articles.css';
	
	$article = articles_fetch(array('id' => '117'));
	$out .= render_full_article($article);
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
