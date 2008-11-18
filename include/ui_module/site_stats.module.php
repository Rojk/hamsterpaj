<?php
$serialized = file_get_contents(PATHS_INCLUDE . 'cache/live_stats.phpserialized');
$info = unserialize($serialized); 

$options['output'] .= '<h4>Besökare</h4>' . "\n";
$options['output'] .= cute_number($info['visitors']) . "\n";
$options['output'] .= '<h4>Inloggade</h4>' . "\n";
$options['output'] .= cute_number($info['logged_in']) . "\n";
$options['output'] .= '<h4>Medlemmar</h4>' . "\n";
$options['output'] .= cute_number($info['members']) . "\n";
$options['output'] .= '<h4>Sidvisningar idag</h4>' . "\n";
$pageviews = query_cache(array('query' => 'SELECT views FROM pageviews WHERE date = "' . date('Y-m-d') . '" LIMIT 1'));
$options['output'] .= cute_number($pageviews[0]['views']); 
?>