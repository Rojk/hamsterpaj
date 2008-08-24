<?php
	$friends = $_SESSION['friends'];

	$options['output'] .= '					<ul>' . "\n";
	foreach($friends as $friend)
	{
		$friend_onlinestatus = login_onlinestatus($friend['lastaction'], $friend['lastaction']);
		if($friend_onlinestatus['handle'] == 'online')
		{
			$friend_status = (mb_strlen($friend['user_status'], 'UTF8') > 17) ? mb_substr($friend['user_status'], 0, 14, 'UTF8') . '...' : $friend['user_status'];
			$options['output'] .= '						<li><a class="ui_business_card" href="#"><img src="http://images.hamsterpaj.net/famfamfam_icons/status_online.png" /></a> <a href="/traffa/profile.php?user_id=' . $friend['user_id'] . '">' . $friend['username'] . '</a> - ' . ((strlen(trim($friend['user_status'])) > 0) ? $friend_status : 'Ingen status') . '</li>' . "\n";
		}
	}
	$options['output'] .= '					</ul>' . "\n";
	$options['output'] .= '					<p><a href="/traffa/friends.php">Visa alla vänner &raquo;</a></p>' . "\n";
?>