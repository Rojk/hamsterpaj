<?php
	function friends_fetch($options)
	{
		$query = 'SELECT f.friend_id AS user_id, l.username, l.lastaction, l.lastrealaction, l.session_id, u.user_status, u.image, u.gender, u.birthday';
		$query .= ' FROM friendslist AS f, login AS l, userinfo AS u';
		$query .= ' WHERE f.user_id = "' . $options['user_id'] . '" AND l.id = f.friend_id AND u.userid = l.id AND is_removed = 0';
		$query .= ' ORDER BY l.username ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		while($data = mysql_fetch_assoc($result))
		{
			$friends[$data['user_id']] = $data; // Save in array
		}
		
		return $friends;
	}
	
	function friends_fetch_online_smart($options)
	{
		$query = 'SELECT f.friend_id AS user_id, l.username, l.lastaction, l.lastrealaction, u.user_status';
		$query .= ' FROM friendslist AS f, login AS l, userinfo AS u';
		$query .= ' WHERE f.user_id = "' . $options['user_id'] . '" AND l.id = f.friend_id AND u.userid = l.id AND is_removed = 0 AND l.lastaction >= "' . (time()-180) . '" AND l.lastrealaction >= "' . (time()-600) . '"';
		$query .= ' ORDER BY l.username ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		while($data = mysql_fetch_assoc($result))
		{
			$friends[$data['user_id']] = $data; // Save in array
		}
		
		return $friends;
	}
	
	function friends_actions_fetch($options)
	{
		$query = 'SELECT f.friend_id AS user_id, f.url, f.action_id, f.read, f.action, f.label, l.username';
		$query .= ' FROM friends_notices AS f, login AS l, userinfo AS u';
		$query .= ' WHERE 1 AND';
		$query .=  isset($options['user_id']) ? ' f.user_id = "' . $options['user_id'] . '"' : '';
		$query .=  isset($options['friend_id']) ? ' f.friend_id = "' . $options['friend_id'] . '"' : '';
		$query .= ' AND l.id = f.friend_id';
		$query .= ' AND u.userid = l.id';
		$query .= ' AND l.is_removed = 0';
		$query .= $options['show'] == "new" ? ' AND f.read = "0"' : '';
		$query .= ' ORDER BY l.username ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		while($data = mysql_fetch_assoc($result))
		{
			$friends_notices[$data['user_id']]['actions'][$data['action_id']] = $data; // Save in array
			$friends_notices[$data['user_id']]['username'] = $data['username'];
		}
		return $friends_notices;
	}
	
	function friends_actions_insert($options)
	{
		$friends_options['friend_id'] = $_SESSION['login']['id'];
		$friends = friends_fetch($friends_options);
		foreach($friends as $friend)
		{
			$query = 'INSERT INTO friends_notices (user_id, timestamp, friend_id, action, url, label)';
			$query .= ' VALUES("' . $friend['user_id'] . '", "' . time() . '", "' . $_SESSION['login']['id'] . '", "' . $options['action'] . '", "' . $options['url'] . '", "' . $options['label'] . '")';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}
	}
?>