<?php
require('../include/core/common.php');
if (is_privilegied('ip_ban_admin'))
{
	
	if (isset($_GET['regip']))
	{
		$query = 'SELECT username, id FROM login WHERE regip = "' . $_GET['regip'] . '" AND username <> "Borttagen"';
	}
	else if (isset($_GET['lastip']))
	{
		$query = 'SELECT username, id FROM login WHERE lastip = "' . $_GET['lastip'] . '" AND username <> "Borttagen"';
	}
	$result = mysql_query($query);
	while ($data = mysql_fetch_assoc($result))
	{
		$usernames .= ' <a href="/traffa/profile.php?id=' . $data['id'] . '">' . $data['username'] . '</a>';
	}
	echo $usernames;
}
?> 
