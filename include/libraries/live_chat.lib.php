<?php

function live_chat_load_events($options)
{
	$tail['filename'] = LIVE_CHAT_STORAGE_PATH . $options['type'] . '/' . $options['reference_id'];
	$tail['line_count'] = LIVE_CHAT_MAX_READ_LINES;
	$tail['buffer_length'] = LIVE_CHAT_STORAGE_BUFFER_LENGTH;

	$options['min_id'] = (isset($options['min_id'])) ? $options['min_id'] : 0;
	
	$rows = tail($tail);
	$events = array();
	foreach($rows AS $row)
	{
		$event = unserialize(trim($row));
		if($event['id'] > $options['min_id'])
		{
			$event['message'] = clickable_links($event['message']);
			$event['message'] = setsmilies($event['message']);
			$events[] = $event;
		}
	}

	return $events;
}

function live_chat_log_update($options)
{
	$chatroom = $options['type'] . '_' . $options['reference_id'];
	
	$query = 'UPDATE live_chat_users SET last_update = "' . time() . '" WHERE user = "' . $_SESSION['login']['username'] . '" AND chatroom = "' . $chatroom . '" LIMIT 1';
	mysql_query($query);
	if(mysql_affected_rows() != 1)
	{
		// Sometimes two requests happen at the same second, causing the UPDATE to affect 0 rows
		// To make absolutely sure the user isn't in the chatroom, we do an extra check.
		$query = 'SELECT last_update FROM live_chat_users WHERE user = "' . $_SESSION['login']['username'] . '" AND chatroom = "' . $chatroom . '" LIMIT 1';
		if(mysql_num_rows(mysql_query($query)) == 0)
		{
			$query = 'INSERT INTO live_chat_users (chatroom, user, last_update) VALUES("' . $chatroom . '", "' . $_SESSION['login']['username'] . '", "' . time() . '")';
			mysql_query($query);		
			$options['event_type'] = 'join';
			live_chat_event($options);
		}
	}
}

function live_chat_event($options)
{
	$event['id'] = live_chat_event_id(array('type' => $options['type'], 'reference_id' => $options['reference_id']));

	$event['timestamp'] = date('H:i');
	$event['event_type'] = $options['event_type'];
	$event['message'] = str_replace(array("\r", "\n"), null, nl2br($options['message']));
	$event['username'] = (isset($options['username'])) ? $options['username'] : $_SESSION['login']['username'];
	$event['user_id'] = $_SESSION['login']['id'];
	$event['age'] = ($_SESSION['userinfo']['birthday'] != '0000-00-00') ? date_get_age($_SESSION['userinfo']['birthday']) : 0;

	if($options['username'] == $_SESSION['login']['username'] && $_SESSION['userinfo']['image'] == 2)
	{
		$event['user_photo'] = $_SESSION['login']['id'];
	}

	if($_SESSION['login']['username'] == 'Snaxman' && rand(1, 5) == 3)
	{
		$event['message'] .= ' Förresten, Justin Timberlake är inte så bra...';
	}

	$line = "\n" . serialize($event);
	file_put_contents(LIVE_CHAT_STORAGE_PATH . $options['type'] . '/' . $options['reference_id'], $line, FILE_APPEND);	
}

function live_chat_chatroom_decode($chatroom)
{
	$explosion = explode('_', $chatroom);
	$options['type'] = $explosion[0];
	$options['reference_id'] = $explosion[1];

	return $options;
}

function live_chat_users_gbc()
{
	// Garbage cleaning process, loggin out all non-active users
	$query = 'SELECT * FROM live_chat_users WHERE last_update < ' . (time()-10);
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$options = live_chat_chatroom_decode($data['chatroom']);
		$options['event_type'] = 'part';
		$options['username'] = $data['user'];
		
		live_chat_event($options);
	}
	
	$query = 'DELETE FROM live_chat_users WHERE last_update < ' . (time()-10);
	mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
}

