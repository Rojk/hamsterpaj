<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/open_source.lib.php');
	
	$open_source_config['open_source_menu_path'] = 'theme_creation';
	$open_source_config['title'] = 'Skapa egna profilteman, en open source-relaterad sida på Hamsterpaj.net';
	open_source_top($open_source_config);
?>

<h1>Skapa egna profilteman - howto</h1>
<p>
	Alla teman som görs blir tillgängliga för alla användare, och dessa teman måste godkännas först. Det går inte att göra ett eget tema och ha det för sig själv.
</p>

<p>
	Just nu kan teman endast ändra utseende på "profile_head", den navigationsmodul där användarens foto och medlemsinformation visas. Eventuellt kommer detta ändras senare.
	När man ändrar ett tema får man endast ändra färger, och i viss mån även dimensioner. Det är inte tillåtet att ta bort eller lägga till delar eller drastiskt förändra strukturen på
	"profile_head". Storleken på "profile-head" får inte ändras en enda pixel.
</p>

<h2>Redigera bakgrund</h2>
<p>
	Bakgrunden kan ändras hur som helst, de runda hörnen eller kantlinjerna behöver inte vara kvar som de är. En bra bild att utgå ifrån är silver-temats bakgrund: <a href="http://images.hamsterpaj.net/profile_themes/silver/profile_head_bg.png">http://images.hamsterpaj.net/profile_themes/silver/profile_head_bg.png</a>.
	Tänk på att allt för mycket bilder, skrikiga färger eller för kraftigt mönster gör texten svårläst, och minskar chansen för att ditt tema ska godkännas.<br />
	Spara din bakgrundsbild som en PNG-bild med låg eller ingen komprimering!
</p>

<h2>CSS-redigering</h2>
<p>
	Med varje tema följer en css-fil, här är det möjligt att anpassa textfärg och en del andra egenskaper. CSS-redigering kräver att du kan lite om webbutveckling. Har du gjort en bra bakgrund men är
	osäker på hur du hanterar CSS så kan du få hjälp av någon i Open Source-forumet, glöm bara inte att visa din bakgrundsbild!<br />
	Den enda CSS-information som måste finnas med är:
	<pre>
.profile_head_TEMA .profile_head
{
	background: url('http://images.hamsterpaj.net/profile_themes/TEMA/profile_head_bg.png');
}
	</pre>
	Ersätt TEMA med namnet på ditt tema, ett tema-namn är kort, på engelska, utan specialtecken och med blanksteg ersatta med understreck. Exempelvis blue_flowers.
</p>

<h2>Uppladdning</h2>
<p>
	<a href="/traffa/profile.php?id=625058">lef-91</a> är tema-ansvarig, skicka ett gästboksinlägg till honom med länk till din tema-bild, din css-fil och önskat namn på temat.
</p>

<h2>Upphovsrätt</h2>
<p>
	Rent juridiskt har du en himlans massa rättigheter, men vi orkar inte riktigt med det trasslet. Hela Hamsterpaj är Open Source och det mesta materialet får användas fritt. Skickar du in en bild får du finna dig i
	att vi använder den och eventuellt redigerar den. Vi tillåter inte teman där ditt namn skrivits in i bilden (eller ja, gör du det i foto-arean kan det vara ok). Vi betalar inte för teman och
	vi har idag inget system för att hålla reda på vem som gjort vad.
</p>

<?php
	open_source_bottom($open_source_config);
?>