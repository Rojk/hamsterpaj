<?php
	/* Remove old users */
	$query = 'SELECT id, username FROM login WHERE (lastlogon < UNIX_TIMESTAMP() - 60*60*24*183 OR lastlogon IS NULL) AND is_removed = "0" AND id != 857929 AND id != 2348 AND id != 876354'; // We don't wish to remove webmaster or tha hamster or anonym
	$result = mysql_query($query);
	while($data = mysql_fetch_assoc($result))
	{
		log_admin_event('user removed', $data['username'], '2348', $data['id'], $data['id']);
		login_remove_user($data['id']);
	}
?>
