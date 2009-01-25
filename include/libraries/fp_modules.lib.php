<?php
	function fp_modules_list($modules)
	{
		$o .= '<form method="post" class="fp_module_admin_list">' . "\n";
		$o .= '<input type="hidden" name="action" value="update_modules" />' . "\n";
		$o .= '<input type="submit" value="spara" />' . "\n";
		$o .= '<ol>' . "\n";
		foreach($modules AS $module)
		{
			$module_ids[] = $module['id'];
			$o .= '<li>' . "\n";
			$o .= '<p>' . $module['score'] . 'p / ' . cute_number($module['clicks']) . ' klick</p>' . "\n";
			$o .= '<h3><a href="/admin/fp_module.php?action=edit&id=' . $module['id'] . '">' . $module['name'] . '</a></h3>' . "\n";
			$o .= '<br style="clear: both;" />' . "\n";
			$o .= '<label>Release</label><label>Removal</label><label>Prio</label>' . "\n";
			$o .= '<input type="text" name="' . $module['id'] .'_launch" value="' . date('Y-m-d H:i', $module['launch']) . '" />' . "\n";
			$o .= '<input type="text" name="' . $module['id'] .'_removal" value="' . date('Y-m-d H:i', $module['removal']) . '" />' . "\n";
			$o .= '<input type="text" name="' . $module['id'] .'_priority" id="prio_' . $module['id'] . '" value="' . $module['priority'] . '" class="prio" />' . "\n";
			$o .= '</li>' . "\n";
		}
		$o .= '<input type="hidden" name="module_ids" value="' . implode(',', $module_ids) . '" />' . "\n";
		$o .= '</ol>' . "\n";
		$o .= '</form>' . "\n";
		
		return $o;
	}
	
	function fp_modules_fetch($options)
	{
		$query = 'SELECT * FROM fp_modules WHERE 1';
		$query .= (isset($options['removal_min'])) ? ' AND removal >= "' . $options['removal_min'] . '"' : '';
		$query .= (isset($options['removal_max'])) ? ' AND removal <= "' . $options['removal_max'] . '"' : '';
		$query .= (isset($options['launch_min'])) ? ' AND launch >= "' . $options['launch_min'] . '"' : '';
		$query .= (isset($options['launch_max'])) ? ' AND launch <= "' . $options['launch_max'] . '"' : '';
		$query .= (isset($options['id'])) ? ' AND id = "' . $options['id'] . '"' : '';

		$query .= (isset($options['order-by'])) ? ' ORDER BY ' . $options['order-by'] : ' ORDER BY priority';		
		$query .= (isset($options['order-direction'])) ? ' ' . $options['order-direction'] : ' DESC';

		$result = mysql_query($query) or report_sql_error($query);
		while($data = mysql_fetch_assoc($result))
		{
			$modules[] = $data;
		}
		
		return $modules;
	}
	
	
	function fp_module_form($module)
	{
		$o .= '<form method="post" class="fp_module_form">' . "\n";
		$o .= '<input type="hidden" name="mode" value="' . (isset($module['id']) ? 'edit' : 'create') . '" />' . "\n";
		
		$o .= '<ul id="fp_admin_labels">
						<li><label for="module_name">Namn</label></li>
						<li><label for="module_release">Release</label></li>
						<li><label for="module_removal">Borttagning</label></li>
					</ul>' . "\n";
	
		$launch = (isset($module['launch'])) ? $module['launch'] : time();
		$removal = (isset($module['removal'])) ? $module['removal'] : time()+86400*1.8;
		$o .= '<ul id="fp_admin_fields">
						<li><input type="text" name="name" id="module_name" value="' . $module['name'] . '" /></li>
						<li><input type="text" name="launch" id="module_release" value="' . date('Y-m-d H:i', $launch) . '" /></li>
						<li><input type="text" name="removal" id="module_removal" value="' . date('Y-m-d H:i', $removal) . '" /></li>
					</ul>' . "\n";
		
		$o .= '<ul id="fp_admin_options">
						<li>
							<input type="radio" name="code_mode" value="html" id="code_html"' . (($module['code_mode'] != 'php') ? ' checked="checked"' : '') . ' />
							<label for="code_html">HTML</label>
						</li>
						<li>
							<input type="radio" name="code_mode" value="php" id="code_php"' . (($module['code_mode'] == 'php') ? ' checked="checked"' : '') . ' />
							<label for="code_php">PHP</label>
						</li>
						<li>
							<input type="checkbox" name="commenting" id="module_commenting" value="true"' . (($module['commenting'] == 'true') ? ' checked="checked"' : '') . ' />
							<label for="module_commenting">Kommentering</label>
						</li>
						<li>
							<input type="checkbox" name="grading" id="module_grading" value="true"' . (($module['grading'] == 'true') ? ' checked="checked"' : '') . ' />
							<label for="module_grading">Betygsättning</label>
						</li>
						<li>
							<input type="checkbox" name="published" id="module_published" value="true"' . (($module['published'] == 'true') ? ' checked="checked"' : '') . ' />
							<label for="module_published">Publiceringsdatum</label>
						</li>
						<li>
							<select name="format">
								<option value="normal">Normalt</option>
								<option value="2_3"' . (($module['format'] == '2_3') ? ' selected="true"' : '') . '>Två-tredjedels</option>
							</select>
						</li>
					</ul>' . "\n";

	
		$o .= '<label class="fp_admin_code_label">Kod</label>' . "\n";
		if($module['id'] > 0 && !isset($module['code']))
		{
			$code = htmlspecialchars(file_get_contents(PATHS_INCLUDE . 'fp_modules/' . $module['id'] . '.php'));
		}
		else
		{
			$code = $module['code'];
		}
		$o .= '<textarea name="code" wrap="off">' . $code . '</textarea>' . "\n";
	
		$o .= '<p>Sätt gärna style-egenskaper via style="" istället för med nya klasser/IDn, då det blir en röra att hålla koll på alla klasser efter ett tag...</p>' . "\n";
		
	
		$o .= '<input type="submit" value="Spara" />' . "\n";
		$o .= '<input type="submit" name="preview" value="Förhandsgranska" />' . "\n";
		$o .= '</form>' . "\n";

		$o .= '<a href="/admin/fp_module_list.php">Sortera moduler</a>' . "\n";
		
		return $o;
	}
	
?>