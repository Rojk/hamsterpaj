<?php
$resources[] = array('label' => 'Hela Hamsterpaj (.zip)', 'url' => 'http://www.hamsterpaj.net/open_source/site_2008_04_16.zip', 'highlight' => true);
$resources[] = array('label' => 'Gruppen "HP Open Source"', 'url' => 'http://www.hamsterpaj.net/traffa/groups.php?action=goto&groupid=10958');
$resources[] = array('label' => 'Hamsterpajs databasstruktur', 'url' => 'http://217.118.208.249/phpmyadmin/', 'alert' => 'Anvand anvandarnamn hp_structure och tomt losenord.');
$resources[] = array('label' => 'PHP-dokumentationen', 'url' => 'http://www.php.net/manual/en/');
$resources[] = array('label' => 'Hjälp till med fler länkar...', 'url' => 'http://www.hamsterpaj.net/diskussionsforum/hamsterpaj/open_source/hjaelp_till_med_fler_braatthasaker/sida_1.php');

$return .= '<ul>' . "\n";
foreach($resources as $resource)
{
	$return .= '<li><a href="' . $resource['url'] . '"' . ((isset($resource['highlight']) && $resource['highlight'] == true) ? ' class="highlighted"' : '') . ((isset($resource['alert']) && strlen($resource['alert']) > 0) ? ' onclick="alert(' . "'" . $resource['alert'] . "'" . ')"' : '') . '>' . $resource['label'] . '</a></li>' . "\n";
}
$return .= '</ul>' . "\n";
?>