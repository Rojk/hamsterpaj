<?php
	define('GROUP_CHECK_FORCE', 45);
	define('GROUP_CHECK_OK_MINUS', 50);
	define('GROUP_SHOW_WARNING', 30);
	
	ob_start();
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE . 'libraries/live_chat.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/discussion_forum.lib.php');

	$ui_options['menu_path'] = array('traeffa', 'grupper');
	$ui_options['admtoma_category'] = 'groups';
	$ui_options['javascripts'] = array('scripts.js');
	
	$ui_options['stylesheets'][] = 'live_chat.css';
	$ui_options['javascripts'][] = 'live_chat.js';
	
		$ui_options['javascripts'][] = 'discussion_forum.js';	


	ui_top($ui_options);

	if(login_checklogin() != 1)
  {
    jscript_alert('Du måste vara inloggad för att komma åt denna sidan!');
    jscript_location('/');
    die();
  }

function group_create_new($name, $owner, $newmembers, $description)
{

		$sql = 'SELECT groupid FROM groups_list WHERE name LIKE "' . $name . '"';
    $unique_username_query = mysql_query($sql) or die('Query failed: ' . mysql_error());

    if(mysql_num_rows($unique_username_query) > 0)
    {
      jscript_alert("Namnet är upptaget, välj ett annat");
			die(jscript_location($_SERVER['PHP_SELF']));
    }

	if (count($_SESSION['groups_members']) < 16)
	{
 		/* max 10 groups check */
		$query = 'INSERT INTO groups_list (owner, take_new_members, name, presentation) VALUES (' . $owner . ', ' . $newmembers . ', "' . $name . '", "' . $description . '")';
		mysql_query($query) or die(report_sql_error($query));
		$id = mysql_insert_id();
		$insertquery = 'INSERT INTO groups_members (groupid, userid , approved ) VALUES (' . $id . ', ' . $owner . ', 1)'; 
		mysql_query($insertquery) or die(report_sql_error($insertquery));
	}
	else
	{
		jscript_alert('Du kan bara äga 15 grupper');
	}

}

function group_list_groups($userid, $search)
{
	echo '<form action="' . $_SERVER['PHP_SELF'] . '?action=search_group" method="post">';
	echo '<input type="text" class="textbox" name="search_text" /> ';
	echo '<input type="submit" class="button" value="Sök" />';
	echo '</form><br />';

	if (isset($search))
	{
		$query = 'SELECT l.username, g.name, g.owner, g.presentation, g.groupid FROM groups_list AS g, login AS l ';
		$query.= 'WHERE g.take_new_members = 1 AND (name LIKE "%' . $search . '%" OR g.presentation LIKE "%' . $search . '%") ';
		$query.= 'AND g.owner = l.id ';
		$selectquery = 'SELECT groupid FROM groups_members WHERE userid = ' . $userid;
		$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
		while ($data = mysql_fetch_assoc($result))
		{
    		$query .= ' AND g.groupid <> ' . $data['groupid'];
  		}			
	}
	else 
	{
		$query = 'SELECT l.username, g.owner, g.name, g.presentation, g.groupid FROM groups_list AS g, login AS l ';
		$query.= 'WHERE g.take_new_members = 1 AND g.owner = l.id ';
		$selectquery = 'SELECT groupid FROM groups_members WHERE userid = ' . $userid;
		$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
	
		while ($data = mysql_fetch_assoc($result))
		{
			$query .= ' AND g.groupid <> ' . $data['groupid'];
		}
		
	}

	$query .= ' ORDER BY groupid DESC LIMIT 20';
	$result = mysql_query($query) or die(report_sql_error($query));
	
	echo '<h2>Grupper du kan ansöka till</h2>';

 	if (mysql_num_rows($result) == 0)
	{
		echo 'Tyvärr finns det inga grupper du kan gå med i';
	}

	while ($data = mysql_fetch_assoc($result))
 	{
		echo '<div class="blue_faded_div">';
		echo '<span style="font-size: 14px; font-weight: bold;"><a href="' . $_SERVER['PHP_SELF'] . '?action=goto&amp;groupid=' . $data['groupid'] . '">' . $data['name'] . '</a></span><br />';
		echo '<span style="font-style: italic;">' . substr($data['presentation'], 0, 22) . '... </span>';
		echo '<br /><br />';
		echo 'Skapad av: <a href="/traffa/profile.php?id=' . $data['owner'] . '">' . $data['username'] . '</a>';
		echo '<br />';
		echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=apply&groupid=' . $data['groupid'] . '">Gå med i denna grupp >></a>';
		echo '</div>';
		echo '<br />';		
 	}	
	
}

function group_add_to_group($groupid, $userid, $invited = 0)
{
	$selectquery = 'SELECT owner FROM groups_list WHERE groupid = ' . $groupid;
	$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
	$data = mysql_fetch_assoc($result);

	if($data['owner'] == $_SESSION['login']['id'] || $invited)
	{
		$query = 'UPDATE groups_members SET approved = 1 WHERE userid = ' . $userid . ' AND groupid = ' . $groupid;
 		mysql_query($query) or die(report_sql_error($query));
		if ($invited) 
		{
			$group_data['groups_members'] = array('groupid');
  	  $groups = login_load_group_data($userid, $group_data);
  	  $_SESSION = array_merge($_SESSION, $groups);
		}
	}
	else 
	{
		echo 'Du kan inte validera ansökningar i grupper du inte äger';
	}
}

