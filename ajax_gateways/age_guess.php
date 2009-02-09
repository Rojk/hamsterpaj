<?php
	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'age_guess.lib.php');
	
	$output['result'] = age_guess_result();
	$output['hourglass'] = age_guess_hourglass();
	$output['toplist'] = age_guess_toplist();
	$output['statistics'] = age_guess_statistics();
	$output['main'] = age_guess_image();

	echo json_encode($output);
	
?>