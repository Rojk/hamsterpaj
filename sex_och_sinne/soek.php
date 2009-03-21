<?php 
	require_once('../include/core/common.php');
	include_once(PATHS_LIBRARIES . 'sex_sense.lib.php');
	include_once(PATHS_LIBRARIES . 'sex_sense_ui.lib.php');
	$ui_options['stylesheets'][] = 'sex_sense.css';
	$ui_options['javascripts'][] = 'sex_sense.js';
	$ui_options['menu_path'] = array('sex_sense', 'search');
	$ui_options['title'] = 'Sök - Sex och sinne - Hamsterpaj.net';
	
	$out .= '<img id="sex_and_sense_top" src="http://images.hamsterpaj.net/sex_and_sense/sex_and_sense_top.png" alt="Sex och sinne" />' . "\n";
	$out .= '<h1>Sök i sex och sinne</h1>';
	
	$out .= '<div class="sex_sense_search">' . "\n";
	$out .= '<form action="/sex_och_sinne/soek.php" method="get">
					<input type="text" name="q" style="width: 300px;" />
					<input type="submit" value="Sök" class="button_60" />
					</form>';
	$out .= '</div>' . "\n";
	
	if (isset($_GET['q']) && !empty($_GET['q']))
	{
		$options['is_released'] = 1;
		$options['match_against'] = array('against' => $_GET['q'], 'match' => array('q.title', 'q.question')); 
		$options['ignore_no_posts_found_error'] = true;
		$options['limit'] = 30;
		$questions = sex_sense_fetch_posts($options);
		
		$out .= sex_sense_render_posts($questions);
		
		if(count($questions) < 1)
		{
			$out .= '<h2>Hittade inget som matchade din fråga</h2><h3>Använd färre ord i sökningen</h3>Försök att skriva in nyckelord, såsom "<strong>prickar snoppen</strong>" istället för "Jag har några prickar på snoppen, vad ska jag göra?".<br /><h3>Avancerat</h3>Vill du ta bort ett ord ur din sökning kan du göra såhär: "sex -vaginalsex" (hittar allt som innehåller sex men inte vaginalsex).';
		}
	}
		
	ui_top($ui_options);
	echo $out;
	ui_bottom();
	
?>