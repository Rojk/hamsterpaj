<?php
	require('../include/core/common.php');

	$CHANNEL = 'chat';
	
	if(isset($_GET['message']) && $_SESSION['webchat']['last_message'] != $_GET['message'] && strlen($_GET['message']) > 0)
	{
		$query = 'INSERT INTO webchat(channel, user, timestamp, text) VALUES("' . $CHANNEL . '", "' . $_SESSION['login']['id'] . '", "' . time() . '", "' . $_GET['message'] . '")';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

		$_SESSION['webchat']['last_message'] = $_GET['message'];
		
		event_log_log('webchat_post');
	}
	else
	{
		$_SESSION['webchat']['last_read_id'] = (isset($_SESSION['webchat']['last_read_id'])) ? $_SESSION['webchat']['last_read_id'] : 0;
	
//		$_SESSION['webchat']['last_read_id'] = 0;
	
		$query = 'SELECT w.*, l.username FROM webchat AS w, login AS l';
		$query .= ' WHERE l.id = w.user AND w.channel = "' . $CHANNEL . '" AND w.id > "' . $_SESSION['webchat']['last_read_id'] . '"';
		$query .= ' ORDER BY w.id DESC LIMIT 25';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$entries = array();
		while($data = mysql_fetch_assoc($result))
		{
			$_SESSION['webchat']['last_read_id'] = ($data['id'] > $_SESSION['webchat']['last_read_id']) ? $data['id'] : $_SESSION['webchat']['last_read_id'];
			$data['time_readable'] = date('H:i:s', $data['timestamp']);
			$highlight = strpos(strtolower($data['text']), strtolower($_SESSION['login']['username']));
			if($highlight > 0 || $highlight === 0)
			{
				$data['highlight'] = 'true';
			}
			else
			{
				$data['highlight'] = 'false';				
			}
			
			// This is for private messages
			if($data['text']{0} != '@' || is_privilegied('use_ghosting_tools') || strtolower(substr($data['text'], 0, 1 + strlen($_SESSION['login']['username']))) == strtolower('@' . $_SESSION['login']['username']) || $data['user'] == $_SESSION['login']['id'])
			{
				array_unshift($entries, $data);
			}
		}
	
		echo json_encode($entries);
	}
?>