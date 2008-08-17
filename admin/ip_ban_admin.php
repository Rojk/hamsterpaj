<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('admin', 'ban');
	
	if(!is_privilegied('ip_ban_admin'))
	{
		header('location: /');
		die();
	}
	
	switch(isset($_GET['action']) ? $_GET['action'] : 'home')
	{
		default:
		case 'home':
			$out .= rounded_corners_top(array(), true);
			$out .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?action=add">' . "\n";
			$out .= 'IP: <input type="text" name="ip">' . "\n";
			$out .= 'Anledning på max 255 tecken: <input type="text" name="reason">' . "\n";
			$out .= '<input type="submit" value="IP-banna!" class="button_120" />';
			$out .= '</form>' . "\n";
			$out .= rounded_corners_bottom(array(), true);
			
			$out .= '<table>' . "\n";
			
			$out .= '<td><strong>Datum</strong></td>';
			$out .= '<td><strong>IP</strong></td>';
			$out .= '<td><strong>Anledning</strong></td>';
			$out .= '<td><strong>Utslängd av</strong></td>';
			$out .= '<td><strong>Ta bort</strong></td>' . "\n";
			
			$query = 'SELECT i.ip AS ip, i.reason AS reason, i.timestamp AS timestamp, l.username AS banned_by_username, l.id AS banned_by_user_id FROM ip_ban_list AS i, login AS l WHERE i.banned_by = l.id';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			while($data = mysql_fetch_assoc($result))
			{
				$out .= '<tr>';
				$out .= '<td><strong>' . date('Y-m-d', $data['timestamp']) . ': </strong></td>';
				$out .= '<td>' . $data['ip'] . '</td>';
				$out .= '<td>' . $data['reason'] . '</td>';
				$out .= '<td><a href="/traffa/profile.php?id=' . $data['banned_by_user_id'] . '">' . $data['banned_by_username'] . '</a></td>';
				$out .= '<td><a href="' . $_SERVER['PHP_SELF'] . '?action=remove_confirm&ip=' . $data['ip'] . '">[X]</a></td>';
				$out .= '</tr>' . "\n";
			}
			
			$out .= '</table>' . "\n";
		break;
		
		case 'add':
			if(isset($_POST['ip']) && isset($_POST['reason']) && substr($_POST['ip'], 0, 7) != '192.168' && preg_match('/^(25[0-5]|2[0-4]\d|[01]?\d\d|\d)\\.(25[0-5]|2[0-4]\d|[01]?\d\d|\d)\\.(25[0-5]|2[0-4]\d|[01]?\d\d|\d)\\.(25[0-5]|2[0-4]\d|[01]?\d\d|\d)$/', $_POST['ip']))
			{
				$query = 'INSERT INTO ip_ban_list(ip, reason, banned_by, timestamp) VALUES ("' . $_POST['ip'] . '", "' . $_POST['reason'] . '", ' . $_SESSION['login']['id'] . ', ' . time() . ')';
				if(@mysql_query($query))
				{
					echo 'Ip-adressen lades till i systemet! <a href="' . $_SERVER['PHP_SELF'] . '">&laquo; Tillbaka</a>.' . "\n";
					log_admin_event('ip banned', $_POST['ip'], $_SESSION['login']['id'], 0, 0);
				}
				else
				{
					// Primary key...
					echo 'Ip-adressen kunde inte läggas till i systemet. Om den redan finns i systemet, kontakta en Sysop med information: ' . __FILE__ . ' on line ' . __LINE__;
				}
			}
			else
			{
				$out .= 'Ip-adress ogiltig. Kontakta Sysop.';
			}
			
		break;
		
		case 'remove_confirm':
			$out .= '<h2>Vill du verkligen ta bort ' . $_GET['ip'] . ' ifrån bannade-ip-listan?</h2>' . "\n";
			$out .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?action=remove">' . "\n";
			$out .= '<input type="hidden" name="ip" value="' . $_GET['ip'] . '">' . "\n";
			$out .= '<input type="submit" value="Ja, klart jag vill!" class="button_120" />';
			$out .= '</form>' . "\n";
		break;
		
		case 'remove':
			$query = 'DELETE FROM ip_ban_list WHERE ip = "' . $_POST['ip'] . '"';
			mysql_query($query);
			$out .= 'Borta!';
		break;
	}
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>