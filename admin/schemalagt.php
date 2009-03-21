<?php

require('../include/core/common.php');
require_once(PATHS_LIBRARIES . 'schedule.lib.php');
$ui_options['menu_path'] = array('admin', 'schemalagt');
$ui_options['stylesheets'][] = 'schedule_list.css';
if(!is_privilegied('schedule_admin'))
{
	header('location: /');
	die();
}

ui_top($ui_options);

if(count($_GET) > 0)
{
	$options['release'] = strtotime($_GET['release']);
	$options['id'] = $_GET['id'];
	schedule_event_update($options);
}

echo '<h1>Schemalagt innehåll på Hamsterpaj.net</h1>' . "\n";

unset($options);

$scheduled = schedule_event_fetch();

foreach($scheduled AS $event)
{
	$buffer[$event['type']]++;
	$options['events'][] = $event;
}

$item_types['new_game'] = '<a href="/onlinespel/admin/">Spel</a>';
$item_types['new_flash'] = '<a href="/flashfilmer/admin/">Flashfilmer</a>';
$item_types['new_clip'] = '<a href="/filmklipp/admin/">Filmklipp</a>';
$item_types['new_image'] = '<a href="/roliga_bilder/admin/">Roliga bilder</a>';
$item_types['poll'] = '<a href="/poll/">Undersökningar</a>';
$item_types['sex_sense'] = 'Sex och sinne';
$item_types['music_guess'] = 'Gissa låten';

$warning_levels[100] = array('text' => 'Gott om objekt', 'class' => 'schedule_buffer_5');
$warning_levels[65] = array('text' => 'Fyll gärna på mer', 'class' => 'schedule_buffer_4');
$warning_levels[40] = array('text' => 'Börjar ta slut', 'class' => 'schedule_buffer_3');
$warning_levels[18] = array('text' => 'Brist på objekt!', 'class' => 'schedule_buffer_2');
$warning_levels[0] = array('text' => 'Akut brist på objekt!', 'class' => 'schedule_buffer_1');

foreach($item_types AS $handle => $label)
{
	$percentage = round($buffer[$handle] / (count($schedule_slots[$handle])*14) * 100);
	foreach($warning_levels AS $level => $info)
	{
		if($percentage >= $level)
		{
			echo '<div class="' . $info['class'] . '">' . "\n";
			echo $label . ': ' . $info['text'] . ' (' . $percentage . '% fullt)' . "\n";
			echo '</div>' . "\n";
			break;
		}
	}
}

echo '<p>Målet är att alltid ha minst två veckor i bufferten, här ovanför visas hur välfyllda buffertarna är för olika typer av objekt. Det går att fylla till över 100% det skadar inte systemet på något sätt och det är ingen risk att objekt försvinner.</p>' . "\n";

echo '<h2>Detaljerad händelselista</h2>' . "\n";
$options['editable'] = true;
schedule_event_list($options);

ui_bottom();
?>
