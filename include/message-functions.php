<?php

function messages_pre_compose()
{
	echo '<div class="grey_faded_div">';
	echo '<h2>Skicka nytt meddelande</h2>';
	echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="get">';
	echo '<strong>Mottagare</strong><br />';
	echo '<input type="hidden" name="action" value="compose" />';
	echo '<input type="text" name="recipient_username" class="textbox" />';
	echo '<input type="submit" value="Skriv meddelande" class="button" />';
	echo '</form>';
	echo '</div>';
}

function messages_count_unread($user_id)
{
	$query = 'SELECT COUNT(id) AS unread FROM messages_new WHERE recipient = "' . $user_id . '" AND recipient_status = 0';
	$result = mysql_query($query) or die(report_sql_error($query));
	$data = mysql_fetch_assoc($result);
	return $data['unread'];
}

function messages_compose($user_id, $user_name, $title = null)
{
	$user_name = htmlspecialchars($user_name);
	$user_id = htmlspecialchars($user_id);
	$title = str_replace('"', '\\"', stripslashes($title));
	if($user_id < 1 && strlen($user_name) > 0)
	{
		$query = 'SELECT id FROM login WHERE username LIKE "' . str_replace('_', '\_', $user_name) . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query));
		$data = mysql_fetch_assoc($result);
		$user_id = $data['id'];
	}
	if(strlen($user_name) < 1 && $user_id > 0 && is_numeric($user_id))
	{
		$query = 'SELECT username FROM login WHERE id = "' . $user_id . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query));
		$data = mysql_fetch_assoc($result);
		$user_name = $data['username'];
	}
	if($user_id < 1 || strlen($user_name) < 1)
	{
		return false;
	}
	$fetch['login'] = array('id', 'username', 'regtimestamp', 'regip', 'lastlogon', 'lastip', 'lastaction', 'lastusernamechange', 'lastusername');
	$fetch['userinfo'] = array('contact1', 'contact2', 'gender', 'birthday', 'image', 'forum_signature', 'forum_posts');
	$fetch['traffa'] = array('firstname', 'profile_modules', 'guestbook_entries');
		
	$userinfo = login_load_user_data($user_id, $fetch);
	traffa_draw_user_div($user_id, $userinfo);
	
	echo '<div class="grey_faded_div">';
	echo '<h2>Skickar meddelande till ' . $user_name . '</h2>';
	echo '<p>';
	echo '<form name="compose" action="' . $_SERVER['PHP_SELF'] . '?action=send" method="post">';
	echo '<div style="float: right; width: 65px;">';
	echo '<strong>Infoga smilies</strong><br />';
	echo listSmilies('document.compose.message');
	echo '</div>';
	$title = htmlspecialchars(stripslashes($title));
	echo '<strong>Rubrik:</strong> <input type="text" name="title" class="textbox" style="width: 502px;" value="' . $title . '" maxlength="' . MESSAGES_MAX_TITLE_STRLEN . '"/><br />';
	echo '<input type="hidden" name="recipient" value="' . $user_id . '" />';
	echo '<textarea name="message" class="textbox" style="width: 550px; height: 200px;"></textarea><br />';
	echo '</div>';
	echo '<input type="submit" class="button" value="Skicka" />';
	echo '</form>';
	return true;
}

function fetch_new_discussion_id($sender_id, $recipient_id) {
	$query = 'SELECT MAX(discussion) AS discussion FROM messages_new WHERE ';
	$query .= '(sender = "' . $sender_id . '" AND recipient = "' . $recipient_id . '") ';
	$query .= 'OR ';
	$query .= '(sender = "' . $recipient_id . '" AND recipient = "' . $sender_id . '") ';
	$result = mysql_query($query) or die(report_sql_error($query));
	$data = mysql_fetch_assoc($result);
	return $data['discussion'] + 1;
}

