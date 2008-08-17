<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('hamsterpaj', 'om_hamsterpaj');
	
	require(PATHS_INCLUDE . 'libraries/articles.lib.php');
	$ui_options['stylesheets'][] = 'articles.css';
	
	$article = articles_fetch(array('id' => '63'));
	$out .= render_full_article($article);
	
/*	echo rounded_corners_top(array('color' => 'blue'));

<h1 style="margin-top: 0px">Information om hamsterpaj.net</h1>

<img src="/images/hamster.jpg" style="float: right;" />

<p class="intro">Här hittar du information om siten. Är det något annat du undrar om så föreslår vi att du skriver en rad i forumet under <a href="http://www.hamsterpaj.net/forum/hamsterpaj/allmaent_om_hamsterpaj/">Allmänt om Hamsterpaj.</a></a></p>

<h2>Kortfattat om vår verksamhet</h2>
<p>
Webbsiten hamsterpaj.net startades i Oktober 2003 och drivs av Hamsterpaj AB<br />
Vi tillhandahåller underhållning och tidsfördriv för ungdomar mellan 13 och 18 år, även om även äldre är välkomna. Utbudet består i huvudsak av flashfilmer, onlinespel, nedladdningsbara spel och filmklipp.<br />
Vi erbjuder även en community-tjänst där användare kan skapa sin egna sida och kommunicera med andra användare.<br />
Verksamheten finiansieras med annonspengar. 
</p>

<h2>Kontaktinformation</h2>
<p>
<strong>Vill du anmäla ett fel, föreslå spel eller filmer eller har problem med stulna bilder</strong><br />
Registrera dig som medlem och starta en tråd i diskussionsofrumet "Allmänt om Hamsterpaj".<br />
<strong>Är du journalist, polis, skriver en uppsats eller vill försöka sälja grejer</strong><br />
Johan Höglund är VD, C.E.O och tonåring. Det är bara att skicka ett mail till Johan@hamsterpaj.net eller att ringa 0768 999 580 om du har någon fråga.<br />
Det är ingen idé att klaga på visningsbilder, spel eller liknande hos Johan. Och förresten, vi vill inte köpa några mobiltjänster eller T-shirts med kul tryck.<br />
<strong>Vill du köpa annonser eller har frågor kring demografi och priser</strong><br />
Hamsterpaj arbetar sedan sommaren 2006 med Media Act som ensam säljpartner. Det betyder att alla frågor om annonsering hanteras av Jörgen eller Linda på Media Act. Du kan läsa med om Media Act på <a href="http://www.mediaact.se/">www.mediaact.se</a> eller maila direkt:<br />
Jorgen.Falk@mediaact.se<br />
Linda.Selenhag@mediaact.se<br />
</p>


<h2>Varför just hamsterpaj?</h2>
<p>
När klass 1A började på <a href="http://www.portalensgymnasium.se/" target="_blank">Portalens Gymnasium</a> sensommaren 2003 så stog det "MUSTERAPI" på tavlan.
Ingen visste vad ordet kom ifrån, men det stog där. Efter någon månad hade ordet ändrats till "MSTERPAJ".<br />
Då kläckte våran sötnos <a href="http://www.hamsterpaj.net/traffa/profile.php?id=1503">xianze</a> namnet "Hamsterpaj".<br />
Johan, som ville starta upp en ny site i stil med <a href="http://www.megadomain.net/" target="_blank">megadomain</a> nappade direkt. Namnet var taget och projektet drogs igång.
</p>

<h2>Hur vi arbetar</h2>
<p>
	Hamsterpaj hålls samman av ett stort nätverk individer som var för sig bidrar till helheten. Vi har tagit fram ett avancerat incitaments- och
	uppföljningssystem där vi kontrollerar alla steg i värdekedjan och gör punktinsatser för att öka profiten och bibehålla lönsamheten. Vill du se
	en förenklad schematisk bild över vårt arbete så återfinns en sådan i <a href="/mattan/hamsterpajfabriken.php">Hamsterpajfabriken</a>.
</p>
<br />
<?php
	echo rounded_corners_bottom(array('color' => 'blue'));
	*/
	
	ui_top($ui_options);
		echo $out;
	ui_bottom();
?>
