<?php
	require('include/core/common.php');
	include('include/libraries/schedule.lib.php');
	
	//$var = sex_sense_fetch_posts($data['fetch_item_options']);
	
	$options['release_after'] = time();
	$options['released'] = 0;
	$events = schedule_event_fetch($options);
	foreach ($events as $event)
	{
		$data = unserialize($event['data']);
		preint_r($data);
	}
	

?>
	