<?php
	require('include/core/common.php');
	
	if(!login_checklogin())
	{
		die('Du blev nog utloggad.');
	}
	
	if(!is_privilegied('use_debug_tools'))
	{
		die('Fisk');
	}
	
	/*if(!isset($_SESSION['handy_auth']) || $_SESSION['handy_auth'] == false)
	{
		if(!isset($_POST['handy_login']))
		{
			die(''
				. '<form method="post">'
				. '<input type="password" name="handy_login" />'
				. '<input type="submit" value="Kaka" />'
				. '</form>'
			);
		}
		else
		{
			if(md5(sha1(md5($_POST['handy_login']))) == $handy_login_password_hash && !isset($_SESSION['handy_auth_cannot_login']))
			{
				$_SESSION['handy_auth'] = true;
			}
			else
			{
				// A hacker trap!
				if(in_array(strtolower($_POST['handy_login']), array('a', 'aa', '1', '111', 'aaaa', 'zzzz', 'z', 'zz', 'zzz', '0', 'steve', 'kaka', 'hubba', 'fesk', 'fisk', 'hubbafesk', 'hamsterpaj', 'pass', 'password', 'l0s3n0rd', 'pw', 'raksmorgas', 'hamster', 'hamstern')))
				{
					$_SESSION['handy_auth_cannot_login'] = true;
				}
					die('Fejl, fejl, fejl.');
			}
		}
	}	*/
	
	$valid_page_handles = array('session', 'server_vars', 'phpinfo', 'remote_session', 'userinfo_from_database',  'encoders_decoders', 'handy_information_logout');
	$page = (isset($_GET['page']) && in_array($_GET['page'], $valid_page_handles)) ? $_GET['page'] : 'default';


	$page_descriptions = array();
	$page_descriptions['default'] = 'Start';
	$page_descriptions['session'] = 'Session information';
	$page_descriptions['remote_session'] = 'Remote session information';
  $page_descriptions['userinfo_from_database'] = 'Userinfo (from database)';
	$page_descriptions['phpinfo'] = 'Phpinfo()';
	$page_descriptions['server_vars'] = 'Server variables';
	$page_descriptions['encoders_decoders'] = 'Encoders and decoders';
	$page_descriptions['handy_information_logout'] = 'Logout (handy.php)';
	
	echo '<h1>Handy information</h1>' . "\n";
	
	echo '<ul style="padding: 0px">' . "\n";
	foreach($page_descriptions as $handle => $user_friendly)
	{
		echo '<li style="float: left;list-style-type: none; margin: 5px"><a href="?page=' . $handle . '" style="color: black;' . (($page == $handle) ? 'font-weight: bold' : '') . '">' . $user_friendly . '</a></li>' . "\n";
	}
	echo '</ul>' . "\n\n";
	
	echo '<br style="clear: both" />' . "\n\n";
	
	echo '<h2>' . $page_descriptions[$page] . '</h2>' . "\n";
	
	switch($page)
	{
		case 'session':
			preint_r($_SESSION);
		break;
		
		case 'server_vars':
			echo '<h3>$_SERVER</h3>' . "\n";
			preint_r($_SERVER);
			
			echo '<h3>$_ENV</h3>' . "\n";
			preint_r($_ENV);

			echo '<h3>Loaded ini files:</h3>' . "\n";
			if($ini_files = php_ini_scanned_files()){
				if(strlen($ini_files) > 0) {
	  			preint_r(explode(',', str_replace('\n', '', $ini_files)));
				}
				else
				{
					echo 'Could not find any ini-files...';
				}
			}
		break;
		
		case 'phpinfo':
			phpinfo();
		break;
		
		case 'remote_session':
			if(isset($_GET['username']) && mb_strtolower($_GET['username']) != 'borttagen')
			{
				$query = 'SELECT session_id FROM login WHERE username = "' . $_GET['username'] . '"';
				$result = mysql_query($query) or report_sql_error($query);
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					if(strlen($data['session_id']) > 0)
					{
						$remote_session = session_load($data['session_id']);
						preint_r($remote_session);
					}
					else
					{
						echo 'Could not find session id in database.';
					}
				}
				else
				{
					echo 'Found ' . mysql_num_rows($result) . ' users, expected one (1) user!';
				}
			}
			else
			{
				echo 'Username: '
				. '<form>'
				. '<input type="hidden" name="page" value="remote_session" />'
				. '<input type="text" name="username" />'
				. '<input type="submit" value="Convert" />'
				. '</form>';
			}
		break;

		case 'userinfo_from_database':
			if(isset($_GET['username'], $_GET['userid']))
			{
				$query = 'SELECT l.*, u.*, p.*'
							 . ' FROM login AS l, userinfo AS u, preferences AS p'
							 . ' WHERE ' . (( empty($_GET['username']) && is_numeric($_GET['userid']) ) ? 'l.id = ' . (int)$_GET['userid'] : 'l.username LIKE "' . $_GET['username'] . '"') . ' AND u.userid = l.id AND p.userid = l.id'
							 . ' LIMIT 1';
				$result = mysql_query($query) or report_sql_error($query);

				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					preint_r($data, 'Table login + userinfo + preferences [<a href="?page=userinfo_from_database&list_login_log=' . $data['userid'] . '" style="color: #000">Show logins from login_log</a>]');
					if(isset($data['module_states']))
					{
						preint_r(unserialize($data['module_states']), 'Module states');
					}
				}
				else
				{
					echo 'Could not find any users matching your query!';
				}
			}
			else if(isset($_GET['list_login_log']) && is_numeric($_GET['list_login_log']))
			{
				$query = 'SELECT *'
							 . ' FROM login_log'
							 . ' WHERE user_id = ' . $_GET['list_login_log']
							 . ' ORDER BY logon_time DESC'
							 . ' LIMIT 50';
				$result = mysql_query($query) or report_sql_error($query);

				echo '<h3>Listing 50 last rows in login_log</h3>';
				echo '<table>';
				echo '<tr>' . '<td>Logon time</td><td>Timestamp</td><td>Impressions</td><td>IP</td>' . '</tr>';
				while($data = mysql_fetch_assoc($result))
				{
					echo '<tr>';
					echo '<td>' . fix_time($data['logon_time']) . '</td><td>' . $data['logon_time'] . '</td>'
							.'<td>' . $data['impressions'] . '</td>'
							.'<td>' . long2ip($data['ip']) . '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			else
			{
				echo '<form method="get">' . "\n"
						.'<input type="hidden" name="page" value="userinfo_from_database" />' . "\n"
						.'Username: <input type="text" name="username" /> OR userid: <input type="text" name="userid" />' . "\n"
						.'<input type="submit" value="Show userinfo" />' . "\n"
						.'</form>' . "\n";
			}
		break;
		
		case 'encoders_decoders':
		$type = isset($_GET['type']) ? $_GET['type'] : '';
		$go_back = ' <a href="?page=encoders_decoders" style="color: black">[ Back ]</a><br />' . "\n";
		
		echo '<h3>IP-adresses</h3>' . "\n";
		if($type == 'ip2host' && isset($_GET['ip']) && ereg('^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(/[0-9]{1,2}){0,1}$', $_GET['ip']))
		{
			echo gethostbyaddr($_GET['ip']) . $go_back;
		}
		else
		{
			echo 'IP: '
			. '<form>'
			. '<input type="hidden" name="page" value="encoders_decoders" />'
			. '<input type="hidden" name="type" value="ip2host" />'
			. '<input type="text" name="ip" />'
			. '<input type="submit" value="Convert" />'
			. '</form>';
		}
		
		if($type == 'ip2long' && isset($_GET['ip']) && ereg('^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(/[0-9]{1,2}){0,1}$', $_GET['ip']))
		{
			echo ip2long($_GET['ip']) . $go_back;
		}
		else
		{
			echo 'ip2long(): '
			. '<form>'
			. '<input type="hidden" name="page" value="encoders_decoders" />'
			. '<input type="hidden" name="type" value="ip2long" />'
			. '<input type="text" name="ip" />'
			. '<input type="submit" value="Convert" />'
			. '</form>';
		}
		
		if($type == 'long2ip' && isset($_GET['long']) && is_numeric($_GET['long']))
		{
			echo long2ip($_GET['long']) . $go_back;
		}
		else
		{
			echo 'long2ip(): '
			. '<form>'
			. '<input type="hidden" name="page" value="encoders_decoders" />'
			. '<input type="hidden" name="type" value="long2ip" />'
			. '<input type="text" name="long" />'
			. '<input type="submit" value="Convert" />'
			. '</form>';
		}
		
		echo '<h3>Serialize &gt; preint_r():</h3>' . "\n";
		
		if($type == 'serialize2preint_r' && isset($_GET['data']))
		{
			preint_r(unserialize(stripslashes($_GET['data']))) . $go_back;
		}
		else
		{
			echo 'Serialized data string: (Note: DO NOT convert untrusted data)'
			. '<form>'
			. '<input type="hidden" name="page" value="encoders_decoders" />'
			. '<input type="hidden" name="type" value="serialize2preint_r" />'
			. '<textarea name="data"></textarea>'
			. '<input type="submit" value="Convert" />'
			. '</form>';
		}
		
		echo '<h3>Hash calculators</h3>' . "\n";
		
		if($type == 'md5' && isset($_POST['data']))
		{
			echo md5($_POST['data']) . $go_back;
		}
		else
		{
			echo 'MD5:'
			. '<form method="post" action="?page=encoders_decoders&type=md5">'
			. '<input type="text" name="data" />'
			. '<input type="submit" value="Convert" />'
			. '</form>';
		}
		
		if($type == 'sha1' && isset($_POST['data']))
		{
			echo sha1($_POST['data']) . $go_back;
		}
		else
		{
			echo 'SHA1:'
			. '<form method="post" action="?page=encoders_decoders&type=sha1">'
			. '<input type="text" name="data" />'
			. '<input type="submit" value="Convert" />'
			. '</form>';
		}
		
		if($type == 'hamsterpaj_password_hash' && isset($_POST['data'], $_POST['data_do_not_show']))
		{
			$data = empty($_POST['data_do_not_show']) ? $_POST['data'] : $_POST['data_do_not_show'];
			echo hamsterpaj_password(utf8_decode($data)) . $go_back;
		}
		else
		{
			echo 'Hamsterpaj password hash:'
			. '<form method="post" action="?page=encoders_decoders&type=hamsterpaj_password_hash">'
			. '<input type="text" name="data" />'
			. ' (OR leave blank and use &lt;input type="password"&gt;: <input type="password" name="data_do_not_show" /> )'
			. ' <input type="submit" value="Convert" />'
			. '</form>';
		}
		
		
		if($type == 'base64encode' && isset($_GET['data']))
		{
			echo base64_encode($_GET['data']) . $go_back;
		}
		else
		{
			echo 'Base64encode:'
			. '<form>'
			. '<input type="hidden" name="page" value="encoders_decoders" />'
			. '<input type="hidden" name="type" value="base64encode" />'
			. '<input type="text" name="data" />'
			. '<input type="submit" value="Convert" />'
			. '</form>';
		}
		
		if($type == 'base64decode' && isset($_GET['data']))
		{
			echo base64_decode($_GET['data']) . $go_back;
		}
		else
		{
			echo 'Base64decode:'
			. '<form>'
			. '<input type="hidden" name="page" value="encoders_decoders" />'
			. '<input type="hidden" name="type" value="base64decode" />'
			. '<input type="text" name="data" />'
			. '<input type="submit" value="Convert" />'
			. '</form>';
		}
		
		if($type == 'timestamp2readable' && isset($_GET['timestamp']) && is_numeric($_GET['timestamp']))
		{
			echo date('Y-m-d H:i:s', $_GET['timestamp']) . $go_back;
		}
		else
		{
			echo 'Timestamp to readable (Y-m-d H:i:s):'
			. '<form>'
			. '<input type="hidden" name="page" value="encoders_decoders" />'
			. '<input type="hidden" name="type" value="timestamp2readable" />'
			. '<input type="text" name="timestamp" />'
			. '<input type="submit" value="Convert" />'
			. '</form>';
		}
			
		break;
		
		case 'handy_information_logout':
		 $_SESSION['handy_auth'] = false;
		 unset($_SESSION['handy_auth']);
		 jscript_location('/');
		break;
		
		default:
			echo '<h3>Time</h3>' . "\n";
			echo 'Current time: ' . date('Y-m-d H:i:s') . '<br />' . "\n";
			echo 'Current timestamp: ' . time() . '<br />' . "\n";
			
			echo 'Timestamp at ' . date('Y-m-d 00:00:00') . ': ' . strtotime(date('Y-m-d 00:00:00')) . '<br />' . "\n";
			
			$timestamp_tomorrow = strtotime(date('Y-m-d 00:00:00', time() + 86400));
			echo 'Timestamp at ' . date('Y-m-d H:i:s', $timestamp_tomorrow) . ': ' . $timestamp_tomorrow . '<br />' . "\n";
			
			$timestamp_day_after_tomorrow = strtotime(date('Y-m-d 00:00:00', time() + 86400 + 86400));
			echo 'Timestamp at ' . date('Y-m-d H:i:s', $timestamp_day_after_tomorrow) . ': ' . $timestamp_day_after_tomorrow . '<br />' . "\n";
			
			$timestamp_monday_this_week =  strtotime( ((date('D') == 'Mon') ? 'Today' : 'last Monday') );
			echo 'Timestamp last Monday this week: &nbsp; &nbsp;' . $timestamp_monday_this_week . ' (' . date('Y-m-d H:i:s', $timestamp_monday_this_week) . ')';
			
			echo '<h3>IP</h3>' . "\n";
			echo '<h4>Your IP</h4>';
			echo $_SERVER['REMOTE_ADDR'] . '<br />' . gethostbyaddr($_SERVER['REMOTE_ADDR']);
			
			echo '<h4>IP -&gt; Hostname</h4>'
				. '<form>'
				. '<input type="hidden" name="page" value="encoders_decoders" />'
				. '<input type="hidden" name="type" value="ip2host" />'
				. '<input type="text" name="ip" />'
				. '<input type="submit" value="Convert" />'
				. '</form>';
				
			echo '<h4>Loaded functions</h4>';
			
			$loaded_functions = get_defined_functions();
			echo '<ul>' . "\n";
			foreach($loaded_functions['user'] as $function_name)
			{
				echo "\t" . '<li>' . $function_name . '</li>' . "\n";
			}
			echo '</ul>' . "\n";
	}
?>
