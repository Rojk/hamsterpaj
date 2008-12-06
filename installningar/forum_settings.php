<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	$ui_options['menu_path'] = array('installningar','forum_installningar');

	$ui_options['stylesheets'][] = 'settings.css';
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';

	$ui_options['title'] = 'Ändra dina forum-inställningar på hamsterpaj.net';

	if(login_checklogin() != 1)
	{
		jscript_alert('Du måste vara inloggad för att komma åt denna sidan!');
		jscript_location('/');
		die();
	}

	if($_GET['action'] == 'perform_changes')
	{
			$newdata['preferences']['forum_enable_smilies'] = ($_POST['forum_enable_smilies'] == 0) ? 0 : 1;
			$newdata['preferences']['forum_subscribe_on_create'] = ($_POST['forum_subscribe_on_create'] == 0) ? 0 : 1;
			$newdata['preferences']['forum_subscribe_on_post'] = ($_POST['forum_subscribe_on_post'] == 0) ? 0 : 1;
		
		login_save_user_data($_SESSION['login']['id'], $newdata);
		session_merge($newdata);
		jscript_alert('Ändrat, fixat och donat :)');
		jscript_location($_SERVER['PHP_SELF']);
	}

/* Frivillig information */

	$out .= rounded_corners_tabs_top($void, true);
	$out .= '<h2 style="margin-top: 0px;">Foruminställningar</h2>' . "\n";
	$out .= '<form action="' . $_SERVER['PHP_SELF'] . '?action=perform_changes" method="post" name="forum_settings">';
	$out .= '<strong>Smileysar i forumet</strong><br />'."\n";
	$out .= 'Vill du visa smileysar i forumet för forumkategorier med modereringsnivåer under "Normal"? <input type="checkbox" name="forum_enable_smilies" value="1" ' . ($_SESSION['preferences']['forum_enable_smilies'] == '1' ? 'checked="checked" ' : '') . ' />' . "\n";
	$out .= '<br />';
	$out .= '<br />';
	$out .= '<strong>Bevaka trådar du skapat</strong><br />'."\n";
	$out .= 'Vill du automagiskt bevaka trådar du skapat? <input type="checkbox" name="forum_subscribe_on_create" value="1" ' . ($_SESSION['preferences']['forum_subscribe_on_create'] == '1' ? 'checked="checked" ' : '') . ' />' . "\n";
	$out .= '<br />';
	$out .= '<br />';
	$out .= '<strong>Bevaka trådar som du skrivit i</strong><br />'."\n";
	$out .= 'Vill du automagiskt bevaka trådar som du har skrivit i? <input type="checkbox" name="forum_subscribe_on_post" value="1" ' . ($_SESSION['preferences']['forum_subscribe_on_post'] == '1' ? 'checked="checked" ' : '') . ' />' . "\n";
	$out .= '<br />';
	$out .= '<br />';
	$out .= '<br />';
	$out .= '<input type="submit" value="Spara inställningar &raquo;" class="button_150" id="forum_settings_submit" />';
	$out .= '</form>' . "\n";
	$out .= rounded_corners_tabs_bottom($void, true);

	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
