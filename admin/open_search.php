<?php

	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'open_search.lib.php');
	
	if(!is_privilegied('open_search'))
	{
		ui_top();
		echo 'Inget för dig...';
		ui_bottom();
		exit;
	}
	else
	{
		if(count($_POST) > 0)
		{
			if(isset($_GET['action']))
			{
				switch($_GET['action'])
				{
					case 'add':
						echo open_search_add_box_execute($_POST, array('json_encode' => true));
						break;
					case 'edit':
						echo open_search_edit_box_execute($_POST, array('json_encode' => true));
						break;
				}
			}
			else
			{
				echo 'Ingen action!';
			}
		}
		else
		{
			$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
			$ui_options['stylesheets'][] = 'rounded_corners.css';
			$ui_options['stylesheets'][] = 'open_search.css';
			$ui_options['javascripts'][] = 'open_search_admin.js';
			$ui_options['menu_path'] = array('admin', 'open_search');
			$ui_options['title'] = 'Administration för söklådor (Open search)';
			$action = isset($_GET['action']) ? $_GET['action'] : 'home';
			
			if(!isset($_GET['ajax']))
			{
				ui_top($ui_options);		
				echo open_search_menu_list($action);
			}
			switch($action)
			{
				case 'home':
					echo '<h1>Välj något av alternativen högst upp.</h1>'."\n";
					echo open_search_stats_query_list_mini();
					echo open_search_stats_boxes_list();
					break;
				case 'add_search_box':
					echo '<h1>Lägg till söklåda</h1>';
					echo open_search_addbox_draw_input(array('action'=>'add', 'formname'=>'add', 'submit_value'=>'Skapa låda'));
					break;
				case 'help':
					if(!isset($_GET['ajax']))
						echo '<h1>Liten hjälpdel</h1>';
					echo open_search_help_list($_GET['what']);
					break;
				case 'edit_search_boxes':
					echo '<h1>Redigera söklådor</h1>';
					echo open_search_edit_list();
					break;
				case 'view_stats':
					echo '<h1>Se statistik</h1>';
					echo open_search_stats_query_list(array('no_limit'=>true));
					echo open_search_stats_boxes_list();
					break;
			}

			if(!isset($_GET['ajax']))
			{
				echo rounded_corners_tabs_bottom();
				ui_bottom();
			}
		}
	}

?>