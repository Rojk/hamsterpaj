<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/groups.lib.php');
	$ui_options['menu_path'] = array('grupper');

	$ui_options['stylesheets'][] = 'groups.css';

	$request = groups_get_action($_SERVER['REQUEST_URI']);
	switch($request['action'])
	{
		case 'view_group':
			$ui_options['title'] = $request['group']['name'] . ' - Grupper på Hamsterpaj.net';
			$content .= '<h1>' . $request['group']['name'] . '</h1>' . "\n";
				$content .= rounded_corners_top();
					$content .= '<form action="' . $_SERVER['php_self'] . '?action=new_post&amp;groupid=' . $groupid . '" method="post" name="postform">' . "\n";
					$content .= '<label>Meddelande:</label>' . "\n";
					$content .= '<textarea name="group_message" class="textbox" style="width: 99%; height: 110px;"></textarea><br />' . "\n";
					$content .= '<input type="submit" value="Skicka" name="submit_message" class="button_60"/><br />' . "\n";
					$content .= '</form>' . "\n";
				$content .= rounded_corners_bottom();
				
				unset($options);
				$options['group_id'] = 1;
				
				$entries = groups_entries_fetch($options);
				preint_r($entries);
				
				$content .= groups_entries_render($entries);
				
									
			$content .= ($request['group']['founder'] == $_SESSION['login']['id']) ? '<h2>Du är administratör för den här gruppen.</h2>' . "\n" : '';
			$content .= '<p>' . nl2br($request['group']['description']) . '</p>' . "\n";
			break;
		case 'create_group':
			$group = groups_create($request['group_info']);
			
			/*$sql .= 'SELECT handle FROM groups WHERE founder = ' . $_SESSION['login']['id'] . ' ORDER BY created_timestamp ASC LIMIT 1';
			$data = mysql_fetch_assoc(mysql_query($sql));*/
			header('Location: /grupper/');
			preint_r($group);
			break;
		case 'new_group':
			$content .= groups_create_form();
			break;
		
		default:

			$content .= '<h1>Myspys i Hamsterpajs gruppsystem</h1>' . "\n";

			$content .= rounded_corners_top(array('color' => 'orange'));
				$content .= '<h2>Sök bland Hamsterpajs grupper</h2>' . "\n";
			$content .= rounded_corners_bottom(array('color' => 'orange'));
/*
			$content .= rounded_corners_top(array('color' => 'blue'), true);
				$content .= '<h2>Hamsterpajs aktivaste grupper</h2>' . "\n";
				$groups = groups_fetch(array('limit' => 6));
				$content .= groups_list($groups);
			$content .= rounded_corners_bottom(array('color' => 'blue'), true);

			$content .= rounded_corners_top(array('color' => 'blue'), true);
				$content .= '<h2>Hamsterpajs största grupper</h2>' . "\n";
				$groups = groups_fetch(array('limit' => 6, 'order-by' => 'member_count', 'order-direction' => 'DESC'));
				$content .= groups_list($groups);
			$content .= rounded_corners_bottom(array('color' => 'blue'), true);
*/
			$content .= rounded_corners_top();
				$content .= '<h2>Mina grupper</h2>' . "\n";
				$groups = groups_fetch(array('founder' => $_SESSION['login']['id'], 'order-by' => 'id', 'order-direction' => 'DESC'));
				$content .= groups_list($groups);
			$content .= rounded_corners_bottom();
			
			$content .= rounded_corners_top();
				$content .= '<h2>Hamsterpajs senaste skapade grupper</h2>' . "\n";
				$groups = groups_fetch(array('limit' => 6, 'order-by' => 'id', 'order-direction' => 'DESC'));
				$content .= groups_list($groups);
			$content .= rounded_corners_bottom();
			$content .= '<a href="skapa_grupp">Skapa ny grupp</a>' . "\n";
			break;
	}
			$content .= '<a href="/grupper/">Tillbaka</a>';
	
	ui_top($ui_options);
	echo $content;
	preint_r($request);
	ui_bottom();
?>