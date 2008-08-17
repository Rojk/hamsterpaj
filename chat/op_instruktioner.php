<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('chatt', 'op_instruktioner');
	$ui_options['title'] = 'OP-instriktioner för hamsterpajchatten';
	ui_top($ui_options);
?>

<h1>Instruktioner för operatörer</h1>

<p>
Först kommer här en kort lista instruktioner, nedan finns tankarna bakom dessa utvecklade.
</p>

<ul>
	<li>Inga scripts som automatiskt kickar, bannar eller flyttar användare</li>
	<li>Arbetsgång när en användare bryter reglerna:
		<ol>
			<li>Tillsägelse</li>
			<li>Kick</li>
			<li>Tillsägelse direkt vid rejoin</li>
			<li>Kick</li>
			<li>Tillsägelse direkt vid rejoin, hot om ban</li>
			<li>Kickban</li>
		</ol>
		</li>
	<li>Kommunicera inte via kickreason, användare ser inte den</li>
	<li>Misstänkta pedofiler
		<ul>
			<li>Om Soode, Johan, kray-c eller skeggis är online: låt pedofilen vara och kontakta ircop</li>
			<li>Om ingen av ovanstående svarar: kickban, skicka sedan loggar som ett privat meddelande till Soode på Hamsterpaj</li>
		</ul>
	</li>
	<li>Användare som tjafsar emot eller vill diskutera regelbrott skall inte kickas, om du inte orkar ta diskussionen, säg: <em>"Det är så jag tolkar reglerna, har du invändningar kan du kontakta Soode på Hamsterpaj."</em></li>
</ul>

<?php
	ui_bottom();
?>