function messages_send($sender, $recipient, $title, $message, $allowhtml = 0, $mass_message_id = 0)
{
	if (!is_array($recipient))
	{
		$recipient = array($recipient);
	}
	
	$discussion = fetch_new_discussion_id($sender, $recipient);
	if ($allowhtml != '1')
	{
		$message = $message;
	}
	else
	{
		$message = addslashes($message);
	}

	$message = nl2br($message);
	$title = htmlspecialchars($title);

	foreach ($recipient as $this_recipient) 
	{
		$query = 'INSERT INTO messages_new(sender, recipient, title, message, timestamp, discussion, mass_message_id) ';
		$query .= 'VALUES("'.$sender.'", "'.$this_recipient.'", "'.$title.'", "'.$message.'", UNIX_TIMESTAMP(), '.$discussion.', '. $mass_message_id . ')';
		mysql_query($query) or die(report_sql_error($query));
		$message_id = mysql_insert_id();
		$query = 'UPDATE userinfo SET messages_recieved = messages_recieved + 1 WHERE userid = ' . $this_recipient . ' LIMIT 1';
		mysql_query($query) or die(report_sql_error($query));
		$query = 'UPDATE userinfo SET messages_sent = messages_sent + 1 WHERE userid = ' . $sender . ' LIMIT 1';
		mysql_query($query) or die(report_sql_error($query));

		$query = 'SELECT session_id FROM login WHERE id = "' . $this_recipient . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query));
		$sessid_data = mysql_fetch_assoc($result);
		if(strlen($sessid_data['session_id']) > 1)
		{
			if($_SESSION['login']['id'] == $sender)
			{
				$bubblemessage = '"Så var det dags igen... ' . $_SESSION['login']['username'] . ' skickade nyss ett <b>nytt meddelande</b> till dig. Du kan <a href="/traffa/messages.php?action=read&message_id=' . $message_id . '">klicka här</a> om du vill läsa meddelandet."';
			}
			else
			{
				$bubblemessage = '"Tjena kompis! Du har nyss fått ett nytt <b>meddelande!</>! <a href="/traffa/messages.php">Till dina meddelanden &raquo;</a>"';
			}
			$remote_session = session_load($sessid_data['session_id']);
			$remote_session['bubblemessage'][] = $bubblemessage;
			$remote_session['notices']['unread_messages'] += 1;
			session_save($sessid_data['session_id'], $remote_session);
		}
	}
}

function messages_can_send($sender, $recipient, $title, $message)
{
	$return = null;
	if($recipient == 2348)
	{
		$return .= 'Webmaster är ett administrationskonto som inte används av någon människa.';
		$return .= 'Använd forumet eller hamsterpaj -> Support för att ställa en fråga om siten.' . "\n";
	}
	if(strlen($title) < MESSAGES_MIN_TITLE_STRLEN)
	{
		$return .= 'Titeln måste vara minst ' . MESSAGES_MIN_TITLE_STRLEN . ' tecken lång.' . "\n";
	}
	if(strlen($title) > MESSAGES_MAX_TITLE_STRLEN)
	{
		$return .= 'Titeln får inte vara mer än ' . MESSAGES_MAX_TITLE_STRLEN . ' tecken lång.' . "\n";
	}
	if (trim($title) == '')
  {
    $return .= 'Titeln måste vara minst ' . MESSAGES_MIN_TITLE_STRLEN . ' tecken lång.' . "\n";
  }
	if(!is_numeric($recipient))
	{
		$return .= 'Det verkar som om mottagare har angivits felaktigt. Detta är ett internt serverfel och bör aldrig kunna inträffa. Kontakta administratör.' . "\n";
	}
	if(strlen($message) < MESSAGES_MIN_MESSAGE_STRLEN)
	{
		$return .= 'Du måste skriva minst ' . MESSAGES_MIN_MESSAGE_STRLEN . ' tecken i ditt meddelande.' . "\n";
	}
	$message_check = content_check($message);
	if($message_check != 1)
	{
		$return .= $message_check . "\n";
	}
	$title_check = content_check($title);
	if($title_check != 1)
	{
		$return .= $title_check . "\n";
	}
	if(userblock_check($recipient, $sender) == 1)
	{
		$return .= 'Mottagaren har blockerat dig och meddelandet kan därför inte levereras.' . "\n";
	}

	if(strlen($return) > 1)
	{
		return $return;
	}
	return true;
}

