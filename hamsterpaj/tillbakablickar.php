<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('hamsterpaj', 'tillbakablickar');
	ui_top($ui_options);
	
	echo '<h1>En god portion nostalgi</h1>' . "\n";
	echo '<p><a href="/traffa/profile.php?id=3">Johan</a> som startade Hamsterpaj har försökt komma ihåg att ta screenshots på sidan lite då och då. Så här i efterhand kan man önska att det hade blivit fler, stora delar 2004 och 2005 är som svarta hål. Sitter du på gamla screenshots från Hamsterpaj? Kanske har du en från Emoland-skämtet? Maila till Johan@hamsterpaj.net!</p>' . "\n";
	
	$screens[1] = 'Oktober 2003';
	$screens[2] = 'Oktober 2003';
	$screens[3] = 'Oktober 2003';
	$screens[4] = '16 oktober 2003';
	$screens[5] = '11 maj 2004';
	$screens[6] = '7 augusti 2005';
	$screens[7] = '3 mars 2006';
	$screens[8] = '18 september 2006';
	$screens[9] = '14 januari 2007';
	$screens[10] = '31 januari 2007';
	$screens[11] = '25 februari 2007';
	$screens[12] = '3 mars 2007';
	$screens[13] = 'Mitten av mars 2007';
	$screens[14] = '19 augusti 2007';
	$screens[15] = '6 oktober 2007';
	$screens[16] = '30 oktober 2007';
	$screens[17] = '24 februari 2008';
	$screens[20] = '5 september 2008';
	$screens[21] = '12 december 2008';
	
	echo '<ol>' . "\n";
	
	foreach($screens AS $id => $date)
	{
		echo '<li style="margin-top: 10px;"><a href="/tillbakablickar/hamsterpaj' . $id . '.jpg" target="_blank">' . $date . '</a></li>' . "\n";
	}
	
	echo '</ol>' . "\n";
	
	ui_bottom();
?>