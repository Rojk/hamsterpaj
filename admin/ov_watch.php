<?php

require('../include/core/common.php');
require_once(PATHS_INCLUDE . 'libraries/posts.php');
require_once(PATHS_INCLUDE . 'libraries/markup.php');
//require_once($hp_includepath . '/libraries/markup.php');
//require_once($hp_includepath . '/libraries/games.lib.php');
//require_once($hp_includepath . '/libraries/schedule.lib.php');
//require_once(PATHS_INCLUDE . 'libraries/tips.lib.php');

$ui_options['menu_path'] = array('admin', 'ov_watch');

ui_top($ui_options);

if(!is_privilegied('use_statistic_tools'))
{
	echo 'inte för dig...';
}
else
{

	$query = 'SELECT * FROM login l, admin_counts ac WHERE l.userlevel > 2 AND l.id = ac.user_id ORDER BY l.username';
	$result = mysql_query($query);
	while($data = mysql_fetch_assoc($result))
	{
		$ovs[] = $data;
	}
	
	echo '<h1>Ordningsvakter och deras förehavanden</h1>' . "\n";
	echo '<table>' . "\n";
	echo '<tr>' . "\n";
	echo '<td>Namn</td>' . "\n";
	echo '<td>Id</td>' . "\n";
	echo '<td>Borttagna inlägg</td>' . "\n";
	echo '<td>Nekade visningsbilder</td>' . "\n";
	echo '<td>Validerade visningsbilder</td>' . "\n";
	echo '<td>Andel nekade visningsbilder</td>' . "\n";
	echo '</tr>' . "\n";
	foreach($ovs as $ov)
	{
		echo '<tr>' . "\n";
		echo '<td>' . $ov['username'] . '</td>' . "\n";
		echo '<td>' . $ov['id'] . '</td>' . "\n";
		echo '<td>' . $ov['posts_removed'] . '</td>' . "\n";
		echo '<td>' . $ov['avatars_denied'] . '</td>' . "\n";
		echo '<td>' . $ov['avatars_approved'] . '</td>' . "\n";
		// echo '<td>' . $ov['avatars_denied'] / ($ov['avatars_denied'] + $ov['avatars_approved']) . '</td>' . "\n";
		echo '<td>' . round($ov['avatars_denied'] / ($ov['avatars_denied'] + $ov['avatars_approved']) * 100, 2).' % </td>' . "\n";
		echo '</tr>' . "\n";
	}
	echo '</table>' . "\n";
}	

//preint_r($_SESSION);
ui_bottom();

?>
