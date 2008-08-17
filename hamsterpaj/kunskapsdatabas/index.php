<?php
	require('/storage/www/standard.php');
	require(PATHS_INCLUDE . 'libraries/knowledge_database.lib.php');
		
	$knowledge_database_config['database'] = 'hamsterpaj';
	$knowledge_database_config['url'] = $_SERVER['REQUEST_URI'];
	$knowledge_database_config['url_prefix'] = '/hamsterpaj/kunskapsdatabas/';
	
	/*$index .= '<h1>Index eller så...</h1>' . "\n";
	$knowledge_database_config['pages']['index']['output'] = $index;
	$knowledge_database_config['pages']['index']['title'] = 'Kunskapsdatabasen om Hamsterpaj';*/
	
	$request = knowledge_database_parse_request($knowledge_database_config);
	
	$ui_options['title'] = $request['title'];
	ui_top($ui_options);
	
	echo $request['response'];
	
	ui_bottom();
?>