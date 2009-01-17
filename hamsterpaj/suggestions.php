<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/suggestions.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/guestbook.lib.php');
	
	$ui_options['menu_path'] = array('hamsterpaj', 'foerslag');
	$ui_options['stylesheets'][] = 'suggestions.css';
	$ui_options['javascripts'][] = 'suggestions.js';
		
	$ui_options['menu_addition']['hamsterpaj']['children']['foerslag']['children']['waiting'] = array('label' => 'Obehandlade förslag', 'url' => '?action=view_waiting');
	$ui_options['menu_addition']['hamsterpaj']['children']['foerslag']['children']['processed'] = array('label' => 'Granskade förslag', 'url' => '?action=view_processed');
	$ui_options['menu_addition']['hamsterpaj']['children']['foerslag']['children']['compose'] = array('label' => 'Skicka in ett förslag', 'url' => '?action=compose');
	
	if(isset($_POST['action']))
	{
		$action = $_POST['action'];
	}
	elseif($_GET['action'] == 'compose')
	{
		$action = 'compose';
		$ui_options['menu_path'] = array('hamsterpaj', 'foerslag', 'compose');
	}
	elseif($_GET['action'] == 'edit' && is_privilegied('suggestion_admin'))
	{
		$action = 'edit';
	}
		elseif($_GET['action'] == 'create' && is_privilegied('suggestion_admin'))
	{
		$action = 'create';
	}
	elseif($_GET['action'] == 'delete' && is_privilegied('suggestion_admin'))
	{
		$action = 'delete';
	}
	elseif ($_GET['action'] == 'update')
	{
		$action = 'update';
	}
	elseif($_GET['action'] == 'view_waiting')
	{
		$action = 'view_waiting';
		$ui_options['menu_path'] = array('hamsterpaj', 'foerslag', 'waiting');
	}
	elseif($_GET['action'] == 'view_processed')
	{
		$action = 'processed';
		$ui_options['menu_path'] = array('hamsterpaj', 'foerslag', 'processed');
	}
	else
	{
		$action = 'start';
	}
	
	$ui_options['title'] = 'Förslag - Hamsterpaj.net';
	
	ui_top($ui_options);
	
	echo rounded_corners_top();
	echo '<a href="/hamsterpaj/suggestions.php?action=compose">Skicka in förslag</a> | <a href="/hamsterpaj/suggestions.php?action=view_waiting">Visa obehandlade</a> | <a href="/hamsterpaj/suggestions.php?action=view_processed">Visa granskade</a>';	
	echo rounded_corners_bottom();
	switch($action)
	{
		case 'create':
			$suggestion = $_POST;
			$suggestion['display_level'] = (is_privilegied('suggestion_admin')) ? $_POST['display_level'] : 'normal';
			suggestion_create($suggestion);
			
			echo '<h1>Tack för ditt förslag</h1>' . "\n";
			echo '<a href="/hamsterpaj/suggestions.php">Tillbaks till förslags-sidan</a>' . "\n";
			
			guestbook_insert(array(
				'sender' => 2348,
				'recipient' => 57100,
				'is_private' => 1,
				'message' => 'Detta är ett förslag från förslagslådan, som numera kommer i GB:' . "\n" . $suggestion['text'];
			));
			
			break;
		
		case 'compose':
			if(login_checklogin())
			{
				suggestion_form();
			}
			else
			{
				echo '<h1>Bara inloggade medlemmar kan skicka förslag!</h1>' . "\n";
				echo '<script>womAdd("tiny_reg_form_show();");</script>' . "\n";
			}
			break;
		
		case 'edit':
			$fetch['id'] = array($_GET['id']);
			$suggestions = suggestion_fetch($fetch);
			$suggestion = array_pop($suggestions);
			suggestion_form($suggestion);
			break;
			
		case 'update':
				$query = 'SELECT author FROM suggestions WHERE id = "' . $_POST['id'] . '" LIMIT 1';
				$result = mysql_query($query);
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					$message['recipient'] = $data['author'];
					$message['sender'] = 2348;
					$message['message'] = 'Hej, ditt förslag har uppdaterats, ny status för ditt förslag är: ' . $SUGGESTIONS['classifications'][$_POST['classification']]['label'] . '!' . "\n";
					$message['message'] .= (strlen($_POST['responsible_username']) > 1) ? 'Ansvarig för ditt förslag är: ' . $_POST['responsible_username'] : '';
					$message['message'] .= "\n" . 'Texten i det berörda förslaget lyder: ' . "\n" . $_POST['text'];
					$message['message'] .= "\n\n" . 'Svaret på ditt förslag lyder: ' . "\n" . $_POST['reply'];
					guestbook_insert($message);
				}
				suggestion_update($_POST);
				jscript_alert('Fixat och donat!');
				jscript_location('/hamsterpaj/suggestions.php?action=view_waiting');
			break;
			
		case 'delete':
		 	$options['id'] = $_GET['id'];
		 	$options['display_level'] = 'removed';
			suggestion_update($options);
			break;
			
		case 'view_waiting':
			echo '<h1>Förslag som väntar på att granskas</h1>';
			$fetch['classification'] = array('waiting');
			$suggestions = suggestion_fetch($fetch);
			suggestion_list($suggestions);
			break;
			
		case 'processed':
			echo '<h1>Förslag som vi granskat</h1>' . "\n";
			$fetch['classification'] = array('in_the_future', 'denied', 'completed', 'assigned');
			$suggestions = suggestion_fetch($fetch);
			suggestion_list($suggestions);
			break;

		default:
			include('suggestions_start.html');
	}

	ui_bottom();
?>