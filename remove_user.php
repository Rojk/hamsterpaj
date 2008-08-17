<?php
	require('./include/core/common.php');
	$ui_options['current_menu'] = 'hamsterpaj';
	$ui_options['title'] = 'Användare borttagen - Hamsterpaj.net';
	ui_top($ui_options);
	
	if(is_privilegied('remove_user'))
	{
		if(isset($_GET['userid']) && is_numeric($_GET['userid']))
		{
			$query = 'SELECT id, session_id, username FROM login WHERE id = "' . $_GET['userid'] . '"';
			$result = mysql_query($query) or report_sql_error($query);
			$data = mysql_fetch_assoc($result);
			$user_to_sess = $data['session_id'];
			$userid = $data['id'];
			$old_username = $data['username'];
			unlink('/var/lib/php/session2/sess_' . $data['session_id']);

			log_admin_event('user removed', $data['username'], $_SESSION['login']['id'], $_GET['userid'], $_GET['userid']);
			login_remove_user($_GET['userid']);
			echo '<h1>Knäppgök borttagen</h1>';
		}
	}

	ui_bottom();
?>


