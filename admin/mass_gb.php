<?php
	require('../include/core/common.php');
	$ui_options['title'] = 'GB-massutskick - Hamsterpaj.net';
	$ui_options['stylesheets'][] = 'forms.css';
	
	if ( !is_privilegied('igotgodmode') )
	{
		die('Wortwortwort!');
	}
	/*
	Array
	(
	    [forum_privs] => on
	    [igotgodmode_privs] => on
	    [message] => sdfghjklöä
	)
	*/
	if ( $_GET['action'] == 'submit' )
	{
		$sql = 'INSERT INTO mass_gb_history SET timestamp = UNIX_TIMESTAMP(),';
		$sql .= ' sent_by = ' . $_SESSION['login']['id'] . ',';
		$sql .= ' message = "' . $_POST['message'] . '"';
		mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
		
		$sql = 'SELECT user FROM privilegies GROUP BY(user)';
		$result = mysql_query($sql);
		while ( $data = mysql_fetch_assoc($result) )
		{
			$privilegied_users[] = $data['user'];
		}
		foreach ( $privilegied_users as $user )
		{
			$sql = 'SELECT session_id FROM login WHERE id = ' . $user . ' LIMIT 1';
			$result = mysql_query( $sql );
			$data = mysql_fetch_assoc( $result );
			$privilegied_users_with_sessids[$user] = $data['session_id'];
		}
		foreach ( $privilegied_users_with_sessids as $user_id => $sessid )
		{
			$privilegied_users_session_data[$user_id] = session_load($sessid);
		}
		foreach ( $privilegied_users_session_data as $user_id => $session_data )
		{
			$users_privilegies[$user_id] = $session_data['privilegies'];
		}
		foreach ( $users_privilegies as $user_id => $privilegies )
		{
			
		}
		preint_r( $users_privilegies );
	}
	
	$out .= '<fieldset>
	<legend>MassGB-SPAM!</legend>
	<style>
	.recipient_filters li {
		list-style-type:none;
	}
	</style>
	<form action="' . $_SERVER['SCRIPT_URI'] . '?action=submit" method="post">
	<table class="form">
	<tr>
	<td colspan="2">
	<ul class="recipient_filters">
		<li>
		<input type="checkbox" name="forum_privs" />
		<label for="forum_privs">OV</label>
		</li>
		<li>
		<input type="checkbox" name="ip_ban_privs" />
		<label for="ip_ban_privs">Admins</label>
		</li>
		<li>
		<input type="checkbox" name="igotgodmode_privs" />
		<label for="igotgodmode_privs">Sysöps</label>
		</li>
	</ul>
	</td>
	</tr>
	<tr>
	<td>
	<label for="message">Meddelande <strong>*</strong></label>
	</td>
	<td>
	<textarea name="message" style="width: 470px; height: 300px;"></textarea>
	</td>
	</tr>
	<tr>
	<td colspan="2">
	<input type="submit" value="Skicka meddelande" onclick="this.disabled = true; this.value = \'Klicka inte förfan...\'" />
	</td>
	</tr>
	</table>
	</form>
	</fieldset>' . "\n";
	
	echo ui_top( $ui_options );
	echo $out;
	echo ui_bottom();
?>