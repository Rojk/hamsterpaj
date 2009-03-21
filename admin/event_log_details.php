<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 't67Graph.lib.php');
	require(PATHS_LIBRARIES . 't67Core.lib.php');
	$ui_options['menu_path'] = array('admin', 'statistik');
	$ui_options['title'] = 'Statistik för Hamsterpaj.net';
	$ui_options['stylesheets'][] = 't67Graph.css';
	$date = date('Y-m-d');

	if(!is_privilegied('use_statistic_tools'))
	{
		jscript_location("/");
	}
	
	$query = 'SELECT * FROM event_log WHERE `date` = "' . $date . '" ORDER BY `date` ASC';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	
	while($data = mysql_fetch_assoc($result))
	{
		//preint_r($data);
		$graph[$data['event']][$data['hour']] = $data['count'];
	}
	
	foreach($graph AS $event => $values)
	{
		$out .= t67Graph_line(array('bars' => $values, 'title' => $event));
		foreach ($values AS $hour => $count)
		{
			$out .= $hour . ': ' . $count . '<br />';
		}
	}	
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>


