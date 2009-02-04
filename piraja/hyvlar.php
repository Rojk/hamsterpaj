<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE  . 'libraries/entertain.lib.php');
	require_once(PATHS_INCLUDE  . 'libraries/rank.lib.php');
	require_once(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/fp_modules.lib.php');

	$ui_options['javascripts'][] = 'fp_common_modules.js';
	$ui_options['stylesheets'][] = 'fp_common_modules.css';
	
	$ui_options['title'] = 'Test av rakhyvlar';
	$ui_options['menu_path'] = array('hamsterpaj');

	$ui_options['custom_logo'] = 'http://images.hamsterpaj.net/piraja/hp_piraja_logo.png';

	event_log_log('piraja');

	ui_top($ui_options);
?>
<style>
	.piraja_hyvlar
	{
		display: block;
		float: left;
		width: 270px;
	}

	.piraja_hyvlar li
	{
		margin-bottom: 10px;
	}

	.piraja_hyvlar li h2
	{
		font-weight: bold;
	}

	.piraja_hyvlar li .description
	{
		font-weight: bold;
	}

	.piraja_hyvlar li p
	{
		margin: 0px;
		padding: 0px;
		margin-top: 3px;
		margin-bottom: 3px;
	}

</style>

<h1>Pirajan Petrus testar rakhyvlar för män</h1>

<img src="http://images.hamsterpaj.net/piraja/hyvlar.png" />

<ol class="piraja_hyvlar">
	<li>
		<h2>Samurajen</h2>
		<p class="description">
			Gillette Fusion (utan vibrator men med en massa blad!)
		</p>
		<span class="price">
			Pris: 139 kr
		</span>
		<p>
			Okej... nu har jag testat Gillette Fusion med hela 5 rakblad. Jag måste erkänna att jag var lite nervös.
			Skulle den här samurajen karva sig ner till benmärgen? Rakhyveln hade lite problem med lång skäggväxt
			då det var lite svårt för de långa stråna att få plats mellan de trångt sittande bladen. Har man däremot
			endast lite skäggstubb funkar den super.
		</p>
		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>

	<li>
		<h2>Kommunisten</h2>
		<p class="description">
			Gibellini (Lidl)
		</p>
		<span class="price">
			Pris 39,90 kr
		</span>
		<p>
			Är detta en osthyvel? DDR-feeling över hela paketet.
		</p>
		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>

	<li>
		<h2>Uma Thurman</h2>
		<p class="description">
			Gillette Fusion Power (med vibrator)
		</p>
		<span class="price">
			Pris: 179 kr
		</span>
		<p>
			Beatrix Kiddo (från <em>Kill Bill</em>) i formen av en rakhyvel. Inget jag testat tidigare
			använder sina vassa blad med sådan precision. En klar favorit när du vill ha den där sammetslena
			huden alla trånar efter. Dock hade den samma problem som sin icke vibrerande broder, svårt
			med lång stubb.
		</p>
		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>
</ol>

<ol class="piraja_hyvlar">
	<li value="4">
		<h2>Skalbaggen</h2>
		<p class="description">
			Coop (utan vibrator)
		</p>
		<span class="price">
			Pris: 54,90 kr
		</span>
		<p>
			Den här känns som en arg skalbagge - i silver med vassa käftar. Rakhyveln gör grovjobbet men är
			ingen finlirare. Den fungerar dock ypperligt på lång skäggväxt.
		</p>
		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>

	<li>
		<h2>Vinnaren</h2>
		<p class="description">
			Gillette M3 Power (med vibrator)
		</p>
		<span class="price">
			Pris: 179 kr
		</span>
		<p>
			Ännu en förklädd vibrator med vassa tänder. M 3:an tar hand om det långa stubbet bättre än
			storebrodern GFP men är inte lika vass på att få huden sammetslen. Måste nog ses som testets
			vinnare ändå på grund av bästa allroundförmåga.
		</p>
		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>

	<li>
		<h2>Volvon</h2>
		<p class="description">
			Gillette Mach 3 (utan vibrator)
		</p>
		<span class="price">
			Pris: 99 kr
		</span>
		<p>
			Rakhyvlarnas Volvo V70. Trogen, stabil, inget extra, men man blir å andra sidan inte besviken.
		</p>
		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>
</ol>

<br style="clear: both;" />

<h1>Om testet</h1>
<p>
	Det här är ett litet experiment där Hamsterpaj lånar lite innehåll från Piraja, en tidning
	som ligger ute på landets gymnasieskolor.<br />
	Den tappre testaren heter Petrus och jobbar till vardags som testkanin på Piraja. 
</p>

<?php
echo '<p><a href="/piraja/">Mer fr&aring;n Piraja p&aring; Hamsterpaj</a></p>' . "\n";
	ui_bottom();
	?>
