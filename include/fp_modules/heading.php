<?php
	$headings[999] = (date('m-d') == '06-20') ? 'Glad midsommar Ã¶nskar vi pÃ¥ Hamsterpaj!<br />Och grattis sÃ¤ger vi till <a href="/traffa/profile.php?id=15">Heggan</a> som fyller Ã¥r idag!' : '';
	$headings[998] = (date('m-d') == '11-06') ? 'Idag Ã¤ter man bakelser i GÃ¶teborg, i Ã¶vriga landet flaggar man!' : '';
	$headings[997] = (date('m-d') == '12-24') ? 'God jul Ã¶nskar vi pÃ¥ Hamsterpaj!' : '';
	
	$headings[503] = (date('m-d') >= '07-25' && date('m-d') <= '08-03') ? '<span style="color: #f91208;">H</span><span style="color: #fe4e03;">e</span><span style="color: #fcd702;">j </span><span style="color: #236412;">o</span><span style="color: #10388f;">c</span><span style="color: #9721e5;">h </span><span style="color: #f91208;">v</span><span style="color: #fe4e03;">Ã¤</span><span style="color: #fcd702;">l</span><span style="color: #236412;">k</span><span style="color: #10388f;">o</span><span style="color: #9721e5;">m</span><span style="color: #f91208;">m</span><span style="color: #fe4e03;">e</span><span style="color: #fcd702;">n </span><span style="color: #236412;">t</span><span style="color: #10388f;">i</span><span style="color: #9721e5;">l</span><span style="color: #f91208;">l </span><span style="color: #fe4e03;">H</span><span style="color: #fcd702;">a</span><span style="color: #236412;">m</span><span style="color: #10388f;">s</span><span style="color: #9721e5;">t</span><span style="color: #f91208;">e</span><span style="color: #fe4e03;">r</span><span style="color: #fcd702;">p</span><span style="color: #236412;">a</span><span style="color: #10388f;">j</span><span style="color: #9721e5;">!</span>' : '';
	$headings[502] = (date('m-d') >= '08-05' && date('m-d') <= '08-15') ? 'Dags att bÃ¶rja skolan igen, men misstrÃ¶sta inte, www.hamsterpajiskolan.se finns ju :)' : '';
	$headings[501] = (date('m-d') >= '10-01' && date('m-d') <= '10-10') ? 'Snart Ã¤r det dags att ta fram krattan, hÃ¶sten har kommit!' : '';
	$headings[500] = (date('m-d') >= '12-05' && date('m-d') <= '12-10') ? 'Kommer julbocken i GÃ¤vle brinna i Ã¥r igen tro?' : '';
	//'Glad pÃ¥sk, med mycket godis och nyborrade tÃ¤nder Ã¶nskar vi pÃ¥ Hamsterpaj'
	
	
	$headings[57] = (date('G') == 1) ? 'Det som stÃ¥r i bibeln glÃ¶mmer man lÃ¤tt, men det som stÃ¥r i <a href="/traffa/profile.php?id=13632">skeggis</a> kalsonger, det glÃ¶mmer man aldrig!' : '';
	$headings[55] = (date('G') == 3) ? 'GÃ¥ och lÃ¤gg dig! Du kan vÃ¤l drÃ¶mma om <a href="/diskussionsforum/mellan_himmel_och_jord/skraep_och_spam/snoppnaesa_eller_naessnopp/sida_1.php">snoppnÃ¤sor</a> eller nÃ¥t?' : '';
	$headings[54] = (date('G:i') >= '3:30' && date('G:i') < '4:30') ? 'Klockan 03.00 Ã¤r det natt och 05.00 Ã¤r det morgon, men vad Ã¤r klockan 04.00? (Svar: En halvtimma kvar tills Hamsterpajen backupar sig sjÃ¤lv)' : '';
	$headings[53] = (date('G') >= 5 && date('G') < 6) ? 'Det behÃ¶vs mycket kaffe fÃ¶r att orka upp vid den hÃ¤r tiden' : '';
	$headings[49] = (date('G') >= 1 && date('G') < 6) ? 'I Norrland Ã¤r myggen sÃ¥ stora att det Ã¤r skÃ¶nt nÃ¤r de suger' : '';
	
	$headings[7] = (date('G') >= 11 && date('G') < 13 && in_array(date('w'), array(1, 2, 3, 4, 5))) ? 'Tjena, vad sÃ¤gs om en <a href="/flashfilmer/topplistan/">kul flashfilm</a> nu under lunchrasten?' : '';
	$headings[51] = (date('G:i') == '13:37') ? 'Ã„r du en sÃ¥n dÃ¤r 1337 h4xx0r?' : '';
	$headings[50] = (date('G') >= 19 && date('G') < 23) ? 'VÃ¤lkommen hit, ha en skÃ¶n kvÃ¤ll' : '';

	$random_quotes = array(
		'Hej och vÃ¤lkommen till Hamsterpaj',
		'"Tritone, det Ã¤r helt enkelt du. SÃ¶t, inslag utav en jordgubbe, mjuk som vanilj och go som en gelÃ©godis."<br />Galten jÃ¤mfÃ¶r Tritone med alkolÃ¤sk frÃ¥n systemet',
		'HÃ¶rt hos plastikkirurgen:<br />- <a href="http://excds.ath.cx/fun/other/ektapa/ektapa.html" target="_blank">Dr. Ekta Pattar</a> tar emot!',
		'Visst Ã¤r 65654 ett fint nummer?',
		'Vet du om att Stora mossen ligger mitt emellan Trosa och Fittja?',
		'Visste du att Tradera har <a href="http://www.google.se/search?rlz=&=&q=johan+h%F6glund" target="_blank">lÃ¥ga priser pÃ¥ Johan HÃ¶glund</a> just nu?'
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
	
	$output .= ($_SERVER['REMOTE_ADDR'] == '217.28.207.226') ? '<h1>Va? Har ni inte slutat Ã¤nnu? Kullaviksskolan Ã¤r alltsÃ¥ fÃ¶re er!</h1>' : '';
	$output .= ($_SERVER['REMOTE_ADDR'] == '217.21.232.204') ? '<h2>En blackebergare? Silfverstolpe is watching you!<h2>' : '';
	$output .= ($_SERVER['REMOTE_ADDR'] == '90.224.61.189') ? '<h2>Hej Joar!</h2>' : '';
?>