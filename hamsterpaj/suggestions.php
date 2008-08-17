<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/suggestions.lib.php');
	require(PATHS_INCLUDE . 'guestbook-functions.php');
	
	$ui_options['menu_path'] = array('hamsterpaj', 'foerslag');
	$ui_options['stylesheets'][] = 'suggestions.css';
	$ui_options['javascripts'][] = 'suggestions.js';
		
	$ui_options['menu_addition']['hamsterpaj']['children']['foerslag']['children']['waiting'] = array('label' => 'Obehandlade förslag', 'url' => '?action=view_waiting');
	$ui_options['menu_addition']['hamsterpaj']['children']['foerslag']['children']['assigned'] = array('label' => 'På väg', 'url' => '?action=view_assigned');
	$ui_options['menu_addition']['hamsterpaj']['children']['foerslag']['children']['denied'] = array('label' => 'Avslagna', 'url' => '?action=view_denied');
	$ui_options['menu_addition']['hamsterpaj']['children']['foerslag']['children']['in_the_future'] = array('label' => 'Lagda på is', 'url' => '?action=view_in_the_future');
	$ui_options['menu_addition']['hamsterpaj']['children']['foerslag']['children']['completed'] = array('label' => 'Genomförda', 'url' => '?action=view_completed');
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
	elseif($_GET['action'] == 'view_waiting')
	{
		$action = 'view_waiting';
		$ui_options['menu_path'] = array('hamsterpaj', 'foerslag', 'waiting');
	}
	elseif($_GET['action'] == 'view_assigned')
	{
		$action = 'view_assigned';
		$ui_options['menu_path'] = array('hamsterpaj', 'foerslag', 'assigned');
	}
	elseif($_GET['action'] == 'view_completed')
	{
		$action = 'view_completed';
		$ui_options['menu_path'] = array('hamsterpaj', 'foerslag', 'completed');
	}
	elseif($_GET['action'] == 'view_denied')
	{
		$action = 'view_denied';
		$ui_options['menu_path'] = array('hamsterpaj', 'foerslag', 'denied');
	}
	elseif($_GET['action'] == 'view_in_the_future')
	{
		$action = 'view_in_the_future';
		$ui_options['menu_path'] = array('hamsterpaj', 'foerslag', 'in_the_future');
	}
	else
	{
		$action = 'start';
	}
	
	ui_top($ui_options);
	
	
	
	switch($action)
	{
		case 'create':
			$suggestion = $_POST;
			$suggestion['display_level'] = (is_privilegied('suggestion_admin')) ? $_POST['display_level'] : 'normal';
			suggestion_create($suggestion);
			
			echo '<h1>Tack för ditt förslag</h1>' . "\n";
			echo '<a href="/hamsterpaj/suggestions.php">Tillbaks till förslags-sidan</a>' . "\n";
			
			// Ace
			require_once(PATHS_INCLUDE . 'message-functions.php');
			$title = 'Förslag: ' . $_POST['category'] . ': ' . substr($suggestion['text'], 0, 30);
			$message = 'Räkmacka på Umba!\n\n\n' . $suggestion['text'];
			messages_send(2348, 57100, $title, $message);
			
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
			suggestion_update($_POST);
			jscript_alert('Fixat');
			echo '<script>history.go(-2);</script>' . "\n";
			
			$query = 'SELECT author FROM suggestions WHERE id = "' . $_POST['id'] . '" LIMIT 1';
			$result = mysql_query($query);
			if(mysql_num_rows($result) == 1)
			{
				$data = mysql_fetch_assoc($result);
				$message = 'Hej, ditt förslag har uppdaterats, ny status för ditt förslag är: ' . $SUGGESTIONS['classifications'][$_POST['classification']]['label'] . '!' . "\n";
				$message .= (strlen($_POST['responsible_username']) > 1) ? 'Ansvarig för ditt förslag är: ' . $_POST['responsible_username'] : '';
				$message .= "\n" . 'Texten i det berörda förslaget lyder: ' . "\n" . $_POST['text'];
				new_entry($data['author'], 2348, $message);
			}
			break;
			
		case 'view_waiting':
			echo '<h1>Förslag som väntar på att granskas</h1>';
			$fetch['classification'] = array('waiting');
			$suggestions = suggestion_fetch($fetch);
			suggestion_list($suggestions);
			break;

		case 'view_assigned':
			echo '<h1>Förslag som är på väg</h1>';
			$fetch['classification'] = array('assigned');
			$suggestions = suggestion_fetch($fetch);
			suggestion_list($suggestions);
			break;
			
		case 'view_completed':
			echo '<h1>Genomförda förslag</h1>';
			$fetch['classification'] = array('completed');
			$suggestions = suggestion_fetch($fetch);
			suggestion_list($suggestions);
			break;
			
		case 'view_denied':
			echo '<h1>Förslag som inte kommer genomföras</h1>';
			$fetch['classification'] = array('denied');
			$suggestions = suggestion_fetch($fetch);
			suggestion_list($suggestions);
			break;
			
		case 'view_in_the_future':
			echo '<h1>Förslag som kanske kommer genomföras i framtiden</h1>';
			$fetch['classification'] = array('in_the_future');
			$suggestions = suggestion_fetch($fetch);
			suggestion_list($suggestions);
			break;

		default:
			include('suggestions_start.html');
	}

	ui_bottom();
?>