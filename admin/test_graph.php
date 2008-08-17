<?php
	require_once("/storage/www/standard.php");
	require(PATHS_INCLUDE . 'jpgraph/jpgraph.php');
	require(PATHS_INCLUDE . 'jpgraph/jpgraph_line.php');
	
	if("a" == "a")
	{
		
		$dates_to_plot = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12');
		$views_to_plot = array('0', '10', '5', '7', '2', '5', '10', '1', '9', '3', '1', '8');
		
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
?>