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

function admin_delete_photo($user_id, $image_id) // NOT USED
{
	global $hp_includepath;
	require_once($hp_includepath . 'message-functions.php');
	deletePhoto($user_id, $image_id);
	$title = 'En bild i ditt fotoalbum har tagits bort';
	$message = 'Bild nummer ' . intval($image_id + 1) . ' i ditt fotoalbum har raderats av en administratör.' . "\n";
	$message .= 'Det kan finnas många anledningar till att en bild tas bort, men oftast beror det på något av följande:' . "\n";
	$message .= '* Bilden innehöll rasistisk eller nazistisk propaganda.' . "\n";
	$message .= '* Bilden var pornografisk.' . "\n";
	$message .= '* Bilden var rent ut sagt äcklig eller vidrig, och kunde verka obehaglig för våra yngre medlemmar.' . "\n";
	$message .= '* Bilden var kränkande.' . "\n";
	$message .= "\n\n";
	$message .= 'Vi som arbetar med hamsterpaj vill göra siten till en så trevlig webbplats som möjligt, därför är behöver vi';
	$message .= ' ibland ta bort bilder. Vi hoppas att du förstår varför bilden togs bort och önskar dig en trevlig tid här på hamsterpaj.';
	$message .= "\n\n\n" . 'Med vänliga hälsningar, hamsterpaj.net administrations-team.';
	messages_send(2348, $user_id, $title, $message);
	//log_admin_event('deleted photo', $message , $_SESSION['login']['id'], $user_id, $image_id);
	//loggning görs i deletePhoto()
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
