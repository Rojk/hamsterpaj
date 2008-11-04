<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
	
	$uri_parts = explode('/', $_SERVER['REQUEST_URI']);
	
	$out .= '<ul>
	<li>
		<a href="/fotoblogg/">Min dagbok</a>
	</li>
	<li>
		<a href="/fotoblogg/Lef/">Lefs dagbok</a>
	</li>
	<li>
		<a href="/fotoblogg/ladda_upp/">Ladda upp</a>
	</li>
	<li>
		<a href="/fotoblogg/instaellningar/">Inställningar</a>
	</li>
	</ul>
	
	' . "\n";
	
	switch ($uri_parts[2])
	{
		case 'instaellningar':
			$out .= '<h2>INSTÄLLNINGAR</h2>' . "\n";
		break;
		
		case 'ladda_upp':
			$out .= '<h2>LADDA UPP!</h2>' . "\n";
		break;
			
		default:
			$out .= 'Välkommen till ' . "\n";
			$out .= preg_match('/s$/', $uri_parts[2]) ? $uri_parts[2] : $uri_parts[2] . 's';
			$out .= ' fotoblogg!';
		break;
	}
	$out .= '<br /><br />' . preint_r($uri_parts);
	/*
	/fotoblogg/user/
	/fotoblogg/instaellningar
	/fotoblogg/ladda_upp
	/fotoblogg <- sin egen
	*/
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