function messages_delete($messages, $user_id)
{
	$query = 'UPDATE messages_new SET recipient_status = 2 WHERE (id = 0 ';
	$unreadquery = 'SELECT COUNT(*) AS unreadmessages FROM messages_new WHERE recipient_status = 0 AND recipient = ' . $user_id;
	foreach($messages AS $name => $value)
	{
		if(is_numeric($name) && $value == 'delete')
		{
			$query .= 'OR id = ' . $name . ' ';

		}
	}
	$query .= ') AND recipient = ' . $user_id;

	/* Delete the messages */
	mysql_query($query) or die(report_sql_error($query));
	/* Count unread messages and fix the numbers in session-key notices */
	$unreadresult = mysql_query($unreadquery) or die(report_sql_error($unreadquery));
	$unreaddata = mysql_fetch_assoc($unreadresult);
	if($user_id == $_SESSION['login']['id'])
	{
		$_SESSION['notices']['unread_messages'] = $unreaddata['unreadmessages'];
	}
	

	if(mysql_affected_rows() == 0)
	{
//Next line is commented out due to a bug causing mysql_affected_rows() to always return 0. Reason unknown.
//		return false;
		return true;
	}
	$query = 'UPDATE userinfo SET messages_recieved = messages_recieved - ' . mysql_affected_rows() . ' WHERE userid = ' . $user_id . ' LIMIT 1';
	mysql_query($query) or die(report_sql_error($query));
	return true;
}

function messages_view($message_id, $user_id, $quoting = 0)
{
	$query = 'SELECT m.id, m.timestamp, m.recipient_status AS recipient_status, CONCAT(mm.title, m.title) AS title, ';
	$query .= 'CONCAT(mm.message, m.message) AS message, ';
	$query .= 'm.discussion AS discussion, m.recipient AS recipient_id, m.sender AS sender_id, rlogin.username AS recipient_username, ';
	$query .= 'slogin.username AS sender_username, rinfo.birthday AS recipient_birthday, sinfo.birthday AS sender_birthday, ';
	$query .= 'rinfo.gender AS recipient_gender, sinfo.gender AS sender_gender, rzip.spot AS recipient_location, ';
	$query .= 'szip.spot AS sender_location, rinfo.image AS recipient_image, sinfo.image AS sender_image ';
	$query .= 'FROM messages_new AS m, mass_messages AS mm, login AS slogin, login AS rlogin, userinfo AS sinfo, userinfo AS rinfo, zip_codes AS szip, zip_codes AS rzip ';
	$query .= 'WHERE mm.id = m.mass_message_id AND rlogin.id = m.recipient AND slogin.id = m.sender AND rinfo.userid = m.recipient AND rzip.zip_code = rinfo.zip_code AND szip.zip_code = sinfo.zip_code AND ';
	$query .= 'sinfo.userid = m.sender AND (m.sender = ' . $user_id . ' OR m.recipient = ' . $user_id . ') AND m.id = "' . $message_id . '"';

	$result = mysql_query($query) or die(report_sql_error($query));
	if(mysql_num_rows($result) != 1)
	{
		return false;
	}
	$data = mysql_fetch_assoc($result);
	if($data['recipient_status'] == 0 && $data['recipient_id'] == $user_id)
	{
		mysql_query('UPDATE messages_new SET recipient_status = 1 WHERE id = ' . $data['id'] . ' LIMIT 1');
		$_SESSION['notices']['unread_messages'] -= 1;
	}
	echo '<div class="grey_faded_div">';

	$tooltip_sender = '<b>' . $data['sender_username'] . '</b>';
	if($data['sender_image'] == 1 || $data['sender_image'] == 2)
	{
		$tooltip_sender .= '<br /><img src=' . IMAGE_URL . '/images/users/thumb/' . $data['sender_id'] . '.jpg />';
	}
	if($data['sender_gender'] == 'P')
	{
		$tooltip_sender .= '<br />Kön: kille';
	}
	elseif($data['sender_gender'] == 'F')
	{
		$tooltip_sender .= '<br />Kön: tjej';
	}
	if(isset($data['sender_birthday']) && $data['sender_birthday'] != '0000-00-00')
	{
		$tooltip_sender .= '<br />Ålder: ' . date_get_age($data['sender_birthday']) . 'år';
	}
	if(strlen($data['sender_location']) > 1)
	{
		$tooltip_sender .= '<br />Bor: ' . trim($data['sender_location']);
	}
	$sender_link = '<a href="/traffa/profile.php?id=' . $data['sender_id'] . '" ';
	$sender_link .= 'onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'' . $tooltip_sender . '\', \'trail\', true));">';
	$sender_link .=  $data['sender_username'] . '</a>';

	$tooltip_recipient = '<b>' . $data['recipient_username'] . '</b>';
	if($data['recipient_image'] == 1 || $data['recipient_image'] == 2)
	{
		$tooltip_recipient .= '<br /><img src=' . IMAGE_URL . '/images/users/thumb/' . $data['recipient_id'] . '.jpg />';
	}
	if($data['recipient_gender'] == 'P')
	{
		$tooltip_recipient .= '<br />Kön: kille';
	}
	elseif($data['recipient_gender'] == 'F')
	{
		$tooltip_recipient .= '<br />Kön: tjej';
	}
	if(isset($data['recipient_birthday']) && $data['recipient_birthday'] != '0000-00-00')
	{
		$tooltip_recipient .= '<br />Ålder: ' . date_get_age($data['recipient_birthday']) . 'år';
	}
	if(strlen($data['recipient_location']) > 1)
	{
		$tooltip_recipient .= '<br />Bor: ' . trim($data['recipient_location']);
	}
	$recipient_link = '<a href="/traffa/profile.php?id=' . $data['recipient_id'] . '" ';
	$recipient_link .= 'onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'' . $tooltip_recipient . '\', \'trail\', true));">';
	$recipient_link .=  $data['recipient_username'] . '</a>';
	
	echo '<h2 style="margin-bottom: 2px;">Meddelande ';
	if($user_id != $data['sender_id'])
	{
		echo 'från ' . $sender_link;
	}
	if($user_id != $data['recipient_id'])
	{
		echo ' till ' . $recipient_link;
	}
	echo '</h2>';
	echo '<b style="margin-left: 3px;">Skickades ' . fix_time($data['timestamp']) . '</b>';
	echo '<p style="margin-left: 3px;">';
	echo '<b>' . $data['title'] . '</b><br />';
	echo setsmilies($data['message']);
	echo '</p></div>';
	if($data['sender_id'] == $user_id)
	{
		echo '<input type="button" value="Nytt meddelande till ' . $data['recipient_username'] . '" class="button" ';
		echo 'onclick="window.location=\'' . $_SERVER['PHP_SELF'] . '?action=compose&recipient_id=' . $data['recipient_id'] . '\';" />';
	}
	elseif($quoting == 1)
	{
		return true;
	}
	else
	{
		if(substr($data['title'], 0, 5) != 'Svar:')
		{
			$title = 'Svar: ' . $data['title'];
		}
			else
		{
			$title = $data['title'];
		}
		$answer_url = $_SERVER['PHP_SELF'] . '?action=compose&recipient_id=' . $data['sender_id'] . '&title=' . addslashes($title);
		$answer_url .= '&quote&message_id=' . $message_id;
		echo '<input type="button" value="Svara" class="button" onclick="window.location=\'' . $answer_url . '\';" />';
	}


	echo '<h2>Relaterade meddelanden</h2>';

	$options = array('mode' => 'associated', 'user' => $data['sender_id'], 'discussion' => $data['discussion']);
	if($data['sender_id'] == $user_id)
	{
		$options['user'] = $data['recipient_id'];
	}
	messages_list($user_id, $options);

	return true;
}

