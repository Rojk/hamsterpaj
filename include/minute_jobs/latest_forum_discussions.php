<?php
require_once(PATHS_INCLUDE . 'libraries/discussions.php');
require_once(PATHS_INCLUDE . 'libraries/tags.php');

/*

$quality_slots[0] = array('quality_min' => '-1', 'quality_max' => '-0.65');
$quality_slots[1] = array('quality_min' => '-0.65', 'quality_max' => '0.4');
$quality_slots[2] = array('quality_min' => '0.4', 'quality_max' => '1');

foreach($quality_slots AS $quality_slot => $fetch)
{
	$fetch['limit'] = 10;
	$fetch['type'] = 'forum';
	
	$discussions = discussions_fetch($fetch);
	
	$serialized = serialize($discussions);
	$file = fopen(PATHS_INCLUDE . 'cache/new_discussions/' . $quality_slot . '.phpserialized', 'w');
	
	fwrite($file, $serialized);
}

*/

	$fetch = array();
	$fetch['limit'] = 10;
	$fetch['type'] = 'forum';
	
	$discussions = discussions_fetch($fetch);
	
	$serialized = serialize($discussions);
	$file = fopen(PATHS_INCLUDE . 'cache/new_discussions/1.phpserialized', 'w');
	
	fwrite($file, $serialized);

?>