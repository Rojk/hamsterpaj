<?php
	require('../include/core/common.php');
	$ui_options['stylesheets'][] = 'abuse.css';
	$ui_options['javascripts'][] = 'start.js';
	$ui_options['title'] = 'Rapportfunktionen för ett säkert Hamsterpaj';
	$ui_options['menu_path'] = array('hamsterpaj', 'rapportera');
	ui_top($ui_options);
	
	echo '<div id="abuse">' . "\n";
	if(login_checklogin() && isset($_GET['report_type']) && isset($_GET['reference_id']) && is_numeric($_GET['reference_id']))
	{
		$query = 'SELECT reference_id FROM abuse WHERE reference_id = "' . $_GET['reference_id'] . '" LIMIT 1';
		$result = mysql_query($query);
		while ($row = mysql_fetch_assoc($result))
		{
			$is_it_reported = $row['reference_id'];
		}
		if (strlen($is_it_reported) > 0)
		{
			$rounded = '<h1 style="margin: 0px">Inlägget har redan blivit rapporterat, men tack ändå ;)';
			echo rounded_corners($rounded, array('color' => 'orange_deluxe'), true);
		}
		else
		{
			echo '<h1>' . $abuse_headers[$_GET['report_type']] . '</h1>';
			
			echo $abuse_info[$_GET['report_type']];
			
			echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">' . "\n";
			echo '<input type="hidden" name="report_type" value="' . $_GET['report_type'] . '" />' . "\n";
			echo '<input type="hidden" name="reference_id" value="' . $_GET['reference_id'] . '" />' . "\n";
			foreach($abuse_alternatives_by_type[$_GET['report_type']] AS $handle)
			{
				echo '<input type="radio" name="abuse_type" class="abuse_radio" value="' . $handle . '" id="abuse_' . $handle . '" />' . "\n";
				echo '<label for="abuse_' . $handle . '">' . $abuse_types[$handle]['label'] . '</label>' . "\n";
				echo '<p>' . $abuse_types[$handle]['description'] . '</p>' . "\n";
			}
			echo '<label for="abuse_freetext">Fritextbeskrivning</label>' . "\n";
			echo '<textarea name="freetext" id="abuse_freetext"></textarea>' . "\n";
			
			echo '<input type="submit" class="button_70" value="Nästa &raquo;" />' . "\n";
			echo '</form>' . "\n";
		}
	}
	elseif(login_checklogin() && isset($_POST['reference_id']) && is_numeric($_POST['reference_id']))
	{
		$query = 'INSERT INTO abuse (timestamp, reporter, report_type, reference_id, abuse_type, freetext)';
		$query .= ' VALUES("' . time() . '", "' . $_SESSION['login']['id'] . '", "' . $_POST['report_type'] . '", "' . $_POST['reference_id'] . '", "' . $_POST['abuse_type'] . '", "' .$_POST['freetext'] . '")';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$url = forum_get_url_by_post($_POST['reference_id']);
		
		echo '<h1>Din rapport har sparats</h1>' . "\n";
		echo '<p>Vi kommer att granska din rapport och återkomma till dig med ett personligt svar inom 24 timmar.</p>' . "\n";
		echo '<h2>Ärende-id: #' . mysql_insert_id() . '</h2>' .  "\n";
		echo '<br /> <a href="' . $url . '">Gå tillbaka till forumet</a>';
		

		if($_POST['report_type'] == 'guestbook_entry')
		{
			$query = 'UPDATE traffa_guestbooks SET is_private = 0, deleted = 0 WHERE id = "' . $_POST['reference_id'] . '" AND recipient = "' . $_SESSION['login']['id'] . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			
			echo '<h1>OBS! Viktigt om rapporterade gästboksinlägg</h1>' . "\n";
			echo '<p>För att våra ordningsvakter ska kunna granska ett inlägg får det inte vara privat eller borttaget. Privatisera inte och ta inte bort inlägget!</p>' . "\n";
		}

	}
	else
	{
?>
	<h1>Rapportfunktionen för ett säkert Hamsterpaj</h1>
	<p>
		Vid varje gästboksinlägg, forumsinlägg, privat meddelande och vid darje användare finns en röd liten knapp med ett utropstecken på.
		Det är en rapportknapp, när du ser något som inte hör hemma på Hamsterpaj, som snuskgubbar, spam, reklam-meddelanden eller liknande
		så trycker du på rapportknappen. Då kommer du till den här sidan, men då visas en guide för hur du anmäler det du sett.
	</p>
	<p>
		Tänk på att alltid anmäla just det som är olämpligt. Om någon har skrivit skräp i forumet så anmäl inläggen istället för användaren,
		annars blir det svårt för våra ordningsvakter att se vad användaren har gjort för fel.
	</p>
	<h2>Har du redan gjort en rapport?</h2>
	<p>
		Här kan du fylla i ditt ärende-ID och se status för din rapport.
	</p>
	<h2>Endast medlemmar kan anmäla</h2>
	<p>
		För att enklare kunna följa upp anmälningar och för att minska mängden skräp-anmälningar som görs så har vi begränsat rapportfunktionen så
		att endast medlemmar kan göra anmälningar.
	</p>
<?php
	}
	
	echo '</div>' . "\n";
	
	ui_bottom();
?>
