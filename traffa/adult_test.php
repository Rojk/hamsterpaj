<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('mattan', 'gratis_musik');
	$ui_options['title'] = 'Gratis musik på Hamsterpaj!';
	ui_top($ui_options);


	$counter = 0;
	echo '<ul class="column_1">' . "\n";
	foreach($entries AS $handle => $entry)
	{
		if($count == ceil(count($entries)/2))
		{
			echo '</ul>' . "\n";
			echo '<ul class="column_2">' . "\n";
		}
		echo '<li>' . "\n";
		echo '<input type="checkbox" name="' . $handle . '" value="1" id="adult_test_input_' . $handle . '" />' . "\n";
		echo '<label for="adult_test_input_' . $handle . '">' . $entry['label'] . '</label>' . "\n";
		echo '</li>' . "\n";
		$counter++;
	}
	echo '</ul>' . "\n";
	
	ui_bottom();
?>


