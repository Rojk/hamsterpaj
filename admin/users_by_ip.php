<?php
	require('../include/core/common.php');
	ui_top();
	
	if(!is_privilegied('ip_ban_admin'))
	{
		die('Not authorized');
	}
	
		$query = 'SELECT ll.logon_time, ll.user_id, l.username FROM login_log AS ll, login AS l WHERE ll.user_id = l.id AND ll.ip = "' . ip2long($_GET['ip']) . '" ORDER BY ll.logon_time DESC LIMIT 250';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		echo '<table>' . "\n";
		while($data = mysql_fetch_assoc($result))
		{
			echo '<tr><td>' . date('Y-m-d H:i:s', $data['logon_time']) . '</td><td><a href="/traffa/profile.php?id=' . $data['user_id'] . '">' .$data['username'] . '</a></td></tr>' . "\n";
		}	
		echo '</table>' . "\n";
	ui_bottom();
?>