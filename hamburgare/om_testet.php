<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('annat', 'hamburgare', 'om_testet');
	$ui_options['title'] = 'Gissa Hamburgaren på Hamsterpaj';
	$ui_options['stylesheets'][] = 'burgers.css';
	$ui_options['menu_addition']['annat']['children']['hamburgare']['children']['test'] = array('label' => 'Gör testet', 'url' => '/hamburgare/test.php');
	$ui_options['menu_addition']['annat']['children']['hamburgare']['children']['alla_burgare'] = array('label' => 'Alla burgare', 'url' => '/hamburgare/alla_burgare.php');
	$ui_options['menu_addition']['annat']['children']['hamburgare']['children']['om_testet'] = array('label' => 'Om testet', 'url' => '/hamburgare/om_testet.php');					
	ui_top($ui_options);
?>

	<img src="http://images.hamsterpaj.net/hamburgers/pommesfrittes.jpg" style="float: right;" />

	<h1>Fina bilder men fula burgare!</h1>
	<p>
		Inspirerade av blogginlägget <a href="http://www.thewvsr.com/adsvsreality.htm" target="_blank">Fast Food: Ads vs. Reality</a> bestämde vi oss för att göra en jämförelse. Vi handlade totalt fjorton hamburgare från McDonalds, Burger King, Max Hamburgare och Sibylla. Sedan fotograferade vi alla burgarna.
	</p>
	<h3>Så här gick vi till väga</h3>
	<ul>
		<li>Hamburgarna köptes som "take away" och  förvarades i påsen som resturangen gav oss</li>
		<li>Alla hamburgare fotograferades inom en timma från köpet</li>
		<li>Vi har inte mosat eller förstört hamburgarna på något sätt</li>
		<li>Under fotograferingen använde vi oss av gråkort för att få exakt rätt färger</li>
		<li>Bilderna togs mot vit bakgrund, vi använde oss av en systemkamera och två studioblixtar</li>
		<li>Vi har inte ändrat bilderna i datorn, annat än att justera färgerna mot gråkortet</li>
	</ul>


	<h2>Vart tog den gröna, fina salladen vägen?</h2>
	<p>
		På reklambilderna innehåller de flesta hamburgarna grön, vågig och fräsh sallad. Det står att det är isbergssallad, fast de flesta av oss vet att isbergssallad inte ser ut sådär.
	</p>

	<img src="http://images.hamsterpaj.net/hamburgers/gruppfoto_638.jpg" />

	<h2>Får hamburgerkedjornas luras såhär?</h2>
	<p>
		Vi är inga jurister, men att läsa lagen på riksdagen.se är ganska enkelt. Fram tills 1995 var det helt klart förbjudet att luras på det här sättet, då stog det såhär i lagen:
	</p>
	<blockqoute>
		Näringsidkare som vid marknadsföring av vara, tjänst eller annan
		nyttighet uppsåtligen använder vilseledande framställning, vilken avser
		hans egen eller annans näringsverksamhet och är ägnad att påverka
		efterfrågan på nyttigheten, dömes till böter eller fängelse i högst ett
		år.
	</blockqoute>
	
	<p>
		Sedan ändrade man lagen och nu är det inte lika enkelt att förstå vad som är tillåtet och inte.<br />Såhär står det i <a href="http://riksdagen.se/webbnav/index.aspx?nid=3911&bet=1995:450" target="_blank">Marknadsföringslag (1995:450)</a>
	</p>

	<blockqoute>
		6 § En näringsidkare får vid marknadsföringen inte använda
		påståenden eller andra framställningar som är vilseledande i fråga om
		näringsidkarens egen eller någon annan näringsidkares
		näringsverksamhet.<br />
		<br />
		Detta gäller särskilt framställningar som avser<br />
		<br />
		1. produktens art, mängd, kvalitet och andra egenskaper,<br />
		<br />
		2. produktens ursprung, användning och inverkan på hälsa eller
		miljö,<br />
		<br />
		3. produktens pris, grunderna för prissättningen och
		betalningsvillkoren,<br />
		<br />
		4. näringsidkarens egna eller andra näringsidkares kvalifikationer,
		ställning på marknaden, kännetecken och andra rättigheter,<br />
		<br />
		5. belöningar och utmärkelser som har tilldelats näringsidkaren.
	</blockqoute>


	<h2>Inköpsställen</h2>
	<ul>
		<li>Burger King, Järntorget i Göteborg</li>
		<li>Sjuans Gatukök (Sibylla) på Vasagatan i Göteborg</li> 
		<li>Max Hamburgare, Allum köpcenter, Partille utanför Göteborg</li>
		<li>McDonalds Partille</li>
	</ul>

<?php

	event_log_log('burgers_about');
	ui_bottom();
?>


