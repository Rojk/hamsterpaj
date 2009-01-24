<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('installningar', 'avregistrera');
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';
	
	if(login_checklogin() != 1)
	{
		header('location: /?rm=1');
		die();
	}

	if($_GET['verify'] == 1)
	{
		if (hamsterpaj_password(utf8_decode($_POST['password'])) == $_SESSION['login']['password']) {
			login_remove_user($_SESSION['login']['id']);
			$_SESSION = null;
			session_destroy();
			header('Location: /index.php');
			die();
		}
		else {
			echo '<script>alert(\'Fel lösenord!\');</script>';
		}
	}

	ui_top($ui_options);

	$output .= rounded_corners_tabs_top();
	echo '<h1 style="margin-top: 0;">Avregistrering</h1>';
	echo '<p>Det finns möjligheten att ta bort sig från hamsterpaj. Trycker du på "Ta bort mig" här nedan finns ingen återvändo!</p>';
	
	echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?verify=1"';
	echo ' onsubmit="return confirm(\'Du är på väg att ta bort dig från hamsterpaj.net Vill du fortsätta?\');">';
	echo 'Skriv in ditt lösenord för att ta bort ditt konto:<br />';
	echo '<input type="password" name="password" class="textbox"><br />';
	echo '<br /><input type="submit" value="Ta bort mig" class="button_90">';
	echo '</form>';
	$output .= rounded_corners_tabs_bottom();

	ui_bottom();
?>
