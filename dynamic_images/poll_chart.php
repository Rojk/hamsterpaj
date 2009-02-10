<?php
	require('../include/core/common.php');
	require(PATHS_VENDORS . 'jpgraph/jpgraph.php');
	require(PATHS_VENDORS . 'jpgraph/jpgraph_pie.php');


	unset($data);
	for($i = 1; $i <= SURVEY_MAX_ALTERNATIVES; $i++)
	{
		if(isset($_GET['alt_' . $i]))
		{
			$data[$i] = $_GET['alt_' . $i];
		}
	}
	if(count($data) == 0 && is_numeric($_GET['poll_id']))
	{
		$poll = poll_fetch(array('id' => $_GET['poll_id']));
		for($i = 1; $i <= SURVEY_MAX_ALTERNATIVES; $i++)
		{
			if(strlen($poll[0]['alt_' . $i]) > 0)
			{
				$data[$i] = $poll[0]['alt_' . $i . '_votes'];
			}
		}
	}
	
	$graph  = new PieGraph(190,160);
	$graph->SetAntiAliasing();
	$graph->SetShadow();
		
	$p1 = new PiePlot($data);
	$p1->ShowBorder();
	$graph->SetFrame(false,'darkblue',2); 
	$p1->SetSliceColors($survey_chart_colors);

	$graph->Add($p1);

	$graph->Stroke(); 
?>