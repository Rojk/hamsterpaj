<?php

	require('/home/www/standard.php');
	$ui_options['current_menu'] = 'hamsterpaj';
	
	if(login_checklogin() != 1)
	{
		header('location: /?rm=1');
		die();
	}


	if($_GET['verify'] == 1)
	{
		if (sha1(utf8_decode($_POST['password']) . PASSWORD_SALT) == $_SESSION['login']['password_hash']) {
			login_remove_user($_SESSION['login']['id']);
			$_SESSION = null;
			session_destroy();
			header('location: /msg.php?message=remove_success');
			die();
		}
		else {
			echo '<script>alert(\'Fel lösenord!\');</script>';
		}
	}

	ui_top($ui_options);
	
	echo '<h1>Avregistrering</h1>';
	echo '<p>Nu finns möjligheten att ta bort sig från hamsterpaj. Trycker du på "Ta bort mig" här nedan finns ingen återvändo! :O</p>';
	
	echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?verify=1"';
	echo ' onsubmit="return confirm(\'Du är på väg att ta bort dig från hamsterpaj.net Vill du fortsätta?\');">';
	echo 'Skriv in ditt lösenord för att ta bort ditt konto:<br />';
	echo '<input type="password" name="password" class="textbox">';
	echo '<br /><input type="submit" value="Ta bort mig" class="button_90">';
	echo '</form>';
//	echo '<a href="' . $_SERVER['PHP_SELF'] . '?verify=1"';
//	echo ' onclick="return confirm(\'Du är på väg att ta bort dig från hamsterpaj.net Vill du fortsätta?\');">Ta bort mig</a>';


	ui_bottom();
?>
