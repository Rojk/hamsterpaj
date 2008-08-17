<?php
	require('include/core/common.php');
	
	if(is_numeric($_GET['id']))
	{
		$query = 'UPDATE recent_updates SET clicks = clicks + 1 WHERE id = "' . $_GET['id'] . '" LIMIT 1';
		mysql_query($query);
		
		if($_GET['source'] == 'global_notice')
		{
			$_SESSION['recent_update_notifier'][$_GET['id']] = 10;
			event_log_log('recent_update_global_notice_click');
		}
		else
		{
			event_log_log('recent_update_fp_click');
		}

		if($_GET['url'] != '#survey')
		{
			header('location: ' . html_entity_decode($_GET['url']));
		}
	}
?>