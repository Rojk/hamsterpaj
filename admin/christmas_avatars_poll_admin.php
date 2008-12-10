<?php

	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/christmas_avatars_polls.lib.php');
		
	if(!is_privilegied('christmas_avatar_poll'))
	{
		header('Location: /index.php');
		exit;
	}
	
	//  UI-options
	$ui_options['title'] = 'Julavataromröstning - Administration - Hamsterpaj.net';
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	$ui_options['stylesheets'][] = 'rounded_corners.css';
	$ui_options['javascripts'][] = 'christmas_avatar_admin.js';
	ui_top($ui_options);
	$action = isset($_GET['action']) ? $_GET['action'] : 'home';

	echo christmas_avatar_admin_menu_list( $action == 'remove_poll' ? 'edit' : $action);
	switch($action)
	{
		case 'home':
		default:
			//show the current polls
			echo christmas_avatar_current_polls_list(array('admin-links'=>true));
			break;
		
		case 'add':
			echo christmas_avatar_admin_add();
			break;
		case 'edit':
			if(isset($_GET['poll_id']))
			{
				if(is_numeric($_GET['poll_id']))
				{
					echo christmas_avatar_edit_poll(array('poll_id'=>intval($_GET['poll_id'])));
				}
			}
			else
			{
				echo christmas_avatar_admin_edit_list();
			}
			break;
		case 'remove_poll':
			if(isset($_GET['poll_id']) && is_numeric($_GET['poll_id']))
			{
				echo christmas_avatar_admin_remove_poll( intval($_GET['poll_id']) );
			}
			break;
	}

	
	echo rounded_corners_tabs_bottom(array('return'=>true));
	ui_bottom();
?>