<p class="ingress">
I takt med att Hamsterpaj har vuxit och blivit mer och mer populärt har våra servrar fått jobba hårdare och hårdare. Under hösten 2006 bestämde vi oss för att byta ut hela maskinparken och dessutom byta webbhotell.<br />
Här kan du läsa om hur själva flytten går till, uppdelad i ett antal olika steg.
</p>

<img src="http://images.hamsterpaj.net/article_illustrations/pajflytt.jpg" class="illustration" />

<h2>
<img src="http://images.hamsterpaj.net/done.png" />
Steg 1, hitta en ny leverantör</h2>
<p>
Efter att vi hade bestämt oss för att lämna Webcows i Östersund och flytta till en annan leverantör behövde vi hitta en ny leverantör. Kravlistan var ganska enkel.
</p>
<ul>
<li>Servrarna skulle bo max två timmar bort från Johan eller Magnus</li>
<li>Hög driftsäkerhet och snabb uppkoppling</li>
<li>Duktig personal som  är lätt att få kontakt med</li>
<li>Bra priser, vi vill betala för uppkoppling och ström - inte tjänstebilar och kostymklädda konsulter</li>
</ul>

<h3>Först träffar vi DCS i Stockholm</h3>
<p>Johan och Heggan blir hämtade i en vit och lätt rostig gammal skåpbil vid Sundbybergs pendeltågsstation. Efter några minuter i bil kommer vi fram till ett anonymt hus i Solvalla. Där inne möts vi av switchar och datorer som står och skräpar i hörnen och oändliga mängder nätverkskabel. Vid ett skrivbord sitter en kille i tjocka glasögon och gråa mjukisbyxor med matchande tröja. Han äter pepparkakor och dricker Coca Cola samtidigt som han knackar på tangentbordet.<br />
Vi slår oss ner i ett sammanträdesrum och vi diskuterar vad vi behöver. I runda slängar blev det tre stycken hiskeligt dyra datorer plus ett par hundra megabits internetuppkoppling och ett par terabyte trafik varje månad. 
</p>
<p>
Efter mötet äter vi lunch och ser oss omkring i lokalerna, på övervåningen dånar det av hundratals fläktar. Vi går in i ett mörkt rum med blinkande små gröna och blåa lysdioder från golv till tak. Fredrik som är CTO (vi tror det betyder chef) på DCS tänder lampan och vi ser mängder med datorer fastskruvade i skåp. Varje dator är mellan 5-10cm hög, ungefär en halvmeter bred och en meter djup. Tätt, tätt sitter dom från golv till tak, med fiberoptiska kablar mellan sig. En bit bort står ett gigantiskt monster på golvet, en luftkylare som måste väga ett par hundra kilo.<br />
På vägen ut passerar vi en pall(!) med pepparkakor.<br />
<em>- "Ja, vi skulle dela ut dom till våra kunder förra julen, men det blev inte av. Vill ni ha några?"</em>, säger Fredrik.
</p>
<p>
Vi tackar för oss, och traskar därifrån med var sin pepparkaksburk, fast beslutna om att "här ska Hamsterpaj bo".<br />
Johan hade ont i magen nästa dag.
</p>


<h3>Ett par veckor senare, Professional Internet</h3>
<p>
Kaserntorget, Göteborg. Johan kommer dit ensam, hoppar av cykeln och traskar upp för trapporna i sin vanliga hood-tröja.<br />
Herrarna från PIN gör ett sobert intryck med skjorta och ett fint konferensrum. Vi pratar en stund och kommer fram till att de erbjuder ungefär samma sak som DCS, fast med lite högre driftsäkerhet och till ett mycket högre pris.
</p>
<p>
DCS var billigare och bjöd dessutom på pepparkakor. Så vi väljer DCS.
</p>

<h2>
<img src="http://images.hamsterpaj.net/done.png" />
Steg 2, beställa servrar</h2>
<p>
Eftersom Hamsterpaj har ganska ont om pengar och det var ganska dyra pjäser som DCS hade rekommenderat (totalt en bit över hundra tusen)  så drar det ut på tiden. Dessutom får vi lite extrakostnader som vi inte hade räknat med.<br />
Men i slutet av Januari får vi en kredit hos SEB och beställer servrarna.
</p>

<img src="http://images.hamsterpaj.net/article_illustrations/rack.jpg" class="illustration" />

