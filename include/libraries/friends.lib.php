<?php
	function friends_fetch($options)
	{
		$query = 'SELECT f.friend_id AS user_id, l.username, l.lastaction, l.lastrealaction, u.image, u.gender, u.birthday';
		$query .= ' FROM friendslist AS f, login AS l, userinfo AS u';
		$query .= ' WHERE f.user_id = "' . $options['user_id'] . '" AND l.id = f.friend_id AND u.userid = l.id AND is_removed = 0';
		$query .= ' ORDER BY l.username ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		$data = mysql_fetch_assoc($result);
		
		return $return;
	}
?>