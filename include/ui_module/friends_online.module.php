<?php
	//require_once('../libraries/friends.lib.php');
	if($_SESSION['friends_lastupdate'] < time() - 60)
	{
		unset($_SESSION['friends']);
		$options['user_id'] = $_SESSION['login']['id'];
		$_SESSION['friends'] = friends_fetch_online_smart($options);
		$_SESSION['friends_lastupdate'] = time();
	}

	$friends = $_SESSION['friends'];

	$options['output'] .= '					<ul>' . "\n";
	foreach($friends as $friend)
	{
		$total_strlen = mb_strlen($friend['user_status'] . $friend['username'], 'UTF8');
		$max_length = 24;
		$friend_status = ($total_strlen > 30) ? mb_substr($friend['user_status'], 0, $max_length - strlen($friend['username']), 'UTF8') . '...' : $friend['user_status'];
		$options['output'] .= '						<li><a class="ui_business_card" href="/traffa/profile.php?user_id=' . $friend['user_id'] . '"><img src="http://images.hamsterpaj.net/famfamfam_icons/status_online.png" /></a> <a href="/traffa/profile.php?user_id=' . $friend['user_id'] . '">' . $friend['username'] . '</a> - <span title="' . ((strlen(trim($friend['user_status'])) > 0) ? $friend['user_status'] : 'Ingen status') . '">' . ((strlen(trim($friend['user_status'])) > 0) ? $friend_status : 'Ingen status') . '</span></li>' . "\n";
	}
	$options['output'] .= '					</ul>' . "\n";
	$options['output'] .= '					<p><a href="/traffa/friends.php">Visa alla vänner &raquo;</a></p>' . "\n";
?>