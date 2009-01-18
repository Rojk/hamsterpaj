<?php
	require('../include/core/common.php');
		
	if(isset($_GET['url']) && substr($_GET['url'], 0, 1) != '/' && login_checklogin() && is_numeric($_GET['friend_id']))
	{
		$query = 'UPDATE friends_notices';
		$query .= ' SET read = "1"';
		$query .= ' WHERE user_id = "' . $_SESSION['login']['id'] . '" AND friend_id = "' . $_GET['friend_id'] . '" AND url = "' . html_entity_decode($_GET['url']) . '"';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		$_SESSION['friends_actions_lastupdate'] = 0;
		
		header('Location: ' . $_GET['url']);
	}
?>