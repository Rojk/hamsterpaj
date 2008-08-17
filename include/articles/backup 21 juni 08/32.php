<style>
ul li, ol li
{
  margin-bottom:  5px;
}

.ahlens_update
{
  background: #dedede;
 border: 1px solid #ababab;
}
</style>

<div class="ahlens_update">
<h3>Åhléns har ändrat sina rutiner efter vår artikel</h3>
<p>
Åhléns har numera ändrat sina rutiner så att detta inte längre är möjligt att genomföra. Vi har uppnåt vårt syfte och systemet är säkrat.
</p>
</div>

<p>
	&ndash; <strong>Ursäkta, jag gör ett sista-minuten-arbete om nytänkande betalsystem. Jag tänkte
	bara fråga om det är OK att jag tar några kort på ert presentkortsställ?</strong>
	<br />
	Kassörskan blir lite osäker och ringer chefen, som hänvisar mig till huvudkontoret - att fota i butiken är inte okej!
</p>

<p>
	När hon skyndar vidare för att hjälpa nästa kund tänker hon inte på att jag hänger tillbaks ett presentkort
	i hållaren på disken. Nu har vi på riktigt inlett kortbedrägeriet.
</p>

<img src="http://images.hamsterpaj.net/article_illustrations/presentkort/head.jpg" />

<h2>Vad vi tänker göra</h2>
<p>
	Nu spolar vi tillbaka bandet ett par dagar, det är måndag eftermiddag och jag befinner mig på IKEA
	för att köpa ett köksbord. Vid utgången hänger presentkort på ett ställ, det är den där nya sortens
	presentkort med magnetremsa. Man väljer själv hur mycket pengar man vill ladda på kortet med och sedan håller
	kortet självt reda på hur mycket som finns kvar när någon handlar.<br />
	På baksidan av varje kort står ett långt nummer, olika på varje kort. Jag funderar lite:<br />
</p>
<p>
<img src="http://images.hamsterpaj.net/article_illustrations/presentkort/card_back.jpg" style="float: right; margin: 3px;" />
	<em>
		&ndash; Det där numret måste ju betyda någonting... Kanske är det så att det finns en databas
		någonstans som håller reda på kortnummer och hur mycket pengar som finns på kortet. Då borde 
		det ju räcka med att ha någons nummer för att kunna handla på dennes presentkort, och numren finns ju
		här, helt öppet. Det är bara att fota med mobilkameran!<br />
		Skaffar man sig bara en magnetkortsskrivare och tar reda på några kortnummer så borde det ju gå att
		handla på andras kort!
	</em>
</p>
<p>
	Jag och Henrik bestämde oss för att testa detta, eftersom Åhléns har ett likadant system och ligger lite
	mer centralt valde vi dem istället.
</p>

<h2>Hur vi gick till väga</h2>
<ol>
	<li>
		Vi surfade in på <a href="http://www.affarsit.se/" target="_blank">Affärs-IT</a> och köpte en 
		magnetkortsskrivare. Sen kom <a href="/traffa/profile.php?id=645579">Ekonomi-pär</a> och yrade
		någonting om obetalda fakturor...
	</li>
	<li>
		Jag åkte ner till Åhléns och hämtade två tomma presentkort från en obemannad kassa. 
	</li>

	<li>
		På kontoret läste vi in ett kort som vi kallar för originalkortet i datorn.<br />
<img src="http://images.hamsterpaj.net/article_illustrations/presentkort/card_read.png" style="border: 1px solid #565656; margin: 2px;" /><br />
		Kortnumret är: 5045 0756 2000 3554 107, de andra siffrorna vet vi inte vad de betyder.
	</li>

	<li>
		Nu plockade vi fram det andra kortet, med nummer:<br />
		<pre>5045 0756 2000 3553 919</pre><br />
		Det kortet, som vi kallar för fejkkortet, drog vi i magnetkortsskrivaren och skrev in originalkortets
		nummer i magnetremsan.
	</li>
	
	Nu har vi alltså två stycken presentkort från Åhléns med exakt samma magnetremsa, men den tryckta koden
	på baksidan skiljer sig åt. Fejkkortet har vi plockat loss från pappersbiten med information, originalkortet
	sitter kvar och ser helt orört ut.<br />
	Dags att smuggla in originalkortet i kassan!
	
	<li>
		Med en kamera i ena handen och presentkortet i andra stegar jag in på Åhléns, går upp till övre plan,
		promenerar runt lite och frågar till slut en kassörska om jag får ta några bilder. Det får jag inte, så
		jag "hänger tillbaka" presentkortet jag har i handen och går iväg.
	</li>
	
	<li>
<img src="http://images.hamsterpaj.net/article_illustrations/presentkort/henrik_shop.jpg" style="float: right; border: 1px solid #565656; margin: 2px;" />
		Nu går Henrik in i butiken, upp till övre plan och fram till kassan. Han ställer sig i kön, tar presentkortet
		som hänger längst fram och laddar på det med 200kr.<br />
