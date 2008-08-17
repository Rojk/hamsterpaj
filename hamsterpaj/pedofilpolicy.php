<?php
	require('../include/core/common.php');
	$ui_options['title'] = 'Pedofilpolicy';

	$ui_options['menu_path'] = array('hamsterpaj', 'pedofilpolicy');
	
	require(PATHS_INCLUDE . 'libraries/articles.lib.php');
	$ui_options['stylesheets'][] = 'articles.css';
	
	$article = articles_fetch(array('id' => '64'));
	$out .= render_full_article($article);
	
/*	echo rounded_corners_top(array('color' => 'blue'));

<h1 style="margin-top: 0;">Hamsterpaj tar ställning mot barnpornografi och pedofili</h1>

<p>
	På senare tid har vuxnas sexuella kontakter med barn över Internet uppmärksammats som ett problem.
	Vi vet idag inte hur omfattande problemet är, men har ändå valt att försöka förhindra att det förekommer
	på Hamsterpaj.<br />
	Vår utgångspunkt är att vuxna som inleder en sexuell relation med ett barn utsätter barnet för lidande
	och riskerar att sätta djupa sår hos barnet för resten av livet.
</p>

<h2>Förbud att söka kontakt med barn i relationsbyggande syfte</h2>
<p>
	Personer som fyllt 20 år får inte söka kontakt med barn och unga under 15 år relationsbyggande syfte.<br />
	Den som bryter mot denna enkla regel riskerar att, utan förvarning eller tillsägelse, slängas ut från Hamsterpaj
	och därmed förlora sitt användarkonto. Hamsterpajs representanter väljer själva när och om någon som bryter mot denna
	regel ska stängas av.<br />
</p>

<h2>Spridande eller innehav av barnpornografi strider mot svensk lag</h2>
<p>
	Vi har aldrig någonsin stött på  barnpornografi på Hamsterpaj, skulle vi någon gång råka ut för det så raderas det
	omedelbums och IP-nummer plus all annan relevant information skickas upp till rikskrim.
</p>

<!--
<h2>Det fria ordet, rätten att tänka, tycka och argumentera</h2>
<p>
	Många samlingsplatser på nätet går mot paranoia och filtrerar bort eller stänger ner obekväma eller kontroverisella
	diskussioner. Det vill vi undvika, vi tror på det fria ordets rätt att existera, vi tror att man ska få lov att
	tycka, tänka och argumentera för vad man vill så länge som
	<ul>
		<li>ingen kränks</li>
		<li>det inte uppmanas till brott</li>
		<li>information som leder till att barn och unga i oförstånd riskerar att skada sig själv eller andra sprids (hit räknas bombrecept och tips om droger)</li>
	</ul>

	<h3>Som exempel kan nämnas att följande är okej att hävda</h3>
	<ul>
		<li>Jorden är platt</li>
		<li>Gud existerar (<a href="/forum_new/index.php?action=view_thread&thread_id=109990">diskutera</a>)</li>
		<li>Den ariska rasen är överlägsen alla andra raser</li>
		<li>Kvinnor bör inte få köra bil och skall giftas bort före 18 års ålder</li>
		<li>Sexuellt umgänge med barn botar HIV</li>
	</ul>
	Vi tror att ovanstående <strong>dumheter</strong> ska bemötas med fakta och argument, istället för att ignoreras.
</p>
-->
<?php

<h2>Hamsterpaj saknar åldersgräns</h2>
<p>
	Vi bygger Hamsterpaj för tonåringar, men den är öppen för alla. Vi tror att en del material kan vara
	olämpligt för barn under 13 år och avråder därför dessa från att besöka sidan.<br />
	Även om vi inte tror att vuxna personer har någon behållning av att använda Hamsterpaj, så tycker vi
	inte att det är motiverat att hemlighålla någonting för den vuxna världen.
</p>

<br />

	echo rounded_corners_bottom(array('color' => 'blue'));
*/	
	ui_top($ui_options);
		echo $out;
	ui_bottom();
?>


