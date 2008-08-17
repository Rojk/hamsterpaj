
<div id="user_flags">
	<?php
		include(PATHS_INCLUDE . 'user_flags.php');
		$query = 'SELECT ufl.* FROM user_flags AS uf, user_flags_list AS ufl WHERE user = "' . $userinfo['login']['id'] . '" AND ufl.id = uf.flag';
		$result = mysql_query($query) or die(report_sql_error($query));
		while($data = mysql_fetch_assoc($result))
		{
			$flags[] = $data;
		}
		
		/* Add some special flags */
		switch($userinfo['login']['userlevel'])
		{
			case 5:
				$flags[] = array('handle' => 'sysop', 'title' => 'Sysop', 'id' => 123);
				break;
			case 4:
				$flags[] = array('handle' => 'admin', 'title' => 'Administratör', 'id' => 121);			
				break;
			case 3:
				$flags[] = array('handle' => 'ov', 'title' => 'Ordningsvakt', 'id' => 122);			
				break;
			case 2:
				$flags[] = array('handle' => 'mh', 'title' => 'Medhjälpare', 'id' => 124);			
				break;
		}
		
		foreach($flags AS $data)
		{
			echo '<img src="http://images.hamsterpaj.net/user_flags/' . $data['handle'] . '.png" alt="' . $data['title'] . '" title="' . $data['title'] . '" id="' . $data['id'] . '" />' . "\n";
		}
	?>
</div>
<div id="flag_info">
	
</div>