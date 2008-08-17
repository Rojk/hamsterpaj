<?php
	require('include/core/common.php');
	$query = 'UPDATE internal_ads SET clicks = clicks + 1 WHERE handle LIKE "' . $_GET['ad'] . '" LIMIT 1';
	mysql_query($query);
	
	event_log_log('internal_ad_click');
	
	header('Location: ' . $_GET['redirect']);
	
?>