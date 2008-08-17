<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";

	$query = 'SELECT f.friend_id, l.username FROM friendslist AS f, login AS l WHERE f.user_id = "' . $userid . '" AND l.id = f.friend_id AND l.username NOT LIKE "Borttagen"';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	
	while($data = mysql_fetch_assoc($result))
	{
		$friends[$data['friend_id']] = $data['username'];
	}
	
	$query = 'SELECT f.user_id, l.username FROM friendslist AS f, login AS l WHERE f.friend_id = "' . $userid . '" AND l.id = f.user_id AND l.username NOT LIKE "Borttagen"';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	
	while($data = mysql_fetch_assoc($result))
	{
		$remote_friends[$data['user_id']] = $data['username'];
	}
	
	foreach(array_keys($remote_friends) AS $id)
	{
		if(!isset($friends[$id]))
		{
			$remote[] = array('id' => $id, 'username' => $remote_friends[$id]);
		}
	}
	
	foreach(array_keys($friends) AS $id)
	{
		if(isset($remote_friends[$id]))
		{
			$two_way[] = array('id' => $id, 'username' => $friends[$id]);
		}
		else
		{
			$one_way[] = array('id' => $id, 'username' => $friends[$id]);			
		}
	}
	
	function friends_cloud($users)
	{
		echo '<ul class="digga_cloud" style="margin: 0px; padding: 0px;">' . "\n";
		$class = 'even';
		foreach($users AS $user)
		{
			echo '<li class="' . $class . '">' . "\n";
			$class = ($class == 'odd') ? 'even' : 'odd';
			echo '<a href="/traffa/profile.php?id=' . $user['id'] . '">' . $user['username'] . '</a> ';
			echo '</li>' . "\n";
		}
		echo '<li style="float: none; clear: both;"></li>' . "\n";
		echo '</ul>' . "\n";
	}

/*	
	function friends_cloud($users)
	{
		foreach($users AS $user)
		{
			echo '<a href="/traffa/profile.php?id=' . $user['id'] . '">' . $user['username'] . '</a> ';
		}
	}
*/

	echo '<h3>Användare som ' . $userinfo['login']['username'] . ' är kompis med</h3>';
	friends_cloud($two_way);
	
	echo '<h3>Användare som ' . $userinfo['login']['username'] . ' vill vara kompis med, men som dissar ';
	if($userinfo['userinfo']['gender'] == 'P')
	{
		echo 'honom';
	}
	elseif($userinfo['userinfo']['gender'] == 'F')
	{
		echo 'henne';
	}
	else
	{
		echo 'eh, den?';
	}
	echo '</h3>';
	friends_cloud($one_way);
	
	echo '<h3>Användare som vill vara kompis med ' . $userinfo['login']['username'] . ' men som blir dissade</h3>';
	friends_cloud($remote);
	
?>

</div>
