<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('mattan');
	$ui_options['stylesheets'][] = 'under_mattan.css';
	ui_top($ui_options);

	echo '<h1 style="margin-top: 0;">Under mattan - Din nöjesdel</h1>' . "\n";

	$output .= rounded_corners_top(array('color' => 'blue'));
		$info = '<div class="mattan">' . "\n";
		$info .= '<a href="/mattan/koerkort.php">' . "\n";
		$info .= '<img src="http://images.hamsterpaj.net/drivers-license/question_illustrations/17.jpg" />' . "\n";
		$info .= '</a>' . "\n";
		$info .= '<div>' . "\n";
		$info .= '<h2>Öva på trafikregler!</h2>' . "\n";
		$info .= 'Hamsterpaj har ett gratis teoriprogram för dig som ska ta körkort. <br />Knappt 200 frågor, många med bilder till, systemet håller koll på vilka frågor du kan och vilka du behöver öva mer på.<br /><a href="/mattan/koerkort.php">Börja öva »</a>' . "\n";
		$info .= '<br style="clear: both;" /></div></div>' . "\n";
		echo $info;
	$output .= rounded_corners_bottom(array('color' => 'blue'));

	$output .= rounded_corners_top(array('color' => 'blue'));
		$info = '<div class="mattan">' . "\n";
		$info .= '<a href="/mattan/bakgrundsbilder.php">' . "\n";
		$info .= '<img src="http://images.hamsterpaj.net/mattan/maas.png" />' . "\n";
		$info .= '</a>' . "\n";
		$info .= '<div>' . "\n";
		$info .= '<h2>Bakgrundsbilder</h2>' . "\n";
		$info .= 'Massor med häftiga bakgrundsbilder, ladda ner och använd. så enkelt är det.
		<a href="/mattan/bakgrundsbilder.php">Pimpa mitt skrivbord! »</a>' . "\n";
		$info .= '<br style="clear: both;" /></div></div>' . "\n";
		echo $info;
	$output .= rounded_corners_bottom(array('color' => 'blue'));

	$output .= rounded_corners_top(array('color' => 'blue'));
		$info = '<div class="mattan">' . "\n";
		$info .= '<a href="/mattan/ascii_art.php">' . "\n";
		$info .= '<img src="http://images.hamsterpaj.net/mattan/bart.png" />' . "\n";
		$info .= '</a>' . "\n";
		$info .= '<div>' . "\n";
		$info .= '<h2>ASCII-art</h2>' . "\n";
		$info .= 'Massor med "ASCII"-bilder, Det är bilder som är ritade med bokstäver!<br />
		Du kan rita din egen bild också :)<br />
		<a href="/mattan/ascii_art.php">Mot ASCII och vidare! »</a>' . "\n";
		$info .= '<br style="clear: both;" /></div></div>' . "\n";
		echo $info;
	$output .= rounded_corners_bottom(array('color' => 'blue'));

	$output .= rounded_corners_top(array('color' => 'blue'));
		$info = '<div class="mattan">' . "\n";
		$info .= '<a href="/mattan/gratis_musik.php">' . "\n";
		$info .= '<img src="http://images.hamsterpaj.net/mattan/trod.png" />' . "\n";
		$info .= '</a>' . "\n";
		$info .= '<div>' . "\n";
		$info .= '<h2>Ingen våldsironi - för Counter-Strike är ju verklighet</h2>' . "\n";
		$info .= 'Ladda hem T-röd - Jag är en tönt som spelar Counter-Strike, musik från Beastie Boys, Fronda, Dia Psalma med flera.
		Snabbt och gratis - helt lagligt! <br />
		<a href="/mattan/gratis_musik.php">Till gratismusiken »</a>' . "\n";
		$info .= '<br style="clear: both;" /></div></div>' . "\n";
		echo $info;
	$output .= rounded_corners_bottom(array('color' => 'blue'));

	echo $output;


// Lite skit:) feel free to erase.
/*echo rounded_corners_top(array('color' => 'orange_deluxe'), true);
echo '<p>You better run, you better take cover</p>';
echo rounded_corners_bottom(array('color' => 'orange_deluxe'), true);*/
?>

<?php
	ui_bottom();
?>
