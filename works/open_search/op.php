<?php

	require('/home/www/standard.php');
	require_once(PATHS_LIBRARIES . 'open_search.lib.php');

	if(isset($_GET['type'], $_GET['q'], $_GET['search']))
	{
		open_search_execute($_GET['q'], $_GET['type']);
	}
	elseif(isset($_GET['type']))
	{
		echo open_search_make_box($_GET['type']);
	}
	else
	{
		$ui_options['title'] = 'Enklare sökning på Hamsterpaj';
		$ui_options['stylesheets'][] = 'open_search.css';
		$ui_options['javascripts'][] = 'open_search.js';
		$ui_options['menu_path'] = array('hamsterpaj');
		$ui_options['header_extra'] = open_search_list_head();
		ui_top($ui_options);
		
		if(strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7') === false && strstr($_SERVER['HTTP_USER_AGENT'], 'Firefox') === false)
		{
			echo rounded_corners_top(array('color' => 'red'), true);
			echo '<img src="'.IMAGE_URL.'firefox.png" alt="Firefox" />'."\n";
			echo '<h2 style="padding-left: 110px;display:inline;">Du använder fel webbläsare</h2>'."\n";
			echo '<p>Söklådorna fungerar endast i Firefox och Internet Explorer 7 eller högre. Bara så att du vet!</p>'."\n";
			echo '<img src="'.IMAGE_URL.'ie7.png" style="float:right;" alt="Internet Explorer 7" />'."\n";
			echo '<br style="clear:both;" />';."\n"
			echo rounded_corners_bottom(array('color' => 'red'), true);
		}
		
		echo '<h1>Enklare sökning på Hamsterpaj</h1>'."\n";
		echo '<h2>Vad är söklådor?</h2>'."\n";
		echo '<p>Söklådor heter egentligen "Sökmotorer" enligt firefox men eftersom det är lite vilseledande så kallar vi det för söklådor.</p>'."\n";
		echo '<p style="float:left;"><img src="'.IMAGE_URL.'open_search_screenshot.png" style="float:right;" />En söklåda är boxen uppe till höger om adressfältet i Firefox eller Internet Explorer 7 (eller högre). Den gör så att du enklare och snabbare kan söka på hamsterpaj. T.ex. om du är inne i filmklipp och vill söka efter roliga spel måste du då navigera dig till roliga bilder och sedan där söka. Med söklådor är det mycket enklare. Du väljer bara "Bilder - HP" i, skriver i vad du vill söka efter och trycker på Enter.</p>'."\n";
		echo '<br style="clear:both;" />'."\n";
		
		echo '<h2>Välj vilken typ av sökruta du vill ha</h2>'."\n";
		echo open_search_boxes_list();		
		ui_bottom();
	}


?>