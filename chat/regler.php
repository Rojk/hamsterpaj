<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('chatt', 'regler');
	$ui_options['title'] = 'Regler för hamsterpajchatten';
	
	if(isset($_SESSION['new_design']))
	{
		unset($_SESSION['new_design']);
	}
	else
	{
		$_SESSION['new_design']  = true;
	}
	
	ui_top($ui_options);
?>

<h1>Regler för Hamsterpajs chatt</h1>

<p>
	Personer med <strong>%</strong> eller <strong>@</strong> framför namnet är ordningsvakter. Om
	någon sådan säger åt dig så lyd, annars riskerar du avstäning.<br />
	Egentligen handlar de här reglerna bara om sunt förnuft, men ha i bakhuvudet att <strong>tillgång till
	hamsterpajs chatt är ett privilegium - inte en rättighet</strong>!
</p>

<h2>Regler som gäller i alla kanaler</h2>
<ul>
	<li>Be inte andra skriva tangentkombinationer för att chatta med dig, exempelvis:
		<ul>
			<li><em>Någon som vill chata skriv 123!</em></li>
			<li><em>Hej, skriv ABC för att chatta med mig!</em></li>
		</ul>
		</li>
	<li>Fråga inte om någon vill se din snopp eller snippa i webcam, fråga inte om du får se någon
			annans snopp/snippa heller! Det här gäller både i kanalerna och privat!
	</li>
	<li>Förstör inte genom att skriva strunt eller många rader snabbt (spamma)</li>
	<li>Låt bli CAPS LOCK, fyll inte ut dina inlägg med utropstecken, frågetecken eller onödigt många
	bokstäver i rad.</li>
	<li>MP3-scripts, awayscripts eller andra scripts som generar onödig text är förbjudna. (Det här
	gäller bara för mer avancerade användare som kör egna IRC-klienter).</li>
	<li>Inga awaynicks eller onödiga nickbyten.</li>
	<li></li>
	<li>Var inte otrevligt på något sätt.</li>
</ul>

<p>
	Skulle du mot förmodan stöta på en misstänkt pedofil så kontakta Soode, antingen via chatten eller här på Hamsterpaj!
</p>

<h2>Specialregler, #moget</h2>
<ul>
	<li>Femton års åldersgräns!</li>
	<li>Inga java-klienter.</li>
</ul>

<h2>Om du blir felaktigt avstängd</h2>
<p>
	Det händer sällan att ordningsvakter gör fel, men det kan inträffa.<br />
	Om du tycker dig ha blivit felaktigt avstängd så skall du kontakta <a href="/traffa/profile.php?id=299825">Soode</a>
	på Hamsterpaj. Skicka ett privat meddelande där du talar om:
	<ul>
		<li>Vad du hette på chatten</li>
		<li>Vilken kanal du blivit utkastad från</li>
		<li>Hur mycket klockan var, så exakt som möjligt</li>
		<li>Vem som kastade ut dig, om du vet detta</li>
		<li>En förklaring till vad som hänt</li>
	</ul>
</p>

<?php
	ui_bottom();
?>
