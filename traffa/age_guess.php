<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE . 'libraries/age_guess.lib.php');
	$ui_options['menu_path'] = array('traeffa', 'age_guess');
	$ui_options['title'] = 'Gissa åldern på Hamsterpaj';
	$ui_options['javascripts'][] = 'age_guess.js';
	$ui_options['stylesheets'][] = 'age_guess.css';
	ui_top($ui_options);
?>

<h1>Gissa åldern</h1> 

	<div id="age_guess_main">
		<?php echo age_guess_image(); ?>
	</div>
	<div class="age_guess_panel">
		<div id="age_guess_result">
			<p>
				Hur poängräkningen fungerar kan du läsa om en bit ner.<br />
			</p>
		</div>
		<div id="age_guess_inputs">
			<h3>Hur gammal tror du personen till vänster är?</h3>
			<?php
				for($i = 6; $i < 26; $i++)
				{
					echo '<div id="age_guess_input_' . $i . '">' . $i . '</div>' . "\n";
				}		
			?>
			<button id="age_guess_skip" class="button_150">Hoppa över denna bild</button>
		</div>
		
		<div id="age_guess_statistics">
			<?php echo age_guess_statistics(); ?>
		</div>
		
		<div id="age_guess_toplist">
			<?php echo age_guess_toplist(); ?>
		</div>
	</div>
	
	<br style="clear: both;" />
	<h1>Så här räknar vi poäng</h1>
	<p>
		Poängen räknas veckovis, natten till måndag börjar en ny vecka och alla startar på noll poäng.
	</p>
	<p>
		Rätt svar ger fem poäng.<br />
		Ett år ifrån ger inga poäng.<br />
		Mer än ett år ifrån ger ett minuspoäng mindre än antalet år du missar med, som mest kan du få fem minuspoäng på en gissning.
		Exempelvis ger tre år ifrån två minuspoäng, medans sex och åtta år ifrån båda ger fem minuspoäng.
	</p>
	<p>
		Det finns ingen gräns för hur många bilder du får gissa på.
	</p>
	<p>
		På grund av elvaårig pojkar som tävlar i att få flest minuspoäng har vi nu satt en gräns, så att det inte går att få mindre än -10 poäng.
	</p>
<?php
	if(login_checklogin())
	{
		echo '<h1>Så här gissar andra om din ålder</h1>' . "\n";
		$query = 'SELECT * FROM age_guess_logs WHERE user = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			echo '<table>' . "\n";
			foreach($data AS $field => $value)
			{
				if($field != 'user' && $value != 0)
				{
					echo '<tr><td>' . substr($field, 4) . '</td><td>' . $value . '</td></tr>' . "\n";
				}
			}
			echo '</table>' . "\n";
		}
	}

	ui_bottom();
?>


