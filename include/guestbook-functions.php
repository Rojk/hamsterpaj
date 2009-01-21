<?php


/*









NOTE: This file is deprecated, use the new system instead!













*/

/*
Databas-struktur:
	Tabell: traffa_guestbooks
		id
		timestamp
		recipient
		sender
		message
	Tabell: traffa
		guestbook_entries

Denna fil kräver shared-functions.php!

Funktioner:
	new_entry($recipient, $sender, $text){}
		Tar emot två ID-nummer och en text. Utför ingen kontroll av indata utan skriver slaviskt inlägget till databasen
	list_entries($userid, $offset = 0){}
		Listar inlägg hos $userid, $offset anger vart vi börjar lista. Tar hand om paging, ritar inte ut formulär.
	delete_entry($entry_id, $recipient){}
		Tar bort $entry_id om det ägs av $recipient och returnerar 1. Annars skriver det ut ett felmeddelande och returnerar 0.
		Uppdaterar även guestbook_entries hos $recipients.
	draw_reply_form($recipient, $recipientid){}
		Här skall både användarnamn och användar-id skickas! Ritar upp svara-formuläret (ska ligga i popup).
	draw_message_form($recipient, $text=null, $errormsg = null) {}
		Ritar upp meddelandeformulär. Kräver recipient, kan ta emot såväl felmeddelande som text.
	spamcheck($sender, $text, $to){}
		Gör diverse kontroller, returnerar 1 om allt är OK, annars returneras ett felmeddelande.	
*/


function new_entry($recipient, $sender, $message, $private = 0, $answereid = null, $post_id = 0)
{	
	//$message = htmlspecialchars($message);
	$private = ($private == 1)? 1 : 0;
	$insert_sql = 'INSERT INTO traffa_guestbooks(timestamp, recipient, sender, message, is_private, forum_post)  ';
	$insert_sql .= 'VALUES(UNIX_TIMESTAMP(), ' . $recipient . ', ' . $sender . ', \'' . $message . '\', ' . $private . ', ' . $post_id . ')';

	mysql_query($insert_sql) or die(report_sql_error($insert_sql));
	$update_sql = 'UPDATE traffa SET guestbook_entries = guestbook_entries + 1 WHERE userid = ' . $recipient . ' LIMIT 1';
	mysql_query($update_sql) or die('Ett kritiskt fel uppstod! Felet uppstod i new_entry(). Felet uppstod när data uppdaterades.<br />Felinfo:<br />' . mysql_error());
	$sessid_sql = 'SELECT session_id FROM login WHERE id = "' . $recipient . '" LIMIT 1';
	$sessid_result = mysql_query($sessid_sql) or die(report_sql_error($sessid_sql));
	$sessid_data = mysql_fetch_assoc($sessid_result);
	if(strlen($sessid_data['session_id']) > 5)
	{
		$remote_session = session_load($sessid_data['session_id']);
		$remote_session['notices']['unread_gb_entries'] += 1;
		session_save($sessid_data['session_id'], $remote_session);
	}
	
	if (isset($answereid))
	{
	$query = 'UPDATE traffa_guestbooks SET answered = "Y" WHERE id = "' . $answereid . '" AND recipient = "' . $_SESSION['login']['id'] . '" LIMIT 1';
	mysql_query($query) or die('Ett kritiskt fel uppstod! Felet uppstod i new_entry(). Felet uppstod när data uppdaterades.<br />Felinfo:<br />' . mysql_error());
	}
}

