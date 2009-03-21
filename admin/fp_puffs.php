<?php
	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'entertain.lib.php');
	require_once(PATHS_LIBRARIES . 'rank.lib.php');
	require_once(PATHS_LIBRARIES . 'photos.lib.php');
	require_once(PATHS_LIBRARIES . 'fp_modules.lib.php');

	$ui_options['javascripts'][] = 'fp_common_modules.js';

	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'fp_modules.css';
	$ui_options['stylesheets'][] = 'fp_common_modules.css';
	

	$ui_options['title'] = 'Förhandsgranskat fp-puffar';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';
	

	$out .= '<ol id="fp_module_list">' . "\n";
	$query = 'SELECT * FROM fp_puffs ORDER BY id DESC';
	$result = mysql_query($query);
	while($data = mysql_fetch_assoc($result))
	{
		$out .= '<li class="module_2_3">' . "\n";			
		$out .= '<div class="module">' . "\n";
		$out .= '<p>Visar modul "' . $data['title'] . '"</p>' . "\n";
		$out .= '</div>' . "\n";
		$out .= '<div class="puff">' . "\n";
		$out .= $data['content'];
		$out .= '</div>' . "\n";
		$out .= '</li>' . "\n";
	}
	$out .= '</ol>' . "\n";
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
	?>
