<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	
	$action = (( isset($_GET['action']) && in_array($_GET['action'], array('insert', 'delete', 'compare', 'copy')) ) ? $_GET['action'] : 'default-');

	$ui_options['stylesheets'][] = 'user_profile.css';
	$ui_options['stylesheets'][] = 'user_lists.css.php';
	
	if($action == 'insert')
	{
		$ui_options['javascripts'][] = 'user_lists.js';
	}
	
	ui_top($ui_options);
	
	$show_user = (( isset($_GET['user']) && is_numeric($_GET['user']) ) ? intval($_GET['user']) : false);
	if(!$show_user && login_checklogin()){ $show_user = $_SESSION['login']['id']; }
	
	if(!$show_user)
	{
		echo '<h1>Ogiltig användare</h1>' . "\n";
		echo 'Haxxorz!';
	}
	
	$profile_params['user_id'] = $show_user;
	echo profile_head($profile_params);
	
	
	
	$username = 'John Doe';



	switch($action)
	{
		case 'insert':
			if(login_checklogin())
			{
				if(isset($_POST['save']))
				{
					$items = array();
					
					$current_item = 0;
					while(isset($_POST['item_text_' . $current_item]) && isset($_POST['item_checked_' . $current_item]) && in_array($_POST['item_checked_' . $current_item], array('checked', 'unchecked')))
					{
					 $items[] = array('checked' => $_POST['item_checked_' . $current_item], 'text' => $_POST['item_text_' . $current_item]);
					 
					 // Important!
					 $current_item++;
					}
					
					// Do something here in the future maybe?
					
					if(count($items) > 0)
					{
						$query_save = array();
						foreach($items AS $item)
						{
							$query = 'SELECT list_text_id'
							       . ' FROM user_lists_texts'
							       . ' WHERE list_text LIKE "' . $item['text'] . '"'
							       . ' LIMIT 1';
							$result = mysql_query($query) or report_sql_error($query);
							
							if(mysql_num_rows($result) > 0)
							{
								$data = mysql_fetch_assoc($result);
								$list_text_id = (int) $data['list_text_id'];
							}
							else
							{
								$query = 'INSERT INTO user_lists_texts(list_text)'
								       . ' VALUES("' . $item['text'] . '")';
								if(!mysql_query($query))
								{
									report_sql_error($query);
									ui_bottom();
									die();
								}
								
								$list_text_id = mysql_insert_id();
							}
							
							$query_save[] = '(' . $_SESSION['login']['id'] . ', ' . $list_text_id . ', "' . $item['checked'] . '")';
						}
						
						$query = 'INSERT INTO user_lists_users(user_id, list_id, checked) VALUES ' . implode(', ', $query_save);
						mysql_query($query) or report_sql_error($query);
						
						jscript_alert('Fixat och donat!');
						jscript_location('/traffa/profile.php');
					}
					else
					{
						echo 'Nä, nu skickade du nog in ett tomt resultat.';
					}
				}
				else
				{
					echo '<form id="user_lists_insert_form" method="post" action="/traffa/user_lists.php?action=insert">' . "\n";
					
					echo '<ul id="user_lists_insert_items" class="user_lists_list"> </ul>' . "\n";
					
					echo '<br style="clear: both" />' . "\n";
					
					echo '<input type="submit" value="Spara" class="button_60" name="save" style="margin-left: 40px" />' . "\n";
					echo '<input type="button" id="user_lists_insert_add_item" class="button_100" value="Lägg till fler..." style="margin-left: 87px" />' . "\n";
					echo '</form>';
				}
			}
			else
			{
				echo 'Du måste vara inloggad för att få se den här sidan!';
			}
		break;
		
		case 'delete':
			if(login_checklogin())
			{
				$delete_items = array();
				foreach($_POST as $key => $value)
				{
					if(preg_match('/^delete_item_([0-9]+)$/', $key, $matches))
					{
						$delete_items[] = 'list_id = ' . $matches[1];
					}
				}
				
				if(count($delete_items) > 0)
				{
					$query = 'DELETE FROM user_lists_users'
					       . ' WHERE user_id = ' . $_SESSION['login']['id']
					       . ' AND (' . implode(' OR ', $delete_items) . ')';
					mysql_query($query) or report_sql_error($query);
					
					jscript_alert('Borttaget!');
					jscript_location('/traffa/profile.php');
				}
			}
			else
			{
				echo 'Du har blivit utloggad.';
			}
		break;
		
		case 'compare':
		
		break;
		
		case 'copy':
			if(login_checklogin())
			{
				$copy_items = array();
				foreach($_POST as $key => $value)
				{
					if(preg_match('/^copy_item_([0-9]+)$/', $key, $matches))
					{
						$copy_items[] = '(' . $matches[1] . ', ' . $_SESSION['login']['id'] . ')';
					}
				}
				
				if(count($copy_items) > 0)
				{
					$query = 'INSERT INTO user_lists_users(list_id, user_id)'
					       . ' VALUES ' . implode(', ', $copy_items);
					mysql_query($query) or report_sql_error($query);
					
					jscript_alert('Kopierat, du kan nu hitta sakerna du kopierade på din lista!');
				}
				else
				{
					jscript_alert('Fel: Du valde inget att kopiera!');
				}
				jscript_location('/traffa/user_lists.php');
			}
		break;
		
		default:
			$query = 'SELECT li_t.list_text AS text, li_u.checked AS checked, li_t.list_text_id AS id'
			       . ' FROM user_lists_texts AS li_t, user_lists_users AS li_u'
			       . ' WHERE li_u.user_id = ' . $show_user . ' AND li_t.list_text_id = li_u.list_id';
			$result = mysql_query($query) or report_sql_error($query);
			
			if(mysql_num_rows($result) > 0)
			{
				echo '<h1>Visar lista för ' . $username . '</h1>' . "\n";

				if(login_checklogin())
				{
					echo '<form method="post" action="/traffa/user_lists.php?action=' . (($_SESSION['login']['id'] == $show_user) ? 'delete' : 'copy') . '" method="post">' . "\n";
				}
				
				echo '<ul class="user_lists_list">' . "\n";
				while($data = mysql_fetch_assoc($result))
				{
					$extra = ((login_checklogin() && $_SESSION['login']['id'] == $show_user) ?
						'<input type="checkbox" name="delete_item_' . $data['id'] . '" id="delete_item_' . $data['id'] . '_checkbox" /> <label for="delete_item_' . $data['id'] . '_checkbox">Ta bort</label>'
					:
						'<input type="checkbox" name="copy_item_' . $data['id'] . '" id="copy_item_' . $data['id'] . '_checkbox" /> <label for="copy_item_' . $data['id'] . '_checkbox">Kopiera</label>');
						
					echo "\t" . '<li class="' . $data['checked'] . '">' . $data['text'] . '</li>' . "\n";
					echo "\t" . '<li class="options">' . (login_checklogin() ? $extra : ' ') . '</li>' . "\n";
				}
				echo '</ul>' . "\n";
				echo '<br style="clear: both" />' . "\n";
				
				if(login_checklogin())
				{
					echo '<input type="submit" value="' . (($_SESSION['login']['id'] == $show_user) ? 'Ta bort' : 'Kopiera') . '" class="button_70" style="float: right" />' . "\n";
					echo '</form>' . "\n";
					
					if($_SESSION['login']['id'] == $show_user)
					{
						echo '<a href="/traffa/user_lists.php?action=insert">Lägg till saker på listan...</a>' . "\n";
					}
				}

			}
			else
			{
				if($_SESSION['login']['id'] == $show_user)
				{
					echo '<h1>Du har inget på din lista</h1>' . "\n";
					echo 'Du har inte några saker på din lista. <a href="/traffa/user_lists.php?action=insert">Lägg till saker på din lista...</a>' . "\n";
				}
				else
				{
					echo '<h1>Ingen lista ännu</h1>' . "\n";
					echo 'Användaren har inte skapat någon lista än.';
				}
			}
	}
	ui_bottom();
?>