<?php
	require('include/core/common.php');
	
	$ui_options['current_menu'] = 'hamsterpaj';
	$ui_options['title'] = 'Tävling';
	$ui_options['stylesheets'][] = 'tavling.css';

	$output .= '<div id="competition">';
	$output .= '<div id="form">';
	$output .= '<h2>Tävling</h2>';
	$output .= '<p>Förnamn: <input type="text" name="namn" /></p>';
	$output .= '<p>Efternamn: <input type="text" name="namn" /></p>';
	$output .= '<p>Användarnamn: <input type="text" name="namn" /></p>';
	$output .= '<p>Adress: <input type="text" name="namn" /></p>';
	$output .= '<p>Postnummer: <input type="text" name="namn" /></p>';
	$output .= '<p>Postort: <input type="text" name="namn" /></p>';
	$output .= '<p>Telefonnummer: <input type="text" name="namn" /></p>';
	$output .= '</div>';
	$output .= '<p>Fråga A</p>';
	$output .= '<label for="a1"><input type="radio" id="a1" name="a" />Svar 1</label><br />';
	$output .= '<label for="a2"><input type="radio" id="a2" name="a" />Svar 2</label><br />';
	$output .= '<label for="a3"><input type="radio" id="a3" name="a" />Svar 3</label><br />';
	$output .= '<p>Fråga B</p>';
	$output .= '<label for="b1"><input type="radio" id="b1" name="a" />Svar 1</label><br />';
	$output .= '<label for="b2"><input type="radio" id="b2" name="a" />Svar 2</label><br />';
	$output .= '<label for="b3"><input type="radio" id="b3" name="a" />Svar 3</label><br />';

	$output .= '</div>';
	
	
	ui_top($ui_options);
	echo $output;		
	ui_bottom();
?>