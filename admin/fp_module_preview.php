<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	
	$ui_options['title'] = 'Redigera modul';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';
	
	if (!is_privilegied('fp_module_rearrange'))
	{
		ui_top($ui_options);
		echo '<div class="error">';
		echo '<strong>Nu äter hamstern upp dig! :)</strong>';
		echo '</div>';
		ui_bottom();
		exit;
	}
	
	ui_top($ui_options);
	
	if(isset($_GET['filename']))
	{
		$module = cache_load('fp_module_' . $_GET['filename']);
		
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
		$ui_opions['stylesheets'] = array_unique($ui_options['stylesheets']);
	}

	echo $output;

	ui_bottom();
	?>
