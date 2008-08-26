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
			$friend_status = (mb_strlen($friend['user_status'], 'UTF8') > 17) ? mb_substr($friend['user_status'], 0, 14, 'UTF8') . '...' : $friend['user_status'];
			$options['output'] .= '						<li><a class="ui_business_card" href="/traffa/profile.php?user_id=' . $friend['user_id'] . '"><img src="http://images.hamsterpaj.net/famfamfam_icons/status_online.png" /></a> <a href="/traffa/profile.php?user_id=' . $friend['user_id'] . '">' . $friend['username'] . '</a> - ' . ((strlen(trim($friend['user_status'])) > 0) ? $friend_status : 'Ingen status') . '</li>' . "\n";
	}
	$options['output'] .= '					</ul>' . "\n";
	$options['output'] .= '					<p><a href="/traffa/friends.php">Visa alla vänner &raquo;</a></p>' . "\n";
?>