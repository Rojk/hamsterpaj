<?php
	require('../include/core/common.php');
	$ui_options['current_menu'] = 'hamsterpaj';
	$ui_options['title'] = 'Pornalizer';
	ui_top($ui_options);
	
	$query = 'SELECT * FROM event_log ORDER BY event, date ASC, hour ASC';
	$result = mysql_query($query);
	while($data = mysql_fetch_assoc($result))
	{
		$chart[$data['event']][$data['date'] . '_' . $data['hour']] = $data['count'];
	}

	foreach($chart AS $event_type => $event)
	{
		$url = '/event_log_chart.php?';
		foreach($event AS $xaxis => $yaxis)
		{
			$url .= $xaxis . '=' . $yaxis . '&';
		}
		
		$url = substr($url, 0, -1);
		
		echo '<h2>' . $event_type . '</h2>' . "\n";
		echo '<img src="' . $url . '" /><br />' . "\n";
		echo $url;
		
	}


	ui_bottom();
?>


