<?php
	function fp_modules_list($modules)
	{
		$o .= '<form method="post">' . "\n";
		$o .= '<input type="hidden" name="action" value="update_modules" />' . "\n";
		$o .= '<input type="submit" value="spara" />' . "\n";
		$o .= '<ol id="fp_module_list">' . "\n";
		foreach($modules AS $module)
		{
			$module_ids[] = $module['id'];
			$o .= '<li>' . "\n";
			$o .= '<h3>' . $module['name'] . '</h3>' . "\n";
			$o .= '<p>' . $module['score'] . 'p / ' . cute_number($module['clicks']) . ' klick</p>' . "\n";
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

		$query .= (isset($options['order-by'])) ? ' ORDER BY ' . $options['order-by'] : ' ORDER BY priority';		
		$query .= (isset($options['order-direction'])) ? ' ' . $options['order-direction'] : ' DESC';

		$result = mysql_query($query) or report_sql_error($query);
		while($data = mysql_fetch_assoc($result))
		{
			$modules[] = $data;
		}
		
		return $modules;
	}
?>