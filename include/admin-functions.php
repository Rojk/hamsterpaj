<?php

function ov_check_info($user_id)
{
	$keys = array('visible_level', 'firstname', 'surname', 'email', 'msn', 'streetaddress', 'zip_code', 'birthday', 'phone_ov');
	$query = 'SELECT firstname, surname, email, msn, streetaddress, zip_code, birthday, visible_level, phone_ov';
	$query .= ' FROM userinfo u';
	$query .= ' WHERE u.userid="' . $user_id . '"';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if($data = mysql_fetch_assoc($result))
	{
		$filled = true;
		foreach($keys as $key)
		{
			if($data[$key] == null or $data[$key] == '')
			{
				$filled = false;
			}
		}
	}
	return $filled;
}

function admin_report_event($username, $action, $resource_id)
{
/*	global $hp_includepath;
	$log_text = date('Y-m-d H:i:s') . "\t" . $username . "\t" . $action . "\t" . $resource_id . "\n";
	$logfile = fopen($hp_includepath . 'admin_logs/' . date('Y-m-d') . '.log', 'a');
	fwrite($logfile, $log_text);
	fclose($logfile);*/
	log_admin_event('report event', $action , $_SESSION['login']['id'], $username, $resource_id);
}


// Används för att räkna ordningsvakters förehavanden
function admin_action_count($admin_id, $event)
{
	switch ($event)
	{
		case 'post_removed':
			$query_insert = 'INSERT INTO admin_counts (user_id, posts_removed) VALUES ("' . $admin_id . '", 1)';
			$query_update = 'UPDATE admin_counts SET posts_removed = posts_removed + 1 WHERE user_id="' . $admin_id . '"';
		break;
		case 'avatar_denied':
			$query_insert = 'INSERT INTO admin_counts (user_id, avatars_denied) VALUES ("' . $admin_id . '", 1)';
			$query_update = 'UPDATE admin_counts SET avatars_denied = avatars_denied + 1 WHERE user_id="' . $admin_id . '"';
		break;
		case 'avatar_approved':
			$query_insert = 'INSERT INTO admin_counts (user_id, avatars_approved) VALUES ("' . $admin_id . '", 1)';
			$query_update = 'UPDATE admin_counts SET avatars_approved = avatars_approved + 1 WHERE user_id="' . $admin_id . '"';
		break;
	}
	log_to_file('admin', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'admin_action_count ' . $event, $query_insert);
	mysql_query($query_insert) or mysql_query($query_update) or die(report_sql_error($query_update, __FILE__, __LINE__));
}

?>
