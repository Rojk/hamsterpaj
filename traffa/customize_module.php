<?php
	
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'profile-modules.php');
  $ui_options['menu_path'] = array('traffa');

	ui_top($ui_options);

	$profile_modules = explode(',', $_SESSION['traffa']['profile_modules']);
	$profile_modules[] = 18; //Everyone has the flag module

	if(!isset($_GET['id']) || !is_numeric($_GET['id']))
	{
		echo '<h2>Hacker!</h2>';
	}
	else if (!in_array($_GET['id'], $profile_modules))
	{
		echo '<h2>Du har inte aktiverat modulen ' . $modules[$_GET['id']]['title'] . '</h2>';
		echo '<p>Klicka <a href="/traffa/profile.php?id=' . $_SESSION['login']['id'] . '#modules">här</a> för att komma till din profil och modulväljaren.';
		echo ', välj modulen och tryck på spara så är det klart!</p>';
	}
	else if($_SESSION['login']['id'] > 0)
	{
		include(PATHS_INCLUDE . 'profile_modules/' . $modules[$_GET['id']]['filename'] . '-customize.php');
	}
	else
	{
		echo 'Du måste vara inloggad för att komma åt denna sida!';
	}
	ui_bottom();
?>
