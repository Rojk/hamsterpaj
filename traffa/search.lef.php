<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('traeffa', 'soek');	
	$ui_options['stylesheets'][] = 'search.lef.css';
	
	$out .= rounded_corners_top(array(orange_full), true);
	$out .= '<h1>Sök användare på Hamsterpaj</h1>' . "\n";
	$out .= '<p>Nu kan du söka användare på Hamsterpaj med ett helt nytt gränssnitt. Förhoppningsvis ska du kunna träffa några nya trevliga kompisar.</p>' . "\n";
	$out .= rounded_corners_bottom(array(orange_full), true);

	$out .= rounded_corners_top(array(orange_full), true);
	$out .= '<h1>Sök användare via användarnamn</h1>' . "\n";
	$out .= '<form name="username" action="" method="post">' . "\n";
	$out .= 'Användarnamn: <input type="text" name="username" />' . "\n";
	$out .= '<input name="submit" type="submit" value="Sök!" class="button_50" />' . "\n";
	$out .= '</form>' . "\n";
	$out .= rounded_corners_bottom(array(orange_full), true);

	$out .= '<div id="searchmap">Sök användare via karta.</div>' . "\n";
	
	$out .= '<div id="sex">' . "\n";
	$out .= '<div id="male">Pojkar</div>' . "\n";
	$out .= '<div id="female">Flickor</div>' . "\n";
	$out .= '</div><br style="clear: both;" />' . "\n";
	
	$out .= '<div id="age">Sök användare efter ålder</div>' . "\n";
	$out .= '<div id="image">Välj om du vill lista användare utan bild</div>' . "\n";
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
