<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";

	if($userinfo['login']['id'] == $_SESSION['login']['id'])
	{
?>
	<h1>Du har inte valt vad du vill ha på din presentation än!</h1>
	<p>
		Här på Hamsterpaj kan man inte bara ha en text på sin presentation, utan även foton, citat, en sorts
		framtidsplanerare och en del andra små moduler.<br />
		Just nu är din presentation tom, du måste välja moduler innan det syns något här. Precis under den här
		rutan har du en modulväljare, där du kan välja upp till fem stycken moduler och även välja ett
		färgtema för din sida. Om du inte vet vad du vill ha så kan vi tipsa om <em>Presentationstext</em>,
		<em>Fotoalbum</em> och <em>Favoritcitat</em>.
	</p>
	<p>
		Och du - det är bara du som ser inställningsrutan, den syns alltså inte för andra.
	</p>
<?php
	}
	else
	{
		echo '<h1>' . $userinfo['login']['username'] . ' har inte skapat sin presentation än!</h1>' . "\n";
		echo '<p>Innan det syns något här så måste ' . $userinfo['login']['username'] . ' välja presentationsmoduler.</p>' . "\n";
	}
?>
</div>
