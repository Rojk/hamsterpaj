<?php
	try
	{
		require('../include/core/common.php');
		require_once(PATHS_INCLUDE . 'libraries/groups_active.lib.php');
	
		$action = $_POST['action'];
		switch($action)
		{
			case 'new_post':
				$auth = group_check_auth($_SESSION['login']['id'], $_POST['groupid'], 1);
		    	if($auth)
		    	{
					$_POST['group_message'] = trim($_POST['group_message']);
					if (strlen($_POST['group_message']) > 0)
					{
						group_send_new_message($_POST['groupid'], $_SESSION['login']['id'], $_POST['group_message']);
						$div_code = 'orange';
						if (preg_match("/(".$_SESSION['login']['username']."|Magic word: igotgodmodeigotgubbmode)/i", $data['text'])) 
						{
								$div_code = 'orange_deluxe';
						}
						echo rounded_corners_top(array('color' => $div_code));
						echo '<table class="body" style="width: 95%;"><tr><td style="vertical-align: top; width: 75px;">';
						echo ui_avatar($_SESSION['login']['id']);
						echo '</td><td style="vertical-align: top;">';
						echo fix_time(time()) . ' <a href="javascript:void(0);" onclick="javascript:document.postform.group_message.value=document.postform.group_message.value + \''.$data['username'].': \';document.postform.group_message.focus();">[^]</a><br/>'; 
						echo '<a href="/traffa/profile.php?id=' . $_SESSION['login']['id'] . '">';
						echo '<b>' . (($_SESSION['login']['id'] == 43273) ? '<span style="color: #FF60B6">GheyAtrapp</span>' : $_SESSION['login']['username'])  . '</b></a> ';
						echo ui_birthday_cake($_SESSION['userinfo']['birthday']) . ' ';
						echo '<br/>';
						echo setSmilies(discussion_forum_parse_output($_POST['group_message']));
						echo '</td></tr></table>';
						echo rounded_corners_bottom(array('color' => $div_code));
					
					}
					else
					{
						throw new Exception('Nånting måste du skriva! (Nånting, charmigt Heggan!)');
					}
				}
			break;
			
			case 'fetch_new_posts':
				$groupid = $_POST['groupid'];
				
				$selectquery = 'SELECT message_count AS total FROM groups_list WHERE groupid = ' . $groupid;
				$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
				$group_message_count = mysql_fetch_assoc($result);

				// Om antal meddelanden är större än jag läst:
				$selectquery = 'SELECT read_msg AS total_read FROM groups_members WHERE groupid = ' . $groupid . ' AND userid = ' . $_SESSION['login']['id'];
				$result = mysql_query($selectquery) or die(report_sql_error($selectquery));
				$user_message_count = mysql_fetch_assoc($result);

				//$groupid = $_POST['groupid'];
				if($group_message_count['total'] > $user_message_count['total_read'])
				{
					$count_new_posts = $group_message_count['total'] - $user_message_count['total_read'];
					// Set messages read
					$query = 'UPDATE groups_members SET read_msg = read_msg + ' . $count_new_posts . ' WHERE userid = ' . $_SESSION['login']['id'] . ' AND groupid = ' . $groupid;
					mysql_query($query) or die(report_sql_error($query));
					
					$query = 'SELECT login.username, groups_scribble.userid, groups_scribble.timestamp, groups_scribble.text, groups_scribble.id, userinfo.image, userinfo.birthday FROM login, groups_scribble, userinfo WHERE login.id = groups_scribble.userid AND groups_scribble.groupid = ' . $groupid . ' AND userinfo.userid = groups_scribble.userid AND groups_scribble.deleted = 0 ORDER BY groups_scribble.id DESC LIMIT ' . $count_new_posts . '';
					$result = mysql_query($query) or die(report_sql_error($query));
					
					
					while ($data = mysql_fetch_assoc($result))
					{
						$div_code = 'orange';
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
							echo '<img src="' . IMAGE_URL . 'user_no_image.png" alt="Ingen visninsbild"/>';
						}
						echo '</td><td style="vertical-align: top;">';
						echo fix_time($data['timestamp']) . ' <a href="javascript:void(0);" onclick="javascript:document.postform.group_message.value=document.postform.group_message.value + \''.$data['username'].': \';document.postform.group_message.focus();">[^]</a><br/>'; 
						echo '<a href="' . $hp_url . '/traffa/profile.php?id=' . $data['userid'] . '">';
						echo '<b>' . (($data['userid'] == 43273) ? '<span style="color: #FF60B6">GheyAtrapp</span>' : $data['username'])  . '</b></a> ';
						if ($owner == $_SESSION['login']['id'])
						{
							echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=remove_post&amp;groupid=' . $groupid . '&amp;postid=' . $data['id'] . '">[Ta bort]</a>';
						}
						echo ui_birthday_cake($data['birthday']) . ' ';
						echo '<br/>';
						echo setSmilies(discussion_forum_parse_output($data['text']));
						echo '</td></tr></table>';
						echo rounded_corners_bottom(array('color' => $div_code));
					}
				}
			
			break;
			
			default:
				throw new Exception('No input!');
			break;
		}
	}
	catch (Exception $error)
	{
		echo '<div class="form_notice_error">';
		echo $error->getMessage();
		echo '</div>';
	}
?>