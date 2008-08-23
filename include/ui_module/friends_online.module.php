<?php
	$friends = $_SESSION['friends'];

	$options['output'] .= '					<ul>' . "\n";
	foreach($friends as $friend)
	{
		if($friend['onlinestatus']['handle'] == 'online')
		{
			$options['output'] .= '						<li><a class="ui_business_card" href="#"><img src="http://images.hamsterpaj.net/famfamfam_icons/status_online.png" /></a> <a href="/traffa/profile.php?user_id=' . $friend['user_id'] . '">' . $friend['username'] . '</a> - ' . ((strlen(trim($friend['user_status'])) > 0) ? $friend['user_status'] : 'Ingen status') . '</li>' . "\n";
		}
	}
	$options['output'] .= '					</ul>' . "\n";
	$options['output'] .= '					<p><a href="/traffa/friends.php">Visa alla vänner &raquo;</a></p>' . "\n";
?>