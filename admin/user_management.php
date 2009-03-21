<?php
	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'guestbook.lib.php');
	$ui_options['menu_path'] = array('admin', 'user_management');
	$ui_options['title'] = 'User Management - Hamsterpaj.net';
	
	if(!is_privilegied('user_management_admin'))
	{
		die('Not authorized');
	}
	
	ui_top($ui_options);
	
	$out = '<h1 style="margin-top: 0px;">User management</h1>'. "\n";
	$out .= '<form method="get">' . "\n";
	$out .= '<h2>Load user</h2>' . "\n";
	$out .= '<input type="text" name="username" />' . "\n";
	$out .= '<input type="submit" value="Load" />' . "\n";
	$out .= '</form>' . "\n";
	echo rounded_corners($out, array('color' => 'green'), true);
	
	if(isset($_GET['username']))
	{
		
		$query = 'SELECT l.*, u.* FROM login AS l, userinfo AS u WHERE l.username = "' . $_GET['username'] . '" AND u.userid = l.id';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		if(mysql_num_rows($result) == 1)
		{
			$user = mysql_fetch_assoc($result);
			$user_session = session_load($user['session_id']);
			if(isset($_POST['action']))
			{
				switch($_POST['action'])
				{
					case 'quality_level':
					if(is_privilegied('read_only_admin'))
					{
						$query = 'UPDATE login SET quality_level = "' . $_POST['quality_level'] . '", quality_level_expire = "' . $_POST['expire'] . '" WHERE id = "' . $user['id'] . '" LIMIT 1';
						mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
						// trace('user_management_error', 'Query: ' . $query . ', Error: ' . mysql_error());
						$user_session['login']['quality_level'] = $_POST['quality_level'];
						$user_session['login']['quality_level_expire'] = $_POST['expire'];
						echo '<p>User quality level updated</p>' . "\n";
						preint_r($_POST);
					}
					else
					{
							echo 'Du har inte privilegier för att sätta RO\'s' . "\n";
					}
						break;
					case 'gb_warning':
						guestbook_insert(array(
							'sender' => 2348,
							'recipient' => $user['id'],
							'is_private' => 1,
							'message' => $_POST['message']
						));
						echo '<p>Guestbook message sent</p>' . "\n";
						break;
					case 'user_abuse':
						$query = 'INSERT INTO user_abuse (user, timestamp, admin, freetext) VALUES("' . $user['id'] . '", "' . time() . '", "' . $_SESSION['login']['id'] . '", "' . $_POST['freetext'] . '")';
						mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
						break;
				}
				
				if($user['id'] != $_SESSION['login']['id'])
				{
					session_save($user['session_id'], $user_session);
				}
			}
			
			echo '<hr />' . "\n";
			
			$out = '<h1 style="margin-top: 0px;"><a href="/traffa/profile.php?id=' . $user['id'] . '">' . $user['username'] . '</a>, member since ' . date('Y-m-d H:i', $user['regtimestamp']) . '</h1>' . "\n";
			
			
			
			$out .= '<hr />' . "\n";
			if(is_privilegied('read_only_admin'))
			{
				$out .= '<h2 style="margin-top: 0px;">Forum quality level</h2>' . "\n";
				$out .= '<form method="post">' . "\n";
				$out .= '<input type="hidden" name="action" value="quality_level" />' . "\n";
				$out .= '<select name="quality_level">' . "\n";
				for($i = 5; $i >= 0; $i--)
				{
					$selected = ($user['quality_level'] == $i) ? ' selected="selected"' : '';
					$out .= '<option value="' . $i . '"' . $selected . '>' . $i . '</option>' . "\n";
				}
				$out .= '</select>' . "\n";
				$out .= '<select name="expire">' . "\n";
				$out .= ($user['quality_level_expire'] > time()) ? '<option value="' . $user['quality_level_expire'] . '">' . date('Y-m-d H:i', $user['quality_level_expire']) . '</option>' . "\n" : '';
				$out .= '<option value="' . (time()+7200) . '">2 tim</option>' . "\n";
				$out .= '<option value="' . (time()+86400) . '">1 dygn</option>' . "\n";	
				$out .= '<option value="' . (time()+86400*2) . '">2 dygn</option>' . "\n";	
				$out .= '<option value="' . (time()+86400*3) . '">3 dygn</option>' . "\n";	
				$out .= '<option value="' . (time()+86400*7) . '">1 vecka</option>' . "\n";	
				$out .= '<option value="' . (time()+86400*14) . '">2 veckor</option>' . "\n";	
				$out .= '<option value="' . (time()+86400*28) . '">4 veckor</option>' . "\n";	
				$out .= '</select>' . "\n";
				$out .= '<input type="submit" value="Save" />' . "\n";
				$out .= '</form>' . "\n";			
			}
			else
			{
				echo 'Du har inte privilegier för att sätta RO\'s' . "\n";
			}
			
			$out .= '<hr />' . "\n";
			$out .= '<h2>Anonymous guestbook message</h2>' . "\n";
			$out .= '<p>Message will be sent to ' . $_GET['username'] . ' as a guestbook entry from webmaster</p>' . "\n";
			$out .= '<form method="post">' . "\n";
			$out .= '<input type="hidden" name="action" value="gb_warning" />' . "\n";
			$out .= '<textarea name="message" style="width: 620px; height: 50px;"></textarea>' . "\n";
			$out .= '<input type="submit" value="Send" />' . "\n";
			$out .= '</form>' . "\n";
			
			$out .= '<hr />' . "\n";
			$out .= '<h2>Abuse log</h2>' . "\n";
			$out .= '<form method="post">' . "\n";
			$out .= '<input type="hidden" name="action" value="user_abuse" />' . "\n";
			$out .= '<input type="text" name="freetext" style="width: 550px;" />' . "\n";
			$out .= '<input type="submit" value="Save" />'. "\n";
			$out .= '</form>' . "\n";
			
			
			$query = 'SELECT * FROM user_abuse WHERE user = "' . $user['id'] . '" ORDER BY id DESC';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			$out .= '<ul style="margin: 0px;">' . "\n";
			while($data = mysql_fetch_assoc($result))
			{
				$out .= '<li>' . "\n";
				$out .= date('Y-m-d H:i', $data['timestamp']) . ' <strong>' . $data['admin'] . '</strong> ' . $data['freetext'];
				$out .= '</li>' . "\n";
			}
			$out .= '</ul>' . "\n";
			
			echo rounded_corners($out, $void, true);
		}
		else
		{
			echo '<p class="error">User not found, please check spelling</p>' . "\n";
		}
	}

	ui_bottom();
?>