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