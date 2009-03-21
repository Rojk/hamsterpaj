<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'movie_compability.lib.php');
	$ui_options['menu_path'] = array('dev', 'visa_sessionsdata');
	ui_top($ui_options);

	if(is_privilegied('use_debug_tools'))
	{
		echo '<h1>Visar sessionsdata</h1>' . "\n";
		preint_r($_SESSION);
	}
	else
	{
		preint_r(array('fisk' => 'kakor'));
	}
	
	ui_bottom();
?>


