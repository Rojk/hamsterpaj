<?php
	require('../include/core/common.php');
	
	if(login_checklogin())
	{
		switch($_GET['report_type'])
		{
			case 'post':
				if($_GET['action'] == 'unreport' && is_privilegied('abuse_report_handler'))
				{
					$query = 'DELETE FROM abuse_reports WHERE reference_id = "' . $_GET['reference_id'] . '" AND object_type = "post" LIMIT 1';
				}
				if($_GET['action'] == 'report')
				{
					event_log_log('forum_post_reported');
					$query = 'INSERT INTO abuse_reports (reference_id, object_type, author, text) VALUES("' . $_GET['reference_id'] . '", "post", "' . $_SESSION['login']['id'] . '", "' . $_GET['comment'] . '")';
				}
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));			
				break;
			case 'user':
				if($_GET['action'] == 'unreport' && is_privilegied('abuse_report_handler'))
				{
					$query = 'DELETE FROM abuse_reports WHERE reference_id = "' . $_GET['reference_id'] . '" AND object_type = "user" LIMIT 1';
				}
				if($_GET['action'] == 'report')
				{
					event_log_log('user_reported');
					$query = 'INSERT INTO abuse_reports (reference_id, object_type, author, text) VALUES("' . $_GET['reference_id'] . '", "user", "' . $_SESSION['login']['id'] . '", "' . $_GET['comment'] . '")';
				}
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);			
				break;
		}

	}
	
?>