<?php
	function guestbook_fetch($options, $render_deleted)
	{
		$options['offset'] = (isset($options['offset']) && is_numeric($options['offset'])) ? $options['offset'] : 0;
		$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 30;
		if(isset($options['recipient']))
		{
			$options['recipient'] = (is_array($options['recipient'])) ? $options['recipient'] : array($options['recipient']);
		}
		if(isset($options['sender']))
		{
			$options['sender'] = (is_array($options['sender'])) ? $options['sender'] : array($options['sender']);
		}
		$query = 'SELECT tg.*, l.username, u.image, u.birthday, u.gender';
		$query .= ' FROM traffa_guestbooks AS tg, login AS l, userinfo AS u';
		if ($render_deleted)
		{
			$query .= ' WHERE l.id = tg.sender AND u.userid = tg.sender AND tg.deleted = 0';
		}
		else
		{
			$query .= ' WHERE l.id = tg.sender AND u.userid = tg.sender';
		}
		
		$query .= ' AND tg.deleted = 0';
		if(!(in_array($_SESSION['login']['id'], $options['recipient'])))
		{
			if(login_checklogin())
			{
				$query .= ' AND (tg.is_private = "N" OR tg.sender = "' . $_SESSION['login']['id'] . '")';
			}
			else
			{
				$query .= ' AND tg.is_private = "N" ';
			}
		}
		$query .= (isset($options['recipient'])) ? ' AND  tg.recipient IN("' . implode('", "', $options['recipient']) . '")' : '';
		$query .= (isset($options['sender'])) ? ' AND  tg.sender IN("' . implode('", "', $options['sender']) . '")' : '';
		$query .= ' ORDER BY tg.id DESC';
		$query .= ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];

		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			$entries[] = $data;
		}

		return $entries;
	}

	function guestbook_insert($entry)
	{
		if ($entry['is_mass_gb'] !== true)
		{
			if($_SESSION['login']['id'] == $entry['sender'])
			{
				if($_SESSION['last_gb_entry'] > time() - 5)
				{
					return false;
				}
			}
	
			if(strlen($entry['message']) == 0)
			{
				return false;
			}
	
			if($entry['recipient'] == 2348 && $entry['sender'] != 2348)
			{
				$_SESSION['posted_gb_to_webmaster'] = true;
			}
		}

		$entry['is_private'] = ($entry['is_private'] == 1) ? 1 : 0;
		$query = 'INSERT INTO traffa_guestbooks(timestamp, recipient, sender, message, is_private)';
		$query .= ' VALUES("' . time() . '", "' . $entry['recipient'] . '", "' . $entry['sender'] . '", "' . $entry['message'] . '", "' . $entry['is_private'] . '")';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$entry['id'] = mysql_insert_id();

		$query = 'UPDATE userinfo SET gb_entries = gb_entries + 1 WHERE userid = "' . $entry['recipient']. '" LIMIT 1';
		if (!mysql_query($query))
		{
			report_sql_error($query, __FILE__, __LINE__);
			return false;
		}

		if(isset($entry['reply-to']))
		{
			$query = 'UPDATE traffa_guestbooks SET answered = "Y", `read` =  1 WHERE id = "' . $entry['reply-to'] . '" AND recipient = "' . $entry['sender'] . '" LIMIT 1';
			if (!mysql_query($query))
			{
				report_sql_error($query, __FILE__, __LINE__);
				return false;
			}
		}
		if ($entry['is_mass_gb'] !== true)
		{
			$query = 'SELECT session_id FROM login WHERE id = "' . $entry['recipient'] . '" LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			$data = mysql_fetch_assoc($result);
			if(strlen($data['session_id']) > 5)
			{
				$remote_session = session_load($data['session_id']);
				$remote_session['notices']['unread_gb_entries'] += 1;
				$entry['image'] = $_SESSION['userinfo']['image'];
				$entry['timestamp'] = time();
				$entry['message'] = stripslashes($entry['message']);
				$entry['username'] = $_SESSION['login']['username'];
				$remote_session['unread_gb_entries'][] = $entry;
				session_save($data['session_id'], $remote_session);
			}
		}

		$_SESSION['last_gb_entry'] = time();
		return true;
	}

	function guestbook_list($entries)
	{
		$out .= '<ul class="guestbook_entries">' . "\n";
		foreach($entries AS $entry)
		{
			$out .= "\n\n";
			$out .= '<!-- Post #' . cute_number($entry['id']) . ' by ' . $entry['username'] . ' at ' . date('Y-m-d H:i:s', $entry['timestamp']) . '-->' . "\n";
			/*
			$class = ($entry['read'] == 0) ? ' class="unread"': '';
			$out .= '<li' . $class . '>' . "\n";
			if($entry['image'] == 1 || $entry['image'] == 2)
			{
				$out .= '<img src="http://images.hamsterpaj.net/images/users/thumb/' . $entry['sender'] . '.jpg" class="user_avatar" id="entry_' . $entry['id'] . '_photo" />' . "\n";
			}
			else
			{
				$out .= '<img src="http://images.hamsterpaj.net/images/users/no_image_mini.png" class="user_avatar" id="entry_' . $entry['id'] . '_photo" />' . "\n";
			}

			$out .= '<div class="container">' . "\n";
			$out .= '<div class="top_bg">' . "\n";
			$out .= '<div class="bottom_bg">' . "\n";
			*/
			$options['user_id'] = $entry['sender'];
			$options['type'] = ($entry['read'] == 0) ? 'unread': 'standard';
			$out .= message_top($options);

			$out .= '<div id="guestbook_entry_' . $entry['id'] . '">' . "\n";
			$out .= '<span class="timestamp">' . fix_time($entry['timestamp']) . '</span>' . "\n";

			$out .= '<a href="/traffa/profile.php?id=' . $entry['sender'] . '">' . $entry['username'] . '</a> ' . "\n";
			$genders = array('f' => 'F', 'm' => 'P');
			$out .= $genders[$entry['gender']];
			$out .= (date_get_age($entry['birthday']) > 0) ? date_get_age($entry['birthday']) : '';

			$out .= '<span class="unanswered" id="unanswered_label_' . $entry['id'] . '"' . $style . '>' . "\n";
			$out .= ($entry['answered'] != 'Y') ? '(Obesvarat)' : '';
			$out .= '</span>' . "\n";

			$style = ($entry['is_private'] == 0) ? ' style="display: none;"' : '';
			$out .= '<span class="private" id="private_label_' . $entry['id'] . '"' . $style . '>(Privat)</span>' . "\n";

			$out .= '<p>' . setSmilies(nl2br($entry['message'])) . '</p>' . "\n";

			$out .= '<p class="gb_entry_controls">' . "\n";
			$out .= (login_checklogin() && $entry['recipient'] == $_SESSION['login']['id'] && $entry['sender'] != $_SESSION['login']['id']) ? '<a href="/traffa/guestbook.php?view=' . $entry['sender'] . '" class="gb_reply_control" id="reply_control_' . $entry['id'] . '">Svara</a>' . "\n" : '';
			$out .= '<a href="/traffa/guestbook.php?view=' . $entry['sender'] . '&history=' . $entry['recipient'] . '">Historik</a>' . "\n";
			$out .= '<a href="/traffa/guestbook.php?view=' . $entry['sender'] . '">Gå till</a>' . "\n";

			$out .= ($entry['recipient'] == $_SESSION['login']['id']) ? '<a href="/installningar/userblock.php?action=block&username=' . $entry['username'] . '" class="gb_block_control">Blockera</a>' . "\n" : '';


			if(login_checklogin() && $entry['recipient'] == $_SESSION['login']['id'] && $entry['sender'] != $_SESSION['login']['id'])
			{
				$out .= '<a href="/traffa/guestbook.php?view=' . $entry['sender'] . '" class="gb_delete_control" id="delete_control_' . $entry['id'] . '">Ta bort</a>' . "\n";
			}

			if(login_checklogin() && $entry['recipient'] == $_SESSION['login']['id'] && $entry['sender'] != $_SESSION['login']['id'])
			{
				$private_style = ($entry['is_private'] == 1) ? ' style="display: none;"' : '';
				$unprivate_style = ($entry['is_private'] == 0) ? ' style="display: none;"' : '';

				$out .= '<a href="/traffa/guestbook.php?view=' . $entry['sender'] . '" class="gb_private_control" id="private_control_' . $entry['id'] . '"' . $private_style . '>';
				$out .= 'Gör privat</a>' . "\n";

				$out .= '<a href="/traffa/guestbook.php?view=' . $entry['sender'] . '" class="gb_unprivate_control" id="unprivate_control_' . $entry['id'] . '"' . $unprivate_style . '>';
				$out .= 'Gör offentligt</a>' . "\n";

				$out .= '<a href="/hamsterpaj/abuse.php?report_type=guestbook_entry&reference_id=' . $entry['id'] . '" class="abuse_button"><img src="http://images.hamsterpaj.net/abuse.png" /></a>' . "\n";
			}

			$out .= '</p>' . "\n";

			$out .= guestbook_form(array('recipient' => $entry['sender'], 'reply-to' => $entry['id'], 'form_id' => 'gb_reply_form_' . $entry['id']));

			$out .= '</div>' . "\n";
			$out .= message_bottom();
			/*
			$out .= '</div>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '</div>' . "\n";

			$out .= '</li>' . "\n";
			*/
		}
		$out .= '</ul>' . "\n";

		return $out;
	}

	function guestbook_form($options)
	{
		$options['form_id'] = (isset($options['form_id'])) ? $options['form_id'] : rand(0, 9999999);
		$out .= '<form class="gb_form" method="post" action="/ajax_gateways/guestbook.json.php" id="' . $options['form_id'] . '">' . "\n";
		$out .= '<input type="hidden" name="action" value="insert" />' . "\n";
		$out .= '<input type="hidden" name="recipient" value="' . $options['recipient'] . '" />' . "\n";
		$out .= '<textarea name="message" id="' . $options['form_id'] . '_message"></textarea>' . "\n";
		$out .= '<input type="checkbox" name="private" value="1" id="' . $options['form_id'] . '_private_check" />' . "\n";
		$out .= '<label for="' . $options['form_id'] . '_private_check">Privat inlägg</label>' . "\n";
		$out .= '<input type="submit" class="button_60" value="Skicka" />' . "\n";
		$out .= '</form>' . "\n";

		return $out;
	}

	function guestbook_page()
	{
				//Get pagenumber
		$page = 1;
		if(isset($_GET['page']) && is_numeric($_GET['page']))
		{
			$page = intval($_GET['page']);
			if($page < 1 || $page > 999)
			{
				$page = 1;
			}
		}
		$fetch['offset'] = (($page - 1) * 30);
		$fetch['limit'] = 30;
		$entries = guestbook_fetch($fetch);
		$out .= guestbook_list($entries);

		//Create Pagination links
		if(isset($_GET['page']) && is_numeric($_GET['page']))
		{
			$page = intval($_GET['page']);
			if($page > 1)
			{
				$out .= ' <a href="' . $_SERVER['PHP_SELF'];
			$out .= (isset($_GET['view'])) ? '?view=' . $_GET['view'] . '&page' : '?page';
			$out .= '=' . ($page - 1) . '">&laquo; Föregående</a> |';
			}

			if($page > 0)
			{
				$out .= ' ' . $page . ' | <a href="' . $_SERVER['PHP_SELF'];
			$out .= (isset($_GET['view'])) ? '?view=' . $_GET['view'] . '&page' : '?page';
			$out .= '=' . ($page + 1) . '">Nästa &raquo;</a>';
			}
		}
		else
		{
			$out .= ' <a href="' . $_SERVER['PHP_SELF'];
			$out .= (isset($_GET['view'])) ? '?view=' . $_GET['view'] . '&page' : '?page';
			$out .= '=2">Nästa &raquo;</a>';
		}
		return $out;
	}
	
	function guestbook_p12_shield($display)
	{
		if($display == 'on')
		{
			$options['color'] = 'orange';
			$out .= rounded_corners_top($options);
				$out .= '<img class="guestbook_anti_p12_sign" src="http://images.hamsterpaj.net/anti_p12_sign.png" />' . "\n";
				$out .= '<h2 class="guestbook_anti_p12_header">Anti P12-skylt</h2>' . "\n";
				$out .= '<p style="margin: 0;">Om denna rutan är synlig betyder det att Jag inte vill ha jobbiga gästboksinlägg ifrån omogna människor, 
										till exempel inlägg där någon frågar om jag vill ha camsex eller om jag vill lägga till personen msn “Hej msn? puss”. 
										Jag känner inte dig så varför skulle jag vilja det?</p>' . "\n";
				$out .= '<p style="margin-bottom: 0;">Det äcklet som skriver och frågar mig om sex och sådant kommer att bli rapporterad och en Ordningsvakt 
										kommer att granska gästboksinlägget som skrevs till mig. Sedan kommer Ordningsvakter antagligen att radera äcklets användarkonto 
										och stänga av denne ifrån sajten ifall denne förtjänar det.</p>' . "\n";
			$out .= rounded_corners_bottom();
		}
		return $out;
	}

?>
