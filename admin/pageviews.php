<?php
	require('../include/core/common.php');
	require(PATHS_VENDORS . 'jpgraph/jpgraph.php');
	require(PATHS_VENDORS . 'jpgraph/jpgraph_line.php');
	
	if(!is_privilegied('use_statistic_tools'))
	{
		die('fiskrojk');
	}
	
	$ui_options['menu_path'] = array('admin', 'pageviews');
	$ui_options['title'] = 'Sidvisningsstatistikverktyg';
	
	switch(isset($_GET['action']) ? $_GET['action'] : 'default')
	{
		case 'generate_graph':
			if(isset($_GET['from_date'], $_GET['to_date']) && preg_match('/^20[0-9]{2}-[0-9]{2}-[0-9]{2}$/', $_GET['from_date']) && preg_match('/^20[0-9]{2}-[0-9]{2}-[0-9]{2}$/', $_GET['to_date']))
			{
				$query = 'SELECT date, views FROM pageviews WHERE date >= "' . $_GET['from_date'] . '" AND date <= "' . $_GET['to_date'] .'" ORDER BY date ASC LIMIT 9999';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
				$dates_to_plot = array();
				$views_to_plot = array();
				while($data = mysql_fetch_assoc($result))
				{
					$dates_to_plot[] = $data['date'];
					$views_to_plot[] = $data['views'];
				}
				
				$graph = new Graph(1000, 500, 'auto');    
				$graph -> SetScale('textlin');
				
				$graph -> xaxis -> SetTickLabels($dates_to_plot);
				$graph -> xaxis -> SetLabelAngle(90);
				
				$lineplot = new LinePlot($views_to_plot);
				$lineplot -> SetColor('orange');
				
				$graph -> Add($lineplot);
				$graph -> Stroke();
			}
			else
			{
				die('Invalid input.');
			}
			
		// Notice: No break;!	
		
		default:
			ui_top($ui_options);
			
			echo '<h1>Visa statistik, sidvisningar (pageviews)</h1>' . "\n";
			echo '<h2>1. Välj periodicitet</h2>' . "\n";
			echo rounded_corners_top();
			
			$query = 'SELECT MIN(date) AS min_date, MAX(date) AS max_date FROM pageviews';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
			if(mysql_num_rows($result) == 1)
			{
				$data = mysql_fetch_assoc($result);
				$last_day = strtotime($data['min_date']);
				$end_day = strtotime($data['max_date']);
				$one_day = 86400;
				
				$dates = array();
				while($last_day <= $end_day)
				{
					$dates[] = date('Y-m-d', $last_day);
					$last_day += $one_day;
				}
				
				echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="get">' . "\n";
				echo '<input type="hidden" name="action" value="generate_graph" />' . "\n";
				
				echo 'Generera statistk från ';
				
				// Start day...
				$default_date = date('Y-m-d', strtotime('-1 week'));
				echo '<select name="from_date">' . "\n";
				foreach($dates as $date)
				{
					echo "\t" . '<option' . (($date == $default_date) ? ' selected="selected"' : '') . '>' . $date . '</option>' . "\n";
				}
				echo '</select>' . "\n";
				
				echo ' till ';
				
				// End day...
				$default_date = date('Y-m-d', strtotime('now'));
				echo '<select name="to_date">' . "\n";
				foreach($dates as $date)
				{
					echo "\t" . '<option' . (($date == $default_date) ? ' selected="selected"' : '') . '>' . $date . '</option>' . "\n";
				}
				echo '</select>';
				
				echo ' <input type="submit" class="button_40" value="Go &raquo;" />' . "\n";
				echo '</from>' . "\n";
			}
			else
			{
				echo 'Error on line ' . __LINE__;
			}
			
			echo rounded_corners_bottom();
			
			ui_bottom();
	}
?>