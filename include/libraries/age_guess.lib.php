<?php

	/* Normally, code isn't executed directly in a library, but this makes sure that the age_guess array is populated */
	if(!isset($_SESSION['age_guess']['score']) || true)
	{
		age_guess_populate_session();
	}

	function age_guess_populate_session()
	{
		if(login_checklogin())
		{
			$query = 'SELECT * FROM age_guess_scoring WHERE user = "' . $_SESSION['login']['id'] . '" AND week = "' . date('YW') . '"';
			$result = mysql_query($query);
			$data = mysql_fetch_assoc($result);
			$_SESSION['age_guess']['score'] = $data['score'];
			$_SESSION['age_guess']['viewed_images'] = $data['viewed_images'];
			$_SESSION['age_guess']['correct_guesses'] = $data['correct_guesses'];
			$_SESSION['age_guess']['correct_ratio'] = $data['correct_ratio'];
		}
	}

	function age_guess_toplist()
	{
		$return .= '<h3>Topplista</h3>' . "\n";
		$return .= '<ol>' . "\n";
		$query = 'SELECT l.username, ags.user, ags.score FROM login AS l, age_guess_scoring AS ags WHERE l.id = ags.user ORDER BY ags.score DESC LIMIT 5';
		$result = mysql_query($query);
		while($data = mysql_fetch_assoc($result))
		{
			$return .= '<li><a href="/traffa/profile.php?id=' . $data['user'] . '">' . $data['username'] . '</a> (' . $data['score'] . 'p)</li>' . "\n";			
		}
		$return .= '</ol>' . "\n";
		$return .= '...' . "\n";

		$return .= '<ol>' . "\n";
		$query = 'SELECT COUNT(*) AS rank FROM age_guess_scoring WHERE score > "' . $_SESSION['age_guess']['score'] . '"';
		$result = mysql_query($query);
		$data = mysql_fetch_assoc($result);
		$start_rank = $data['rank'] - 1;

		$query = 'SELECT l.username, ags.user, ags.score FROM login AS l, age_guess_scoring AS ags WHERE l.id = ags.user AND score > "' . $_SESSION['age_guess']['score'] . '" ORDER BY ags.score ASC LIMIT 2';
		$result = mysql_query($query);
		for($row = 1; $data = mysql_fetch_assoc($result); $row++)
		{
			$value = ($row == 2) ? ' value="' . $start_rank . '"': '';
			$extra_toplist = '<li' . $value . '><a href="/traffa/profile.php?id=' . $data['user'] . '">' . $data['username'] . '</a> (' . $data['score'] . 'p)</li>' . "\n" . $extra_toplist;
		}
		$extra_toplist .= '<li><a href="/traffa/profile.php?id=' . $_SESSION['login']['id'] . '">' . $_SESSION['login']['username'] . '</a> (' . $_SESSION['age_guess']['score'] . 'p)</li>' . "\n";
		$query = 'SELECT l.username, ags.user, ags.score FROM login AS l, age_guess_scoring AS ags WHERE l.id = ags.user AND score <= "' . $_SESSION['age_guess']['score'] . '" AND ags.user != "' . $_SESSION['login']['id'] . '" ORDER BY ags.score DESC LIMIT 2';
		$result = mysql_query($query);
		while($data = mysql_fetch_assoc($result))
		{
			$extra_toplist .= '<li><a href="/traffa/profile.php?id=' . $data['user'] . '">' . $data['username'] . '</a> (' . $data['score'] . 'p)</li>' . "\n";
		}

		$return .= $extra_toplist;
		
		$return .= '</ol>' . "\n";
		return $return;
	}
	
	function age_guess_hourglass()
	{
		$return .= 'Hourglass';	
		return $return;
	}

	function age_guess_statistics()
	{
		$return .= '<h3>Din statistik, den här veckan</h3>' . "\n";
		$return .= '<table>' . "\n";
		$return .= '<tr><th>Poäng</th><th>Visade bilder</th><th>Rätta gissningar</th></tr>' . "\n";
		$return .= '<tr><td>' . $_SESSION['age_guess']['score'] . 'p</td>' . "\n";
		$return .= '<td>' . $_SESSION['age_guess']['viewed_images'] . '</td>' . "\n";
		$return .= '<td>' . $_SESSION['age_guess']['correct_guesses'] . ' (' . round($_SESSION['age_guess']['correct_ratio']*100) . '%)</td>' . "\n";
		$return .= '</tr></table>' . "\n";
		
		return $return;
	}
	
	function age_guess_age_inputs()
	{
		for($i = 1; $i < 100; $i++)
		{
			$return .= $i . ' ';
		}
		return $return;
	}

	function age_guess_result()
	{
		global $AGE_GUESS_COMMENTS;
		
		$return .= '<img src="http://images.hamsterpaj.net/images/users/thumb/' . $_SESSION['age_guess']['current_user'] . '" />' . "\n";

		$age_diff = ($_GET['guessed_age'] == 'skip') ? 1 : $_GET['guessed_age'] - $_SESSION['age_guess']['current_age'];

		switch(abs($age_diff))
		{
			case 0:
				$score = 5;
				$score_class = 'positive';
				break;
			case 1:
				$score = 0;
				$score_class = 'unchanged';
				break;
			default:
				$score = abs($age_diff)*-1 + 1;
				$score = ($score < -5) ? -5 : $score;
				$score_class = 'negative';
		}
		
		$return .= '<h2 class="age_guess_score_' . $score_class . '">' . $score . 'p</h2>' . "\n";

		foreach($AGE_GUESS_COMMENTS AS $comment => $differences)
		{
			foreach($differences AS $difference)
			{
				$comments_by_diff[$difference][] = $comment;
			}
		}
		$comment = ($_GET['guessed_age'] == 'skip') ? 'Hoppade över' : $comments_by_diff[$age_diff][rand(0, count($comments_by_diff[$age_diff])-1)];
		$return .= '<h2>' . $comment . '</h2>' . "\n";
		
		
		$return .= '<p class="age_guess_answer_text"><a href="/traffa/profile.php?id=' . $_SESSION['age_guess']['current_user'] . '">' . $_SESSION['age_guess']['current_username'] . '</a>' . "\n";
		$return .= ' är ' . $_SESSION['age_guess']['current_age'] . ' år</p>';
		
		$return .= '<button onclick="window.open(\'/traffa/profile.php?id=' . $_SESSION['age_guess']['current_user'] . '\');">Besök i nytt fönster</button>' . "\n";
		
		if(is_numeric($_GET['guessed_age']))
		{
			event_log_log('age_guess_guess');
			
			/* Log answer to database */
			$insertquery = 'INSERT INTO age_guess_logs (user, age_' . $_GET['guessed_age'] . ') VALUES("' . $_SESSION['age_guess']['current_user'] . '", 1)';
			$updatequery = 'UPDATE age_guess_logs SET age_' . $_GET['guessed_age'] . ' = age_' . $_GET['guessed_age']  . ' + 1 WHERE user = "' . $_SESSION['age_guess']['current_user'] . '" LIMIT 1';
			
			mysql_query($insertquery) or mysql_query($updatequery);

			if(login_checklogin())
			{
				$correct = ($age_diff == 0) ? 1 : 0;

				$insertquery = 'INSERT INTO age_guess_scoring (user, week, score, viewed_images, correct_guesses, correct_ratio)';
				$insertquery .= ' VALUES("' . $_SESSION['login']['id'] . '", "' . date('YW') . '", "' . $score . '", 1, ' . $correct . ', correct_guesses/viewed_images)';
				$updatequery = 'UPDATE age_guess_scoring SET score = score + ' . $score . ', viewed_images = viewed_images + 1, correct_guesses = correct_guesses + ' . $correct . ', correct_ratio = correct_guesses/viewed_images';
				$updatequery .= ' WHERE user = "' . $_SESSION['login']['id'] . '" AND week = "' . date('YW') . '" LIMIT 1';				
				mysql_query($insertquery) or mysql_query($updatequery);
				
				$_SESSION['age_guess']['score'] += $score;
				$_SESSION['age_guess']['correct_guesses'] += $correct;
				
				if($_SESSION['age_guess']['score'] < -10)
				{
					$_SESSION['age_guess']['score'] = -10;
					$query = 'UPDATE age_guess_scoring SET score = -10 WHERE user = "' . $_SESSION['login']['id'] . '" AND week = "' . date('YW') . '" LIMIT 1';
					mysql_query($query);
				}
				
				/* Find out if the remote user is online, if so, open the session and send a quicknote about the vote */
				if($score != 5)
				{
					$query = 'SELECT session_id, lastaction FROM login WHERE id = "' . $_SESSION['age_guess']['current_user'] . '" LIMIT 1';
					$result = mysql_query($query);
					if($data = mysql_fetch_assoc($result))
					{
						if($data['lastaction'] > time()-600)
						{
							$remote_session = session_load($data['session_id']);
							$remote_session['notice_message'] = 'Du, <a href="/traffa/profile.php?id=' . $_SESSION['login']['id'] . '">' . $_SESSION['login']['username'] . '</a> gissade nyss att du är ' . $_GET['guessed_age'] . ' år gammal i <a href="/traffa/age_guess.php">Gissa Åldern</a>!';
							session_save($data['session_id'], $remote_session);
						}
					}
				}
			}
		}
		
				
		return $return;
	}
	
	function age_guess_image()
	{
		$date_min = date('Y-m-d', strtotime('-25 years'));
		$date_max = date('Y-m-d', strtotime('-6 years'));
		
		$query = 'SELECT l.id, l.username, u.birthday FROM login AS l, userinfo AS u';
		$query .= ' WHERE l.id NOT IN ("' . implode('", "', $_SESSION['age_guess']['seen_users']) . '")';
		$query .= ' AND u.birthday != "0000-00-00" AND l.id = u.userid AND u.image = 2 AND l.username NOT LIKE "Borttagen"';
		$query .= ' AND u.birthday > "' . $date_min . '" AND u.birthday < "' . $date_max . '"';
		$query .= ' ORDER BY l.lastlogon DESC LIMIT ' . rand(0, 100) . ', 1';

		$result = mysql_query($query);
		$data = mysql_fetch_assoc($result);

		$_SESSION['age_guess']['current_user'] = $data['id'];
		$_SESSION['age_guess']['current_age'] = date_get_age($data['birthday']);
		$_SESSION['age_guess']['current_username'] = $data['username'];

		$_SESSION['age_guess']['seen_users'][] = $data['id'];
		$_SESSION['age_guess']['viewed_images']++;
		
		$return .= '<img src="' . IMAGE_URL . 'images/users/full/' . $_SESSION['age_guess']['current_user'] . '.jpg" />' . "\n";

		return $return;
	}
	
	function age_guess_answer()
	{
		$return .= '<h1>RÄTT ELLER FEL!</h1>' . "\n";
		return $return;
	}

?>