<h2>Steg 3, Installation och konfigurering av servrarna</h2>
<p>
Från början kommer vi överrens om att tekniken på DCS ska installera Debian Linux på de tre maskinerna och maila över kontouppgifterna till oss måndagen den tolfte februari. Den trettonde får vi veta att teknikern är magsjuk, men vi ska få uppgifterna på torsdag, femtonde.<br />
När vi har användarnamn och lösenord till servrarna loggar Heggan in och installerar MySQL, Apache, PHP och alla andra små program som behövs för att Hamsterpaj ska fungera. Dessutom måste många program ställas in väldigt noggrant för att så många besökare som möjligt ska kunna använda sidan. Detta tar ett par dagar att göra, beroende på hur mycket Heggan sitter på chatten...
</p>

<h2>Steg 4, Frysning och flytt av koden</h2>
<p>
När allting är installerat och klart fryser vi koden som driver Hamsterpaj. Normalt sett brukar vi gå in och ändra saker lite här och där mest hela tiden, byta någon färg, laga någon bugg eller lägga till en extra funktion.<br />
Själva frysningen går ut på att man låser filerna, så att ingen kan ändra något. Detta för att man inte ska riskera att ändra i någon fil som redan har flyttats.
</p>
<p>
Därefter kopieras all kod över till de nya maskinerna, eftersom Hamsterpaj består av ett par hundra script som måste packas ihop, flyttas över och sedan packas upp kan detta ta någon timma. Det är ingenting om märks när man surfar på Hamsterpaj.
</p>

<h2>Steg 6, Bilderna fryses och flyttas</h2>
<p>
När koden är flyttas ska alla bilderna flyttas. För att inte riskera att någon byter visningsbild eller laddar upp en bild på de gamla maskinerna så stänger vi av alla bildfunktionerna. Nu kan man inte längre ladda upp, byta ut eller ta bort bilder från Hamsterpaj.<br />
När vi har låst alla bildfunktioner börjar vi flytta över bilderna. Senast jag såg efter hade vi 95 000 visningsbilder och ännu fler bilder i fotoalbumen. Det tar flera timmar att flytta över allting till de nya servrarna.
</p>

<h2>Steg 7, Databasen dumpas och flyttas</h2>
<p>
När kod och bilder har flyttats är det bara innehållet kvar, nu kopieras alla miljoner forumsinlägg över. Vartenda meddelande, gästboksinlägg och användarkonto skickas över Internet till de nya servrarna.<br />
Först skriver vi ett stort meddelande längst upp på Hamsterpaj om att allting som skrivs eller görs kommer att försvinna. Sedan dumpar vi databasen, vi gör en fil där vi sparar ner allting.
</p>
<p>
Vi flyttar över den samtidigt som Hamsterpaj är uppe på Internet och snurrar, det går fortfarande att skriva i forumet och göra det mesta på Hamsterpaj, men det händer på de gamla servrarna som snart ska sluta användas.<br />
När databasen har skickats över till de nya servrarna har vi två kopior av Hamsterpaj. Nya och gamla, på två olika ställen.
</p>

<h2>Steg 8, Testkörning</h2>
<p>
Jag och Heggan ställer om våra datorer så att de använder de nya servrarna medans alla andra surfar på de gamla. Nu testar vi att logga in, skriva inlägg och ladda upp bilder för att se så att allting fungerar som det ska. När allting verkar fungera bra är det dags för nästa steg
</p>

<h2>Steg 9, Domännamnen pekas om</h2>
<p>
Alla datorer på Internet har ett IP-nummer, ungefär som att alla människor i Sverige har ett personnummer. De här IP-numren är ganska svåra att komma ihåg, exempelvis 217.198.148.144. Därför har man domännamn, som till exempel hamsterpaj.net och lunarstorm.se, varje domännamn pekar på en IP-adress.
</p>

<p>
Våra nya servrar kommer ha andra IP-nummer än de gamla, därför måste vi peka om domänen hamsterpaj.net till det nya numret. Lite beroende på vilken internetleverantör man har kan det ta olika lång tid innan man får det nya numret. En del får det nya numret och börjar surfa på de nya servrarna direkt, andra kan få vänta i ett par timmar. Därför kan det hända att din kompis som du chattar med på MSN fortfarande kommer till "gamla" hamsterpaj medans du kommer till nya. 
</p>

<p>
De gamla och nya servrarna är inte ihopkopplade på något sätt, så om man skickar ett meddelande på de gamla servrarna så kommer det inte komma fram på "de nya". Därför ska man inte skriva något viktigt på gamla Hamsterpaj.<br />
</p>