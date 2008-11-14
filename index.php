<?php
	require('include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');

	$ui_options['javascripts'][] = 'start.js';

	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'start.css';

	$ui_options['title'] = 'Startsidan på Hamsterpaj - Tillfredsställelse utan sex!';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';

	$fp_modules = cache_load('fp_module_order');
	
	foreach($fp_modules AS $filename)
	{
		$module = cache_load('fp_module_' . $filename);
		if($module['display'] != 1)
		{
			continue;
		}

		if($module['phpenabled'] == 1)
		{
			include(PATHS_INCLUDE . 'fp_modules/' . $filename);
		}
		else
		{
			$output .= file_get_contents(PATHS_INCLUDE . 'fp_modules/' . $filename);
		}
		
		foreach($module['stylesheets'] AS $css)
		{
			$ui_options['stylesheets'][] = $css;			
		}
	}
	
	$ui_opions['stylesheets'] = array_unique($ui_options['stylesheets']);
	
	ui_top($ui_options);
	echo $output;
	ui_bottom();
	?>
