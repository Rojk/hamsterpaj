<?php
	require('../include/core/common.php');

	$ui_options['menu_path'] = array('mattan', 'foerkortningar');

	$ui_options['title'] = 'Förkortningslistan på Hamsterpaj';
	$ui_options['stylesheets'][] = 'acronyms.css';
	$ui_options['javascripts'][] = 'acronyms.js';
	

	ui_top($ui_options);

	$numbers[1] = 'en';
	$numbers[2] = 'två';
	$numbers[3] = 'tre';
	$numbers[4] = 'fyra';
	$numbers[5] = 'fem';
	$numbers[6] = 'sex';
	$numbers[7] = 'sju';
	$numbers[8] = 'åtta';
	$numbers[9] = 'nio';
	$numbers[10] = 'tio';
	$numbers[11] = 'elva';
	$numbers[12] = 'tolv';

	if(isset($_GET['delete']) && is_numeric($_GET['delete']) && is_privilegied('abbr_admin'))
	{
		$query = 'SELECT acronym FROM acronyms WHERE id = ' . $_GET['delete'] . ' LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query));
		$data = mysql_fetch_assoc($result);
		$letter = strtolower($data['acronym'][0]);
				
		$query = 'DELETE FROM acronyms WHERE id = ' . $_GET['delete'] . ' LIMIT 1';
		mysql_query($query) or die(report_sql_error($query));
		jscript_location('/mattan/foerkortningar.php?letter=' . $letter);
	}
	
	if(isset($_GET['edit']) && is_numeric($_GET['edit']) && is_privilegied('abbr_admin'))
	{
		$query = 'SELECT * FROM acronyms WHERE id = ' . $_GET['edit'] . ' LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query));
		$data = mysql_fetch_assoc($result);

		echo '<div class="grey_faded_div">' . "\n";
		echo '<h2>Du ändrar förkortning #' . $data['id'] . ', ' . $data['meaning'] . '</h2>' . "\n";
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?action=update" method="post">' . "\n";
		echo '<input type="hidden" name="id" value="' . $data['id'] . '" />' . "\n";
		echo '<h5>Förkortning</h5>' . "\n";
		echo '<input type="text" name="acronym" value="' . $data['acronym'] . '" />' . "\n";
		echo '<h5>Betydelse</h5>' . "\n";
		echo '<input type="text" class="inp_meaning" name="meaning" value="' . $data['meaning'] . '" />' . "\n";
		echo '<h5>Förklaring</h5>' . "\n";
		echo '<textarea name="explanation" class="txt_explanation">' . $data['explanation'] . '</textarea>' . "\n";
		echo '<input type="submit" class="button" value="Spara ändringar" />' . "\n";
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}
	
	if(isset($_GET['report']) && is_numeric($_GET['report']) && is_privilegied('abbr_admin'))
	{
		$query = 'UPDATE acronyms SET reports = reports + 1 WHERE id = ' . $_GET['report'] . ' LIMIT 1';
		mysql_query($query) or die(report_sql_error($query));
		jscript_alert('Tackar, en ordningsvakt kommer att kika på din rapport');
	}
	
	if($_GET['action'] == 'verify' && is_numeric($_GET['id']) && is_privilegied('abbr_admin'))
	{
		$query = 'UPDATE acronyms SET verified = 1, reports = 0 WHERE id = ' . $_GET['id'] . ' LIMIT 1';
		mysql_query($query) or die(report_sql_error($query));
	}
	
	if($_GET['action'] == 'update' && is_privilegied('abbr_admin'))
	{
		$query = 'UPDATE acronyms SET acronym = "' . $_POST['acronym'] . '", meaning = "' . $_POST['meaning'] . '", explanation = "';
		$query .= $_POST['explanation'] . '", reports = 0, verified = 1 WHERE id = ' . $_POST['id'] . ' LIMIT 1';
		mysql_query($query) or die(report_sql_error());
		$_GET['id'] = $_POST['id'];
	}
	
	function acro_fetch($letter = 'a')
	{
		$query = 'SELECT id, acronym, meaning, explanation, reports, verified FROM acronyms';
		$query .= ' WHERE acronym LIKE "' . $letter . '%"';
		$query .= ' ORDER BY acronym ASC';
		$result = mysql_query($query) or die(report_sql_error($query));
		while($data = mysql_fetch_assoc($result))
		{
			$acronyms[$data['acronym']][] = $data;
		}
		return $acronyms;
	}


	function acro_list($acronyms, $expanded = null)
	{
		global $numbers;
		echo '<div id="acronyms">' . "\n";
		echo '<ul>' . "\n";
		foreach($acronyms AS $acronym => $meanings)
		{
			echo (strtoupper($acronym) == strtoupper($_GET['highlight'])) ? '<li class="expanded">' : '<li class="contracted">' . "\n";
			echo '<h2>' . $acronym . '</h2>' . "\n";
			echo '<h4>';
			$meanings_count = count($meanings);
			echo (array_key_exists($meanings_count, $numbers)) ? $numbers[$meanings_count] : $meanings_count;
			echo ($meanings_count == 1) ? ' betydelse' : ' betydelser';
			echo '</h4>';
			
			echo '<ul>' . "\n";
			foreach($meanings AS $meaning)
			{
				echo '<li>' . "\n";
				echo '<h3>' . $meaning['meaning'] . '</h3>';
				echo '<p>' . $meaning['explanation'] . '</p>' . "\n";
				echo '<div class="controls">' . "\n";
				if(is_privilegied('abbr_admin'))
				{
					echo '<input type="button" class="button_small" id="btn_edit_' . $meaning['id'] . '" value="Ändra" />' . "\n";
					echo '<input type="button" class="button_small" id="btn_delete_' . $meaning['id'] . '" value="Ta bort" />' . "\n";
				}
				if(is_privilegied('abbr_admin'))
				{
					echo '<input type="button" class="button_small" id="btn_report_' . $meaning['id'] . '" value="Rapportera" />' . "\n";
				}
				if(is_privilegied('abbr_admin') && $meaning['verified'] == 0)
				{
					echo '<input type="button" class="button_small" id="btn_verify_' . $meaning['id'] . '" value="Verifiera" />' . "\n";
				}
				echo '</div>' . "\n";
				echo '</li>' . "\n";
			}
			echo '</ul>';
			
			echo '</li>' . "\n";
		}
		echo '</ul>' . "\n";
		echo '</div>' . "\n";
	}
	
	function acro_form($acronym = '')
	{
		echo '<div class="grey_faded_div">' . "\n";
		echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n";
		echo '<input type="hidden" name="action" value="new_acronym" />' . "\n";
		echo '<h3>Förkortning (Stora bokstäver, inga punkter)</h3>' . "\n";
		if(strlen($acronym) > 0)
		{
			echo '<input type="text" name="acronym" disabled="true" value="' . $acronym . '" />' . "\n";
		}
		else
		{
			echo '<input type="text" name="acronym" />' . "\n";
		}
		echo '<h3>Betydelse</h3>' . "\n";
		echo '<input type="text" class="inp_meaning" name="meaning" />' . "\n";
		echo '<h3>Förklaring eller beskrivning</h3>' . "\n";
		echo '<textarea name="explanation" class="txt_explanation"></textarea><br />' . "\n";
		echo 'Om du inte vet vad en förkortning betyder, lägg inte in den då... Kötthuvven...<br />';
		echo '<input type="submit" value="Lägg till" class="button" />' . "\n";
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}

	function acro_add($acronym)
	{
		$query = 'INSERT INTO acronyms(acronym, author, meaning, explanation) VALUES("' . htmlspecialchars($acronym['acronym']) . '", "' . $_SESSION['login']['id'];
		$query .= '", "' . htmlspecialchars($acronym['meaning']) . '", "' . htmlspecialchars($acronym['explanation']) . '")';
		mysql_query($query) or die(report_sql_error());
		$_GET['letter'] = strtolower($acronym['acronym']{0});

	}
	
	if($_POST['action'] == 'new_acronym')
	{
		acro_add($_POST);
	}
	
	$letters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'å', 'ä', 'ö');
	
	echo '<h1>Förkortningslistan på Hamsterpaj</h1>' . "\n";
	echo '<div id="alphabet_list">' . "\n";
	echo '<ol>' . "\n";
	if(is_privilegied('abbr_admin'))
	{
		echo '<li><a href="?reported">[Rp]</a></li>' . "\n";
	}
	foreach($letters AS $letter)
	{
		echo '<li><a href="?letter=' . $letter . '">' . mb_strtoupper($letter, 'utf-8') . '</a></li>' . "\n";
	}
	echo '</ol>' . "\n";
	echo '</div>' . "\n";
	if(isset($_GET['reported']))
	{
		$query = 'SELECT * FROM acronyms WHERE reports > 0 ORDER BY reports DESC LIMIT 10';
		$result = mysql_query($query) or die(report_sql_error($query));
		while($data = mysql_fetch_assoc($result))
		{
			$acronyms[$data['acronym']][] = $data;
		}
		echo '<h2>Visar rapporterade förkortningar</h2>';
	}
	else
	{
		if(!isset($_GET['letter']) && isset($_GET['id']) && is_numeric($_GET['id']))
		{
			$query = 'SELECT acronym FROM acronyms WHERE id = ' . $_GET['id'] . ' LIMIT 1';
			$result = mysql_query($query) or die(report_sql_error($query));
			$data = mysql_fetch_assoc($result);
			$_GET['letter'] = strtolower($data['acronym']{0});
			$_GET['highlight'] = $data['acronym'];
		}
		$acronyms =  (in_array($_GET['letter'], $letters)) ?  acro_fetch($_GET['letter']) : acro_fetch();		
	}
	acro_list($acronyms);

	if(login_checklogin())
	{
		acro_form();
	}
	

	ui_bottom();

?>


