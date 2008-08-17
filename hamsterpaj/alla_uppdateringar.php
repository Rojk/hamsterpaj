<?php

  require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/discussions.php');
	require(PATHS_INCLUDE . 'libraries/posts.php');
	require(PATHS_INCLUDE . 'libraries/quality.php');
	require(PATHS_INCLUDE . 'libraries/forum-antispam.php');
	include($hp_path . 'forum_new/parser.php');

	$ui_options['menu_path'] = array('hamsterpaj', 'nytt');
  ui_top($ui_options);

	$recent_updates = query_cache(array('query' => 'SELECT * FROM recent_updates ORDER BY id DESC'));

	echo '<table>' . "\n";
	foreach($recent_updates AS $data)
	{
		echo '<!-- Recent update "' . $data['label'] . '" -->' . "\n";
		echo '<tr>' . "\n";
		echo '<td class="timestamp">' . fix_time($data['timestamp']) . '</td>';
		echo '<td class="type_label">' . $RECENT_UPDATES[$data['type']] . '</td>';	
		if(strlen($data['url']) > 1)
		{
			echo '<td><a href="/recent_updates_redirect.php?id=' . $data['id'] . '&url=' . urlencode($data['url']) . '">' . $data['label'] . '</a></td>';
		}
		else
		{
			echo '<td>' . $data['label'] . '</td>';			
		}
		echo (strlen($data['url']) > 1) ? '<td class="clicks">' . $data['clicks'] . ' klick</td>' : '';
		echo '</tr>' . "\n";
	}
	echo '</table>' . "\n";

	ui_bottom();
?>
