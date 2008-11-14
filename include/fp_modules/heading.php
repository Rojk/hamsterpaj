<?php
	$headings[999] = (date('m-d') == '06-20') ? 'Glad midsommar önskar vi på Hamsterpaj!<br />Och grattis säger vi till <a href="/traffa/profile.php?id=15">Heggan</a> som fyller år idag!' : '';
	$headings[998] = (date('m-d') == '11-06') ? 'Idag äter man bakelser i Göteborg, i övriga landet flaggar man!' : '';
	$headings[997] = (date('m-d') == '12-24') ? 'God jul önskar vi på Hamsterpaj!' : '';
	
	$headings[503] = (date('m-d') >= '07-25' && date('m-d') <= '08-03') ? '<span style="color: #f91208;">H</span><span style="color: #fe4e03;">e</span><span style="color: #fcd702;">j </span><span style="color: #236412;">o</span><span style="color: #10388f;">c</span><span style="color: #9721e5;">h </span><span style="color: #f91208;">v</span><span style="color: #fe4e03;">ä</span><span style="color: #fcd702;">l</span><span style="color: #236412;">k</span><span style="color: #10388f;">o</span><span style="color: #9721e5;">m</span><span style="color: #f91208;">m</span><span style="color: #fe4e03;">e</span><span style="color: #fcd702;">n </span><span style="color: #236412;">t</span><span style="color: #10388f;">i</span><span style="color: #9721e5;">l</span><span style="color: #f91208;">l </span><span style="color: #fe4e03;">H</span><span style="color: #fcd702;">a</span><span style="color: #236412;">m</span><span style="color: #10388f;">s</span><span style="color: #9721e5;">t</span><span style="color: #f91208;">e</span><span style="color: #fe4e03;">r</span><span style="color: #fcd702;">p</span><span style="color: #236412;">a</span><span style="color: #10388f;">j</span><span style="color: #9721e5;">!</span>' : '';
	$headings[502] = (date('m-d') >= '08-05' && date('m-d') <= '08-15') ? 'Dags att börja skolan igen, men misströsta inte, www.hamsterpajiskolan.se finns ju :)' : '';
	$headings[501] = (date('m-d') >= '10-01' && date('m-d') <= '10-10') ? 'Snart är det dags att ta fram krattan, hösten har kommit!' : '';
	$headings[500] = (date('m-d') >= '12-05' && date('m-d') <= '12-10') ? 'Kommer julbocken i Gävle brinna i år igen tro?' : '';
	//'Glad påsk, med mycket godis och nyborrade tänder önskar vi på Hamsterpaj'
	
	
	$headings[57] = (date('G') == 1) ? 'Det som står i bibeln glömmer man lätt, men det som står i <a href="/traffa/profile.php?id=13632">skeggis</a> kalsonger, det glömmer man aldrig!' : '';
	$headings[55] = (date('G') == 3) ? 'Gå och lägg dig! Du kan väl drömma om <a href="/diskussionsforum/mellan_himmel_och_jord/skraep_och_spam/snoppnaesa_eller_naessnopp/sida_1.php">snoppnäsor</a> eller nåt?' : '';
	$headings[54] = (date('G:i') >= '3:30' && date('G:i') < '4:30') ? 'Klockan 03.00 är det natt och 05.00 är det morgon, men vad är klockan 04.00? (Svar: En halvtimma kvar tills Hamsterpajen backupar sig själv)' : '';
	$headings[53] = (date('G') >= 5 && date('G') < 6) ? 'Det behövs mycket kaffe för att orka upp vid den här tiden' : '';
	$headings[49] = (date('G') >= 1 && date('G') < 6) ? 'I Norrland är myggen så stora att det är skönt när de suger' : '';
	
	$headings[7] = (date('G') >= 11 && date('G') < 13 && in_array(date('w'), array(1, 2, 3, 4, 5))) ? 'Tjena, vad sägs om en <a href="/flashfilmer/topplistan/">kul flashfilm</a> nu under lunchrasten?' : '';
	$headings[51] = (date('G:i') == '13:37') ? 'Ã„r du en sån där 1337 h4xx0r?' : '';
	$headings[50] = (date('G') >= 19 && date('G') < 23) ? 'Välkommen hit, ha en skön kväll' : '';

	$random_quotes = array(
		'Hej och välkommen till Hamsterpaj',
		'"Tritone, det är helt enkelt du. Söt, inslag utav en jordgubbe, mjuk som vanilj och go som en gelÃ©godis."<br />Galten jämför Tritone med alkoläsk från systemet',
		'Hört hos plastikkirurgen:<br />- <a href="http://excds.ath.cx/fun/other/ektapa/ektapa.html" target="_blank">Dr. Ekta Pattar</a> tar emot!',
		'Visst är 65654 ett fint nummer?',
		'Vet du om att Stora mossen ligger mitt emellan Trosa och Fittja?',
		'Visste du att Tradera har <a href="http://www.google.se/search?rlz=&=&q=johan+h%F6glund" target="_blank">låga priser på Johan Höglund</a> just nu?'
	);
	
	$headings[1] = $random_quotes[array_rand($random_quotes)];
	

	foreach($headings AS $score => $heading)
	{
		if(strlen($heading) > 0 && $score > $heading_max_score)
		{
			$page_heading = $heading;
			$heading_max_score = $score;
		}
	}
		
	$output .= '<h1 id="fp_greeting">' . $page_heading . '</h1>' . "\n";
	
	$output .= ($_SERVER['REMOTE_ADDR'] == '217.28.207.226') ? '<h1>Va? Har ni inte slutat ännu? Kullaviksskolan är alltså före er!</h1>' : '';
	$output .= ($_SERVER['REMOTE_ADDR'] == '217.21.232.204') ? '<h2>En blackebergare? Silfverstolpe is watching you!<h2>' : '';
	$output .= ($_SERVER['REMOTE_ADDR'] == '90.224.61.189') ? '<h2>Hej Joar!</h2>' : '';
?>