function live_chat_event_id($options)
{
	$query = 'SELECT last_id FROM live_chat_chatrooms WHERE chatroom = "' . $options['type'] . '_' . $options['reference_id'] . '"';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	if(mysql_num_rows($result) == 1)
	{
		$data = mysql_fetch_assoc($result);
		$id = $data['last_id'] + 1;
		$query = 'UPDATE live_chat_chatrooms SET last_id = "' . $id . '" WHERE chatroom = "' . $options['type'] . '_' . $options['reference_id'] . '"';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	else
	{
		$id = 1;
		$query = 'INSERT INTO live_chat_chatrooms (chatroom, last_id) VALUES("' . $options['type'] . '_' . $options['reference_id'] . '", 1)';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
	return $id;
}

function live_chat_user_list($chatroom)
{
	$query = 'SELECT user FROM live_chat_users WHERE chatroom = "' . $chatroom . '" ORDER BY user ASC';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$users[] = $data['user'];
	}
	
	return $users;
}

function live_chat_render($options)
{
	$id = $options['type'] . '_' . $options['reference_id'];

	unset($_SESSION['live_chat']['chatrooms'][$id]['last_id']);

	$users = live_chat_user_list($id);
	if(!in_array($users, $_SESSION['login']['username']))
	{
		$users[] = $_SESSION['login']['username'];
		sort($users);
	}
	$users = array_unique($users);

	$class = ($_SESSION['login']['id'] == 3) ? 'live_chat_scribble_board' : 'live_chat_chat';
	$class = 'live_chat_scribble_board';

	$output .= '<a name="live_chat_' . $id . '_anchor"></a>' . "\n";
	$output .= '<div id="live_chat_' . $id . '" class="' . $class . '">' . "\n";
	$output .= '	<input type="hidden" class="live_chat_identifier" value="' . $id . '" />' . "\n";
	$output .= '	<input type="hidden" id="live_chat_' . $id . '_enter_submit" value="disabled" />' . "\n";
	$output .= '	<h4 class="navigation_header">Tema</h4>' . "\n";
	$output .= '	<ul id="live_chat_' . $id . '_navigation" class="navigation">' . "\n";
	$output .= '		<li class="current">Chatt</li>' . "\n";
	$output .= '		<li>Kommentering</li>' . "\n";
	$output .= '		<li>Klotterplank</li>' . "\n";
	$output .= '		<li class="help">Hjälp</li>' . "\n";
	$output .= '	</ul>' . "\n";

	$style = (login_checklogin()) ? '' : ' style="display: none;"';
	$output .= '	<form id="live_chat_' . $id . '_form"' . $style . '>' . "\n";
	$output .= '		<div class="recipient">' . "\n";
	$output .= '			<h5>Skicka till</h5>' . "\n";
	$output .= '			<select id="live_chat_' . $id . '_highlight_users">' . "\n";
	$output .= '				<option>Alla</option>' . "\n";
	foreach($users AS $user)
	{
		$output .= '<option>' . $user . '</option>' . "\n";
	}
	$output .= '			</select>' . "\n";
	$output .= '			<input type="checkbox" id="live_chat_' . $id . '_private_check" />' . "\n";
	$output .= '			<label for="live_chat_' . $id . '_private_check">Privat</label>' . "\n";
	$output .= '		</div>' . "\n";
	$output .= '		<div class="compose_area">' . "\n";
	$output .= '			<span class="timer" id="live_chat_' . $id . '_timer"></span>' . "\n";	
	$output .= '			<h5>Meddelande</h5>' . "\n";
	$output .= '			<textarea id="live_chat_' . $id . '_text_input" class="message_input"></textarea>' . "\n";
	$output .= '			<button id="live_chat_' . $id . '_submit">Skicka</button>' . "\n";
	$output .= '		</div>' . "\n";
	$output .= '	</form>' . "\n";

	$output .= '	<ol id="live_chat_' . $id . '_entry_area" class="entry_area">' . "\n";
	$output .= '		<li>' . "\n";
	$output .= '			<span class="timestamp">' . date('H:i') . '</span>' . "\n";
	$output .= '			<span class="username">Hamstern</span>' . "\n";
	$output .= '			<span class="message">Hej och välkommen till chatten!</span>' . "\n";
	$output .= '		</li>' . "\n";
	$output .= '	</ol>' . "\n";
	$output .= '	<div class="user_list">' . "\n";
	$output .= '		<h3 class="user_count"><span id="live_chat_' . $id . '_user_count">' . count($users) . '</span> personer online</h3>' . "\n";
	$output .= '		<select multiple id="live_chat_' . $id . '_user_list">' . "\n";
	foreach($users AS $user)
	{
		$output .= '<option>' . $user . '</option>' . "\n";
	}
	$output .= '		</select>' . "\n";
	$output .= '	</div>' . "\n";
	$output .= '</div>' . "\n";
	
	return $output;
}
?>