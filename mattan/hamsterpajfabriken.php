<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('mattan', 'hamsterpajfabriken');
	$ui_options['title'] = 'Hamsterpajfabriken - där Hamsterpaj tillverkas!';
	ui_top($ui_options);
?>
<h1>Hamsterpajfabriken</h1>
<p>
		Hamsterpaj hålls samman av ett stort nätverk individer som var för sig bidrar till helheten. Vi har tagit fram ett avancerat incitaments- och
	uppföljningssystem där vi kontrollerar alla steg i värdekedjan och gör punktinsatser för att öka profiten och bibehålla lönsamheten.
	Här nedan visas en något förenklad, schematisk bild över hur vi arbetar.
</p>
<?php	
	echo '<img src="http://images.hamsterpaj.net/hamsterpajfabriken.gif" />' . "\n";
	echo '<img src="http://images.hamsterpaj.net/hamsterpajfabriken.gif" />' . "\n";
	echo '<img src="http://images.hamsterpaj.net/hamsterpajfabriken.gif" />' . "\n";

	echo 'Ja, vi skojar, nej vi vet inte vad en värdekedja är för något, men vi har hört kostymnissarna prata om det.';

	ui_bottom();
?>


