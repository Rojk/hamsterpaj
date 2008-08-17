<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Hamsterpaj är nere just nu</title>
<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />
<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />

<style type="text/css">
@import url('/stylesheets/max_connections.css');
</style>

</head>

<body>
	
	<div class="main">
		<h1>Felsökning!</h1>
		<p>
			Vi har ett fel i webbservern, just nu försöker vi överlasta den utan att ha igång databasen. Det är inget farligt, även om det kan se lite konstigt ut.
		</p>
		<p>
			När antalet sidladdningar/sekund går över en viss gräns slår maskinen ifrån och slutar svara, vi börjar hitta felet, frågan är om vi kan lösa det...
		</p>

		<h4>Fortsätt gärna titta in på Hamsterpaj någon gång i minuten!</h4>
		<p>
			Vi behöver ha många besökare för att våra testar ska funka
		</p>

		<p>
			//Johan
		</p>
		<p>
			<?php
				echo rand();
			?>
		</p>

	</div>
	
	<script>
		function reload()
		{
//			window.location = '/' + Math.random() + '.php';
		}
		setTimeout("reload()", 50);
	</script>
	
</body>
</html>
<?php
	exit;
?>
