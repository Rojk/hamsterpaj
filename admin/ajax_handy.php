<?php

	require('../include/core/common.php');
	$ui_options['title'] = 'Handy encoder/decoder - Hamsterpaj.net';
	$ui_options['menu_path'] = array('dev', 'ajax_handy');
	$ui_options['javascripts'][] = 'ajax_handy.js';
	
	if(!login_checklogin())
	{
		die('Du blev nog utloggad.');
	}
	
	if(!is_privilegied('use_debug_tools'))
	{
		die('Fisk');
	}
	
	$out .= '<h2 style="margin: 0px; padding: 2px;">Handy encoder/decoder</h2>' . "\n";
	//$out .= '<p>Ajax Handy är testad och utvecklad i och för Firefox 3. och om det inte fungerar för er så gör ni fel :@</p>' . "\n";
	$out .= '<select style="margin: 2px;" id="send_select">' . "\n";
	$out .= '<option value="choose">Välj encoder/decoder</option>' . "\n";
	
	$options = array(
	'ip2host' => 'IP => Host',
	'ip2long' => 'IP => \'long\'',
	'long2ip' => '\'long\' => IP',
	'serialize2preint_r' => 'Serialized data string(?) bara Joel vet.',
	'md5' => 'Text => MD5',
	'sha1' => 'Text => SHA1',
	'hamsterpaj_password_hash' => 'Text => HP-password',
	'timestamp2readable' => 'Unixtid => Läsbar tid',
	'base64encode' => 'Text => Base64',
	'base64decode' => 'Base64 => Text'
	);
	
	foreach ($options as $value => $desc)
	{
		$out .= '<option value="' . $value . '">' . $desc . '</option>' . "\n";
	}
	
	$out .= '</select><br />' . "\n";
	$out .= 'Text: <br />' . "\n";
	$out .= '<input style="margin: 2px; float: left;" type="text" id="send_value" />' . "\n";
	$out .= '<button style="float: left;" onclick="" class="button_80" id="submit_button">Submit</button>';
	
	$out .= '<br style="clear: both" />' . "\n";
	$out .= 'Resultat:<br />' . "\n";
	$out .= '<textarea id="response_area" name="responseArea" style="width: 500px; height: 200px;" readonly="readonly"></textarea>' . "\n";
	
	ui_top($ui_options);
	echo rounded_corners(utf8_encode($out), $void, true);
	ui_bottom();
	
?>