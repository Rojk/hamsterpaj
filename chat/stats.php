<?php
	require('../include/core/common.php');

	if(in_array($_GET['chan'], array('moget', 'fjortis', 'kuddhornan', 'traffa')))
	{
		$ui_options['menu_path'] = array('chatt', 'statistik', $_GET['chan']);
		$filename = $_GET['chan'];
	}
	else
	{
		$ui_options['menu_path'] = array('chatt', 'statistik');		
		$filename = 'all';
	}
	$ui_options['title'] = 'Chattstatistik för Hamsterpaj.net';
	ui_top($ui_options);
	
	echo '<iframe src="http://ircstats.t67.se/' . $filename . '.html" style="width: 100%; margin: 0px; padding: 0px; height: 3000px; overflow: hidden;" frameborder="0"></iframe>' . "\n";


	ui_bottom();
?>


