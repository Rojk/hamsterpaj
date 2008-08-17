<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/drivers_license.php');
	require(PATHS_INCLUDE . 'configs/drivers_license.php');
			
	$ui_options['menu_path'] = array('mattan', 'koerkort');
	$ui_options['title'] = 'Körkortsfrågor på Hamsterpaj';
	$ui_options['stylesheets'][] = 'drivers-license.css';
	
	$ui_options['javascripts'][] = 'drivers-license.js';
	
	$ui_options['enable_rte'] = true;

	ui_top($ui_options);
	
	$DL_CATEGORIES['license']['label'] = 'Körkort och körkortstillstånd';
	$DL_CATEGORIES['signs']['label'] = 'Skyltar och markeringar';
	$DL_CATEGORIES['drugs']['label'] = 'Alkohol, trötthet och droger';
	$DL_CATEGORIES['darkness']['label'] = 'Mörker och dimma';
	$DL_CATEGORIES['handling']['label'] = 'Väghållning';
	$DL_CATEGORIES['rules']['label'] = 'Trafikregler';
	$DL_CATEGORIES['car']['label'] = 'Bilen';
	$DL_CATEGORIES['designations']['label'] = 'Begrepp och uttryck';
	$DL_CATEGORIES['police']['label'] = 'Farbror blå';
	$DL_CATEGORIES['parking']['label'] = 'Att parkera och stanna';
	$DL_CATEGORIES['accidents']['label'] = 'Olyckor';
	$DL_CATEGORIES['enviroment']['label'] = 'Miljö';
	$DL_CATEGORIES['other']['label'] = 'Osorterade frågor';
	
	
	$query = 'SELECT reference, count FROM count_cache WHERE count_type = "drivers_license_category"';
	$result = mysql_query($query);
	while($data = mysql_fetch_assoc($result))
	{
		$DL_CATEGORIES[$data['reference']]['count'] = $data['count'];
	}
	
	if(login_checklogin())
	{
		$query = 'SELECT * FROM dl_scores WHERE user = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error());
		$data = mysql_fetch_assoc($result);	

		foreach(array_keys($DL_CATEGORIES) AS $handle)
		{
			$DL_CATEGORIES[$handle]['completed'] = $data[$handle];
		}
	}

	

	
	if(isset($_GET['category']) && !isset($DL_CATEGORIES[$_GET['category']]))
	{
		die('Error');
	}
	
	unset($options);
		
	if(!login_checklogin())
	{
?>
	<h1>Teoriprogrammet fungerar bara för medlemmar</h1>
		<img src="http://images.hamsterpaj.net/drivers-license/dl_teaser.png" style="float: right;" />
		<p>
			För att teoriprogrammet ska kunna hålla reda på vilka frågor du redan svarat på, vilka du haft rätt på och
			vilka du svarat fel på måste du vara inloggad som medlem på Hamsterpaj.<br /><br />
			Att bli medlem är helt gratis, du behöver inte tala om vad du heter eller vad du har för personnummer
			och registreringen tar max ett par minuter.<br /><br />
			Vi skickar ingen spam heller, till skillnad från våra konkurrenter...
		</p>
		<a href="/register.php">Bli medlem nu</a>
		<br style="clear: both;" />
<?php
	ui_bottom();
	exit;
	}
	
	unset($options);
	if(isset($_POST['question']))
	{
		$answer = dl_question_answer($_POST['question'], $_POST['answer']);
		if(login_checklogin() && $_GET['action'] == 'test')
		{
			$_SESSION['drivers-license']['answered_questions'][] = $_POST['question'];
		}
	}
	
	if($_GET['action'] == 'test' && login_checklogin())
	{
		$options['mode'] = 'test';
		$return = dl_question_fetch($options);
		dl_question_echo($return['question'], $return['alternatives']);
	}
	elseif($_GET['action'] == 'practice')
	{
		$options['mode'] = 'practice';
		if(isset($_GET['category']))
		{
			$options['category'] = $_GET['category'];
		}
		$return = dl_question_fetch($options);
		if($return == false)
		{
			jscript_alert('Trace');
			jscript_alert('Du har klarat av alla frågor i denna kategori. Om du vill börja om rensar du dina poäng genom att trycka på det röda krysset i kategorilistan.');
			jscript_location($_SERVER['PHP_SELF']);
		}
		else
		{
			dl_question_echo($return['question'], $return['alternatives']);
			/*if($return['question']['id'] == 168)
			{
				treasure_item(4);
			}*/
		}
	}
	elseif($_GET['action'] == 'clear_all')
	{
		dl_clear_all();
		jscript_location($_SERVER['PHP_SELF']);
	}
	elseif($_GET['action'] == 'clear_category')
	{
		dl_clear_category();
		jscript_location($_SERVER['PHP_SELF']);
	}
	else
	{
		dl_index();
	}
	
	echo '<div class="how_it_works">' . "\n";
	echo 'Teoriprogrammet döljer automatiskt frågor som du svarat rätt på två gånger i rad. Ibland kan det hända att du får samma fråga två gånger i rad, det är inget konstigt utan hänger ihop med hur slumpningen fungerar.';
	echo '</div>' . "\n";
	
	ui_bottom();
?>