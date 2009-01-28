<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('spel', 'nord');
	$ui_options['title'] = 'Nord';
	ui_top($ui_options);
	
	echo '<iframe src="http://www.nordgame.com/hamsterpaj_web.jsp" style="width: 100%; margin: 0px; padding: 0px; height: 800px;" frameborder="0"></iframe>' . "\n";

	event_log_log('nord_frame_visit');	

	ui_bottom();
?>


