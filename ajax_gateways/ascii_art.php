<?php
	require('../include/core/common.php');
	if(login_checklogin() && isset($_GET['ascii_art_id'], $_GET['vote']) && is_numeric($_GET['ascii_art_id']) && is_numeric($_GET['vote']) && (int)$_GET['vote'] > 0 && (int)$_GET['vote'] < 6)
	{
		if(mysql_query('INSERT INTO ascii_art_votes (ascii_art_id, vote, userid) VALUES (' . $_GET['ascii_art_id'] . ', ' . $_GET['vote'] . ', ' . $_SESSION['login']['id'] . ')'))
		{
			$query = 'UPDATE ascii_art SET votes = votes + ' . $_GET['vote'] . ', voters = voters + 1 WHERE id = ' . $_GET['ascii_art_id'];
			mysql_query($query) or report_sql_error($query);
		}
	}
	else
	{
		echo 'Foo';
	}
?>