function list_entries($recipient, $entries, $offset = 0, $filter = 0)
{
	global $hp_includepath;
	if($offset < 1 || !is_numeric($offset))
	{ 
		$offset = 0;
	}
	$list_sql = 'SELECT gb.id, gb.timestamp, gb.message, gb.sender, gb.read, gb.answered, gb.forum_post, login.username, info.image, ';
	$list_sql .= 'info.birthday, info.gender, is_private, zip_codes.* ';
	$list_sql .= 'FROM traffa_guestbooks AS gb, login, userinfo AS info, zip_codes ';
	if($filter > 0)
	{
		$list_sql .= 'WHERE (';
		$list_sql .= '(gb.recipient ="' . $recipient . '" AND gb.sender = "' . $filter . '") ';
		$list_sql .= 'OR (gb.recipient = "' . $filter . '" AND gb.sender = "' . $recipient . '") ';
		$list_sql .= ') AND login.id = gb.sender AND info.userid = gb.sender AND zip_codes.zip_code = info.zip_code ';
	}
	else
	{
		$list_sql .= 'WHERE gb.recipient = "' . $recipient . '" AND zip_codes.zip_code = info.zip_code ';
	}
	$list_sql .= 'AND login.id = gb.sender AND info.userid = gb.sender AND deleted = 0 ';
	if(isset($_SESSION['login']['id']))
	{
		$list_sql .= 'AND (gb.is_private = 0 OR gb.sender = ' . $_SESSION['login']['id'] . ' OR gb.recipient = ' . $_SESSION['login']['id'] . ') ';
	}
	else
	{
		$list_sql .= 'AND gb.is_private = 0 ';
	}
	if($filter > 0)
	{
		$list_sql .= 'ORDER BY gb.id DESC';		
	}
	else
	{
		$list_sql .= 'ORDER BY gb.id DESC LIMIT ' . $offset . ', ' . GUESTBOOK_MESSAGES_PER_PAGE;
	}
	$list_result = mysql_query($list_sql) or die(report_sql_error($list_sql));

	if($filter == 0)
	{
		echo '<p class="subtitle">Gästbok - ' . cute_number($entries) . ' inlägg</p>' . "\n";
	}
	else
	{
		echo '<p class="subtitle">Gästbok - Visar historik</p>' . "\n";
	}
	
	
	if($offset > 0)
	{
		echo ' - sida ' . intval(($offset / GUESTBOOK_MESSAGES_PER_PAGE)+1) . ' av ' . intval(($entries / GUESTBOOK_MESSAGES_PER_PAGE)+1);
	}
	echo '</p>' . "\n";
	$unread = array();
	
	if(true)
	{
		while($entry = mysql_fetch_assoc($list_result))
		{
			if($entry['read'] != 1)
			{
				rounded_corners_top(array('color' => 'orange_deluxe', 'id' => 'gb_entry_' . $entry['id']));
			}
			else
			{
				rounded_corners_top(array('color' => 'blue_deluxe', 'id' => 'gb_entry_' . $entry['id']));
				$button_color = 'blue_';
			}
			
			echo '<div class="entry_picture">' . "\n";
			if($entry['image'] == 1 || $entry['image'] == 2)
			{
				echo ui_avatar($entry['sender']);
			}
			echo '</div>' . "\n";
			
			echo '<div class="entry_main">' . "\n";
			echo '<div class="entry_info">' . "\n";
			echo '<span class="gb_private" id="gb_private_' . $entry['id'] . '_label">';
			echo ($entry['is_private'] == 1) ? 'Detta inlägg är privat' : '';
			echo '</span>' . "\n";

			echo '<span class="gb_unanswered" id="gb_unanswered_' . $entry['id'] . '_label">';
			echo ($entry['answered'] != 'Y') ? 'Obesvarat' : '';
			echo '</span>' . "\n";
			
			echo '<span class="timestamp">Skrevs ' . fix_time($entry['timestamp']) . '</span>';
			echo '<a href="/traffa/profile.php?id=' . $entry['sender'] . '">' . $entry['username'] . '</a>' . "\n";
			echo ($entry['gender'] == 'm') ? ' Pojke' : '';
			echo ($entry['gender'] == 'f') ? ' Flicka' : '';
			echo ($entry['birthday'] != '0000-00-00') ? ' ' . date_get_age($entry['birthday']) . ' år' : '';
			echo (strlen($entry['spot']) > 0) ? ' från <a style="cursor: pointer;" onclick="window.open(\'http://www.hitta.se/LargeMap.aspx?ShowSatellite=false&pointX=' . $entry['y_rt90'] . '&pointY=' . $entry['x_rt90'] . '&cx=' . $entry['y_rt90'] . '&cy=' . $entry['x_rt90'] . '&z=6&name=' . $entry['username'] . '%20i%20' . urlencode($entry['spot']) . '\', \'user_map_3\', \'location=false, width=750, height=500\');">' . $entry['spot'] . '</a>' : '';
			echo '</div>' . "\n";
			
			echo '<p>' . "\n";
			echo setsmilies(nl2br($entry['message']));
//			preint_r($entry);
			echo '</p>' . "\n";
			
			echo '</div>' . "\n";
			
			echo '<div class="entry_controls">' . "\n";
			echo ($recipient == $_SESSION['login']['id'] && $entry['sender'] != $_SESSION['login']['id']) ? '<a href="/hamsterpaj/abuse.php?report_type=guestbook_entry&reference_id=' . $entry['id'] . '" class="abuse_button"><img src="http://images.hamsterpaj.net/abuse.png" /></a> ' . "\n" : '';
			echo ($recipient == $_SESSION['login']['id'] && $entry['sender'] != $_SESSION['login']['id']) ? '<button class="' . $button_color . 'button_70" onclick="gb_block_user(\'' . $entry['username'] . '\');">Blockera</button> ' . "\n" : '';
			echo ($recipient == $_SESSION['login']['id'] || $entry['sender'] == $_SESSION['login']['id']) ? '<button id="gb_private_' . $entry['id'] . '" class="' . $button_color . 'button_80" onclick="gb_private(' . $entry['id'] . ');"' . (($entry['is_private'] == 1) ? ' style="display: none;"' : '') . '>Privatisera</button> '  . "\n": '';
			echo ($recipient == $_SESSION['login']['id'] && $entry['sender'] != $_SESSION['login']['id']) ? '<button id="gb_unprivate_' . $entry['id'] . '" class="' . $button_color . 'button_90" onclick="gb_unprivate(' . $entry['id'] . ');"' . (($entry['is_private'] == 1) ? '' : ' style="display: none;"') . '>Avprivatisera</button> ' . "\n" : '';
			echo '<button class="' . $button_color . 'button_70" onclick="gb_history(' . $recipient . ', ' . $entry['sender'] . ');">Historik</button> ' . "\n";
			echo '<button class="' . $button_color . 'button_60" onclick="gb_goto(' . $entry['sender'] . ');">Gå till</button> ' . "\n";
			echo ($recipient == $_SESSION['login']['id'] && $entry['sender'] != $_SESSION['login']['id']) ? '<button class="' . $button_color . 'button_60" onclick="gb_remove(' . $entry['id'] . ', \'' . $entry['username'] . '\', \'' . fix_time($entry['timestamp']) . '\');">Ta bort</button> '  . "\n": '';
			echo ($recipient == $_SESSION['login']['id'] && $entry['sender'] != $_SESSION['login']['id']) ? '<button class="' . $button_color . 'button_50" onclick="gb_answer(\'' . $entry['username'] . '\', ' . $entry['sender'] . ', ' . $entry['id'] . ');">Svara</button> '  . "\n": '';
			echo '</div>' . "\n";


			if($entry['read'] != 1)
			{
				rounded_corners_bottom(array('color' => 'orange_deluxe'));				
			}
			else
			{
				rounded_corners_bottom(array('color' => 'blue_deluxe'));
			}
			
			if($entry['read'] == 0)
			{
				array_push($unread, $entry['id']);
			}
		}
	}
	else
	{
		while($list_data = mysql_fetch_assoc($list_result))
		{
			if($list_data['read'] == 1)
			{
				echo '<div style="background: #f7f7f7">' . "\n";
				
			}
			else
			{
				echo '<div style="background: #ffc777">' . "\n";
			}
			echo '<table class="body"><tr><td style="width: 80px; vertical-align: top;">' . "\n";
			if($list_data['image'] == 1 || $list_data['image'] == 2)
			{
				echo insert_avatar($list_data['sender']);
			}
			echo '</td>' . "\n" . '<td style="vertical-align: top; width: 650px;">' . "\n";
			if($list_data['is_private'] == 1)
			{
				echo '<b>Detta inlägg är privat</b>' . "\n";
			}
			if ($list_data['forum_post'] > 0)
			{
				echo '<div class="orange_faded_div" style="width: auto;">Detta är en kommentar på ett forumsinlägg. <a href="javascript: void(0);" onclick="window.open(\'/forum_new/read_post_popup.php?id=' . $list_data['forum_post'] . '\', Math.random(), \'width=600, height=450, scrollbars=yes\');">Läs inlägget i en popup</a> eller <a href="/forum_new/index.php?action=redirect_to_post&post_id=' . $list_data['forum_post'] . '">gå till tråden i forumet</a>.</div>';
			}
			if ($_SESSION['login']['id'] == $recipient)
			{
				$reply_status = (($list_data['answered'] == 'Y') ? null : ' Obesvarat');
			}
			echo '<p style="color: grey;">';
			echo ($list_data['read'] == 0 ? '<strong>' : null);
			echo '(skrevs ' . fix_time($list_data['timestamp']) . $reply_status . ')';
			echo ($list_data['read'] == 0 ? '</strong>' : null);
			echo '<br />' . "\n";
			echo '<a href="/traffa/profile.php?id=' . $list_data['sender'] . '"><b>' . $list_data['username'] . '</b></a> ';
			if($list_data['gender'] == 'm')
			{
				echo 'Kille ';
			}
			elseif($list_data['gender'] == 'f')
			{
				echo 'Tjej ';
			}
			$userage = date_get_age($list_data['birthday']);
			if($userage > 0)
			{
				echo $userage . ' år ';
			}
			if(strlen($list_data['spot']) > 0)
			{
				echo 'från ' . $list_data['spot'] . ' ';
			};
			echo '</p>' . "\n";
			echo '<p>';
			echo setSmilies(nl2br($list_data['message']));
			echo '</p>' . "\n";
			echo '<p style="text-align: right;">' . "\n";
	
			echo '<form action="/traffa/userblocks.php" method="post" style="display: inline; float: right;">' . "\n";
			if($_SESSION['login']['id'] == $recipient && $filter == 0)
			{
				echo '<input type="submit" value="Blockera" class="button_70" style="display: inline;" onclick="return confirm(\'Du är på väg att blockera avsändaren från att skicka fler gästboksinlägg och meddelanden till dig\');" />' . "\n";
			}
		
			if ($filter == 0)
			{
				echo '<input type="button" onClick="window.location=\'' . $_SERVER['PHP_SELF'] . '?action=history&view=' . $recipient . '&remote=' . $list_data['sender'] . '\';" class="button_70" value="Historik" /> ' . "\n";
			}
			echo '<input type="button" onclick="window.location=\'' . $_SERVER['PHP_SELF'] . '?view=' . $list_data['sender'] . '\';" class="button_60" value="Gå till" /> ' . "\n";
			if($_SESSION['login']['id'] == $recipient)
			{
				echo '<input type="button" onclick="window.open(\'/traffa/gb-reply.php?action=reply&username=' . $list_data['username'] . '&userid=' . $list_data['sender'] . '&answereid=' . $list_data['id'];
				echo '\',\'\' ,\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, width=250, height=200\');" value="Svara" class="button_50" /> ' . "\n";
				if ($filter == 0)
				{
				echo '<input type="button" onclick="var name=confirm(\'Vill du verkligen ta bort inlägget?\'); if(name==true){ window.location=\'' . $_SERVER['PHP_SELF'] . '?action=delete&entry_id=' . $list_data['id'] . '&return_offset=' . $_GET['offset'] . '\'; }" value="Ta bort" class="button_70" />' . "\n";
	
				echo '<input type="hidden" name="addblock" value="' . $list_data['username'] .'" />' . "\n";
				}
			}
			echo '</form>' . "\n";
			if($_SESSION['login']['id'] == $recipient && $filter == 0)
			{
				echo '<button class="button_80" style="display: inline;" onclick="idiot_report(\'' . $list_data['sender'] . '\');" />Rapportera</button>' . "\n";
			}
			echo '</p>' . "\n";
			echo '</td></tr>'  . "\n" . '</table>' . "\n";
			echo '</div>' . "\n";
			echo '<br />' . "\n";
			if($list_data['read'] == 0)
			{
				array_push($unread, $list_data['id']);
			}
		}
	}

	if($offset > 0)
	{
		echo '<input type="button" onclick="window.location=\'' . $_SERVER['PHP_SELF'] . '?offset=' . intval($offset - GUESTBOOK_MESSAGES_PER_PAGE) . '&view=' . $recipient . '\';" value="<<Föregående sida" class="button" style="float: left;" />';
	}
	if($offset + GUESTBOOK_MESSAGES_PER_PAGE < $entries && $entries > GUESTBOOK_MESSAGES_PER_PAGE)
	{
		echo '<input type="button" onclick="window.location=\'' . $_SERVER['PHP_SELF'] . '?offset=' . intval($offset + GUESTBOOK_MESSAGES_PER_PAGE) . '&view=' . $recipient . '\';" value="Nästa sida>>" class="button" style="float: right;" />';
	}

	echo '<div style="text-align: center; margin-bottom: 15px;">';
	for($pages = 1; $pages < intval(($entries / GUESTBOOK_MESSAGES_PER_PAGE)+1) && $pages < 32; $pages++)
	{
		echo '<a href="' . $_SERVER['PHP_SELF'] . '?view=' . $recipient . '&offset=' . (($pages-1) * GUESTBOOK_MESSAGES_PER_PAGE) . '">' . ($pages) . '</a> ';
	}
	echo '</div>';


	if($recipient == $_SESSION['login']['id'] && $filter == 0)
	{
		$read_sql = 'UPDATE traffa_guestbooks SET `read` = 1 WHERE id = 1 '; //Bara för att göra foreach-loopen smidigare :P
		foreach($unread AS $current)
		{
			$read_sql .= 'OR id = ' . $current . ' ';
		}
		mysql_query($read_sql) or die(report_sql_error($read_sql, __FILE__, __LINE__));
	}
}
function delete_entry($entry_id, $recipient)
{
	$delete_sql = 'UPDATE traffa_guestbooks SET deleted = 1 WHERE id = ' . $entry_id . ' AND recipient = ' . $recipient . ' AND `read` = 1 LIMIT 1';
	mysql_query($delete_sql) or die('Ett kritiskt fel har uppstått! Felet uppstod i delete_entry().<br />Felinfo:<br />' . mysql_error());
	if(mysql_affected_rows() != 1)
	{
		jscript_alert('Ett fel har uppstått! Det verkar som om du försökt att ta bort någon annans inlägg!');
		return 0;
	}
	$update_sql = 'UPDATE traffa SET guestbook_entries = guestbook_entries - 1 WHERE userid = ' . $recipient . ' LIMIT 1';
	mysql_query($update_sql) or die('Ett kritiskt fel har inträffat! Felet uppstod i delete_entry() när inläggsräknaren skulle uppdateras.<br />Felinfo:<br />' . mysql_error());
	return 1;
}

