<?php

require('../include/core/common.php');
require_once(PATHS_INCLUDE . 'libraries/posts.php');
require_once(PATHS_INCLUDE . 'libraries/markup.php');

$ui_options['menu_path'] = array('admin', 'ov_watch');
$ui_options['stylesheets'][] = 'forms.css';


if(!is_privilegied('use_statistic_tools'))
{
	$out .= 'inte för dig...';
}
else
{
	$query = 'SELECT user FROM privilegies GROUP BY(user)';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while ($data = mysql_fetch_assoc($result))
	{
		$privilegied_users[] = $data['user'];
	}
	
	$query = 'SELECT l.id, l.username, posts_removed, avatars_denied, avatars_approved FROM login AS l, admin_counts AS ac WHERE (l.id = ac.user_id) AND (';
	$privilegied_users_count = count($privilegied_users);
	$count = 0;
	foreach ($privilegied_users as $user_id)
	{
		$query .= ' l.id = ' . $user_id;
		$count++;
		if ($privilegied_users_count != $count)
		{
			$query .= ' OR';
		}
	}
	$query .= ') ORDER BY l.username';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$ovs[] = $data;
	}
	$out .= '<fieldset>' . "\n";
	$out .= '<legend>Ordningsvakter och deras förehavanden</legend>' . "\n";
	$out .= '<table class="form">' . "\n";
	$out .= '<tr>' . "\n";
	$out .= '<th>Namn</th>' . "\n";
	$out .= '<th>Id</th>' . "\n";
	$out .= '<th>Borttagna inlägg</th>' . "\n";
	$out .= '<th>Nekade visningsbilder</th>' . "\n";
	$out .= '<th>Validerade visningsbilder</th>' . "\n";
	$out .= '<th>Andel nekade visningsbilder</th>' . "\n";
	$out .= '</tr>' . "\n";
	
	foreach($ovs as $ov)
	{
		$out .= '<tr>' . "\n";
		$out .= '<td>' . $ov['username'] . '</td>' . "\n";
		$out .= '<td>' . $ov['id'] . '</td>' . "\n";
		$out .= '<td>' . $ov['posts_removed'] . '</td>' . "\n";
		$out .= '<td>' . $ov['avatars_denied'] . '</td>' . "\n";
		$out .= '<td>' . $ov['avatars_approved'] . '</td>' . "\n";
		// $out .= '<td>' . $ov['avatars_denied'] / ($ov['avatars_denied'] + $ov['avatars_approved']) . '</td>' . "\n";
		$out .= '<td>' . round($ov['avatars_denied'] / ($ov['avatars_denied'] + $ov['avatars_approved']) * 100, 2).' % </td>' . "\n";
		$out .= '</tr>' . "\n";
	}
	$out .= '</table>' . "\n";
}	

ui_top($ui_options);
echo $out;
ui_bottom();

?>
