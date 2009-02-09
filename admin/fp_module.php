<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'fp_modules.lib.php');
	$ui_options['stylesheets'][] = 'fp_modules.css';

	$ui_options['title'] = 'Anpassa startsidemodul';
	$ui_options['menu_path'] = array('hamsterpaj');
	
	if (!is_privilegied('fp_module_rearrange'))
	{
		ui_top($ui_options);
		echo '<div class="error">';
		echo '<strong>Åtkomst nekad</strong>';
		echo '</div>';
		ui_bottom();
		exit;
	}

	if(isset($_POST['preview']))
	{
		$o .= '<h1>Previewing</h1><br /><br />';
		$o .= '<ol id="fp_module_list">';
		$o .= '<li>' . "\n";
		$o .= html_entity_decode(stripslashes($_POST['code']));
		$o .= '</li>' . "\n";
		$o .= '</ol>' . "\n";
		
		
		$module = $_POST;
		$module['launch'] = strtotime($_POST['launch']);
		$module['removal'] = strtotime($_POST['removal']);
		$module['code'] = stripslashes($module['code']);		

		$o .= fp_module_form($module);
	}
	else
	{
		if($_POST['mode'] == 'create')
		{
			$grading = ($_POST['grading'] == 'true') ? 'true' : 'false';
			$commenting = ($_POST['commenting'] == 'true') ? 'true' : 'false';
			$published = ($_POST['published'] == 'true') ? 'true' : 'false';
			$published = ($_POST['piraja'] == 'true') ? 'true' : 'false';
			$published = ($_POST['gadget'] == 'true') ? 'true' : 'false';
			$format = $_POST['format'];
			
			$query = 'INSERT INTO fp_modules(code_mode, launch, removal, name, grading, commenting, published, format, piraja, gadget)';
			$query .= ' VALUES("' . $_POST['code_mode'] . '", "' . strtotime($_POST['launch']) . '", "' . strtotime($_POST['removal']);
			$query .= '", "' . $_POST['name'] . '", "' . $grading . '", "' . $commenting . '", "' . $published . '", "' . $format;
			$query .= '", "' . $piraja . '", "' . $grading . '")';
			mysql_query($query) or die(report_sql_error($query));
			
			$id = mysql_insert_id();
			if($id > 0)
			{
				file_put_contents(PATHS_DYNAMIC_CONTENT . 'fp_modules/' . $id . '.php', html_entity_decode(stripslashes($_POST['code'])));
			}
		}
		
		if($_GET['action'] == 'edit')
		{
			if(isset($_POST['name']))
			{
				$grading = ($_POST['grading'] == 'true') ? 'true' : 'false';
				$commenting = ($_POST['commenting'] == 'true') ? 'true' : 'false';
				$published = ($_POST['published'] == 'true') ? 'true' : 'false';
				$piraja = ($_POST['piraja'] == 'true') ? 'true' : 'false';
				$gadget = ($_POST['gadget'] == 'true') ? 'true' : 'false';
				$format = $_POST['format'];
			
				$query = 'UPDATE fp_modules SET name = "' . $_POST['name'] . '", launch = "' . strtotime($_POST['launch']) . '"';
				$query .= ', removal = "' . strtotime($_POST['removal']) . '", code_mode = "' . $_POST['code_mode'] . '"';
				$query .= ', grading = "' . $grading . '", commenting = "' . $commenting . '", published = "' . $published . '"';
				$query .= ', format = "' . $format . '", piraja = "' . $piraja . '", gadget = "' . $gadget . '" WHERE id = "' . $_GET['id'] . '"';
				
				mysql_query($query);
				
				file_put_contents(PATHS_DYNAMIC_CONTENT . 'fp_modules/' . $_GET['id'] . '.php', html_entity_decode(stripslashes($_POST['code'])));
				
			}
			$module = array_pop(fp_modules_fetch(array('id' => $_GET['id'])));
			$o .= fp_module_form($module);
		}
		else
		{
			$o .= fp_module_form();
		}
	}
		
	ui_top($ui_options);
	echo $o;
	ui_bottom();
	?>