function draw_reply_form($recipient, $recipientid, $answereid, $text = null)
{
	echo 'Skickar meddelande till ' . $recipient;
	echo '<form action="/traffa/gb-reply.php?action=send_reply&username=' . $recipient . '&userid=' . $recipientid . '&answereid=' . $answereid . '" method="post" style="margin: 0px;">';
	echo '<textarea name="message" class="textbox" style="width: 213px; height: 130px;">' . $text . '</textarea><br />';
	echo '<input type="submit" value="Skicka!" class="button_50" style="float: right; margin: 0px; padding: 0px;" />';
	echo 'Gör inlägget privat <input type="checkbox" name="is_private" value="1" />' . "\n";
	echo '</form>';
}

function draw_message_form($recipient, $text = null, $errormsg = null)
{
	if(isset($errormsg))
	{
		echo '<p style="color: red; font-weight: bold;">' . $errormsg . '</p>';
	}
	else
	{
		echo '<p class="subtitle">Skriv nytt meddelande</p>';
	}
?>

<?php
	echo '<form action="/traffa/guestbook.php?action=send_new_message&userid=' . $recipient . '" method="post" name="compose">';
	echo '<textarea name="message" class="textbox" rows="5" cols="75">' . $text . '</textarea><br />';
	echo '<input name="recipient" type="hidden" value="' . $recipient . '" />';
	echo '<strong>Infoga smilies</strong><br />';
	echo listSmilies('document.compose.message') . '<br />';
	echo 'Gör inlägget privat: <input type="checkbox" name="is_private" value="1" /><br />';
	echo '<input type="submit" class="button" value="Skicka!" />';
	echo '</form>';
}

