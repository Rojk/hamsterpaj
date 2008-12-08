<?php
	require('include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');

	$ui_options['javascripts'][] = 'start.js';

	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'start.css';

	$ui_options['title'] = 'Startsidan på Hamsterpaj';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';

	$fp_modules = cache_load('fp_module_order');
	
	foreach($fp_modules AS $filename)
	{
		$module = cache_load('fp_module_' . $filename);
		if($module['display'] != 1)
		{
			continue;
		}

		if($module['phpenabled'] == 1)
		{
			include(PATHS_INCLUDE . 'fp_modules/' . $filename);
		}
		else
		{
			$output .= file_get_contents(PATHS_INCLUDE . 'fp_modules/' . $filename);
		}
		
		foreach($module['stylesheets'] AS $css)
		{
			if(!empty($css))
			{
				$ui_options['stylesheets'][] = $css;
			}
		}
	}
	
	$ui_opions['stylesheets'] = array_unique($ui_options['stylesheets']);
	
	if($_SESSION['login']['id'] == 3)
	{
		$ui_options['xxl'] = '<div style="margin: 10px;">
	<h1 style="font-family: verdana, arial; background: black; margin: -10px; margin-bottom: 10px; text-style: italic; font-size: 35px; font-weight: bold; padding: 10px;"><a style="color: white;" href="http://www.icloud.com/hamsterpaj/">Kolla in Hamsterpaj-grundarens nya jobb!</a></h1>
	<h1 style="font-size: 42px; font-family: verdana, arial;""><a href="http://www.icloud.com/hamsterpaj/" style="text-decoration: underline;">Testa icloud - vinn en ASUS Eee PC! &raquo;</a></h1>
	
	<img src="http://icloud.t67.se/eeepc.jpg" style="float: right; margin-right: -10px;" />
	
	<p style="font-size: 15px;">
		Tänk dig en framtid där du kommer åt ditt skrivbord, dina filer, dina bilder, dina skolarbeten och dina vänner - från vilken dator som helst.
		Drömmen kallas Cloud Computing, xcerion AB i Linköping är ett av de bolag i världen som kommit längst med att förverkliga den.<br />
		Nu vill vi testa hur bra icloud funkar med riktiga användare, fastän vi inte är riktigt klara. Än så länge fungerar icloud bara i Internet Explorer, 
		men vi jobbar hårt med att få Firefox-stöd till mars 2009.
	</p>
	<p style="font-size: 15px;">
		Ta chansen att få en försmak på <a href="http://en.wikipedia.org/wiki/Cloud_computing" target="_blank">cloud computing</a> och framtiden! Delta i tävlingen 
		och du kan bli en av tre lyckliga vinnare. Varje dag lägger vi upp ett nytt foto i icloud, ditt uppdrag är att försöka gissa vart i världen
		fotot är taget. De som varit bäst på att gissa efter två veckor vinner varsin dator!
	</p>
	
	</div>
	<br style="clear: both;" />
		';
	}	
	ui_top($ui_options);
	echo $output;
	ui_bottom();
	?>