function group_del_from_group($groupid, $userid, $ownremove = 0)
{
	$selectquery = 'SELECT owner FROM groups_list WHERE groupid = ' . $groupid;
	$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
	$data = mysql_fetch_assoc($result);

	if($data['owner'] == $_SESSION['login']['id'] || $ownremove)
	{
		$query = 'DELETE FROM groups_members WHERE userid = ' . $userid . ' AND groupid = ' . $groupid;
 		mysql_query($query) or die(report_sql_error($query));

		if ($ownremove)
		{
			$group_data['groups_members'] = array('groupid');
  	  $groups = login_load_group_data($userid, $group_data);
    	$_SESSION = array_merge($_SESSION, $groups);
		}
	}
	else
	{
		echo 'Du kan inte ta bort ansökningar i grupper du inte äger';
	}
}

function group_list_my_groups($userid)
{
	$query = 'SELECT groups_list.owner, groups_list.name, groups_members.groupid, groups_members.notices FROM groups_members, groups_list WHERE groups_members.userid =' . $userid . ' AND groups_list.groupid = groups_members.groupid AND groups_members.approved = 1';
	$result = mysql_query($query) or die(report_sql_error($query));
	echo '<table>';
	echo '<tr><td>Gruppnamn</td><td>Gruppinläggsbevakning</td></tr>';
	echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?action=save_data">';
	while ($data = mysql_fetch_assoc($result))
	{
	
		if ($data['owner'] == $_SESSION['login']['id'])
		{
	    echo '<tr><td><a href="' . $_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $data['groupid'] . '"> <b>' .  $data['name'] . '</b></a></td>';
		}
		else
		{
	    echo '<tr><td><a href="' . $_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $data['groupid'] . '"> ' .  $data['name'] . '</a></td>';
		}		
		
		echo '<td><input type="radio" name="' . $data['groupid'] . '" value="Y"';
		if($data['notices'] == 'Y')
	  {
  	  echo 'checked="1" ';
  	}
		echo '/>Ja! ';
		echo '<input type="radio" name="' . $data['groupid'] . '" value="N"';
		if($data['notices'] == 'N')
	  {
  	  echo 'checked="1" ';
  	}
		echo '/>Nej</td></tr>';
	}
	echo '<tr><td><input type="submit" value="Spara inställningar" class="button"></tr></td>';
	echo '</form>';
	echo '</table>';
}

function group_list_groups_i_applied_to($userid)
{
	$query = 'SELECT groups_list.name, groups_members.groupid FROM groups_members, groups_list WHERE groups_members.userid =' . $userid . ' AND groups_list.groupid = groups_members.groupid AND groups_members.approved = 0';
	$result = mysql_query($query) or die(report_sql_error($query));
	if (mysql_num_rows($result) > 0)
	{
		while ($data = mysql_fetch_assoc($result))
		{
  	  echo $data['name'] . ' <a href="' . $_SERVER['PHP_SELF'] . '?action=remove_me&amp;groupid=' . $data['groupid'] . '">[Ta bort ansökan]</a>';
			echo '<br />';
		}
	}
	else
	{
		echo 'Du har inga ansökningar väntande';
	}
}

function group_list_members($groupid, $admin = 0)
{
 	$query = 'SELECT login.username, groups_members.userid, groups_list.owner
 	 FROM login, groups_members, groups_list
 	 WHERE groups_members.groupid =' .  $groupid . '
 	 AND groups_list.groupid = ' . $groupid . '
 	 AND login.id = groups_members.userid
 	 AND groups_members.approved = 1
 	 ORDER BY login.username ASC';
 	$result = mysql_query($query) or die(report_sql_error($query));
 	$drop_title = 'Medlemmar';
	while ($data = mysql_fetch_assoc($result))
 	{		
		if ($data['username'] != 'Borttagen')
		{
			$drop_data .= ($data['userid'] == $data['owner'] ? '<strong>' : null);
 			$drop_data .= '<a href="/traffa/profile.php?id=' . $data['userid'] . '">' . $data['username'] . '</a>';
			$drop_data .= ($data['userid'] == $data['owner'] ? '</strong>' : null);
			$drop_data .= (($admin == 1 && $data['userid'] != $_SESSION['login']['id']) ? '  <a href="' . $_SERVER['PHP_SELF'] . '?action=remove_user&amp;groupid=' . $groupid . '&amp;userid=' . $data['userid'] . '">[Sparka ur]</a>' : null);
			$drop_data .= '<br />';
 		}
 	}
	
	$drop_expanded = NULL;
	$drop_style=array('style'=>'margin:0px');

	echo rounded_corners_top(array('color' => 'blue'));
	echo ui_dropbox($drop_title, $drop_data, $drop_style, $drop_expanded);
	echo rounded_corners_bottom(array('color' => 'blue'));
}

function group_list_pending_members($groupid, &$drop_expanded)
 {
 	$query = 'SELECT login.username, groups_members.userid FROM groups_members, login WHERE groups_members.groupid =' .  $groupid . ' AND groups_members.approved = 0 AND login.id = groups_members.userid';
 	$result = mysql_query($query) or die(report_sql_error($query));
	$return = '<h2>Medlemmar som ansökt till din grupp</h2>';
 	if (mysql_num_rows($result) > 0) 
	{
		while ($data = mysql_fetch_assoc($result))
 		{
 			$return .= '<a href="/traffa/profile.php?id=' . $data['userid'] . '">' . $data['username'] . '</a>';
			$return .= ' <a href="' . $_SERVER['PHP_SELF'] . '?action=validate&amp;groupid=' . $groupid . '&amp;userid=' . $data['userid'] . '">[Godkänn]</a>';
			$return .= ' <a href="' . $_SERVER['PHP_SELF'] . '?action=remove_user&amp;groupid=' . $groupid . '&amp;userid=' . $data['userid'] . '">[Radera]</a>';
			$return .= '<br />';
 		}
		$drop_expanded = 1;
	}
	else
	{
		$return .= 'Inga ansökningar hittades';
	}
	return $return;
}

