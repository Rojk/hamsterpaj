<?php
	$query_p = 'SELECT l.id, l.username, u.gender, u.birthday FROM login AS l, userinfo AS u ';
	$query_p .= 'WHERE u.userid = l.id AND u.image = 2 AND u.gender = "m" ORDER BY l.lastlogon DESC LIMIT 7';
	
	$query_f = 'SELECT l.id, l.username, u.gender, u.birthday FROM login AS l, userinfo AS u ';
	$query_f .= 'WHERE u.userid = l.id AND u.image = 2 AND u.gender = "f" ORDER BY l.lastlogon DESC LIMIT 7';

	$result_p = mysql_query($query_p) or report_sql_error($query_p, __FILE_, __LINE__);
	$result_f = mysql_query($query_f) or report_sql_error($query_f, __FILE_, __LINE__);
	/*
	for($i = 0; $data = mysql_fetch_assoc($result_p); $i += 2)
	{
		$users[$i] = $data;
	}

	for($i = 1; $data = mysql_fetch_assoc($result_f); $i += 2)
	{
		$users[$i] = $data;
	}
	*/
	
	while ($data = mysql_fetch_assoc($result_f))
	{
		$users[] = $data;
	}
	while ($data = mysql_fetch_assoc($result_p))
	{
		$users[] = $data;
	}
	
	cache_save('hetluften', $users);
?>