function spamcheck($sender, $text, $to = null)
{
	if (strlen(trim($text)) == 0)
	{
		return 'Dina meddelanden måste innehålla minst ett tecken';
	}
	if($to == '2348')
	{
		return 'Webmaster är ett administrationskonto som inte används av någon människa.';
	}
	$spam_sql = 'SELECT COUNT(*) as messages FROM traffa_guestbooks WHERE sender = ' . $sender . ' AND timestamp > UNIX_TIMESTAMP()-600';
	$spam_result = mysql_query($spam_sql) or die ('Ett kritiskt fel har uppstått! Felet uppstod i spamcheck().<br />Felinfo:<br />' . mysql_error());
	$spam_data = mysql_fetch_assoc($spam_result) or die('Ett kritiskt fel har uppstått! Felet uppstod i spamchekc().<br />Feinfo:<br />' . mysql_error());
	if($spam_data['messages'] > 20)
	{
		return 'Du kan max skicka 20 meddelanden på tio minuter. Håll dig till tåls en liten stund ;)';
	}
	/* content_check finns i shared-functions och kollar efter kåta killar, referrer-sidor och annat styggt. Returnerar 1 om meddelandet är ok, annars ett felmeddlande. */
	return content_check($text);
}

function count_unread_gb_entries($userid)
{
	$guestbook_sql = 'SELECT COUNT(*) AS unread FROM traffa_guestbooks WHERE recipient = ' . $userid . ' AND `read` = 0';
	$guestbook_result = mysql_query($guestbook_sql) or die('Ett fel inträffade!' . mysql_error());
	$guestbook_data = mysql_fetch_assoc($guestbook_result);
	return $guestbook_data['unread'];
}
?>