function group_submit_to_group($groupid, $userid)
{
	$selectquery = 'SELECT COUNT(userid) AS user FROM groups_members WHERE groupid = ' . $groupid . ' AND userid = ' . $userid;
	$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
	$data = mysql_fetch_assoc($result);
	if ($data['user'] == 0)
	{
			if (count($_SESSION['groups_members']) < 16)
			{	
				$insertquery = 'INSERT INTO groups_members (groupid, userid , approved ) VALUES (' . $groupid . ', ' . $userid . ', 0)';	
				mysql_query($insertquery) or die(report_sql_error($insertquery));
				$group_data['groups_members'] = array('groupid');
				$groups = login_load_group_data($userid, $group_data);
				$_SESSION = array_merge($_SESSION, $groups);
			}
			else 
			{
				echo 'Du kan bara vara med i 10 grupper';
			}
	}
	else 
	{
		echo 'Du kan inte gå med i en grupp du är med i';
	}
}

function group_send_new_message($groupid, $userid, $text)
{
	/*if (isset($_SESSION['debug']))
	{
		unset($_SESSION['debug']);
		print_r($_POST);
		print_r($_GET);
		jscript_alert('mirkk');
		die();
	}*/
	$query = 'SELECT disabled FROM groups_list WHERE groupid = ' . $groupid;
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$data = mysql_fetch_assoc($result);
	if ($data['disabled'] == 'Y')
	{
		jscript_alert('Gruppen är tillfälligt stängd');
    jscript_location($_SERVER['PHP_SELF']);
		die();
	}
	$text = $text;
	$query = 'SELECT group_points FROM groups_list WHERE groupid = ' . $groupid;
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$data = mysql_fetch_assoc($result);
	$points = group_check_message_score($text);
	
	if ($data['group_points'] > 0)
	{
		$points--;
	}	
	$query = 'UPDATE groups_list SET group_points = group_points + ' . $points . ' WHERE groupid =  ' . $groupid;
 	mysql_query($query) or die(report_sql_error($query));	
 	$query = 'INSERT INTO groups_scribble (userid, groupid, timestamp , text) VALUES (' . $userid . ', ' . $groupid . ', UNIX_TIMESTAMP() , "' . $text . '")';
 	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$query = 'UPDATE groups_list SET message_count = message_count +1 WHERE groupid = ' . $groupid;
	mysql_query($query) or die(report_sql_error($query));	
	
	event_log_log('group_post');
}

