<?php
	require('./include/core/common.php');

	$_SESSION['disablesteve'] = 1;

	$ui_options['current_menu'] = 'hamsterpaj';
	$ui_options['title'] = 'Vila i frid, Steve';

	ui_top($ui_options);
?>

<h1>Vila i frid, Steve</h1>

<img src="http://images.hamsterpaj.net/steve/steve_tombstone.jpg" />

<?php
		
	ui_bottom();
?>


