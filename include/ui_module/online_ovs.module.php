<?php
	function online_ovs()
	{
		$query = query_cache(array('query' => 'SELECT l.username AS username, l.id AS user_id FROM login AS l, privilegies AS pl WHERE pl.user = l.id AND pl.privilegie = "user_management_admin" ORDER BY lastaction DESC LIMIT 5'));
		foreach($query AS $row)
		{
			$out .= '<li><a href="/traffa/profile.php?user_id=' . $row['user_id'] . '">' . $row['username'] . '</a></li>' . "\n";
		}
		return $out;
	}
	
	$options['output'] .= '<div class="ovlist">' . "\n";
	$options['output'] .= '<ul>' . "\n";
	$options['output'] .= online_ovs() . "\n";
	$options['output'] .= '</ul>' . "\n";
	$options['output'] .= '</div>' . "\n";
	?>
