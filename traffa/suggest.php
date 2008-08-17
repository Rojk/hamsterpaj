<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('hamsterpaj', 'rules_and_policies');
	$ui_options['javascripts'][] = 'suggest.js';
	
	$user_id = $_SESSION['login']['id'];
	
	$out .= '<form>';
	$out .= 'First Name:';
	$out .= '<input type="text" id="txt1"onkeyup="showHint(this.value)">';
	$out .= '</form>';
	$out .= '<p>Suggestions: <span id="txtHint"></span></p>';
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();


	
?>