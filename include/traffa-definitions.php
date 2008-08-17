<?php
	/*
		Innehåller listor med personligheter och hårfärger för Träffa.
		Skriv _inte_ över några id-nummer, utan fortsätt i slutet av listan.
		Glöm inte att lägga in beskrivningar av nya personligheter du
		lägger till.
	*/
	
	$traffaDefPersonalities = array(
		1	=> 'Vänster',
		2	=> 'Moderat',
		3	=> 'Punkare',
		4	=> 'HipHopare',
		5	=> 'Datornörd',
		6	=> 'Cooling',
		7	=> 'Fjortis',
		8	=> 'Mobbad',
		9	=> 'Plugghäst',
		10	=> 'Kriminell',
		11	=> 'Blyg',
		12	=> 'Flumbarn',
		13	=> 'Lunartönt',
		14	=> 'Hamsterpaj-älskare',
		15	=> 'Vuxen',
		16	=> 'Snygging',
		17	=> 'Snäll',
		18	=> 'Ärlig',
		19  => 'Carfreak',
		20	=> 'Gamer',
		21  => 'Hårdrockare'
	);
	
	$traffaDefPersonalitiesDesc = array(
		1	=> 'Ofta en femtonårig flicka som lyssnar på Knutna Nävar, Broder Daniel och Doktor Kosmos. Hon vet inte mycket om politik, men att demonstrera och måla stjärnor på kinden är kul och ger en gruppkänsla. Att stå och skrika "Internationell solidaritet - Arbetarklassens kampenhet" gillar hon också, vad det betyder vet hon egentligen inte, men det är nånting om att alla är lika värda.',
		2	=> 'Den sanne MUF\'aren går prydligt klädd i en ljus skjorta, rutig scarf, dyra märkesjeans och har har gelé i håret. Vintertid bär han även en Canada Goose-jacka för att skydda sig mot kylan (eller är det för att visa att han har råd?). Han är oftast lite äldre än vänsterflickan, över 16 år. Antingen är han bosatt i stans lyxigare kvarter eller i en rik förort tillsammans med mamma och pappa. Familjen lever gott på pappans inkomst från chefsjobbet.',
		3	=> 'Vi kom inte på något humoristiskt om punkare, därför säger vi bara tre ord: Död åt Tengil! ',
		4	=> 'Den typiske hiphoparen är oftast 14 och född i förorten till en större stad. Han går oftast klädd i kläder som närmast kan liknas vid tält. Nike-skor och guldkedjor är också nödvändiga tillbehör för denna vilsna själ. Musiksmaken innefattar oftast artister som Jean Paul, 2Pac och Eminem. Ord som "Shoo", "Bre" och "Len" används flitigt för att markera sin grupptillhörighet. ',
		5	=> 'Uteslutande killar som har ett genuint intresse för maskiner och logiska problem. Har efter många års samlande en stor samling mer eller mindre fungerande datorer i hemmet, svänger sig gärna med uttryck som "kompilator", "proxyservrar", "filsystem" och "kernelmoduler". Hittas på IRC sena nätter i kanaler med namn som #linux.se och nätverk som EFnet.',
		6	=> 'Pojke på 10 år som nyss hört ordet "cool". Spelar gärna cool inför sina kompisar och försöker imponera på flickorna genom att dra dem i håret. Alla har vi väl varit där? ;)',
		7	=> 'Fjortisen (Homo Sapiens Fjortissus) Ett flocklevande djur som ofta rör sig i mindre grupper utanför McDonald\'s och liknande. Gärna vid sena tidpunkter på dygnet. Ju senare, desto starkare position i gruppen. Honorna är ca 160 cm långa och har ett läte som kan liknas vid skatans kvitter, medan hanarna kan ha ett mer mänskligt läte. De kan upplevas som mycket störande djur då de under sin vandringar kan gapa mycket. Populationerna har ökat markant på senare tid.',
		8	=> 'Lite udda, lite konstigt, och inte riktigt förstått hur man får kompisar. Hittas oftast i föreningar med filosofin "alla är lika värda", exmpelvis Scouterna och Kyrkans Ungdomsförbund. Den mobbade ser ofta upp till andra "häftiga" typer och försöker bete sig lika coolt själv. Resultatet blir tragiskt nog att mobboffret sitter hemma framför datorn och tykar sig på internet ända tills Mamma säger åt honom/henne att gå och lägga sig. ',
		9	=> 'Sitter alltid tyst på lektionerna djupt nedgrävt i boken och lyckas nästan alltid i sina ansträgningar att bli bäst i klassen på allt förutom gympa. Har till skillnad från den Mobbade även social förmåga och vet hur man fixar kompisar. Pressen hemifrån att prestera har alltid varit hård. ',
		10	=> 'Tycker det är häftigt att vara kriminell och vill gärna vara det. Syndabekännelsen omfattar oftast smygrökning bakom gymnastiksalen och att ha snattat ett tuggummi på Statoil, men även grövre brott, som till exempel att tjuvläsa storasyrrans dagbok, förekommer. ',
		11	=> 'Vi kom inte på något humoristiskt om blyga, därför säger vi bara tre ord: Död åt Tengil!',
		12	=> 'Vi kom inte på något humoristiskt om flumbarn, därför säger vi bara tre ord: Död åt Tengil!',
		13	=> 'Desperata små pojkar och flickor som skickar spam med texter som "RöStA På mIn GuRU Du for pro ja lååvar!!!!!" eller "hejsan någon tjej som visar allt för lite pro adda techno_boy90@hotmail.com". Ibland vet man inte om man skall skratta eller gråta. ',
		14	=> 'Pojke 12 som nyss hittat hamsterpaj. Tycker att vi i Crew bara är såå coola. Har ibland en egen hemsida som han vill att vi ska länka till, killen vill gärna hjälpa till med siten och bli en del av Crew. Skryter gärna vitt och brett om sina obefintliga kunskaper i ASP och HTML, resultatet blir oftast att han blir blockad av Crew och inte förstår någonting. ',
		15	=> 'Villa, familj, hund, bil och efternamnet "Svensson". Går dagligen till sitt enformiga jobb, tjänar sina pengar och semestrar på samma tropiska ö år efter år. Vad personen gör på hamsterpaj är en outgrundlig fråga, registrerade sig kanske eftersom yngste sonen tjatade.',
		16	=> 'Raggar på nätet och utger sig för att se ut som en fotomodell. Ser i verkliga livet inte särskilt speciell ut, men det vet ju inte de i chatten.',
		17	=> 'Vi kom inte på något humoristiskt om snälla, därför säger vi bara tre ord: Död åt Tengil!',
		18	=> 'Oftast är det tjejer i trettonårsåldern som utger sig för att vara ärliga. Kindpussar och kramar är vanligt förekommande i kompisgänget som är för unga för att klassas som fjortisar. De lever ofta i en drömvärld där allt är ljust, vackert och blommigt. Oftast är personen inte alls speciellt ärlig eller pålitlig, men hon gillar imagen och väljer därför denna personlighet.',
		19  => 'Killen/Tjejen som inte bryr sig om lite skit under naglarna ligger gärna med huvudet under en huv och meckar. Gillar oftast feta motorer, Turbos och No2 (NOS). Skulle nog ändå om man hade kunnat glida runt i en fet amerikanare från 50Talet.',
		20	=> 'Finnig pojke på 15 år. Innehaver en värstingdator som kan spela alla de nya spelen. Säger sig vara expert på dator men verkligheten är att hans största förmåga är att hitta en bra server på Counter Strike. Hans kompisar är likadana som han, de kommer ständigt med termer som LOL, OMFG och n00b. Hittas ofta på internetcaféer runt 3-tiden på natten i full färd med att klå upp förvirrade 10-åringar på CS.',
		21  => 'Kängor, mantel, bandtröja och långt hår. Oftast en kille på 16 år som gillar att känna sig "utanför" genom att lyssna på Metal. Avfärdar allt och alla som inte kan ta musiken, för veklingar eller att det inte "krossar". Han får sin styrka fårn den enkla råa kraften som finns i musiken, som han omger sitt liv med. Det som käraktiserar en sann "hårdrockare" och som skiljer honom från din vanliga rock "losern", är förmågan att utföra ritualen som är att headbanga. Då de saknar de enkla pysiska kordinationer som behövs för att kunna dansa, och har en musik-stil som är omöjlig att dansa till, så ruskar dom bara på huvudet till en 4/4 takt. Denn sanna "hårdrockaren" har också lärt sig att bemästra luftgitarren, och använder detta flitigt vart han än går.' 
	);
	
	$traffaDefHaircolors = array(
		1	=> 'Blond',
		2	=> 'Brun',
		3	=> 'Orange',
		4	=> 'Svart',
		5	=> 'Grön',
		6	=> 'Lila',
		7	=> 'Röd',
		8	=> 'Blå'
);
?>