function group_close_group($groupid)
{
	$id = parse_id($groupid);
	if (is_privilegied('groups_superadmin'))
	{
		$query = 'UPDATE groups_list SET disabled = "Y" WHERE groupid = ' . $id;
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
}

function group_list_messages($options)
{
	$groupid = $options['group_id'];
	
	/* Fetch the total messages in group */	
	$selectquery = 'SELECT message_count AS total, owner FROM groups_list WHERE groupid = ' . $groupid;


	$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
	$data = mysql_fetch_assoc($result);
	$total_msg = $data['total'];
	$new_messages = $data['total'];
	/* Get the group owner */
	$owner = $data['owner'];

	/* Fetch read_msg for this user */
	$selectquery = 'SELECT read_msg AS total_read FROM groups_members WHERE groupid = ' . $groupid . ' AND userid = ' . $_SESSION['login']['id'];
	$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
	$data = mysql_fetch_assoc($result);

	$query = 'UPDATE groups_members SET read_msg = ' . $total_msg  . ' WHERE userid = ' . $_SESSION['login']['id'] . ' AND groupid = ' . $groupid;
	mysql_query($query) or die(report_sql_error($query));

	$new_messages = $new_messages - $data['total_read'];

	/* Update the read_msg */
	$query = 'SELECT login.username, groups_scribble.userid, groups_scribble.timestamp, groups_scribble.text, groups_scribble.id, userinfo.image, userinfo.birthday FROM login, groups_scribble, userinfo WHERE login.id = groups_scribble.userid AND groups_scribble.groupid = ' . $groupid . ' AND userinfo.userid = groups_scribble.userid AND groups_scribble.deleted = 0 ORDER BY groups_scribble.id DESC LIMIT ' . (($options['page'] - 1) * 100) . ', 100';

//Lef hackar via råd från heggan. Maskroskisseporr ska inte synas för hennes söta ögon.

if($_SESSION['login']['id'] == '148153') //för att vara säker på att inte döda alla sessioner.148153
{
	/* Fetch the total messages in group */	
	$query = 'SELECT login.username, groups_scribble.userid, groups_scribble.timestamp, groups_scribble.text, groups_scribble.id, userinfo.image, userinfo.birthday FROM login, groups_scribble, userinfo WHERE groups_scribble.text NOT LIKE "%maskrosp0rn%" AND login.id = groups_scribble.userid AND groups_scribble.groupid = ' . $groupid . ' AND userinfo.userid = groups_scribble.userid AND groups_scribble.deleted = 0 ORDER BY groups_scribble.id DESC LIMIT ' . (($options['page'] - 1) * 100) . ', 100';
}

//Död åt lef

	$result = mysql_query($query) or die(report_sql_error($query));
	/* $count_read - Hur många msg har gruppen 	*/

	$count_read = 0;
 	while ($data = mysql_fetch_assoc($result))
 	{
 			for($i = 0; $i < count($surveys); $i++)
 			{
 				if($surveys[$i]['start_time'] >= $data['timestamp'])
 				{
 					echo survey_draw_frame($surveys[$i]);
					unset($surveys[$i]);
 				}
 			}
 			$div_code = ($count_read < $new_messages ? 'orange' : 'blue');
			if (preg_match("/(".$_SESSION['login']['username']."|Magic word: igotgodmodeigotgubbmode)/i", $data['text'])) 
			{
					$div_code = 'orange_deluxe';
			}
	
			echo rounded_corners_top(array('color' => $div_code));

      echo '<table class="body" style="width: 95%;"><tr><td style="vertical-align: top; width: 75px;">';
      if($data['image'] == 1 || $data['image'] == 2)
      {
        echo ui_avatar($data['userid']);
      }
      else
      {
        echo '<img src="http://images.hamsterpaj.net/user_no_image.png" alt="Ingen visninsbild"/>';
      }
      echo '</td><td style="vertical-align: top;">';
      echo fix_time($data['timestamp']) . ' <a href="javascript:void(0);" onclick="javascript:document.postform.group_message.value=document.postform.group_message.value + \''.$data['username'].': \';document.postform.group_message.focus();">[^]</a><br/>'; 
			echo '<a href="' . $hp_url . '/traffa/profile.php?id=' . $data['userid'] . '">';
      echo '<b>' . (($data['userid'] == 43273) ? '<span style="color: #FF60B6">GheyAtrapp</span>' : $data['username'])  . '</b></a> ';

      if ($owner == $_SESSION['login']['id'])
      {
    		echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=remove_post&amp;groupid=' . $groupid . '&amp;postid=' . $data['id'] . '">[Ta bort]</a>';
    	}
      echo birthdaycake($data['birthday']) . ' ';
      echo '<br/>';
      echo setSmilies(discussion_forum_parse_output($data['text']));
      echo '</td></tr></table>';

	echo rounded_corners_bottom(array('color' => $div_code));
			$count_read++;
 	}
}

function group_set_style($subject){
	$pattern = '/\[code\](.+)\[\/code\]/s';
	$replacement = '<pre style="overflow:scroll; width: 100%; border: 1px solid #333">$1</pre>';
	$return = preg_replace($pattern, $replacement, $subject);
	return $return;
}

function group_draw_add_form()
{
	
	$drop_title = '<span style="cursor: pointer">Skapa ny grupp</span>';
	$drop_expanded = NULL;

	$drop_data = '<form action="' . $_SERVER['PHP_SELF'] . '?action=create_group" method="post">';
	$drop_data .= 'Gruppnamn:<br />';
	$drop_data .= '<input type="text" name="group_name" class="textbox" size="35"/><br />';
	$drop_data .= 'Presentation:<br />';
	$drop_data .= '<textarea name="description" class="textbox" style="width: 615px; height: 250px;"></textarea><br />';
	$drop_data .= '<input type="checkbox" name="take_members" class="textbox" />';
	$drop_data .= ' Tar ej emot ansökningar<br /><br />';
	$drop_data .= '<input type="submit" value="Skapa grupp" name="submit_new" class="button" /><br />';
	$drop_data .= '</form>';
	echo rounded_corners_top();
	echo ui_dropbox($drop_title, $drop_data, $drop_style, $drop_expanded);
	echo rounded_corners_bottom();
}

function group_draw_post_form($groupid)
{
//	echo live_chat_render(array('type' => 'group', 'reference_id' => $groupid));
	
//	echo '<br style="clear: both;" />' . "\n";
	
	$query = 'SELECT disabled, group_points FROM groups_list WHERE groupid = ' . $groupid;
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$data = mysql_fetch_assoc($result);
	echo rounded_corners_top(array('color' => 'blue'));
	if ($data['disabled'] == 'Y')
	{
		echo 'Innehållet i denna grupp har granskats av en administratör <br />';
		echo 'Den har ansets bryta mot svensk lag och har därför stängts ner för att förhindra vidare sprindning av budskapet.<br />';
		echo 'Inga nya inlägg kan därför skrivas i denna grupp.<br />';
	}
	else 
	{
		if ($data['group_points'] > GROUP_SHOW_WARNING)
		{
			echo 'Vårt automatiska kontrollsystem har upptäckt att denna grupp innehåller saker som kan bryta mot svensk lag.<br/>';
			echo 'Om detta fortsätter kommer den snart att granskas av en administratör för en manuell kontroll av innehållet.<br/>';
		}
		
		// Post form for writing in the group
		
		echo '<form action="' . $_SERVER['php_self'] . '?action=new_post&amp;groupid=' . $groupid . '" method="post" name="postform">';
		echo '<h2 style="margin-top: 0;">Meddelande:</h2>';
		echo '<textarea name="group_message" class="textbox" style="width: 99%; height: 110px;"></textarea><br />';
		echo '<input type="submit" value="Skicka" name="submit_message" class="button_60"/><br />';
		echo '</form>';
	}
	echo rounded_corners_bottom(array('color' => 'blue'));
}

function group_check_auth($userid, $groupid, $approved)
{
		
	foreach($_SESSION['groups_members'] AS $key => $value)
	{
		if ($value == $groupid)
		{
			$data['valid'] = 1;
			break;
		}
		else
		{
			$data['valid'] = 0;
		}
	}

	if ($data['valid'] == 0) 
	{
		$query = 'SELECT COUNT(*) AS valid FROM groups_members WHERE groupid = ' . $groupid . ' AND userid = ' . $userid . ' AND approved = ' . $approved;
		$result = mysql_query($query) or die(report_sql_error($query));
		$data = mysql_fetch_assoc($result);
	}	

	if ($data['valid'] == 1 || is_privilegied('groups_superadmin') || $_SESSION['login']['id'] == 57100) //Ace har åtkomst
	{
		return 1;
	}
	else
	{
		return 0;
	}	
}

//COmment


function group_check_settings($groupid)
{
  $query = 'SELECT not_member_read_messages AS messages, not_member_read_presentation AS presentation ';
	$query.= 'FROM groups_list WHERE groupid = ' . $groupid;
  $result = mysql_query($query) or die(report_sql_error($query));
  $data = mysql_fetch_assoc($result);
	return $data;
}


function group_check_admin_auth($groupid)
{
	$selectquery = 'SELECT owner FROM groups_list WHERE groupid = ' . $groupid;
	$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
	$data = mysql_fetch_assoc($result);

	if ($data['owner'] == $_SESSION['login']['id'])
	{
		return 1;
	}
	else
	{
		return 0;
	}	
}

function group_invite_member_form($groupid)
{
	$return = '<h2>Bjud in en medlem</h2>';
	$return .= '<form method="post" action="' . $_SERVER['php_self'] . '?action=group_invite&amp;groupid=' . $groupid . '">';
	$return .= '<input type="text" name="inviteuser" class="textbox" />';
	$return .= '<input type="submit" name="sumbit_invite" class="button" value="Bjud in" />';
	$return .= '</form>';
	
	return $return;
}

function group_draw_press_edit_form($groupid) 
{
	$query = 'SELECT presentation, take_new_members, not_member_read_presentation, not_member_read_messages ';
	$query.= 'FROM groups_list WHERE groupid = ' . $groupid;
	$result = mysql_query($query) or die(report_sql_error($query));
	$data = mysql_fetch_assoc($result);

	$return =  '<h2>Ändra presentation för gruppen</h2>';
	$return .= '<form method="post" action="' . $_SERVER['php_self'] . '?action=save_press&amp;groupid=' . $groupid . '">';
	$return .= '<textarea name="press_text" style="width: 615px; height: 250px;">' . stripslashes($data['presentation']) . '</textarea><br />';
	if ($data['take_new_members'] == 1) 
	{
		$return .= 'Tar emot ansökningar: <input type="checkbox" name="take_new" checked="checked" />';
	}
	else
	{
		$return .= 'Tar emot ansökningar: <input type="checkbox" name="take_new" />';	
	}
	$return .= '<br /><br /><b>Personer som inte är medlemmar i gruppen får:</b><br />';
	$return .= 'Läsa presentationen: <input type="checkbox" name="not_member_read_presentation" value="Y"';
	$return .= ($data['not_member_read_presentation'] == 'Y' ? ' checked="checked"' : null);
	$return .= ' /><br />';
	$return .= 'Läsa gruppens inlägg: <input type="checkbox" name="not_member_read_messages" value="Y"';
	$return .= ($data['not_member_read_messages'] == 'Y' ? ' checked="checked"' : null);
	$return .= ' /> (<i>Du måste kryssa i lådan ovan för att denna ska fungera</i>)<br />';
	$return .= '<br /><input type="submit" name="sumbit_press" class="button" value="Spara" />';
	$return .= '</form>';
	
	return $return;

}
function group_press_save($text, $groupid)
{
	$query = 'UPDATE groups_list SET presentation = "' . $text . '" WHERE groupid =' . $groupid;
	mysql_query($query) or die(report_sql_error($query));
}

function group_invite_member($groupid, $username)
{
	global $hp_url;

	$query = 'SELECT id FROM login WHERE username = "' . $username . '" LIMIT 1';
	$result = mysql_query($query) or die(report_sql_error($query));
	if (mysql_num_rows($result) == 0)
	{
		jscript_alert('Personen du ville bjuda in finns inte');
		jscript_location($_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $groupid);
		exit;
	}
	$data = mysql_fetch_assoc($result);
	$userid = $data['id'];

	$selectquery = 'SELECT COUNT(*) AS added FROM groups_members WHERE userid = ' . $userid . ' AND groupid = ' . $groupid;
	$result = mysql_query($selectquery) or die(report_sql_error($query));
	$data = mysql_fetch_assoc($result);

	if ($data['added'] == 0)
	{

		$query = 'SELECT name, owner FROM groups_list WHERE groupid = ' . $groupid;
		$result = mysql_query($query) or die(report_sql_error($query));
		$data = mysql_fetch_assoc($result);
		$groupname = $data['name'];
		$owner = $data['owner'];

		$url = $hp_url . 'traffa/groups.php?action=invited_member&amp;groupid=' . $groupid . '&userid=' . $userid;
		$title = 'Inbjudan att gå med i gruppen: ' . $groupname;
		$message = 'Du har blivit inbjuden till gruppen: ' . $groupname . '<br />';
		$message .= 'Om du vill gå med i min grupp trycker du bara på länken här nedanför<br />';
		$message .= '<a href="' . $url . '">[Bli medlem i gruppen]</a><br />';
	
		$query = 'INSERT INTO groups_members (groupid, userid, approved) VALUES (' . $groupid . ',' . $userid . ', 3)';
		mysql_query($query) or die(report_sql_error($query));

		guestbook_insert(array(
			'sender' => $owner,
			'recipient' => $userid,
			'is_private' => 1,
			'message' => mysql_real_escape_string($message)
		));
	}
	else
	{
		jscript_alert("Du kan inte bjuda in denna person");
	}

}

function group_draw_menu($name = 0, $groupid = NULL, $menu = 0, $remove = 0)
{
	if ($name)	
	{
	echo '<a href="/traffa/groupnotices.php">&laquo; Till gruppnotiser</a><br />';
		$query = 'SELECT name, message_count, take_new_members FROM groups_list WHERE groupid = ' . $groupid;
		$result = mysql_query($query) or die(report_sql_error($query));
		$data = mysql_fetch_assoc($result);
		echo '<h2>' . $data['name'] . ' - ' . (($groupid == 246) ? '<span onclick="alert(' . "'" . 'Nu var du allt haxx!\nInlägg: ' . $data['message_count'] . "'" . ')">L33t h4xx0r</span>' : $data['message_count']) . ' inlägg</h2>';
		if ($data['take_new_members'] == 1 && !array_key_exists($groupid, $_SESSION['groups_members']))
		{
			echo '<br /><a href="' . $_SERVER['PHP_SELF'] . '?action=apply&amp;groupid=' . $groupid . '">Gå med i denna grupp >></a>';
		}
	}
	if($remove)
	{
			echo '<form><input type="button" class="button" value="Gå ur gruppen" onclick="if(confirm(\'Vill du verkligen gå ur gruppen?\')){window.location =  \'' . $_SERVER['PHP_SELF'] . '?action=remove_me&amp;groupid=' . $groupid . '\';}" /></form><br />';
	}
	if ($menu)
	{		
		echo '<br /><br /><a href="' . $_SERVER['PHP_SELF'] . '"><< Tillbaka till dina grupper</a>';
	}
	if(isset($_GET['page']) && is_numeric($_GET['page']))
	{
		$page = intval($_GET['page']);
		if($page > 1)
		{
			echo ' <a href="' . $_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $groupid . '&page=' . ($page - 1) . '">&laquo; Föregående</a> |';
		}
		
		if($page > 0)
		{
			echo ' ' . $page . ' | <a href="' . $_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $groupid . '&page=' . ($page + 1) . '">Nästa &raquo;</a>';
		}
	}
	else
	{
		echo ' <a href="' . $_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $groupid . '&page=2">Nästa &raquo;</a>';
	}	
}

function group_start_list()
{
	echo rounded_corners_top(array('color' => 'blue'));
	echo '<h2 style="margin-top: 0;">Grupper jag är med i</h2>';
	group_list_my_groups($_SESSION['login']['id']);
	echo rounded_corners_bottom(array('color' => 'blue'));

	echo rounded_corners_top(array('color' => 'blue'));	
	echo '<h2 style="margin-top: 0;">Grupper jag ansökt till</h2>';
	group_list_groups_i_applied_to($_SESSION['login']['id']);
	echo '<br />';
	echo rounded_corners_bottom(array('color' => 'blue'));
	
	group_draw_add_form();

	
	echo rounded_corners_top();
	echo '<a href="' . $_SERVER['php_self'] . '?action=list_groups"><h2 style="margin: 0px;">Sök grupper &raquo;</h2></a>';
	echo rounded_corners_bottom();

	if (is_privilegied('groups_superadmin'))
	{
		group_list_admincheck();	
	}

}

function group_draw_index($groupid)
{
	$query = 'SELECT presentation FROM groups_list WHERE groupid = ' . $groupid;
	$result = mysql_query($query) or die(report_sql_error($query));
  $data = mysql_fetch_assoc($result);

	$drop_title = 'Presentation';
	// Jadu, jag antar att du undrar varför jag har TVÅ html_entity_decode() på rad... men det krävs tydligen för att det ska bli rätt :P
	$drop_data .=  html_entity_decode(html_entity_decode(nl2br($data['presentation'])));
	$drop_data .= '<br />';

	$drop_expanded = NULL;
  $drop_style=array('style'=>'margin:0px');

	echo rounded_corners_top(array('color' => 'blue'));
	echo ui_dropbox($drop_title, $drop_data, $drop_style, $drop_expanded);
	echo rounded_corners_bottom(array('color' => 'blue'));
}

function group_list_admin_functions($groupid)
{
	$drop_expanded = NULL;
	$drop_data = group_list_pending_members($groupid, $drop_expanded);
	$drop_data .= group_invite_member_form($groupid);
	$drop_data .= group_draw_press_edit_form($groupid);
	
	$drop_title = 'Administera grupp';
	$drop_style=array('class'=>'grey_faded_div','style'=>'margin-top:20px');

	echo ui_dropbox($drop_title, $drop_data, $drop_style, $drop_expanded);

	echo '<br />';
}

function group_change_status($groupid, $status, $read_presentation, $read_messages)
{
	if ($read_presentation == '') { $read_presentation = 'N'; }
	if ($read_messages == '') { $read_messages = 'N'; }
	$query = 'UPDATE groups_list SET take_new_members = ' . $status . ', ';
	$query.= 'not_member_read_presentation = "' . $read_presentation . '", ';
	$query.= 'not_member_read_messages = "' . $read_messages . '" ';
	$query.= 'WHERE groupid = ' . $groupid;
	mysql_query($query) or die(report_sql_error($query));
}

function group_remove_group($groupid)
{
	echo '<FORM>';
	echo '<input type="button" value="Ta bort gruppen" class="button" onclick="if(confirm(\'Är du säker på att du vill ta bort gruppen och ALLA medlemmar?\')){window.location = \'' . $_SERVER['PHP_SELF'] . '?action=remove_group&amp;groupid=' . $groupid . '\';}" /><br /><br />';
	echo '</FORM>';

}

function group_remove_post($groupid, $postid)
{
	$query = 'UPDATE groups_scribble SET deleted = 1 WHERE groupid = ' . $groupid . ' AND id = ' . $postid . ' LIMIT 1';
	mysql_query($query) or die(report_sql_error($query));	
}

function group_preform_group_remove($groupid)
{
	$query = 'DELETE FROM groups_members WHERE groupid = ' . $groupid;
	mysql_query($query) or die(report_sql_error($query));	
	$query = 'DELETE FROM groups_list WHERE groupid = ' . $groupid;
	mysql_query($query) or die(report_sql_error($query));
}

function group_check_score($groupid)
{
	$query = 'SELECT group_points FROM groups_list WHERE groupid = '  . $groupid;
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$data = mysql_fetch_assoc($data);
	return $data['group_points'];
}

function group_check_message_score($user_text)
{
$user_text = strtolower($user_text);
$words[5] = array('viking', 'ultima thule', 'svensk', 'sverige', 'flagga', 'fana', 'hakkors', '1939', '1945', 'rasist', 'r*sist', 'afa');
$words[10] = array('svenskfientlig', 'svastika', 'swastika', 'nsf', 'arisk', 'blatte', 'bl*tte', 'hell', 'neger', 'negrer', 'salem', 'midgård');
$words[15] = array('arbeit macht frei', 'sieg heil', 'pluton svea', 'hellseger', 'fosterland', 'info14', 'motståndsrörelsen', 'nationalsocialist');
$post_score = 0;

foreach(array_keys($words) AS $score)
{
	foreach($words[$score] AS $word)
	{

		if(substr_count($user_text, ' ' . $word) > 0)
		{
			$post_score += $score;
		}
	}
}
	return $post_score;
}


function group_list_admincheck()
{
/*
	Hämta gruppnamn på grupper där poängen överskrider 75 och skriv ut
*/
	if (is_privilegied('groups_superadmin'))
	{
		echo '<br /><br /><h2>Misstänkt olämpliga grupper</h2>';
		$query = 'SELECT * FROM groups_list WHERE group_points >= ' . GROUP_CHECK_FORCE . ' AND disabled = "N"';
		$result = mysql_query($query) or die(report_sql_error($query, __LINE__, __FILE__));
		while ($data = mysql_fetch_assoc($result))
		{
			echo '<a href="' . $_SERVER['PHP_SELF']  . '?action=admin_check&amp;groupid=' . rand(1000,9999) . (($data['groupid'] * 43) + 32)  . rand(1000,9999) . '">Grupp ' . substr(md5($data['name']), 0, 4) . '</a><br />';
		}
	}
}

function parse_id($groupid)
{
	$id = $groupid;
  $id = substr($id, 4, (strlen($id) - 8));
  $id = (($id - 32) / 43);
	
	return $id;
}

function group_admin_check($groupid)
{
	$id = parse_id($groupid);
	$query = 'SELECT * FROM groups_scribble WHERE groupid = ' . $id;
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while ($data = mysql_fetch_assoc($result))
	{
		echo '<div class="blue_faded_div">' . $data['text'] . '</div><br />';
	}

	echo '<form method="post">';
	echo '<input type="button" value="Godkänn" style="float: left; clear: left;" onclick="if(confirm(\'Är du säker på att du vill godkänna gruppen?\')){window.location = \'' . $_SERVER['PHP_SELF'] . '?action=check_ok&groupid=' . $groupid . '\';}">';
	echo '<input type="button" value="Stäng ner gruppen" style="float: right;" onclick="if(confirm(\'Är du säker på att du vill stänga ner gruppen?\')){window.location = \'' . $_SERVER['PHP_SELF'] . '?action=disable_group&groupid=' . $groupid . '\';}"><br /><br />';
	echo '</form>';
}

function group_check_ok($groupid)
{
	$id = parse_id($groupid);
	$query = 'UPDATE groups_list SET group_points = group_points - ' . GROUP_CHECK_OK_MINUS . ' WHERE groupid = ' . $id;
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	jscript_location($_SERVER['PHP_SELF']);
}

?>

<?php
if (isset($_GET['groupid']) && !is_numeric($_GET['groupid']) && $_GET['action'])
{
	die('Så trixar vi inte med värden(values) sådär');
}
if (isset($_GET['userid']) && !is_numeric($_GET['userid']))
{
	die('Så trixar vi inte med värden(values) sådär');
}

switch($_GET['action'])
  {
    case 'apply':
			group_submit_to_group($_GET['groupid'], $_SESSION['login']['id']);
			jscript_alert('Du har nu ansökt till gruppen');
			jscript_location($_SERVER['PHP_SELF']);
    	break;
    case 'validate':
			if (group_check_admin_auth($_GET['groupid']))
			{
				group_add_to_group($_GET['groupid'], $_GET['userid']);
			}
			jscript_location($_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $_GET['groupid']);
    	break;
    case 'remove_user':
      if (group_check_admin_auth($_GET['groupid']))
			{
				group_del_from_group($_GET['groupid'], $_GET['userid']);
				jscript_location($_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $_GET['groupid']);
				die();
			}
			jscript_location($_SERVER['PHP_SELF']);
			break;
		case 'remove_me':
      $auth = group_check_auth($_SESSION['login']['id'], $_GET['groupid'], 1);
      $auth_not_approved = group_check_auth($_SESSION['login']['id'], $_GET['groupid'], 0);
      if ($auth || $auth_not_approved)
      {
        group_del_from_group($_GET['groupid'], $_SESSION['login']['id'], 1);
				jscript_alert('Du är nu borttagen från gruppen');
      }
			jscript_location($_SERVER['PHP_SELF']);
      break;
		case 'disable_group':
				if (is_privilegied('groups_superadmin'))
				{
					group_close_group($_GET['groupid']);
				}
				jscript_location($_SERVER['PHP_SELF']);				
				break;
    case 'goto':
			$auth = group_check_auth($_SESSION['login']['id'], $_GET['groupid'], 1);
    	$adminauth = group_check_admin_auth($_GET['groupid']);
			$settings = group_check_settings($_GET['groupid']);
			if ($auth || $settings['presentation'] == 'Y')
			{
				group_draw_menu(1, $_GET['groupid']);
				group_draw_index($_GET['groupid']);
				if ($adminauth)
				{
					group_list_members($_GET['groupid'], 1);
					group_list_admin_functions($_GET['groupid']);
				}
				else
				{
					group_list_members($_GET['groupid']);
				}
				if ($auth)
				{
					group_draw_post_form($_GET['groupid']);
				}
				if ($auth || $settings['messages'] == 'Y')
				{
					$page = 1;
					if(isset($_GET['page']) && is_numeric($_GET['page']))
					{
						$page = intval($_GET['page']);
						if($page < 1 || $page > 999)
						{
							$page = 1;
						}
					}
					group_list_messages(array('group_id' => $_GET['groupid'], 'page' => $page));
				}
				if ($adminauth || is_privilegied('groups_superadmin')) 
				{
					group_remove_group($_GET['groupid']);
					group_draw_menu(0, $_GET['groupid'], 1, 1);
				}
				else
				{
					group_draw_menu(0, $_GET['groupid'], 1, ($auth ? 1 : 0));
				}
				break;
			}
			jscript_alert('Du är inte med i denna grupp, och kan därför inte spana in gruppen.');
			jscript_go_back();
			break;
		case 'list_groups':
			group_list_groups($_SESSION['login']['id']);
			group_draw_menu(0, NULL, 1);
			break;
		case 'new_post':
			$auth = group_check_auth($_SESSION['login']['id'], $_GET['groupid'], 1);
      if ($auth)
      {
				$_POST['group_message'] = trim($_POST['group_message']);
				if (strlen($_POST['group_message']) > 0)
				{
					group_send_new_message($_GET['groupid'], $_SESSION['login']['id'], $_POST['group_message']);
				}
				else
				{
					jscript_alert('Nånting måste du skriva!');
				}			
			}
			jscript_location($_SERVER['php_self'] . '?action=goto&groupid=' . $_GET['groupid']);
			//die();			
			break;
		case 'create_group':
			$_POST['take_members'] = isset($_POST['take_members']) ? 0 : 1;
  		group_create_new(htmlspecialchars($_POST['group_name']), $_SESSION['login']['id'], $_POST['take_members'], htmlspecialchars($_POST['description']));			
			jscript_alert('Din grupp är nu skapad');
			jscript_location($_SERVER['PHP_SELF']);
			break;
		case 'group_invite':
			$auth = group_check_admin_auth($_GET['groupid']);
      if ($auth)
			{
				group_invite_member($_GET['groupid'], htmlspecialchars($_POST['inviteuser']));
				jscript_location($_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $_GET['groupid']);
			}
    case 'invited_member':
			$auth = group_check_auth($_SESSION['login']['id'], $_GET['groupid'], 3);
      if ($auth)
      {
				group_add_to_group($_GET['groupid'], $_SESSION['login']['id'], 1);
				jscript_alert('Du är nu medlem i gruppen');
				jscript_location($_SERVER['php_self'] . '?action=goto&groupid=' . $_GET['groupid']);			
			}
    	break;
		case 'save_press':
			$auth = group_check_admin_auth($_GET['groupid']);
      if ($auth)
			{
				group_press_save(htmlspecialchars($_POST['press_text']), $_GET['groupid']);
				$_POST['take_new'] = isset($_POST['take_new']) ? 1 : 0;
				group_change_status($_GET['groupid'], $_POST['take_new'], $_POST['not_member_read_presentation'], $_POST['not_member_read_messages']);

				jscript_location($_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $_GET['groupid']);
			}		
			jscript_location($_SERVER['PHP_SELF']);
			break;
		case 'search_group';
			group_list_groups($_SESSION['login']['id'], htmlspecialchars($_POST['search_text']));
			break;
		case 'search_group_quick';
			group_list_groups($_SESSION['login']['id'], htmlspecialchars($_GET['search_text']));
			break;
		case 'remove_group':
			$auth = group_check_admin_auth($_GET['groupid']);
      if ($auth || is_privilegied('groups_superadmin'))
      {
				group_preform_group_remove($_GET['groupid']);
				jscript_alert('Gruppen borttagen');
			}
			jscript_location($_SERVER['PHP_SELF']);
			break;
		case 'save_data':
			foreach($_POST as $key => $value)
			{
				if (($value == 'Y' || $value == 'N') && is_numeric($key))
				{
					$query = 'UPDATE groups_members SET notices = "' . $value . '" WHERE userid = ' . $_SESSION['login']['id'] . ' AND groupid = "' . $key . '"';
					mysql_query($query) or die(report_sql_error($query));
				}
			} 
			jscript_location($_SERVER['PHP_SELF']);
			break;
		case 'admin_check':
			if (is_privilegied('groups_superadmin'))
			{
				group_admin_check($_GET['groupid']);
			}
			break;
		case 'check_ok':
			if (is_privilegied('groups_superadmin'))
			{
				group_check_ok($_GET['groupid']);
			}
			break;
		case 'remove_post';
			$auth = group_check_admin_auth($_GET['groupid']);
      if ($auth || is_privilegied('groups_superadmin'))
      {
				group_remove_post($_GET['groupid'], $_GET['postid']);
				jscript_alert('Inlägget borttaget');
			}
			jscript_location($_SERVER['PHP_SELF'] . '?action=goto&groupid=' . $_GET['groupid']);
			break;
		default:
			group_start_list();
  }

	ui_bottom();
 ob_end_flush(); 
?>

