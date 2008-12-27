<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/fp_modules.lib.php');

	$ui_options['stylesheets'][] = 'start.css';
	$ui_options['stylesheets'][] = 'fp_module_create.css';

	$ui_options['title'] = 'Arrangera om startsidemoduler';
	$ui_options['menu_path'] = array('hamsterpaj');
	
	if($_POST['action'] == 'update_modules')
	{
		$module_ids = explode(',', $_POST['module_ids']);
		foreach($module_ids AS $id)
		{
			$launch = strtotime($_POST[$id . '_launch']);
			$removal = strtotime($_POST[$id . '_removal']);
					
			$query = 'UPDATE fp_modules SET launch= "' . $launch . '", removal = "' . $removal . '", priority = "' . $_POST[$id . '_priority'] . '"';
			$query .= ' WHERE id = "' . $id . '" LIMIT 1';
			mysql_query($query);
		}
	}



	$o .= '<a href="/admin/fp_module_create.php">Ny modul</a>' . "\n";

	$o .= '<div style="width: 320px; float: left;">' . "\n";
	$modules = fp_modules_fetch(array('removal_min' => time(), 'launch_max' => time()));
	$o .= '<h1>Moduler live</h1>' . "\n";
	$o .= fp_modules_list($modules);
	$o .= '</div>' . "\n";

	$o .= '<div style="width: 300px; float: left;">' . "\n";
	$modules = fp_modules_fetch(array('launch_min' => time(), 'orer-by' => 'launch', 'order-direction' => 'ASC'));
	$o .= '<h1>Skall släppas</h1>' . "\n";
	$o .= fp_modules_list($modules);
	$o .= '</div>' . "\n";
	
	$o .= '<br style="clear: both;" />' . "\n";
	
	$modules = fp_modules_fetch(array('removal_max' => time()));
	$o .= '<h1>För gamla / borttagna</h1>' . "\n";
	$o .= fp_modules_list($modules);

?>
<script src="http://jolts.se/javascripts/json2.js" type="text/javascript"></script>
<script>
function fp_module_admin_sort()
{
	alert('Fail');
}	
</script>
<?php
	ui_top($ui_options);
	echo $o;
	ui_bottom();
	?>
