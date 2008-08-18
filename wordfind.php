<?php
	/* OPEN_SOURCE */
	
//	require('include/core/common.php');
	require('include/core/common.php');
	$ui_options['title'] = 'Startsidan på Hamsterpaj - Sveriges ungdomssida!';
	$ui_options['javascripts'][] = 'wordfind.js';
	ui_top($ui_options);
	
	echo '<table id="wordfind" style="width: 100%; height: 638px;">' . "\n";
	for($r = 0; $r < 10; $r++)
	{
		echo '<tr>' . "\n";
		for($c = 0; $c < 10; $c++)
		{
			echo '<td>' . $r . $c . '</td>' . "\n";
		}
		echo '</tr>' . "\n";
	}
	echo '</table>' . "\n";

	ui_bottom();
?>
