<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'guestbook-functions.php');
	$ui_options['stylesheets'][] = 'abuse.css';
	$ui_options['javascripts'][] = 'start.js';
	$ui_options['javascripts'][] = 'abuse_report_handle.js';
	$ui_options['title'] = 'Rapportfunktionen för ett säkert Hamsterpaj';
	$ui_options['menu_path'] = array('hamsterpaj', 'rapportera');

	if(!is_privilegied('abuse_report_handler'))
	{
		jscript_location("/");
	}
		$out .= '<div id="abuse">' . "\n";
		$out .= '<h1>Inkomna rapporter</h1>' . "\n";
		$query = 'SELECT a.*, l.username AS report_username FROM abuse AS a, login AS l WHERE reply_timestamp = 0 AND l.id = a.reporter ORDER BY id ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			$out .= '<div id="abuse_report_' . $data['id'] . '">';
			$options['color'] = 'blue_deluxe';
			$out .= rounded_corners_top($options);
			switch($data['report_type'])
			{
				case 'forum_post':
					$post_query = 'SELECT content, author FROM forum_posts WHERE id = "' . $data['reference_id'] . '" LIMIT 1';
					$post_result = mysql_query($post_query) or report_sql_error($post_query, __FILE__, __LINE__);
					$post = mysql_fetch_assoc($post_result);
					$post_poster_query = 'SELECT username, id FROM login WHERE id = ' . $post['author'] . ' LIMIT 1';
					$post_poster_result = mysql_query($post_poster_query) or report_sql_error($post_poster_query, __FILE__, __LINE__);
					$post_poster = mysql_fetch_assoc($post_poster_result);
					$out .= '<strong>Forumpost av <a href="/traffa/user_facts.php?user_id=' . $post['author'] . '">' . $post_poster['username'] . '</a> - Rapporterad av <a href="/traffa/profile.php?id=' . $data['reporter'] . '">' . $data['report_username'] . '</a> <a href="/admin/user_management.php?username=' . $data['report_username'] . '">[UA]</a> ' . fix_time($data['timestamp']) . '</strong>: ' . $abuse_types[$data['abuse_type']]['label'] . '<br />' . "\n";
					$out .= '<p style="font-style: italic">' . $data['freetext'] . '</p>' . "\n";
					$out .= discussion_forum_parse_output(((strlen($post['content']) > 1000) ? substr($post['content'], 0, 1000) . ' [b][INLÄGGET HAR KAPATS AV][/b]' : $post['content']));
					$url = forum_get_url_by_post($data['reference_id']);
					$out .= '<br /><a href="' . $url . '">Gå till inlägget</a>' . "\n";
					break;
				case 'guestbook_entry':
					$entry_query = 'SELECT message, sender FROM traffa_guestbooks WHERE id = "' . $data['reference_id'] . '" LIMIT 1';
					$entry_result = mysql_query($entry_query) or report_sql_error($entry_query, __FILE__, __LINE__);
					$entry = mysql_fetch_assoc($entry_result);
					$entry_poster_query = 'SELECT username, id FROM login WHERE id = ' . $entry['sender'] . ' LIMIT 1';
					$entry_poster_result = mysql_query($entry_poster_query) or report_sql_error($entry_poster_query, __FILE__, __LINE__);
					$entry_poster = mysql_fetch_assoc($entry_poster_result);
					$out .= '<strong>Gästboksinlägg av <a href="/traffa/user_facts.php?user_id=' . $entry['sender'] . '">' . $entry_poster['username'] . '</a> - Rapporterat av <a href="/traffa/guestbook.php?view=' . $data['reporter'] . '">' . $data['report_username'] . '</a> <a href="/admin/user_management.php?username=' . $data['report_username'] . '">[UA]</a> ' . fix_time($data['timestamp']) . '</strong>: ' . $abuse_types[$data['abuse_type']]['label'] . '<br />' . "\n";
					$out .= '<p style="font-style: italic">' . $data['freetext'] . '</p>' . "\n";
					$out .= '<p>'. ((strlen($entry['message']) > 1000) ? substr($entry['message'], 0, 1000) . ' [INLÄGGET HAR KAPATS AV]' : $entry['message']) . '</p>' . "\n";
					break;
				case 'photo':
					$photo_query = 'SELECT user FROM user_photos WHERE id = ' . $data['reference_id'] . ' LIMIT 1';
					$photo_result = mysql_query($photo_query) or report_sql_error($entry_query, __FILE__, __LINE__);
					$photo = mysql_fetch_assoc($photo_result);
					$photo_poster_query = 'SELECT username FROM login WHERE id = ' . $photo['user'] . ' LIMIT 1';
					$photo_poster_result = mysql_query($photo_poster_query) or report_sql_error($photo_poster_query, __FILE__, __LINE__);
					$photo_poster = mysql_fetch_assoc($photo_poster_result);
					$out .= '<strong>Bild uppladdad av <a href="/traffa/user_facts.php?user_id=' . $photo['user'] . '">' . $photo_poster['username'] . '</a> - Rapporterat av <a href="/traffa/guestbook.php?view=' . $data['reporter'] . '">' . $data['report_username'] . '</a> <a href="/admin/user_management.php?username=' . $data['report_username'] . '">[UA]</a> ' . fix_time($data['timestamp']) . '</strong>: ' . $abuse_types[$data['abuse_type']]['label'] . '<br />' . "\n";
					$out .= '<p style="font-style: italic">' . $data['freetext'] . '</p>' . "\n";
					$out .= '<a href="http://www.hamsterpaj.net/traffa/photos.php?ajax&user_id=' . $photo['user'] . '&image_id=' . $data['reference_id'] . '#photo"><img src="http://images.hamsterpaj.net/photos/thumb/' . floor($data['reference_id'] / 5000) . '/' . $data['reference_id'] . '.jpg" />';
				break;
			}
			$out .= '<p style="margin-bottom: 0px;"><a style="cursor:pointer" onclick="handleReport(' . $data['id'] . ')">Hantera denna rapport</a></p>' . "\n";
			$out .= rounded_corners_bottom();
			$out .= '</div>';
		}
		$out .= '</div>' . "\n";
		
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
