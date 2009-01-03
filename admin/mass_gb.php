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
		 //HANDLE POSTDATA
		$_POST['forum_privs'] = ($_POST['forum_privs'] == 'on') ? 'on' : 'off';
		$_POST['igotgodmode_privs'] = ($_POST['igotgodmode_privs'] == 'on') ? 'on' : 'off';
		$_POST['ip_ban_privs'] = ($_POST['ip_ban_privs'] == 'on') ? 'on' : 'off';
		$sql = 'INSERT INTO mass_gb_history SET timestamp = ' . time() . ',';
		$sql .= ' sent_by = ' . $_SESSION['login']['id'] . ',';
		$sql .= ' message = "' . $_POST['message'] . '"';
		mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
		
		$sql = 'SELECT user FROM privilegies GROUP BY(user)';
		$result = mysql_query( $sql );
		while ( $data = mysql_fetch_assoc( $result ) )
		{
			$privilegied_user_ids[] = $data['user'];
		}
		foreach ( $privilegied_user_ids as $user_id )
		{
			$sql = 'SELECT privilegie FROM privilegies WHERE user = ' . $user_id . '';
			$result = mysql_query( $sql );
			while ( $data = mysql_fetch_assoc( $result ) )
			{
				$users[$user_id][] = $data['privilegie'];
			}
		}
		foreach ( $users as $user_id => $privilegies )
		{
			$haxx_string = $_POST['forum_privs'] . $_POST['ip_ban_privs'] . $_POST['igotgodmode_privs'];
			
			switch ($haxx_string)
			{
				// OV
				case 'onoffoff':
					if ( !in_array('igotgodmode', $privilegies) && !in_array('ip_ban_admin', $privilegies) && in_array('discussion_forum_remove_posts', $privilegies) )
					{
						$confirmed_recipients[] = $user_id;
					}
				break;
				// OV ADMIN
				case 'ononoff':
					if ( !in_array('igotgodmode', $privilegies) && (in_array('ip_ban_admin', $privilegies) || in_array('discussion_forum_remove_posts', $privilegies)) )
					{
						$confirmed_recipients[] = $user_id;
					}
				break;
				// OV ADMIN SYSOP
				case 'ononon':
					if ( in_array('igotgodmode', $privilegies) || in_array('ip_ban_admin', $privilegies) || in_array('discussion_forum_remove_posts', $privilegies) )
					{
						$confirmed_recipients[] = $user_id;
					}
				break;
				// OV SYSOP
				case 'onoffon':
					if ( (in_array('igotgodmode', $privilegies) || in_array('discussion_forum_remove_posts', $privilegies)) && !in_array('ip_ban_admin', $privilegies) )
					{
						$confirmed_recipients[] = $user_id;
					}
				break;
				// ADMIN SYSOP
				case 'offonon':
					if ( in_array('igotgodmode', $privilegies) || in_array('ip_ban_admin', $privilegies) )
					{
						$confirmed_recipients[] = $user_id;
					}
				break;
				// SYSOP
				case 'offoffon':
					if ( in_array('igotgodmode', $privilegies) && in_array('ip_ban_admin', $privilegies) && in_array('discussion_forum_remove_posts', $privilegies) )
					{
						$confirmed_recipients[] = $user_id;
					}
				break;
				// ADMIN
				case 'offonoff':
					if ( !in_array('igotgodmode', $privilegies) && in_array('ip_ban_admin', $privilegies) && in_array('discussion_forum_remove_posts', $privilegies) )
					{
						$confirmed_recipients[] = $user_id;
					}
				break;
				
				default:
					die('THERE IS NO DEFAULT!');
				break;
			}
		}
		
		preint_r( $confirmed_recipients );
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