<?php
	require_once('include/core/common.php');
	exit;
	$msg = 'Imorgon sker det! Klockan 12:00 kommer vi att mötas på Sergels Torg i Stockholm, där vi kommer att invänta folk med sen ankomst till en stund efter 12:30. Om ni har lyckats glömma det är temat Undre Världen, vilket innebär att ni ska klä ut er till någonting med en kriminell koppling. Och för guds skull, inse hur tråkigt det är om ni kommer i era vanliga tråkkläder och säger att ni är skattefuskare eller fildelare. Visa lite kreativitet nu!' . "\n\n";
	$msg .= 'Vi promenerar till Humlegården och leker och har det mysigt, med tanke på omständigheterna så ställer vi in vattenkriget, bland annat för att citera R3my som sitter bredvid mig, ”Folk med blöta skor är inte glada.” Så vi håller oss på det torra och har kul ändå!' . "\n\n";
	
	$msg .= 'När vi är lagom utmattade så kommer vi att gå till Observatiorielunden för att äta, det finns många olika matmöjligheter där. Eftersom vädret inte verkar bli dåligt så kommer vi att sitta tillsammans utomhus och äta, vila och bara lära känna varandra lite mer.' . "\n";
	
	$msg .= 'Efter det här ska det bli dags att dra oss iväg från stan, nämligen till Älvsjöbadet, vilket vi tar oss till med tunnelbana. Där ska vi grilla, bada, sporta, leka och ha ännu trevligare! Möjligheter att köpa engångsgrillar och mat finns i Hagsätra centrum.' . "\n\n";
	
	$msg .= 'Här vore det trevligt om ni som har det hemma tog med kanske strandtennis, en volleyboll (volleybollnät finns) och andra saker som vi kan roa oss med! Alla sådana inslag är trevliga.' . "\n\n";
	$msg .= 'Här fortsätter kvällen precis så långt vi vill, sista tunnelbanan går 03:16 och när sista pendeltåget går vet jag inte. Men möjligheter att ta sig hem finns alltså!' . "\n\n";
	
	$msg .= 'Vi ser fram emot att se er imorgon, både jag och Rojk. Uppför er och ta med er sådan som vi kan ha kul av på kvällen, och kom med ett glatt humör och en kriminell utstyrsel!' . "\n\n";

	$msg .= 'Puss moii' . "\n\n";

	$message = $msg;
	
	$guestbook_message['sender'] = 134306;
	$guestbook_message['message'] = $message;
	
	$query = 'SELECT user_id FROM irl_attendings WHERE attending = "maybe" OR attending = "yes" AND irl_id = 1';
	$result = mysql_query($query);
	while ($data = mysql_fetch_assoc($result))
	{
		$guestbook_message['recipient'] = $data['user_id'];
		$out .= 'GB sent to: ' . $data['user_id'] . "\n";
		guestbook_insert($guestbook_message);
	}
	echo '<pre>'.$out.'</pre>';
	//guestbook_insert($guestbook_message);
?>