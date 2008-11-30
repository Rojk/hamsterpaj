<?php
	function online_ovs($level)
	{
		//Preliminary stylesheet.
		$nextlevel = $level + 1;
		$userlevel_fetch = ($level == 3) ? "userlevel = 3 OR userlevel = 4" : "userlevel = 5";
		$query = query_cache(array('query' => 'SELECT userlevel, id, username, lastaction FROM login WHERE ' . $userlevel_fetch . ' ORDER BY lastaction DESC LIMIT 5'));
		foreach($query AS $row)
		{
			if($row['lastaction'] > time() - 600)
			{
				$out .= '<li><a href="/traffa/profile.php?user_id=' . $row['id'] . '">' . $row['username'] . '</a></li>' . "\n";
				
				$many[] = 'Igge the new guy!'; //Random data
			}
		}
		//$out .= '<li><a href="/traffa/profile.php?user_id=900497">Iggepigge</a></li>' . "\n";
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
	
	if (online_ovs(3))
	{
		$return .= online_ovs(3);
	}
	elseif (online_ovs(4))
	{
		$return .= online_ovs(4);
	}
	elseif (online_ovs(5))
	{
		$return .= online_ovs(5);
	}
	
	$options['output'] .= '<div class="ovlist">' . "\n";
	$options['output'] .= '<ul>' . "\n";
	$options['output'] .= $return . "\n";
	$options['output'] .= '</ul>' . "\n";
	$options['output'] .= '</div>' . "\n";