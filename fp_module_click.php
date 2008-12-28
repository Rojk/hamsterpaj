<?php
	require('include/core/common.php');
	
	$query = 'UPDATE fp_modules SET clicks = clicks + 1 WHERE id = "' . $_GET['module_id'] . '" LIMIT 1';
	mysql_query($query);
	
	event_log_log('fp_module_click');
	
	header('Location: ' . base64_decode($_GET['url']));
?>