Nu har Henrik fyllt på originalkortet med 200kr, och om vår teori stämmer så ska mitt fejkkort ha samma summa.
	</li>
	
	
	
	<li>
		Jag går fram till info-disken på Åhléns, försöker se lite vilsen ut och säger att jag har fått "ett sånt här kort"
		men jag vet inte hur mycket pengar det är på. Kan man se det på något sätt?<br />
		<em>&ndash; Du har tvåhundra kronor på kortet</em>, säger tjejen leende och räcker över ett kvitto.
	</li>
	
	Det fungerar! Vi har lyckats stjäla pengar av ett annat presentkort, utan att någon har märkt något överhuvudtaget!
	
	<li>
		Dags för det slutliga testet. Jag går och plockar ett par svarta Björn Borg-kalsonger som kostar 199:- och ställer
		mig i kassakön. När det blir min tur håller jag fram presentkortet, även denna kassörska ler vänligt,
		talar om att jag har en krona var på kortet efter avslutat köp och hälsar mig välkommen tillbaka.
	</li>
</ol>

<img src="http://images.hamsterpaj.net/article_illustrations/presentkort/reciepts.jpg" />

<h2>Det här skulle kunna bli mycket värre</h2>
<p>
	Någon dag efter testet på Åhléns upptäcker vi att man kan kontrollera kortsaldon direkt på Åhléns hemsida, efter
	att ha snickrat en stund kunde jag bygga ett program som kontrollerar kortnumren i ordning och skriver ut saldot
	på korten som existerar.
	<pre>
		5045075620003551715 har saldo: 500:-
		........
		5045075620003551723 har saldo: 0:-
		........
		5045075620003551731 har saldo: 441:-
		..................
		5045075620003551749 har saldo: 500:-
		.......................
		5045075620003551772 har saldo: 23:- 
	</pre>
	Vi har inte tittat så noga på korten, men det verkar som om de innehåller en CVV-kod, alltså en sorts kontrollkod
	som talar om att kortet är äkta. Vi gissar att vi inte bara kan byta ut kortnumren på presentkorten och 
	sedan handla, den hemliga CVV-koden måste finnas med och för att få reda på den måste vi låna kortet.
</p>
<p>
	Vad vi däremot skulle kunna göra är att sätta det här i system. Att kopiera kort, hänga tillbaks och bevaka
	korten via nätet. Tänk er följande scenario:
	<ol>
		<li>Vi hämtar 20st kort från Åhléns och läser in magnetremsorna i datorn. Vi sparar ner informationen i olika filer.</li>
		<li>Vi hänger tillbaks 19 kort inne på Åhléns vid någon för tillfället obemannad kassa</li>
		<li>Vi låter datorn kontrollera kortnumren till de 19 korten varje timma, så fort ett kort har laddats på skickar datorn ett SMS till vår telefon</li>
		<li>Vi tar det tjugonde kortet, sätter i kortskrivaren, letar upp filen med magnetremsan som tillhör det laddade kortet och skriver informationen till vårt kort</li>
		<li>Vi går in på Åhléns och köper upp just det belopp som laddats på</li>
	</ol>
	Eftersom kortskrivaren är liten och går på en vanlig USB-port skulle vi kunna sitta i en bil utanför och bara vänta.
</p>

<img src="http://images.hamsterpaj.net/article_illustrations/presentkort/card_writer.jpg" style="float: right; border: 1px solid #565656; margin: 2px;" />

<h2>Åhléns, IKEA och andra kan skydda sig mot detta</h2>
<p>
	I första hand är det företaget <a href="http://www.comdata.com/" target="_blank">Comdata</a> som bär ansvaret för det här systemet.<br />
	De skriver på sin sida:<br />
	<em>
		<h4>Rock-solid technology</h4>
		Gift card transactions are an essential part of the retail landscape. You need assurance that the technology driving your gift card program is completely reliable.
	</em><br />
	<br />
	Ta er i kragen Comdata, Åhléns, IKEA och alla ni andra som kör med osäkra system!<br />
	<ul>
		<li>Förvara korten bakom disk, ha bara demokort ståendes framme</li>
		<li>Kontrollera alltid det tryckta numret mot det som finns på magnetremsan, gärna genom att kassören skriver av de fyra sista siffrorna</li>
		<li>Tryck kortnumret med hologramtryck eller stansa in numret i kortet</li>
	</ul>
</p>

<h2>Varför vi skriver manualer i att begå brott</h2>
<p>
	Första frågan som poppar upp i mångas huvuden efter att ha läst den här artikeln är nog:<br />
	<strong>&ndash; Varför i hela friden skriver en ungdomssajt en guide till hur man stjäl pengar
	av hederliga människor?</strong><br />
	Vi tycker om säkerhet, vi gillar att utforska och vi tycker det är roligt att hitta luckor. Vi tycker att
	man ska bygga säkra system, system som inte går att lura. Man ska inte bygga ett halvtaskigt system 
	och sen förlita sig på att folk inte förstår.<br />
	Därför tror vi på att utforska och att uppmärksamma, vi tror på att tala om vilka luckor som finns
	och därmed sätta press på tillverkaren att säkra upp systemen. 
</p>

<strong>
Avslutningsvis vill vi bara uppmana er läsare att inte stjäla pengar eller hålla på med bedrägeri. Det finns så mycket bättre sätt att tjäna pengar på. Vill ni testa detta, (vilket ni inte vill, för en kortskrivare är ganska dyr), så sätt själva in pengarna som ni snor. Ingen blir glad av en värdelös födelsedagspresent eller förlorade pengar!
</strong>