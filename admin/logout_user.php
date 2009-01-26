<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE . 'libraries/admin.lib.php');

	$ui_options['current_menu'] = 'admin';
	ui_top($ui_options);

	if (!is_privilegied('logout_user'))
	{
		die();
	}

	if (!isset($_GET['action']))
	{
		echo 'vad görru!!!';
	}
	elseif ($_GET['action'] == 'logout')
	{
			$query = 'SELECT id, session_id FROM login WHERE username = "' . $_GET['username'] . '"';
			$result = mysql_query($query) or report_sql_error($query);
			
			if(mysql_num_rows($result) > 0)
			{
			
				$data = mysql_fetch_assoc($result);
				$user_to_sess = $data['session_id'];
				$userid = $data['id'];
				unlink('/var/lib/php/session2/sess_' . $user_to_sess);
				$query = 'UPDATE login SET lastaction = "0" WHERE id = "' . $userid . '"';
				mysql_query($query) or report_sql_error($query);
				log_admin_event('user kicked', $_GET['username'] . ' was loged out by ' . $_SESSION['login']['username'], $_SESSION['login']['id'], $userid, $userid);
				jscript_alert('Personen är nu utloggad');
			}
			else
			{
				jscript_alert('Hittade inte användaren...');
			}
			jscript_go_back();
	}
	ui_bottom();
?>
