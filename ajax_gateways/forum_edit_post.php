<?php
	
require('../include/core/common.php');
require_once(PATHS_INCLUDE . 'libraries/posts.php');
require_once(PATHS_INCLUDE . 'libraries/markup.php');
//require_once($hp_includepath . '/libraries/markup.php');
//require_once($hp_includepath . '/libraries/games.lib.php');
//require_once($hp_includepath . '/libraries/schedule.lib.php');
//require_once(PATHS_INCLUDE . 'libraries/tips.lib.php');


	preint_r($_GET);
	// HELL NO!
	// Med den här raden så kan man cracka 50% av hamsterpajs lösenord på några timmar.
	// I och med att den skriver ut sessionen så får man reda på sin hash, och skriver
	// man då ett program som byter lösenord <generalsettings.php> och hämtar hashen <$_SERVER['SCRIPT_NAME']> (FFS!)
	// Så kan man komma ganska långt. [reformaterat]
	// Hälsar LordDanne.
	// preint_r($_SESSION);

	

?>
