<?php
	require('../include/core/common.php');
		
	$ui_options['menu_path'] = array('admin');
	$ui_options['title'] = 'Adminstart';
	
	ui_top($ui_options);
	
	echo 'Välj i menyn till vänster eller nåt...';
	
	ui_bottom();	
?>