<?php

$resources['start'] = array('label' => 'Open source start', 'url' => '/open_source/index.php');
$resources['theme_creation'] = array('label' => 'Skapa presentationsteman', 'url' => '/open_source/theme_creation.php');

$return .= '<ul>' . "\n";
foreach($resources as $handle => $resource)
{
	$return .= '<li><a href="' . $resource['url'] . '"' . (($handle == $parameters['open_source_menu_path']) ? ' class="highlighted"' : '') . '>' . $resource['label'] . '</a></li>' . "\n";
}
$return .= '</ul>' . "\n";

?>