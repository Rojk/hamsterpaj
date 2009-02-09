<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'photos.lib.php');
	
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
		$module_save_path = realpath(PATHS_DYNAMIC_CONTENT . 'fp_modules/') . '/';
		if(!file_exists($module_save_path . $_GET['filename']) || strncmp(realpath($module_save_path . $_GET['filename']), $module_save_path, strlen($module_save_path)) != 0)
		{
			echo 'Tjockis!';
			ui_bottom();
			exit;
		}
		
		if(isset($_POST['code']))
		{
			$module['display'] = ($_POST['display'] == 1) ? 1 : 0;
			$module['phpenabled'] = ($_POST['phpenabled'] == 1) ? 1 : 0;
			
			$module['stylesheets'] = explode(' ', $_POST['stylesheets']);
			
			cache_save('fp_module_' . $_GET['filename'], $module);
			
			
			$code = stripslashes(html_entity_decode($_POST['code']));
			
			file_put_contents(PATHS_DYNAMIC_CONTENT . 'fp_modules/' . $_GET['filename'], $code);
			
			$output .= '<h1>Sparat! - <a href="/admin/fp_module_rearrange.php">sortera moduler</a></h1>';
		}
		
		$module = cache_load('fp_module_' . $_GET['filename']);
		
		$module['display'] = ($module['display'] == 1) ? ' checked="true"' : '';
		$module['phpenabled'] = ($module['phpenabled'] == 1) ? ' checked="true"' : '';
		
		$module['code'] = file_get_contents(PATHS_DYNAMIC_CONTENT . 'fp_modules/' . $_GET['filename']);

		$output .= '<form method="post">' . "\n";
		$output .= '<input name="display" type="checkbox"' . $module['display'] . ' id="view_control" value="1" /><label for="view_control">Visa på förstasidan</label><br />';
		$output .= '<input name="phpenabled" type="checkbox"' . $module['phpenabled'] . ' id="php_control" value="1" /><label for="php_control">Kör som PHP-fil (script = kryssa, enkel HTML = kryssa inte)</label><br />';
		
		$output .= '<br /><label>CSS-filer (ange bara filnamn, inklusive filändelse, separera med mellanslag)<br /><strong>Stilmallarna ska ligga i stylesheets/fp_modules/</strong> Lef kommer att ge dig spö annars. Srsly. Den mappen är gitignorad. Sedan hämtas stilmallen med fp_modules/stilmallsnamn.css</label><br />' . "\n";
		$output .= '<input type="text" name="stylesheets" style="width: 600px;" value="' . implode(' ', $module['stylesheets']) . '" /><br />';
		
		$output .= '<textarea name="code" style="width: 630px; height: 500px; font-size: 11px;">' . htmlspecialchars($module['code']) . '</textarea>' . "\n";

		$output .= '<input type="submit" value="Spara" />' . "\n";
		$output .= '</form>' . "\n";
		
		$output .= '<a href="/admin/fp_module_preview.php?filename=' . $_GET['filename'] . '">Förhandsgranska</a>' . "\n";
	}

	echo $output;

	ui_bottom();
	?>
