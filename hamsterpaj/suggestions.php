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
			if(login_checklogin())
			{
				$suggestion = $_POST;
				$suggestion['display_level'] = (is_privilegied('suggestion_admin')) ? $_POST['display_level'] : 'normal';
				suggestion_create($suggestion);
				
				echo '<h1>Tack för ditt förslag</h1>' . "\n";
				echo '<a href="/hamsterpaj/suggestions.php">Tillbaks till förslags-sidan</a>' . "\n";
				
				guestbook_insert(array(
					'sender' => 2348,
					'recipient' => 57100,
					'is_private' => 1,
					'message' => mysql_real_escape_string('Detta är ett förslag från förslagslådan, som numera kommer i GB:' . "\n") . $suggestion['text']
				));
			}
			
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
			if(is_privilegied('suggestion_admin'))
			{
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
			}
			break;
			
		case 'delete':
			if(is_privilegied('suggestion_admin'))
			{
			 	$options['id'] = $_GET['id'];
			 	$options['display_level'] = 'removed';
				suggestion_update($options);
			}
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
			?>

<h1>Här kan du lämna förslag till Hamsterpaj!</h1>

<p>
	Om du har hittat en bugg, saknar en funktion eller vill ha en ny flagga så kan du skicka
	in ditt förslag här. Alla förslag läses av de som jobbar här på Hamsterpaj.<br />
	När vi har läst ett förslag svarar vi på det och klassificerar det, skräp och trams tas
	bara bort.
</p>

<h2>Här kan du inte rapportera användare eller inlägg!</h2>
<p>
	Om du vill rapportera en användare besöker du dennes presentation och trycker på "anmäl".
</p>

<h2>Du kan inte ställa frågor till oss här!</h2>
<p>
	Det här är en förslagsfunktion och inte en frågelåda! Du kan be oss skriva manualer eller
	hjälpsidor, men vi svarar aldrig på frågor här.<br />
	Undrar du över något? Starta en diskussion i <a href="/diskussionsforum/">forumet</a>.
</p>

<!-- Rounded corners div. Color: , dimension: -->
<div class="rounded_corners" id="suggestion_start_help">
<img src="http://images.hamsterpaj.net/css_backgrounds/rounded_corners/blue_full_top.png" />
<div class="rounded_corners_blue_full">
	<div style="clear:both"></div>
	<h1 style="margin-top: 0px; padding-top: 4px;">Det är lätt att skicka in ett eget förslag</h1>
		<ol style="margin-bottom: 0px; padding-bottom: 4px;">
			<li>Titta så att förslaget inte redan är inlagt</li>
			<li>Tänk igenom ditt förslag ordentligt
				<ul>
					<li>Blir Hamsterpaj bättre med ditt förslag?</li>
					<li>Tror du det är möjligt att genomföra det?</li>
					<li>Är det fler än du som kommer tycka att det är ett bra förslag?</li>
				</ul>
			</li>
			<li>Skriv ner ditt förslag så tydligt och detaljerat som möjligt</li>
			<li>Läs igenom ditt förslag, är det lätt att förstå vad du menar?</li>
			<li>Tryck på "Skicka in ett förslag" och skicka in ditt förslag!</li>
		</ol>
	<div style="clear:both"></div>
</div>
<img src="http://images.hamsterpaj.net/css_backgrounds/rounded_corners/blue_full_bottom.png" />
</div>


			<?php
	}

	ui_bottom();
?>