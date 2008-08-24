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
		$query .= ' WHERE f.user_id = "' . $options['user_id'] . '" AND l.id = f.friend_id AND u.userid = l.id AND is_removed = 0 AND l.lastaction >= "' . (time()-600) . '"';
		$query .= ' ORDER BY l.username ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		while($data = mysql_fetch_assoc($result))
		{
			$friends[$data['user_id']] = $data; // Save in array
		}
		
		return $friends;
	}
?> 