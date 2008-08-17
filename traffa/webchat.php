<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('traeffa', 'webchat');
	$ui_options['title'] = 'Klotterplanket på Hamsterpaj';
	$ui_options['stylesheets'][]= 'webchat.css';
	$ui_options['javascripts'][]= 'webchat.js';
	ui_top($ui_options);
	
	echo '<h1>Klotterplank (tidig testversion!)</h1>' . "\n";
	echo '<p>Sluta klaga, kom gärna med trevliga, konkreta förslag. Oförskämdheter kan ni ju ge fan i så länge detta är en alfa...</p>' . "\n";
	
	echo '<div id="webchat_form_area">' . "\n";
	echo '<input id="webchat_message_input" />' . "\n";
	echo '</div>' . "\n";
	
	echo '<div id="webchat_user_info">' . "\n";
	
	echo '</div>' . "\n";
	
	echo '<br style="clear: both;" />' . "\n";
	
	echo '<div id="webchat_entry_list">' . "\n";
	$query = 'SELECT * FROM webchat WHERE channel = "' . $CHANNEL . '" ORDER BY id LIMIT 25';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{

	}
	echo '</div>' . "\n";


	ui_bottom();
?>


