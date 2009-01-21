<?php
	require('../include/core/common.php');
	
	//$ui_options['current_menu'] = 'annat';
	$ui_options['menu_path'] = array('fra', 'user_logins');
	$ui_options['title'] = 'Visar inloggningar från en användare';
	$ui_options['stylesheets'][] = 'forms.css';

	if(!is_privilegied('ip_ban_admin'))
	{
		die('Not authorized.');
	}
	
	$out .= '
		<fieldset>
		<legend>User logins</legend>
				<table class="form">
					<tr>
						<td>
							<form action="' . $_SERVER['SCRIPT_URI'] . '" method="get">
								<label for="username">Användarnamn</label><br />
								<input type="text" name="username" /><br />
								<input type="submit" value="JIHAD!?!" onclick="this.disabled = true; this.value = \'Inte längre ;)\'" />
							</form>
						</td>
						<td>
							<form action="' . $_SERVER['SCRIPT_URI'] . '" method="get">
								<label for="username">Användarid</label><br />
								<input type="text" name="user_id" /><br />
								<input type="submit" value="JIHAD!?!" onclick="this.disabled = true; this.value = \'Inte längre ;)\'" />
							</form>
						</td>
						<td>
							<form action="' . $_SERVER['SCRIPT_URI'] . '" method="get">
								<label for="username">IP-adress</label><br />
								<input type="text" name="ip" /><br />
								<input type="submit" value="JIHAD!?!" onclick="this.disabled = true; this.value = \'Inte längre ;)\'" />
							</form>
						</td>
					</tr>
				</table>
		</fieldset>' . "\n";
	
	$highlights[] = '<strong>%VALUE%</strong>';
	$highlights[] = '<em>%VALUE%</em>';
	$highlights[] = '<span style="color: green; font-weight: bold;">%VALUE%</span>';
	$highlights[] = '<span style="color: red; font-weight: bold;">%VALUE%</span>';
	$highlights[] = '<span style="color: blue; font-weight: bold;">%VALUE%</span>';
	$highlights[] = '<span style="color: #ababab; font-weight: bold;">%VALUE%</span>';
	$highlights[] = '<span style="color: green;">%VALUE%</span>';
	$highlights[] = '<span style="color: red;">%VALUE%</span>';
	$highlights[] = '<span style="color: blue;">%VALUE%</span>';
	$highlights[] = '<span style="color: #ababab;">%VALUE%</span>';
	$highlights[] = '<span style="color: #9b0ca0; font-weight: bold;">%VALUE%</span>';
	$highlights[] = '<span style="color: #9b0ca0;">%VALUE%</span>';
	
	if(isset($_GET['user_id']))
	{
		$fetch['login'] = array('username');
		$user_info = login_load_user_data($_GET['user_id'], $fetch, __FILE__, __LINE__);
		$out .=  '<h1>Visar inloggningar från användare #' . $_GET['user_id'] . ' (' . $user_info['login']['username'] . ')</h1>';
	
		$query = 'SELECT * FROM login_log WHERE user_id = "' . $_GET['user_id'] . '" ORDER BY logon_time DESC LIMIT 500';
		$result = mysql_query($query);
		$out .=  '<table style="width: 100%;">';
		while($data = mysql_fetch_assoc($result))
		{
			$ip = long2ip($data['ip']);
			if(!isset($assigned[$ip]))
			{
				if(count($highlights) > 0)
				{
					$assigned[$ip] = array_pop($highlights);
				}
				else
				{
					$assigned[$ip] = '%VALUE%';
				}
			}
	
			$display_ip = str_replace('%VALUE%', $ip, $assigned[$ip]);

			$out .=  '<tr>' . "\n";
			$out .=  '<td>' . date('Y-m-d H:i:s', $data['logon_time']) . '</td>' . "\n";
			$out .=  '<td>' . $data['impressions'] . '</td>' . "\n";
			$out .=  '<td><a href="?ip=' . $ip . '">' . $display_ip . '</a></td>' . "\n";
			$out .=  '</tr>' . "\n";
		}
		$out .=  '</table>';
	}
	elseif ( !empty($_GET['username']) )
	{
		if ( strtolower($_GET['username']) == 'borttagen' )
		{
			die('Nej, det där fungerar inte.');
		}
		
		$query = 'SELECT id FROM login WHERE username = "' . $_GET['username'] . '" LIMIT 1';
		$result = mysql_query($query);
		$data = mysql_fetch_assoc($result);
		
		$out .= '<h2>Visar inloggningar för ' . $_GET['username'] . '</h2>' . "\n";
		
		$query = 'SELECT * FROM login_log WHERE user_id = "' . $data['id'] . '" ORDER BY logon_time DESC LIMIT 500';
		$result = mysql_query($query);
		$out .=  '<table style="width: 100%;">';
		while($data = mysql_fetch_assoc($result))
		{
			$ip = long2ip($data['ip']);
			if(!isset($assigned[$ip]))
			{
				if(count($highlights) > 0)
				{
					$assigned[$ip] = array_pop($highlights);
				}
				else
				{
					$assigned[$ip] = '%VALUE%';
				}
			}
	
			$display_ip = str_replace('%VALUE%', $ip, $assigned[$ip]);

			$out .=  '<tr>' . "\n";
			$out .=  '<td>' . date('Y-m-d H:i:s', $data['logon_time']) . '</td>' . "\n";
			$out .=  '<td>' . $data['impressions'] . '</td>' . "\n";
			$out .=  '<td><a href="?ip=' . $ip . '">' . $display_ip . '</a></td>' . "\n";
			$out .=  '</tr>' . "\n";
		}
		$out .= '</table>' . "\n";
	}
	elseif(isset($_GET['ip']))
	{
		$out .=  '<h1>Visar inloggningar från IP: ' . $_GET['ip'] . '</h1>' . "\n";
		$query = 'SELECT ll.*, l.username FROM login_log AS ll, login AS l ';
		$query .= 'WHERE ll.ip = "' . ip2long($_GET['ip']) . '" AND l.id = ll.user_id ';
		$query .= 'ORDER BY logon_time DESC LIMIT 500';

		$result = mysql_query($query);
		$out .=  '<table style="width: 100%;">';
		while($data = mysql_fetch_assoc($result))
		{
			$out .=  '<tr>' . "\n";
			$out .=  '<td>' . date('Y-m-d H:i:s', $data['logon_time']) . '</td>' . "\n";
			$out .=  '<td><a href="?user_id=' . $data['user_id'] . '">' . $data['username'] . '</a></td>' . "\n";
			$out .=  '<td>' . $data['impressions'] . '</td>' . "\n";
			$out .=  '</tr>' . "\n";
		}
		$out .=  '</table>';
	}

	echo ui_top($ui_options);
	echo $out;
	echo ui_bottom();

?>


