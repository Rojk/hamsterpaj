<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');

	$ui_options['javascripts'][] = 'fp_module_rearrange.js';
	$ui_options['stylesheets'][] = 'fp_module_rearrange.css';
		
	
	$ui_options['title'] = 'Sortera förstasidesmoduler';
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
	
	$filenames = cache_load('fp_module_order');

	$dir = opendir(PATHS_INCLUDE . 'fp_modules/');
	while($filename = readdir($dir))
	{
		if($filename != '.' && $filename != '..' && !in_array($filename, $filenames))
		{
			$filenames[] = $filename;
		}
	}


	$output .= '<ul id="fp_module_rearrange_list">';
	foreach($filenames AS $filename)
	{
		$output .= '<li id="' . $filename . '">' . "\n";
		$output .= '<h3>' . $filename . '</h3>' . "\n";
		$output .= '<a href="/admin/fp_module_customize.php?filename=' . $filename . '">Redigera</a>' . "\n";
		$output .= '</li>' . "\n";
	}
	$output .= '</ul>';


	$output .= '<button id="fp_moudle_rearrange_save">Spara</button>' . "\n";
	$output .= '<h1>Sparningen kan ta tid</h1>' . "\n";
	$output .= '<p>Du får inga timglas och ingen indikation på att systemet sparar, du får inte heller veta när det sparat klart. Lämnar du sidan innan sparningen är klar så avrbyts det hela. Kolla alltså att ändringarna vekrligen slår igenom på förstasidan!</p>';
	echo $output;



	ui_bottom();
	?>
