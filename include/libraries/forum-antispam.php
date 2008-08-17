<?php
	$FORUM_ANTISPAM['credit_increasement'] = 10/3600; /* Ten credits per hour */
	$FORUM_ANTISPAM['max_credits'] = 1000;

	function forum_antispam_credits()
	{
		global $FORUM_ANTISPAM;
		
		$time_diff = time() - $_SESSION['login']['last_forum_antispam_modification'];
		
		$score = $_SESSION['login']['last_forum_antispam_score'];
		
		$score += $time_diff * $FORUM_ANTISPAM['credit_increasement'];
		$score = ($score > $FORUM_ANTISPAM['max_credits']) ? $FORUM_ANTISPAM['max_credits'] : $score;
		
		return $score;
	}
	
	function forum_antispam_update($cost)
	{
		$score = forum_antispam_credits();
		$new_score = $score - $cost;
		
		$new_info['login']['last_forum_antispam_modification'] = time();
		$new_info['login']['last_forum_antispam_score'] = $new_score;
		
		login_save_user_data($_SESSION['login']['id'], $new_info);
		
		session_merge($new_info);
	}
	
	function forum_antispam_cost($options)
	{
		/*
			$options['post_quality'];
			$options['discussion_quality'];
		*/
		if($options['post_quality'] < $options['discussion_quality'])
		{
			$cost = ($options['discussion_quality'] - $options['post_quality']) * -30;
			return $cost - ($cost * 2);
		}
		
		return 0;
	}
?>