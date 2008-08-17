<?php
	require('../include/core/common.php');
	if(login_checklogin())
	{
		$status = utf8_encode($_GET['status']);
		$not_allowed_in_status = 'tinyurl';
		if (stristr(strtolower($status), $not_allowed_in_status))
		{
			die('Inga fula saker i din forumstatus :(');
		}
		$_SESSION['userinfo']['user_status'] = $status;
		$query = 'UPDATE userinfo SET user_status = "' . $status . '", user_status_update = "' . time() . '" WHERE userid = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
?>
