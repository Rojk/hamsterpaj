<?php
	require('/home/www/standard.php');
	require(PATHS_INCLUDE . 'libraries/plump.lib.php');
	$ui_options['menu_path'] = array('spel', 'plump');
	$ui_options['stylesheets'][] = 'plump.css';
	$ui_options['title'] = 'Spela Plump på Hamsterpaj!';
	ui_top($ui_options);

	switch($_GET['action'])
	{
		case 'mark':
			echo '<div class="plump_game_info">' . "\n";
			if(plump_square_free($_GET['number']) && $_GET['plump'] != 'true')
			{
				if(in_array($_GET['number'], plump_combinations($_SESSION['plump']['dices'])))
				{
					$position = plump_pos_by_number($_GET['number']);
					$score = plump_score($position['row'], $position['col']);

					$combinations = plump_combinations($_SESSION['plump']['dices']);
					$max_score = plump_get_max_score($combinations);
								
					if($max_score['score'] > $score)
					{
						echo '<h2 class="plump_error">Du kunde fått ' . $max_score['score'] . ' poäng, men du fick bara ' . $score . '!</h2>';
						echo $max_score['calculation'] . ' = ' . $max_score['result'];
					}
					else
					{
						echo '<h2 class="plump_error">' . $score . ' poäng till dig!</h2>' . "\n";
					}

					$_SESSION['plump']['user_score'] += $score;
					$_SESSION['plump']['board'][$position['row']][$position['col']] = 1;
				}
				else
				{
					echo '<h2 class="plump_error">Den siffran kunde du inte ta, nu fick du en plump!</h2>' . "\n";
					$_SESSION['plump']['user_plumps']++;

					if($_SESSION['plump']['user_plumps'] >= 4 || $_SESSION['plump']['computer_plumps'] >= 4)
					{
						plump_game_over();
						echo '</div>';
						ui_bottom();
						exit;
					}
				}
			}
			else
			{
				$_SESSION['plump']['user_plumps']++;
				if($_SESSION['plump']['user_plumps'] >= 4 || $_SESSION['plump']['computer_plumps'] >= 4)
				{
					plump_game_over();
					echo '</div>';
					ui_bottom();
					exit;
				}				
				echo '<h1>Du fick en plump!</h1>';
			}
			
			/* The computers turn */
			plump_dices();
			$combinations = plump_combinations($_SESSION['plump']['dices']);
			$max_score = plump_get_max_score($combinations);
			if($max_score['score'] > 0)
			{
				$_SESSION['plump']['board'][$max_score['row']][$max_score['col']] = 1;
				$_SESSION['plump']['computer_score'] += $max_score['score'];
				
				echo '<div class="plump_computers_turn">' . "\n";
				echo '<h2>Datorn har gjort sitt drag</h2>' . "\n";
				echo plump_display_dices() . "\n";
				echo '<p>' . $max_score['calculation'] . ' = ' . $max_score['result'] . '<br />Datorn fick ' . $max_score['score'] . ' poäng</p>';
				echo '</div>' . "\n";
			}
			else
			{
				$_SESSION['plump']['computer_plumps']++;
				echo '<h1>Dammit, plump till datorn</h1>' . "\n";
			}

			plump_dices();
			echo '<div class="plump_users_turn">' . "\n";
			echo '<h2>Din tur</h2>' . "\n";
			echo plump_display_dices() . "\n";
			echo '<br /><a href="?action=mark&plump=true">Ta en plump</a>' . "\n";
			echo '</div>' . "\n";
			
			echo plump_score_field();
			
			echo '</div>' . "\n";
			plump_draw_board();

			plump_rules();			
			break;
		default:
			plump_create_game();
			plump_dices();
			echo '<h1>Du slog ' . plump_display_dices() . '</h1>';
			plump_draw_board();
			plump_rules();
			break;
	}
	
	if(login_checklogin())
	{
		if(!isset($_SESSION['plump_highscore']))
		{
			$query = 'SELECT high_score FROM plump WHERE user = "' . $_SESSION['login']['id'] . '" LIMIT 1';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$data = mysql_fetch_assoc($result);
			$_SESSION['plump_highscore'] = $data['high_score'];
		}
		if($_SESSION['plump']['user_score'] > $_SESSION['plump_highscore'])
		{
			$_SESSION['plump_highscore'] = $_SESSION['plump']['user_score'];
			$updatequery = 'UPDATE plump SET high_score = "' . $_SESSION['plump']['user_score']  . '" WHERE user = "' . $_SESSION['login']['id'] . '"';
			$insertquery = 'INSERT INTO plump (user, high_score) VALUES("' . $_SESSION['login']['id'] . '", "' . $_SESSION['plump_highscore'] . '")';
			mysql_query($insertquery) or mysql_query($updatequery) or die(report_sql_error($query, __FILE__, __LINE__));
		}
		
		echo '<div class="orange_faded_div">' . "\n";
		echo '<h2>Ditt rekord: ' . $_SESSION['plump_highscore'] . ' poäng.</h2>' . "\n";
		echo '<h3>Topplista</h3>' . "\n";
		$query = 'SELECT l.username, p.user, p.high_score FROM plump AS p, login AS l WHERE l.id = p.user ORDER BY p.high_score DESC LIMIT 10';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		echo '<ol>' . "\n";
		while($data = mysql_fetch_assoc($result))
		{
			echo '<li>' . $data['high_score'] . 'p - <a href="/traffa/profile.php?id=' . $data['user'] . '">' . $data['username'] . '</a></li>' . "\n";	
		}
		echo '</ol>' . "\n";
		echo '</div>' . "\n";
	}

	ui_bottom();
?>


