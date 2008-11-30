<?php
	function online_ovs()
	{
		$query = query_cache(array('query' => 'SELECT l.username AS username, l.id AS user_id FROM login AS l, privilegies AS pl WHERE pl.user = l.id AND pl.privilegie = "user_management_admin" ORDER BY lastaction DESC LIMIT 5'));
		foreach($query AS $row)
		{
			if($row['lastaction'] > time() - 600)
			{
				$out .= '<li><a href="/traffa/profile.php?user_id=' . $row['id'] . '">' . $row['username'] . '</a></li>' . "\n";
				
				$many[] = 'Igge the new guy!'; //Random data, no matter what value it is
			}
		}
		$count = count($many);
		if ($count > 0)
		{
			return $out;
		}
		else
		{
			return false;
		}
	}
	
	$options['output'] .= '<div class="ovlist">' . "\n";
	$options['output'] .= '<ul>' . "\n";
	$options['output'] .= online_osv() . "\n";
	$options['output'] .= '</ul>' . "\n";
	$options['output'] .= '</div>' . "\n";
	?>