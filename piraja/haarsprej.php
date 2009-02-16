<?php
	require('../include/core/common.php');
	
	$ui_options['title'] = 'Test av hårsprej';
	$ui_options['menu_path'] = array('hamsterpaj');

	$ui_options['custom_logo'] = 'http://images.hamsterpaj.net/piraja/hp_piraja_logo.png';

	event_log_log('piraja');

	ui_top($ui_options);
?>
<style>
	.piraja_haarsprej
	{
		display: block;
		float: left;
		width: 270px;
	}

	.piraja_haarsprej li
	{
		margin-bottom: 10px;
	}

	.piraja_haarsprej li h2
	{
		font-weight: bold;
	}

	.piraja_haarsprej li .description
	{
		font-weight: bold;
	}

	.piraja_haarsprej li p
	{
		margin: 0px;
		padding: 0px;
		margin-top: 3px;
		margin-bottom: 3px;
	}

</style>
<h1>Test av hårsprej</h1>

<img src="http://images.hamsterpaj.net/piraja/harsprej.png" />

<ol class="piraja_haarsprej">
	<li>

		<h2>Kladdigt</h2>
		<p class="description">
			Scwarzkopf taft – Ultra Fixing
		</p>
		<span class="price">
			Pris: 52 kr
		</span>
		<p>
			Håller frisyren på plats, men det blir lite kladdigt i håret. Luktar sliskigt och burken hade kunnat se snyggare ut, men priset är i alla fall okej.
		</p>

		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>

	<li>
		<h2>Gammal och ful design</h2>

		<p class="description">
			Sebastian Professional – Body Double
		</p>
		<span class="price">
			Pris 255 kr
		</span>
		<p>
			Luktar tandläkarmottagning och ger håret en tuggummikänsla. Ful design på flaskan, ser ut som mormors gamla som stått uppe på badrumsskåpet i 20 år. Inte värt priset.
		</p>
		<span class="grade">

			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>

	<li>
		<h2>Bra gummigrepp</h2>
		<p class="description">
			Wella – High Hair
		</p>

		<span class="price">
			Pris: 165 kr
		</span>
		<p>
			Intressant flaska, bra med gummigrepp runt, då behöver man inte oroa sig för att man ska tappa den. Ger en luftig och skön känsla och håller rufset på plats.
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
		<h2>Mumsig hallondoft</h2>
		<p class="description">

			got2b – 2 sexy
		</p>
		<span class="price">
			Pris: 75 kr
		</span>
		<p>
			Doftar hallon! Mycket sofistikerad och snygg flaska som gärna får stå framme i badrummet. Håller frisyren på plats utan att kännas stelt.
		</p>
		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
            <img src="http://images.hamsterpaj.net/piraja/test_score.png" />
            <img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>

	<li>
		<h2>Plus för doften</h2>
		<p class="description">
			System Professional – Just a Minute
		</p>

		<span class="price">
			Pris: 199 kr
		</span>
		<p>
			Håller en bra stadga i håret i mer än en minut, men inte nog länge för att man ska vara nöjd. Plus för doften dock.
		</p>
		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />

		</span>
	</li>

	<li>
		<h2>Bra pris!</h2>
		<p class="description">
			PHC – Professional Hair Care
		</p>

		<span class="price">
			Pris: 65 kr
		</span>
		<p>
			Pluspoäng direct för att priset är bra! Aningen kemisk i doften men sprejen håller frisyren på plats och gör inte håret stelt.
		</p>
		<span class="grade">
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
			<img src="http://images.hamsterpaj.net/piraja/test_score.png" />
		</span>
	</li>
</ol>

<br style="clear: both;" />


<p><a href="/piraja/">Mer fr&aring;n Piraja p&aring; Hamsterpaj</a></p>

<?php
	ui_bottom();
?>