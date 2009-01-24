<?php
	require('../include/core/common.php');
	
	if(login_checklogin() && is_privilegied('abuse_report_handler'))
	{
		if(isset($_GET['report_id'], $_GET['reply']) && is_numeric($_GET['report_id']))
		{
			$query = 'UPDATE abuse SET reply="' . $_GET['reply'] . '", admin_id = ' . $_SESSION['login']['id'] . ', reply_timestamp = ' . time() . ' WHERE id = ' . $_GET['report_id'] . ' LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	
			$query = 'SELECT reporter FROM abuse WHERE id = ' . $_GET['report_id'] . ' LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			$data = mysql_fetch_assoc($result);
			
			$message = 'Hej!' . "\n" . 'Din rapport som du skickade till oss tidigare idag är nu granskad av ' . $_SESSION['login']['username'] . '. Svaret på rapporten är:' . "\n";
			$message .= $_GET["extra"]."\n" . $_GET['reply'];
			$message .= "\n\n" . 'Tack för att du hjälper oss att göra Hamsterpaj till ett bättre och mer trivsamt ställe. Keep on rocking!';
			$message .= "\n\n" . '/Webmaster (referensnummret på rapporten är '. $_GET['report_id'] . ')';
			guestbook_insert(array(
				'sender' => 2348,
				'recipient' => $data['reporter'],
				'message' => mysql_real_escape_string(htmlspecialchars($message))
			));
		}
	}
	else
	{
		die("du har inte tillgång hit");
	}
?>