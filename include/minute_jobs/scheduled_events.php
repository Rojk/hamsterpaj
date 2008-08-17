<?php
	if(isset($_GET['manual']))
	{
		include('/storage/www/standard.php');
		include(PATHS_INCLUDE . 'online_people_generator.php');
		include(PATHS_INCLUDE . 'snyggve_gbc.php');	
	}
	if(isset($_GET['debug_include_standard'])){
		include('/storage/www/standard.php');
	}
//	require_once($hp_includepath . '/libraries/games.lib.php');
	require_once($hp_includepath . '/libraries/schedule.lib.php');

	schedule_releases_do();

?>
