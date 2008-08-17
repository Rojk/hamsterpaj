<?php
	include('../include/jpgraph/jpgraph.php');
	include('../include/jpgraph/jpgraph_line.php');

// Some data
$datax = array("heggan","flipp","holger","lasse","ghey","johan");
$datay = array(28,13,24,50,90,11);

// Setup graph
$graph = new Graph(1000,250,"auto");
$graph->img->SetMargin(40,150,40,80);    
$graph->SetScale("textlin");
$graph->SetShadow();

//Setup title

// Use built in font

// Slightly adjust the legend from it's default position

// Setup X-scale
$graph->xaxis->SetTickLabels($datax);

// Create the first line
$p1 = new LinePlot($datay);
$p1->mark->SetType(MARK_FILLEDCIRCLE);
$p1->mark->SetFillColor("red");
$p1->mark->SetWidth(4);
$p1->SetColor("blue");
$p1->SetCenter();
$graph->Add($p1);

// Output line
$graph->Stroke();
?>
