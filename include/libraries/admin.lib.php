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

function refuse_image($userid, $validator) 
	{
		if($userid == 17505 || $userid == 573633 || $userid == 625747 || $userid == 68767)
		{
			die('Man kan inte ta bort denna bild...');
			exit;
		}

		global $hp_path;
		$query = 'UPDATE userinfo SET image = "3", image_validator = "' . $validator . '" ';
		$query.= ' WHERE userid = "' . $userid . '" LIMIT 1';
		mysql_query($query) or die();
		if(unlink(PATHS_IMAGES . 'users/full/' . $userid . '.jpg') && unlink(PATHS_IMAGES . 'users/thumb/' . $userid . '.jpg'))
		{
			guestbook_insert(array(
				'sender' => 2348,
				'recipient' => $userid,
				'is_private' => 1,
				'message' => mysql_real_escape_string('OBS! Detta meddelande har skickats automatiskt. Det är ingen idé att svara på meddelandet, kontakta någon ordningsvakt eller fråga i forumet.
Din bild har nekats, acceptera det.

Välj en ny bild som följer vår policy:

1) Bilden föreställer dig och ansiktet syns tydligt

2) Det är bara du på bilden

3) Ingen alkohol, ingen porr och inga nazistiska symboler

4) Inget som bryter mot Svensk lag, är upphovsrättskyddat eller är kränkande för någon person förekommer

5) Det är en skarp och ljus bild på dig

6) Bilden är inte taggad från någon annan sida ex. snyggast

7) Du har inte angett rätt ålder/kön så att det överensstämmer med personen på bilden')
			));
    } 
		else 
		{
        	     echo '<script language="javascript">alert("Ett fel uppstod när ' . $userid . '.jpg skulle tas bort!");</script>';
		}
		admin_report_event($_SESSION['login']['username'], 'Refused avatar', $userid);


		log_admin_event('avatar validated', 'denied', $validator, $userid, 0); //image id not available here
		admin_action_count($_SESSION['login']['id'], 'avatar_denied');

	}
?>