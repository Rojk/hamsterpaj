<?php
	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'profile.lib.php');
	require_once(PATHS_LIBRARIES . 'userblock.lib.php');
	require_once(PATHS_LIBRARIES . 'guestbook.lib.php');
	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	$ui_options['stylesheets'][] = 'discussion_forum.css';


	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$params['user_id'] = $_GET['id'];
	}
	// NEW Standards, always use ?user_id= when sending or retrieving an user id.
	elseif(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
	{
		$params['user_id'] = $_GET['user_id'];
	}
	elseif(login_checklogin())
	{
		$params['user_id'] = $_SESSION['login']['id'];
	}
	
	
	if (userblock_checkblock($params['user_id']))
	{
		ui_top();
		echo '<p class="error">IXΘYΣ! Du har blivit blockad, var snel hest så slipper du sånt ;)<br /><em>Visste du förresten att IXΘYΣ betyder Fisk på grekiska?</em></p>';
		ui_bottom();
		exit;
	}
	
	/* Fetching Username and setting page title */

		$query = 'SELECT username FROM login WHERE id = "' .$params['user_id'] . '" LIMIT 1';

		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$data = mysql_fetch_assoc($result);


	$profile = profile_fetch($params);
	$ui_options['title'] .= $data['username'] . ' - Hamsterpaj.net';
	$ui_options['stylesheets'][] = 'profile_themes/' . $profile['profile_theme'] . '.css';

	ui_top($ui_options);

	echo profile_mini_page($profile);

	$query = 'SELECT l.regtimestamp, l.username, u.forum_userlabel, u.forum_posts, u.forum_spam, u.birthday AS user_birthday, t.guestbook_entries';
	$query .= ' FROM login AS l, userinfo AS u, traffa AS t';
	$query .= ' WHERE l.id = "' .$params['user_id'] . '" AND u.userid = l.id AND t.userid = l.id LIMIT 1';

	$result = mysql_query($query) or report_sql_error($query);
	$data = mysql_fetch_assoc($result);

/* Lef fiffling */
	$one_week_ago = time() - 604800;
	$query = 'SELECT user_id FROM user_warnings WHERE user_id = "' . $params['user_id'] . '" AND timestamp > "' . $one_week_ago . '"';
	$result = mysql_query($query) or report_sql_error($query);
	$warned = mysql_num_rows($result);
/* End of Lef */




	/* Set users specialstatus */
	if($warned > 0)
	{
		$specialstatus = 'Varnad';
	}
	elseif(strlen($data['forum_userlabel']) > 0)
	{
		$specialstatus = $data['forum_userlabel'];
	}
	elseif($data['regtimestamp'] < time() - 86400 * 356 * 2.5)
	{
		$specialstatus = 'Veteran';
	}
	elseif($data['regtimestamp'] < time() - 86400 * 356)
	{
		$specialstatus = 'Stammis';
	}
	elseif($data['regtimestamp'] > time() - 86400 * 7)
	{
		$specialstatus = 'Nykomling';
	}

	$user_levels_readable = array(1 => 'Vanlig anv.', 2 => 'Medhjälpare', 3 => 'Ordningsvakt', 4 => 'Administratör', 5 => 'Sysop');

	if ($data['user_birthday'] != '' && $data['user_birthday'] != "0000-00-00")
	{
		$extra1 = '<th>Födelsedag</th>';
		$extra2 = '<td>' . $data['user_birthday'] . '</td>';
	}
	$output .= rounded_corners_tabs_top();
	$out .= '<table>' . "\n";
	$out .= '<tr><th>Medlem sedan</th><th>Specialstatus</th>' . $extra1 . '</tr>' . "\n";
	$out .= '<tr><td>' . date('Y-m-d', $data['regtimestamp']) . '</td><td>' . $specialstatus . '</td>' . $extra2 . '</tr>' . "\n";

	$out .= '<tr><th>Inlägg i forumet</th><th>Spam i forumet</th><th>Gästboksinlägg</th></tr>' . "\n";
	$out .= '<tr><td>' . cute_number($data['forum_posts']) . '</td><td>' . cute_number($data['forum_spam']) . '</td><td>' . cute_number($data['guestbook_entries']) . '</td></tr>' . "\n";

	$out .= '</table>' . "\n";
	echo $out;
	$output .= rounded_corners_tabs_bottom();

	if(is_privilegied('ip_ban_admin') || is_privilegied('remove_user'))
	{
		$query = 'SELECT lastlogon, lastip, regip, lastusername FROM login WHERE id LIKE "' . $params['user_id'] . '" LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query);
		$user_ips = mysql_fetch_assoc($result);
		$out_ip = 'Senaste inloggning <strong>' . fix_time($user_ips['lastlogon']) . '</strong>';
		$out_ip .= ' Från IP <strong>' . $user_ips['lastip'] . '</strong>' . "\n";
		// Fetch all user with that IP
			$query = 'SELECT DISTINCT login_log.ip, login_log.user_id, login.id, login.username FROM login_log, login WHERE login_log.ip = "' . ip2long($user_ips['lastip']) . '" AND login_log.user_id = login.id AND login_log.user_id != "' . $params['user_id'] . '" AND login.is_removed = 0';
			$ip_users = mysql_query($query) or report_sql_error($query);
			$out_ip .= '| Andra med IPt:' . "\n";
			while($ip_user = mysql_fetch_assoc($ip_users))
			{
				$out_ip .= '<a href="http://www.hamsterpaj.net/traffa/profile.php?user_id=' . $ip_user['id'] . '">' . $ip_user['username'] . '</a>' . "\n";
			}
		$out_ip .= '<br />' . "\n";
		$out_ip .= 'Registrerad från IP';
		$out_ip .= ' <strong>' . $user_ips['regip'] . '</strong>' . "\n";
		// Fetch all user with that IP
			$query = 'SELECT DISTINCT login_log.user_id, login.id, login.username FROM login_log, login WHERE login_log.ip = "' . ip2long($user_ips['regip']) . '" AND login_log.user_id != "' . $params['user_id'] . '" AND login_log.user_id = login.id';// AND login.is_removed = 0';
			$ip_users = mysql_query($query) or report_sql_error($query);
			$out_ip .= '| Andra med IPt:' . "\n";
			while($ip_user = mysql_fetch_assoc($ip_users))
			{
				$out_ip .= '<a href="http://www.hamsterpaj.net/traffa/profile.php?user_id=' . $ip_user['id'] . '">' . $ip_user['username'] . '</a>' . "\n";
			}
		if (is_privilegied('ip_ban_admin'))
		{
			$out_ip .= '<br /><strong>Senaste användarnamn:</strong> ' . $user_ips['lastusername'] . "\n";
			$out_ip .= '<br /><a href="/admin/ip_ban_admin.php?handy_link_auto_ip=' . $user_ips['lastip'] . '">Till IP-ban via senaste ip</a>' . "\n";
			$out_ip .= '<br /><a href="/admin/ip_ban_admin.php?handy_link_auto_ip=' . $user_ips['regip'] . '">Till IP-ban via regip</a>' . "\n";
		}
		echo rounded_corners($out_ip, $void, true);
	}

	$admincontrol_out .= is_privilegied('remove_user') ? '<a href="/admin/remove_user.php?userid=' . $params['user_id'] . '" onclick="return confirm(\'Ta bort användaren?\');">Ta bort</a> | ' . "\n" : '';
	$admincontrol_out .= is_privilegied('warnings_admin') ? '<a href="/admin/warnings.php?username=' . $data['username'] . '">Varna!</a> | ' . "\n" : '';
	$admincontrol_out .= is_privilegied('warnings_admin') ? '<a href="/admin/warnings.php?action=viewhistory&user_id=' . $params['user_id'] . '">Varningshistorik</a> | ' . "\n" : '';
	$admincontrol_out .= is_privilegied('user_management_admin') ? '<a href="/admin/user_management.php?username=' . $data['username'] . '">User management</a> |' . "\n" : '';
	$admincontrol_out .= is_privilegied('avatar_admin') ? '<a href="/avatar.php?id=' . $params['user_id'] . '&refuse&admin" onclick="return confirm(\'Är du säker på att du vill ta bort denna bild?\');" />Ta bort avatar</a> | ' . "\n" : '';
//	$admincontrol_out .= is_privilegied('edit_presentation') ? '<a href="/installningar/profilesettings.php?admin_change=' . $params['user_id'] . '">Ändra presentation</a> | ' . "\n" : '';
	$admincontrol_out .= is_privilegied('use_ghosting_tools') ? '<a href="/admin/ghost.php?ghost=' . $data['username'] . '">Ghosta</a> | ' . "\n" : '';
	$admincontrol_out .= is_privilegied('logout_user') ? '<a href="/admin/logout_user.php?action=logout&username=' . $data['username'] . '">Logga ut användare</a>' . "\n" : '';

	if(!empty($admincontrol_out))
	{
		$output .= rounded_corners_tabs_top();
		echo $admincontrol_out;
		$output .= rounded_corners_tabs_bottom();
	}

	if (is_privilegied('user_management_admin'))
	{
		//$out = '<h2 style="margin: 0px;">Varningar</h2>';
		//$query = 'SELECT
		$out .= '<h2 style="margin-top: 0px; margin-bottom: 0px;">Abuse log</h2>';
		$query = 'SELECT * FROM user_abuse WHERE user = "' . $params['user_id'] . '" ORDER BY id DESC';
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

		echo '<h2>De senaste inläggen i forumet och gästboken</h2>' . "\n";
		echo '<p>Inga knappar funkar, men det ska ändå inte visas publikt.</p>' . "\n";


		$forum_posts = discussion_forum_post_fetch(array('disable_forum_lookup' => true, 'author' => $params['user_id'], 'limit' => 7, 'order-direction' => 'DESC'));


		foreach($forum_posts as $post)
		{
			echo '<a href="' . forum_get_url_by_post($post['id']) . '"><h2>Goto goto! Jalla Jalla! Jihad! Jihad! Go fetch it!</h2></a>' . "\n";
			echo discussion_forum_post_render($post, array(), array('show_post_controls' => false));
		}
	}
	
	if(is_privilegied('use_ghosting_tools'))
	{
		$guestbook_posts = guestbook_fetch(array('sender' => $params['user_id'], 'is_private' => 0, 'limit' => 5));
		echo guestbook_list($guestbook_posts);
	}

	echo $output;
	ui_bottom();
?>
