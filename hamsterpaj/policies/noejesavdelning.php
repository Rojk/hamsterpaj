<?php
	require('/home/www/standard.php');
	require(PATHS_LIBRARIES . 'films.lib.php');
	$ui_options['menu_path'] = array('hamsterpaj', 'policies', 'noejesavdelning');
	$ui_options['title'] = 'Hamsterpajs digitala nöjesavdelningar';

	ui_top($ui_options);
?>

<h1>Information om Hamsterpajs digitala nöjesavdelningar</h1>

<p>
	Den här informationssidan berör endast våra avdelningar för
	<ul>
		<li><a href="/bilder/">Roliga bilder</a></li>
		<li><a href="/klipp/">Filmklipp</a></li>
		<li><a href="/flash/">Flashfilmer</a></li>
	</ul>
</p>

<h2>Finns det våld, sex eller droger på er underhållningsavdelning?</h2>
<p>
	På Hamsterpaj lägger vi inte upp någonting som vi bedömmer att kan skada ungdomar över 13 år.
	Vi tar avstånd från och lägger aldrig upp brutalt våld i underhållningssyfte. Filmer där folk
	får allvarligare skador än skrubbsår eller blåmärken publicerar vi inte.
</p>
<p>

</p>
<?php
	$parameters['fetch']['handle'] = array('miss_teen_usa', 'tv4__mensvaerk');
	preint_r($parameters);
	films_fetch_and_list($parameters);
?>

<h2>Så här kan du tipsa om nya roliga grejor!</h2>
<p>
	
</p>


<h2>Vi har sällan licenser eller rättigheter till vårt innehåll</h2>
<p>
	
</p>

<h2>Frågor eller synpunkter?</h2>
<p>
	Kontakta VD Johan Höglund om den här sidan inte gav dig svar på dina frågor.<br />
	<strong>E-post: </strong> Johan@hamsterpaj.net<br />
	<strong>Mobil: </strong> 0768 999 580
</p>

<h2>Information in english</h2>
<p>
	For information in english, please send an email with your request or call our C.E.O<br />
	<strong>E-mail: </strong> Johan@hamsterpaj.net<br />
	<strong>Cell phone: </strong> +46 768 999 580<br />
	Please note that Sweden is GMT+1.
</p>

<?php
	ui_bottom();
?>