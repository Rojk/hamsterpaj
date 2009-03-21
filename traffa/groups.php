<?php
	define('GROUP_CHECK_FORCE', 45);
	define('GROUP_CHECK_OK_MINUS', 50);
	define('GROUP_SHOW_WARNING', 30);
	
	ob_start();
	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'discussion_forum.lib.php');
	require_once(PATHS_LIBRARIES . 'groups_active.lib.php');

	$ui_options['menu_path'] = array('traeffa', 'grupper');
	$ui_options['admtoma_category'] = 'groups';
	$ui_options['javascripts'] = array('scripts.js');
	$ui_options['javascripts'][] = 'discussion_forum.js';	
	$ui_options['javascripts'][] = 'groups.js';	
	$ui_options['stylesheets'][] = 'forms.css';

	ui_top($ui_options);

	if(login_checklogin() != 1)
  {
    jscript_alert('Du måste vara inloggad för att komma åt denna sidan!');
    jscript_location('/');
    die();
  }


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

