<?php unset($last_username); ?>
<style type="text/css">
#article h2, #article h3, #article h4{ margin-top: 20px; }
#article h1{ margin-top: 50px; }
#article p{ margin-bottom: 20px; }
#article h1{ font-size: 2.4em; }
#article h2{ font-size: 2em; }
#article h3{ font-size: 1.5em; }
#article h4{ font-size: 1.2em; }
</style>
<cite>"Tillgång till Hamsterpaj är ingen rättighet, utan ett privilegium!"</cite>
<h2>Nu river vi!</h2>
<p>
	Den 23 juli klockan 22:00 tog vi bort systemet "userlevels".
	Det bestod av användarnivåer ifrån 0 till 5.
	Numera är allting styrt av så kallade privilegier.
	
		<h3>Userlevels (gamla systemet)</h3>
		Det fanns fem olika användarnivåer (som utloggad hade man ingen userlevel alls).
		Dessa användarnivåer kunde göra olika mycket inom administrationen på sajten, och var:
		<ol>
			<li>Vanlig användare (inloggad)</li>
			<li>Medhjälpare</li>
			<li>Ordningsvakt</li>
			<li>Administratör</li>
			<li>Sysop</li>
		</ol>
		Dessa finns alltså inte kvar längre, och vi jobbar på att byta ut dem överallt.
		Detta kan leda till vissa problem under en övergångsperiod.
		
		<h3>Privilegies (nya systemet)</h3>
		Här är det lite annorlunda, alla administrativa åtgärder kräver en privilegie.
		Privilegien kan ha olika värden, oftast är det 0, vilket ger "alla områden" inom privilegiet.
		Andra värden på privilegiet kan vara till exempel ett forum eller område inom <em>underhållningssystemet</em> (flash, film, spel, bilder etc.).
</p>

<h2>Men det heter privilegium, inte privilegie!</h2>
<p>
	Halvt rätt, men man kan säga privilegie också.
	Och försök inte släga SAOL i huvudet på oss, innan du läst <a href="http://g3.spraakdata.gu.se/saob/show.phtml?filenr=1/195/49911.html">Svenska Akademins ordlista över Svenska Språket om ordet ordet privilegie</a>.
</p>

<h2>Jag vill ha ett privilegie! Vem fixar med privilegierna?</h2>
<p style="margin-bottom: 0;">
	För det första frågar du inte om du kan få en privilegie, <a href="http://www.hamsterpaj.net/traffa/profile.php?id=643392">Entrero</a> eller <a href="http://www.hamsterpaj.net/traffa/profile.php?id=57100">Ace</a> ger dig ett om han tycker du verkar lämplig.
	Privilegieansvarig på sajten är <a href="http://www.hamsterpaj.net/traffa/profile.php?id=57100">Ace</a>, forum- och forumprivilegieansvarig är <a href="http://www.hamsterpaj.net/traffa/profile.php?id=643392">Entrero</a>.
</p>

<?php
/*
	$query = 'SHOW COLUMNS FROM privilegies';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		if($data['Field'] == 'privilegie' && substr($data['Type'], 0, 5) == 'enum(')
		{
			// enum(' [...] ')
			$privilegie_types = explode("','", substr($data['Type'], 6, -2));
		}
	}
	
	asort($privilegie_types);
	
	foreach($privilegie_types as $privilegie_type)
	{
		if(!in_array($privilegie_type, array('Välj ett privilegie...', '')))
		{
			$privilegies[$privilegie_type] = array();
		}
	}

	$query = 'SELECT p.privilegie, p.user AS user_id, l.username FROM privilegies AS p, login AS l WHERE p.user = l.id ORDER BY p.privilegie, p.value';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	while($data = mysql_fetch_assoc($result))
	{
		if($data['privilegie'] != '')
		{
			$privilegies[$data['privilegie']][] = $data;
		}
	}
?>

<h2>Alla privilegier</h2>
<p>
	Användarna <?php
	foreach($privilegies['igotgodmode'] as $data)
	{
		if(isset($last_username))
		{
			echo $last_username . ', ';
		}
		
		$last_username = $data['username'];
	}
	echo ' och ' . $last_username;
	unset($last_username);
	?> har alla privilegier på samma gång, något som internt kallas igotgodmode-privilegien.
	Den är till för de som ska ha tillgång till allt, eller de som lyckats hacka sidan och lagt till den privilegien till sig själv ;).
	Namnet "igotgodmode" är inte valt för att det ska låta så nördigt som möjligt, utan det finns en historia bakom det och en liten kodrad som fanns i Hamsterpajs kod förut,
	en liten kodrad som någon glömde ta bort vid ett visst tillfälle... Ursprunget är självklart "god mode", ett "samlingsnamn" för odödlighetsfusk i spel.
</p>

<p>
	Men, alla har inte alla privilegier. Därför kan det ju vara smart om det finns en lista över vem som kan göra vad. Så, här kommer en sån:

</p>

<?php

	//preint_r($privilegies);
	
	foreach($privilegies as $privilegie => $privilegie_data)
	{
		echo '<h3>' . $privilegie . '</h3>' . "\n";
		
		if(count($privilegie_data) > 0)
		{
			foreach($privilegie_data as $data)
			{
				echo '<a href="/traffa/profile.php?user_id=' . $data['user_id'] . '">' . $data['username'] . '</a> ';
			}
		}
		else
		{
			echo '<em>Ingen användare har tilldelats privilegien, men igotgodmode-användare kan komma åt funktionerna: </em>' . "\n";
			foreach($privilegies['igotgodmode'] as $data)
			{
				if(isset($last_username))
				{
					echo $last_username . ', ';
				}
				
				$last_username = $data['username'];
			}
	
			echo ' och ' . $last_username;
			unset($last_username);
		}
	}
*/
?>