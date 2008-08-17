<?php
// If using this file again, privilegies protect it!
die('Kakor');


require('../include/core/common.php');
$ui_options['current_menu'] = 'admin';
ui_top($ui_options);
	function session_broadcast_message($message)
	{
		$session_dir = opendir('/var/lib/php/session/');
		while($filename = readdir($session_dir))
		{
			$file_handle = fopen('/var/lib/php/session/' . $filename, 'a');
			fwrite($file_handle, 'adminmessage|s:' . strlen($message) . ':"' . $message . '";');
		}
	}

	if($_GET['action'] == 'send')
	{
		if($_POST['username'] == 'all')
		{
			session_broadcast_message($_POST['message']);
		}
		else
		{
			$query = 'SELECT id FROM login WHERE username LIKE "' . str_replace('_', '\\_', $_POST['username']) . '" LIMIT 1';
			$result = mysql_query($query) or die(report_sql_error($query));
			$data = mysql_fetch_assoc($result);
			if(session_leave_message($data['id'], $_POST['message']) != true)
			{
				jscript_alert('Det verkar som om ' . $_POST['username'] . ' (' . $data['id'] . ') inte är inloggad.');
			}
			else
			{
				jscript_alert('Done');
			}
		}
	}

?>

<form action="sessionhack.php?action=send" method="post">
Username<br />
<input type="text" name="username" /><br />
Meddelande: (inga specialtecken) <br />
<input type="text" name="message" /><br />
<input type="submit" value="OK"></form>

<?php
	ui_bottom();
?>
