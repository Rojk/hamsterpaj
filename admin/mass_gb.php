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
		$haxx_string = $_POST['forum_privs'] . $_POST['ip_ban_privs'] . $_POST['igotgodmode_privs'];
		
		switch ( $_POST['from'] )
		{
			case 'webmaster':
				$send_from = 2348;
			break;
			case 'me':
				$send_from = $_SESSION['login']['id'];
			break;
			default:
				throw new Exception('$_POST[\'from\'] iz a failz0er...');
			break;
		}
		
		// People who should'nt be recognized, but have privilegies.
		$ignore_list = array(
			714129, // NKL
			
		);
		// People who should be recognised as OV's but have ip_ban_admin
		$admins_to_ov = array(
			848713 
		);
		
		$sql = 'INSERT INTO mass_gb_history SET timestamp = ' . time() . ',';
		$sql .= ' sent_by = ' . $_SESSION['login']['id'] . ',';
		$sql .= ' message = "' . $_POST['message'] . '",';
		$sql .= ' send_gb_from = ' . $send_from . ',';
		$sql .= ' haxx_string = "' . $haxx_string . '"';
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
			if ( !in_array($user_id, $ignore_list) )
			{
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
						if ( (in_array('igotgodmode', $privilegies) || in_array('ip_ban_admin', $privilegies)) && !in_array($user_id, $admins_to_ov) )
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
						if ( (!in_array('igotgodmode', $privilegies) && in_array('ip_ban_admin', $privilegies) && in_array('discussion_forum_remove_posts', $privilegies)) && !in_array($user_id, $admins_to_ov) )
						{
							$confirmed_recipients[] = $user_id;
						}
					break;
					
					default:
						die('THERE IS NO DEFAULT!');
					break;
				}
			}
		}
		foreach ( $confirmed_recipients as $recipient )
		{
			$entry['sender'] = $send_from;
			$entry['recipient'] = $recipient;
			$entry['message'] = $_POST['message'];
			if (!guestbook_insert($entry))
			{
				$out .= 'Failade att skicka meddelande till ' . $recipient . '.<br />' . "\n";
			}
			else
			{
				$out .= 'Meddelande skickat till ' . $recipient . '<br />';
			}
		}
		preint_r( $confirmed_recipients );
		preint_r( $entry );
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
				<table>
						<tr>
							<td style="padding-right: 10px;">
								<label for="forum_privs ip_ban_privs igogtgodmode_privs">Vilka vill du skicka till? <strong>*</strong></label>
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
							<td style="border-left: thin solid #BBB; padding: 10px;">
								<label for="from">Vem vill du skicka från? <strong>*</strong></label><br  />
								<select name="from">
									<option value="choose">Välj</option>
									<option value="webmaster">Webmaster</option>
									<option value="me">Mig själv</option>
								</select>
							</td>
						</tr>
					</table>
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