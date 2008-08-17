<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";
?>
<?php
	$query = 'SELECT m.groupid, l.name, l.presentation, l.owner FROM groups_list AS l, groups_members AS m WHERE ';
	$query.= 'l.groupid = m.groupid AND m.userid = "' . $userinfo['login']['id'] . '" AND m.approved = "1" LIMIT 20;';
	$result = mysql_query($query) or report_sql_error($query);
	
	if (mysql_num_rows($result) > 0)
	{
		while ($data = mysql_fetch_assoc($result))
		{
			echo '<div style="padding: 1px;">';
			echo '<a style="font-weight:bold; text-decoration: none;" href="/traffa/groups.php?action=goto&amp;groupid=' . $data['groupid'] . ' ">' . $data['name'] . '</a> - ';
			echo '<span style="font-style: italic; ">' . substr($data['presentation'], 0, 30) . '... </span><br />';
			echo '</div>';
		}
	}
?>
</div>