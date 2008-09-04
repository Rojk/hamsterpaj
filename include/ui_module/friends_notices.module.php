<?php
	if($_SESSION['friends_actions_lastupdate'] < time() - 60)
	{
		unset($_SESSION['friends_actions']);
		$options['show'] = 'new';
		$options['user_id'] = $_SESSION['login']['id'];
		$_SESSION['friends_actions'] = friends_actions_fetch($options);
		$_SESSION['friends_actions_lastupdate'] = time();
	}

	$friends = $_SESSION['friends_actions'];

	$options['output'] .= '					<ul>' . "\n";
	foreach($friends as $friend)
	{
		$options['output'] .= '									<li>' . $friend['username'] . ' - ' . count($friend['actions']) . (count($friend['actions']) > 1 ? ' nya' : ' nytt') . "\n";
		
		$options['output'] .= '							<div>' . "\n";
		$options['output'] .= '								<ul>' . "\n";
			$friends_actions = $friend['actions'];
			foreach($friends_actions as $friend_action)
			{
				switch ( $friend_action['action'] )
				{
					case 'friendship': $friend_action_action = 'Ny vän'; break;
					case 'photos': $friend_action_action = 'Nytt foto'; break;
					case 'diary': $friend_action_action = 'Nytt dagboksinlägg'; break;
				}
				
				$options['output'] .= '									<li><a href="/traffa/friends_notices_redirect.php?url=' . $friend_action['url'] . '">' . $friend_action_action . ': ' . $friend_action['label'] . '</a></li>' . "\n";
			}
		$options['output'] .= '								</ul>' . "\n";
		$options['output'] .= '							</div>' . "\n";
		$options['output'] .= '						</li>' . "\n";
	}
	
	$options['output'] .= '					</ul>' . "\n";
?>