<?php
	require('../include/core/common.php');
	$ui_options['stylesheets'][] = 'start.css';
	$ui_options['stylesheets'][] = 'fp_module_create.css';

	$ui_options['title'] = 'Anpassa startsidemodul';
	$ui_options['menu_path'] = array('hamsterpaj');

	if($_POST['mode'] == 'create')
	{
		$query = 'INSERT INTO fp_modules(code_mode, launch, removal, name)';
		$query .= ' VALUES("' . $_POST['code_mode'] . '", "' . strtotime($_POST['launch']) . '", "' . strtotime($_POST['removal']) . '", "' . $_POST['name'] . '")';
		mysql_query($query) or die(report_sql_error($query));
		
		$id = mysql_insert_id();
		if($id > 0)
		{
			file_put_contents(PATHS_INCLUDE . 'fp_modules/' . $id . '.php', html_entity_decode(stripslashes($_POST['code'])));
		}
	}

	$o .= '<form method="post">' . "\n";
	$o .= '<input type="hidden" name="mode" value="create" />' . "\n";
	
	$o .= '<ul id="fp_admin_labels">
					<li><label for="module_name">Namn</label></li>
					<li><label for="module_release">Release</label></li>
					<li><label for="module_removal">Borttagning</label></li>
				</ul>' . "\n";

	$o .= '<ul id="fp_admin_fields">
					<li><input type="text" name="name" id="module_name" /></li>
					<li><input type="text" name="launch" id="module_release" value="' . date('Y-m-d H:i') . '" /></li>
					<li><input type="text" name="removal" id="module_removal" value="' . date('Y-m-d H:i', time()+86400*1.8) . '" /></li>
				</ul>' . "\n";
	
	$o .= '<ul id="fp_admin_options">
					<li>
						<input type="radio" name="code_mode" value="html" id="code_html" />
						<label for="code_html">HTML</label>
					</li>
					<li>
						<input type="radio" name="code_mode" value="php" id="code_php" />
						<label for="code_php">PHP</label>
					</li>
					<li>
						<input type="checkbox" name="commenting" id="module_commenting" value="1" />
						<label for="module_commenting">Kommentering</label>
					</li>
					<li>
						<input type="checkbox" name="grading" id="module_grading" value="1" />
						<label for="module_grading">Betygsättning</label>
					</li>
					<li>
						<input type="checkbox" name="published" id="module_published" value="1" />
						<label for="module_published">Publiceringsdatum</label>
					</li>
				</ul>' . "\n";


	$o .= '<label class="fp_admin_code_label">Kod</label>' . "\n";
	$o .= '<textarea name="code"></textarea>' . "\n";

	$o .= '<p>Sätt gärna style-egenskaper via style="" istället för med nya klasser/IDn, då det blir en röra att hålla koll på alla klasser efter ett tag...</p>' . "\n";
	
	$o .= '<input type="submit" value="Spara" />' . "\n";
	$o .= '</form>' . "\n";
	
	ui_top($ui_options);
	echo $o;
	ui_bottom();
	?>
