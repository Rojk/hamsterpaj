<?php
	require('../include/core/common.php');

	switch($_GET['action'])
	{
		case 'remove':
			if(is_numeric($_GET['entry_id']))
			{
				$query = 'SELECT recipient FROM traffa_guestbooks WHERE id = "' . $_GET['entry_id'] . '" LIMIT 1';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				$data = mysql_fetch_assoc($result);
				if($data['recipient'] == $_SESSION['login']['id'])
				{
					$query = 'UPDATE traffa_guestbooks SET deleted = 1 WHERE id = "' . $_GET['entry_id'] . '" LIMIT 1';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
			}
			break;
		case 'recreate':
			if(is_numeric($_GET['entry_id']))
			{
				$query = 'SELECT recipient FROM traffa_guestbooks WHERE id = "' . $_GET['entry_id'] . '" LIMIT 1';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				$data = mysql_fetch_assoc($result);
				if($data['recipient'] == $_SESSION['login']['id'])
				{
					$query = 'UPDATE traffa_guestbooks SET deleted = 0 WHERE id = "' . $_GET['entry_id'] . '" LIMIT 1';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
			}		
			break;
		case 'private':
			if(is_numeric($_GET['entry_id']))
			{
				$query = 'SELECT recipient, sender FROM traffa_guestbooks WHERE id = "' . $_GET['entry_id'] . '" LIMIT 1';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				$data = mysql_fetch_assoc($result);
				if($data['recipient'] == $_SESSION['login']['id'] || $data['sender'] == $_SESSION['login']['id'])
				{
					$query = 'UPDATE traffa_guestbooks SET is_private = 1 WHERE id = "' . $_GET['entry_id'] . '" LIMIT 1';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
			}
			break;
		case 'unprivate':
			if(is_numeric($_GET['entry_id']))
			{
				$query = 'SELECT recipient FROM traffa_guestbooks WHERE id = "' . $_GET['entry_id'] . '" LIMIT 1';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				$data = mysql_fetch_assoc($result);
				if($data['recipient'] == $_SESSION['login']['id'])
				{
					$query = 'UPDATE traffa_guestbooks SET is_private = 0 WHERE id = "' . $_GET['entry_id'] . '" LIMIT 1';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
			}
			break;
	}

?>