function messages_list($user, $options = array('mode' => 'recieved'))
{
/* $options: 
	limit 
	offset
	mode: recieved, sent, conversation, associated
	order: time, sender, recipient, title, 
	direction: asc, desc
	user: id
	discussion: int
*/

	if(!is_numeric($options['offset']))
	{
		$options['offset'] = 0;
	}
	if(!is_numeric($options['limit']))
	{
		$options['limit'] = MESSAGES_ITEMS_PER_PAGE;
	}
	if(isset($options['user']) && !is_numeric($options['user']))
	{
		die('Icke numerisk konversationspartner har angivits, dödar scriptet.');
	}

	$query = 'SELECT m.id, m.timestamp, CONCAT(mm.title, m.title) AS title, m.recipient_status, SUBSTRING(CONCAT(mm.message, m.message), 1, 150) AS message, ';
	if($options['mode'] == 'recieved')
	{
		$query .= 'm.sender AS sender_id, slogin.username AS sender_username, sinfo.birthday AS sender_birthday, ';
		$query .= 'sinfo.gender AS sender_gender, sinfo.image AS sender_image ';
		$query .= 'FROM messages_new AS m, mass_messages AS mm, login AS slogin, userinfo AS sinfo ';
		$query .= 'WHERE mm.id = m.mass_message_id AND slogin.id = m.sender AND sinfo.userid = m.sender AND m.recipient = "' . $user . '" ';
		$query .= 'AND m.recipient_status != 2 ';
	}
	elseif($options['mode'] == 'sent')
	{
		$query .= 'm.recipient AS recipient_id, rlogin.username AS recipient_username, rinfo.birthday AS recipient_birthday, ';
		$query .= 'rinfo.gender AS recipient_gender, rinfo.image AS recipient_image ';
		$query .= 'FROM messages_new AS m, mass_messages AS mm, login AS rlogin, userinfo AS rinfo ';
		$query .= 'WHERE mm.id = m.mass_message_id AND rlogin.id = m.recipient AND rinfo.userid = m.recipient AND m.sender = "' . $user . '" ';
	}
	elseif($options['mode'] == 'conversation' || $options['mode'] == 'associated')
	{
		$query .= 'm.discussion AS discussion, m.recipient AS recipient_id, m.sender AS sender_id, rlogin.username AS recipient_username, ';
		$query .= 'slogin.username AS sender_username, rinfo.birthday AS recipient_birthday, sinfo.birthday AS sender_birthday, ';
		$query .= 'rinfo.gender AS recipient_gender, sinfo.gender AS sender_gender, ';
		$query .= 'rinfo.image AS recipient_image, sinfo.image AS sender_image ';
		$query .= 'FROM messages_new AS m, mass_messages AS mm, login AS slogin, login AS rlogin, userinfo AS sinfo, userinfo AS rinfo ';
		$query .= 'WHERE mm.id = m.mass_message_id AND rlogin.id = m.recipient AND slogin.id = m.sender AND rinfo.userid = m.recipient AND sinfo.userid = m.sender ';
		$query .= 'AND ((m.sender = "' . $options['user'] . '" AND m.recipient = "' . $user . '") OR ';
		$query .= '(m.sender = "' . $user . '" AND m.recipient = "' . $options['user'] . '")) ';
		if($options['mode'] == 'associated')
		{
			$query .= 'AND (discussion > ' . ($options['discussion']-5) . ' AND discussion < ' . ($options['discussion']+5) . ') ';
		}
	}
	switch($options['order'])
	{
		case 'sender':
			$query .= 'ORDER BY m.sender ';
		break;
		case 'recipient':
			$query .= 'ORDER BY m.recipient ';
		break;
		case 'title':
			$query .= 'ORDER BY m.title ';
		break;
		default:
			$query .= 'ORDER BY m.id ';
		break;
	}
	if($options['direction'] == 'ASC')
	{
		$query .= 'ASC ';
	}
	else
	{
		$query .= 'DESC ';
	}
	$query .= 'LIMIT ' . $options['offset'] . ', ' . $options['limit'];
	
	
	
	$result = mysql_query($query) or die(report_sql_error($query));
	if(mysql_num_rows($result) == 0)
	{
		echo 'Här var det tomt!';
	}
	else
	{
		if($options['mode'] == 'recieved')
		{
			echo '<form action="' . $_SERVER['PHP_SELF'] . '?action=delete" method="post">';
		}
		echo '</div><table style="width: 100%; margin-bottom: 3px;" cellspacing="0">';
		echo '<tr style="font-weight: bold;">';

		if(strlen($_GET['action']) < 1)
		{
			$link_action = 'inbox';
		}
		else
		{
			$link_action = $_GET['action'];
		}

		if($options['mode'] == 'conversation' || $options['mode'] == 'recieved' || $options['mode'] == 'associated')
		{
			echo '<td style="width: 150px;">';
			if($options['mode'] != 'associated')
			{
				if($options['direction'] == 'ASC' && $options['order'] == 'sender')
				{
					$link_direction = 'DESC';
				}
				else
				{
					$link_direction = 'ASC';
				}
				echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=' . $link_action . '&order=sender&direction=' . $link_direction;
				if($options['mode'] == 'conversation')
				{
					echo '&user=' . $_GET['user'];
				}
				echo '">Avsändare</a>';
			}
			else
			{
				echo 'Avsändare';
			}
			echo '</td>';
		}
		if($options['mode'] == 'conversation' || $options['mode'] == 'sent')
		{
			if($options['direction'] == 'ASC' && $options['order'] == 'recipient')
			{
				$link_direction = 'DESC';
			}
			else
			{
				$link_direction = 'ASC';
			}
			echo '<td style="width: 150px;">';
			echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=' . $link_action . '&order=recipient&direction=' . $link_direction;
			if($options['mode'] == 'conversation')
			{
				echo '&user=' . $_GET['user'];
			}
			echo '">Mottagare</a></td>';
		}
		echo '<td style="width: 150px;">';
		if($options['mode'] != 'associated')
		{
			if(($options['direction'] == 'DESC' && $options['order'] == 'time') || !isset($options['order']))
			{
				$link_direction = 'ASC';
			}
			else
			{
				$link_direction = 'DESC';
			}
			echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=' . $link_action . '&order=time&direction=' . $link_direction;
			if($options['mode'] == 'conversation')
			{
				echo '&user=' . $_GET['user'];
			}
			echo '">Tid</a>';
		}
		else
		{
			echo 'Tid';
		}
		echo '</td>';

		echo '<td>';
		if($options['mode'] != 'associated')
		{
			if(($options['order'] == 'title' || !isset($options['order'])) && $options['direction'] == 'ASC')
			{
				$link_direction = 'DESC';
			}
			else
			{
				$link_direction = 'ASC';
			}
			echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=' . $link_action .  '&order=title&direction=' . $link_direction;
			if($options['mode'] == 'conversation')
			{
				echo '&user=' . $_GET['user'];
			}
			echo '">Rubrik</a>';
		}
		else
		{
			echo 'Rubrik';
		}
		echo '</td>';
		if($options['mode'] == 'recieved')
		{
			echo '<td style="width: 50px;">Radera</td>';
		}
		echo '</tr>';
		$background = '#e7e7e7';
		while($data = mysql_fetch_assoc($result))
		{
			echo '<tr style="';
			if($data['recipient_status'] == 0)
			{
				echo 'font-weight: bold;';
			}
			if($options['mode'] == 'associated' && $data['discussion'] == $options['discussion'])
			{
				echo 'background-image: url(\'\');';
			}
			else
			{
				echo 'background: ' . $background . ';';
			}
			echo '";>';
			if($options['mode'] == 'conversation' || $options['mode'] == 'recieved' || $options['mode'] == 'associated')
			{
				$tooltip_sender = '<b>' . $data['sender_username'] . '</b>';
				if($data['sender_image'] == 1 || $data['sender_image'] == 2)
				{
					$tooltip_sender .= '<br /><img src=' . IMAGE_URL . '/images/users/thumb/' . $data['sender_id'] . '.jpg />';
				}
				if($data['sender_gender'] == 'P')
				{
					$tooltip_sender .= '<br />Kön: kille';
				}
				elseif($data['sender_gender'] == 'F')
				{
					$tooltip_sender .= '<br />Kön: tjej';
				}
				if(isset($data['sender_birthday']) && $data['sender_birthday'] != '0000-00-00')
				{
					$tooltip_sender .= '<br />Ålder: ' . date_get_age($data['sender_birthday']) . 'år';
				}
				if(strlen($data['sender_location']) > 1)
				{
					$tooltip_sender .= '<br />Bor: ' . $data['sender_location'];
				}
				echo '<td><a href="/traffa/profile.php?id=' . $data['sender_id'] . '" onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'' . $tooltip_sender . '\', \'trail\', true));">' . $data['sender_username'] . '</a></td>';
			}
			if($options['mode'] == 'conversation' || $options['mode'] == 'sent')
			{
				$tooltip_recipient = '<b>' . $data['recipient_username'] . '</b>';
				if($data['recipient_image'] == 1 || $data['recipient_image'] == 2)
				{
					$tooltip_recipient .= '<br /><img src=' . IMAGE_URL . '/images/users/thumb/' . $data['recipient_id'] . '.jpg />';
				}
				if($data['recipient_gender'] == 'P')
				{
					$tooltip_recipient .= '<br />Kön: kille';
				}
				elseif($data['recipient_gender'] == 'F')
				{
					$tooltip_recipient .= '<br />Kön: tjej';
				}
				if(isset($data['recipient_birthday']) && $data['recipient_birthday'] != '0000-00-00')
				{
					$tooltip_recipient .= '<br />Ålder: ' . date_get_age($data['recipient_birthday']) . 'år';
				}
				if(strlen($data['recipient_location']) > 1)
				{
					$tooltip_recipient .= '<br />Bor: ' . $data['recipient_location'];
				}
				echo '<td><a href="/traffa/profile.php?id=' . $data['recipient_id'] . '" ';
				echo 'onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'' . $tooltip_recipient . '\', \'trail\', true));">';
				echo $data['recipient_username'] . '</a></td>';
			}
			$tooltip_title = '<b>' . $data['title']  . '</b><br />' . str_replace('\'', '\\\'', str_replace('"', '\\\\', $data['message']));
			if(strlen($data['message']) == 150)
			{
				$tooltip_title .= '...';
			}

			$tooltip_title = str_replace(array("\n", "\r"), '', $tooltip_title);
			
			echo '<td>' . fix_time($data['timestamp']) . '</td>';
			$data['title'] = (strlen($data['title']) == 0) ? '[Rubrik saknas]' : $data['title'];
			echo '<td><a href="' . $_SERVER['PHP_SELF'] . '?action=read&message_id=' . $data['id'] . '" ';
			echo 'onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'' . $tooltip_title . '\', \'trail\', true));">' . $data['title'] . '</a></td>';
			if($options['mode'] == 'recieved')
			{
				echo '<td><input name="' . $data['id'] . '" value="delete" type="checkbox" style="border: 5x solid blue;" /></td>';
			}
			echo '</tr>';
			if($background == '#e7e7e7')
			{
				$background = '#ffffff';
			}
			else
			{
				$background = '#e7e7e7';
			}
		}
		echo '</table>';
	}
	if($options['mode'] != 'associated')
	{
		echo '<div class="grey_faded_div">';
		echo '<strong>Sida:</strong> ';
		if($options['mode'] == 'conversation')
		{
			$query = 'SELECT MAX(discussion) AS messages FROM messages_new WHERE ';
			$query .= '(sender = ' . $user . ' AND recipient = ' . $options['user'] . ') OR ';
			$query .= '(sender = ' . $options['user'] . ' AND recipient = ' . $user . ')';
		}
		elseif($options['mode'] == 'recieved')
		{
			$query = 'SELECT messages_recieved AS messages FROM userinfo WHERE userid = ' . $_SESSION['login']['id'] . ' LIMIT 1';
		}
		elseif($options['mode'] == 'sent')
		{
			$query = 'SELECT messages_sent AS messages FROM userinfo WHERE userid = ' . $_SESSION['login']['id'] . ' LIMIT 1';
		}
		$result = mysql_query($query) or die(report_sql_error($query));
		$data = mysql_fetch_assoc($result);
		$pages = ceil($data['messages'] / MESSAGES_ITEMS_PER_PAGE);
		for($i = 0; $i < $pages; $i++)
		{
			if(($options['offset'] / MESSAGES_ITEMS_PER_PAGE) == $i)
			{
				echo '<strong>' . ($i+1) . '</strong> ';
			}
			else
			{
				echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=' . $link_action;
				if(isset($options['order']))
				{
					echo '&order=' . $options['order'];
				}
				if(isset($options['direction']))
				{
					echo '&direction=' . $options['direction'];
				}
				if(isset($options['user']))
				{
					echo '&user=' . $options['user'];
				}
				echo '&offset=' . ($i * MESSAGES_ITEMS_PER_PAGE);
				echo '">' . ($i+1) . '</a> ';
			}
		}
		echo '</div>';
	}
	//echo '<div>';
	if($options['mode'] == 'recieved')
	{
		echo '<input type="submit" value="Ta bort markerade" class="button" style="float: right; margin: 3px;" onclick="return confirm(\'Detta kommer ta bort alla markerade inlägg. Vill du fortsätta?\');" /> ';
		echo '</form>';
	}
	if($options['mode'] == 'recieved')
	{
		echo '<input type="button" class="button" style="float: left; margin: 3px;" value="Gå till utkorgen" onclick="window.location=\'' . $_SERVER['PHP_SELF'];
		echo '?action=list_sent\';" /> ';
	}
	else
	{
		echo '<input type="button" style="float: left; margin: 3px;" class="button" value="Gå till inkorgen" onclick="window.location=\'' . $_SERVER['PHP_SELF'];
		echo '\';" /> ';
	}
	if($options['mode']  == 'associated')
	{
		echo ' <input type="button" class="button" style="float: left; margin: 3px;" value="Visa hela konversationen" onclick="window.location=\'' . $_SERVER['PHP_SELF'];
		echo '?action=conversation&user=' . $options['user'] . '\';" /> ';
	}
	//echo '</div>';
	echo '<br style="clear: both;" />';
}
?>
