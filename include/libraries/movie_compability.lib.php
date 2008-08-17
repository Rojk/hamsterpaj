<?php

$MOVIE_CATEGORIES['action']['label'] = 'Action';
$MOVIE_CATEGORIES['romantic']['label'] = 'Romantik';
$MOVIE_CATEGORIES['animated']['label'] = 'Animerat';
$MOVIE_CATEGORIES['swedish']['label'] = 'Svenskt';
$MOVIE_CATEGORIES['fantasy']['label'] = 'Fantasy';
$MOVIE_CATEGORIES['horror']['label'] = 'Skräck';
$MOVIE_CATEGORIES['war']['label'] = 'Krigsfilm';
$MOVIE_CATEGORIES['musical']['label'] = 'Musikal';
$MOVIE_CATEGORIES['comedy']['label'] = 'Komedi';
$MOVIE_CATEGORIES['science fiction']['label'] = 'Science Fiction';
$MOVIE_CATEGORIES['drama']['label'] = 'Drama';



function movie_compability_fetch()
{
	$query = 'SELECT * FROM movies';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$movies[$data['handle']] = $data;
	}
	
	return $movies;
}

function movie_compability_form($movies, $options)
{
	rounded_corners_top(array('id' => 'movie_compability_review_list', 'color' => 'white'));

	echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n";
	if(isset($options['owner']))
	{
		echo '<input type="hidden" name="owner" value="' . $options['owner'] . '" />' . "\n";
	}
	if(login_checklogin())
	{
		$query = 'SELECT * FROM movie_tests WHERE owner = "' . $_SESSION['login']['id'] . '"';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		while($data = mysql_fetch_assoc($result))
		{
			$old_score[$data['movie_id']] = $data['score'];
		}
	}

	foreach($movies AS $movie_handle => $movie)
	{
		echo '<div class="movie">' . "\n";
		echo '<img src="' . IMAGE_URL  . 'movie_compability/folders/' . $movie_handle . '.png" alt="' . $movie_handle . '" />' . "\n";
	
		echo '<div class="scoring">' . "\n";
		for($i = 0; $i <= 5; $i++)
		{
			echo '<div>' . "\n";
			echo '<label for="movie_compability_' . $movie_handle . '_' . $i . '">' . $i . '</label>' . "\n";
			$checked = ($old_score[$movie['id']] == $i && isset($old_score[$movie['id']])) ? ' checked="checked"' : '';
			echo '<input type="radio" name="movie_compability_' . $movie['id'] . '" value="' . $i . '" class="movie_compability_input_scroll" id="movie_compability_' . $movie_handle . '_' . $i . '"' . $checked . ' />' . "\n";
			echo '</div>' . "\n";
		}
		echo '<div class="movie_compability_vote_not_seen">' . "\n";
		echo '<input type="radio" name="movie_compability_' . $movie['id'] . '" value="not_seen" class="movie_compability_input_scroll" for="movie_compability_' . $movie_handle . '_not_seen" />' . "\n";
		echo '<label for="movie_compability_' . $movie_handle . '_not_seen">Ej sett</label>' . "\n";
		echo '</div>' . "\n";
		echo '<br style="clear: both;" />' . "\n";
		echo '</div>' . "\n";

		echo '<h3>' . $movie['title'] . '</h3>' . "\n";
		echo '<p>' . $movie['description'] . '</p>' . "\n";
		
		echo '</div>' . "\n";
	}
	
	rounded_corners_bottom(array('color' => 'white'));
	
	if(!login_checklogin())
	{
		rounded_corners_top();
		echo '<h2>Fyll i ditt namn</h2>' . "\n";
		echo '<div class="first_name">' . "\n";
		echo '<h3>Förnamn</h3>' . "\n";
		echo '<input type="text" class="textbox" name="first_name" />' . "\n";
		echo '</div>' . "\n";
		echo '<div class="surname">' . "\n";
		echo '<h3>Efternamn</h3>' . "\n";
		echo '<input type="text" class="textbox" name="surname" />' . "\n";
		echo '</div>' . "\n";
		rounded_corners_bottom();
	}
	
	echo '<input type="submit" value="Skicka!" class="button" />' . "\n"; 
	echo '</form>' . "\n";
}

function movie_compability_compare($users, $report_user)
{
	/* The $report_user is the user viewing the report */
	global $MOVIE_CATEGORIES;
	
	if(count($users) > 5)
	{
		echo '<h1>Du kan som mest jämföra dig med fyra personer samtidigt!</h1>' . "\n";
		echo '<p>En eller flera personer har plockats bort ur jämförelsen, försök att jämföra max fyra personer åt gången.</p>' . "\n";
		
		$temp = $users;
		$users = array();
		
		$count = 0;
		foreach($users AS $key => $value)
		{
			$users[$key] = $value;
			$count++;
			if($count == 5)
			{
				break;
			}
		}
	}
	
	$i = 1;
	foreach(array_keys($users) AS $user_id)
	{
		$bar_ids[$user_id] = $i;
		$i++;
	}

	$users[$report_user]['username'] = 'Du';
	
	/* Fetch all the test info and store in the $users array */
	$query = 'SELECT * FROM movie_tests WHERE owner IN("' . implode(array_keys($users), '", "') . '")';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$users[$data['owner']]['movie_scores'][$data['movie_id']] = $data['score'];
	}
	
	
	$all_movies = movie_compability_fetch();
	foreach($all_movies AS $movie)
	{
		foreach(array_keys($users) AS $user_id)
		{
			/* Count total seen movies? */
			$users[$user_id]['seen_movies'] += (isset($users[$user_id]['movie_scores'][$movie['id']]['score'])) ? 1 : 0;

			/* Count seen movies in specific category */
			$users[$user_id][$movie['category']]['seen_movies'] += (isset($users[$user_id]['movie_scores'][$movie['id']]['score'])) ? 1 : 0;

			/* Increase the total score for the specific category and user */
			$users[$user_id][$movie['category']]['total_score'] += $users[$user_id]['movie_scores'][$movie['id']]['score'];

			if(isset($users[$report_user]['movie_scores'][$movie['id']]['score']) && isset($users[$user_id]['movie_scores'][$movie['id']]['score']))
			{
				/* Total distance and common movies */
				$users[$user_id]['total_distance'] += abs($users[$report_user]['movie_scores'][$movie['id']]['score'] - $users[$user_id]['movie_scores'][$movie['id']]['score']);
				$users[$user_id]['common_movies']++;
				
				/* Total distance and common movies for this category */				
				$users[$user_id][$movie_category]['total_distance'] += abs($users[$report_user]['movie_scores'][$movie['id']]['score'] - $users[$user_id]['movie_scores'][$movie['id']]['score']);
				$users[$user_id][$movie_category]['common_movies']++;
			}

			/* Mark the movie as seen if anyone of you has seen it */
			if(isset($users[$user_id]['movie_scores'][$movie['id']]['score']))
			{
				$all_movies[$movie['handle']]['seen'] = 1;
				$all_movies[$movie['handle']]['scores'][$user_id] = $users[$user_id]['movie_scores'][$movie['id']]['score'];
			}
		}
	}
	
	/* Loop through all movies and set the score_spread key */
	foreach($all_movies AS $movie_handle => $movie)
	{
		$all_movies[$movie_handle]['average_score'] = array_sum($movie['scores']) / count($movie['scores']);
		foreach($movie['scores'] AS $score)
		{
			$all_movies[$movie_handle]['score_spread'] += abs($all_movies[$movie_handle]['average_score'] - $score);
		}
	}

	foreach(array_keys($users) AS $user_id)
	{
		foreach($MOVIE_CATEGORIES AS $category_handle => $category)
		{
			$users[$user_id][$category_handle]['average_score'] = round($users[$user_id][$category_handle]['total_score'] / $users[$user_id][$category_handle]['seen_movies'], 1);
		}
		
		$users[$user_id]['match'] = 100 - round((($users[$user_id][$movie_category]['total_distance'] / $users[$user_id]['common_movies'])/5)*100);
	}
	
	if(count($users) < 4)
	{
		foreach($users AS $user_id => $user)
		{
			if($user_id != $report_user)
			{
				 $output .= '<h1>Du och ' . $user['username'] . ' matchar till ' . $user['match'] . '%</h1>' . "\n";
			}
		}
		rounded_corners($output, array('style' => 'text-align: center'));
	}
	else
	{
		foreach($users AS $user_id => $user)
		{
			if($user_id != $report_user)
			{
				 $output .= '<h1>Du och ' . $user['username'] . ' matchar till ' . $user['match'] . '%</h1>' . "\n";
			}
		}
		rounded_corners($output, array('style' => 'text-align: center'));		
	}	
	
	rounded_corners_top(array('color' => 'white'));
	echo '<h3>Hur många filmer har ni sett?</h3>' . "\n";
	echo '<div class="bars">' . "\n";
	foreach($users AS $user_id => $user)
	{
		$user_label = ($user_id == $report_user) ? 'Du' : $user['username'];
		echo '<div class="bar_' . $bar_ids[$user_id] . '" style="width: ' . ($user['seen_movies']*6) . 'px;">' . "\n";
		echo '<span class="person">' . $user_label . '</span>' . "\n";
		echo '<span class="movie_count">' . $user['seen_movies'] . 'st</span>' . "\n";
		echo '</div>' . "\n";
	}
	echo '</div>' . "\n";
	rounded_corners_bottom(array('color' => 'white'));
	
	rounded_corners_top(array('color' => 'white', 'id' => 'movie_compare_categories'));
	echo '<h3>Vad tycker ni egentligen om olika sorters film?</h3>' . "\n";
	foreach($MOVIE_CATEGORIES AS $category_handle => $category)
	{
		echo '<div class="bars">' . "\n";
		echo '<h4>' . $category['label'] . '</h4>' . "\n";
		foreach($users AS $user_id => $user)
		{
			echo '<div class="score">' . $user[$category_handle]['average_score'] . '</div>' . "\n";
			echo '<div class="bar_' . $bar_ids[$user_id] . '" style="width: ' . round($user[$category_handle]['average_score']*12) . 'px; float: left;"></div>' . "\n";
		}
		echo '</div>' . "\n";
	}
	echo '<br style="clear: both;" />' . "\n";
	rounded_corners_bottom(array('color' => 'white'));

	rounded_corners_top(array('color' => 'white', 'id' => 'movie_compability_not_seen_list'));
	echo '<h3>Hey, ingen av er har kollat in dom här rullarna!</h3>' . "\n";
	foreach($all_movies AS $movie)
	{
		echo '<ul>' . "\n";
		if($movie['seen'] != 1)
		{
			echo '<li id="movie_not_seen_control_' . $movie['handle'] . '" class="movie_not_seen_control">' . $movie['title'] . '</li>' . "\n";
			
			echo '<div class="movie" id="movie_not_seen_' . $movie['handle'] . '">' . "\n";
			echo '<img src="' . IMAGE_URL  . 'movie_compability/folders/' . $movie['handle'] . '.png" alt="' . $movie['handle'] . '" />' . "\n";
			echo '<h3>' . $movie['title'] . '</h3>' . "\n";
			echo '<p>' . $movie['description'] . '</p>' . "\n";
			echo '</div>' . "\n";
		}
		echo '</ul>' . "\n";
	}
	rounded_corners_bottom(array('color' => 'white'));

	echo '<div class="bars">' . "\n";
	foreach($users AS $user_id => $user)
	{
		echo '<div class="bar_' . $bar_ids[$user_id] . '" style="width: 100px; float: left; margin-right: 10px; text-align: center;">' . $user['username'] . '</div>' . "\n";
	}
	echo '</div>' . "\n";

	/* Movies with high score spread */
	foreach($all_movies AS $movie_handle => $movie)
	{
		$score_spreads[$movie_handle][] = $movie['score_spread'];
	}
	arsort($score_spreads);
	rounded_corners_top(array('color' => 'white', 'id' => 'movie_compare_score_spread'));
	echo '<h3>Filmer ni inte är överrens om</h3>' . "\n";
	$i = 0;
	foreach($score_spreads AS $movie_handle => $score_spread)
	{
		if($i < 4 && $score_spread > 5)
		{
			echo '<div class="bars">' . "\n";
			echo '<h4>' . $movie_handle . '</h4>' . "\n";
			foreach($all_movies[$movie_handle]['scores'] AS $user_id => $score)
			{
				echo '<div class="score">' . $score . '</div>' . "\n";
				if(in_array($user_id, array_keys($all_movies[$movie_handle]['scores'])))
				{
					echo '<div class="bar_' . $bar_ids[$user_id] . '" style="width: ' . round($score*18) . 'px; float: left;"></div>' . "\n";
				}
				else
				{
					echo 'Ej sett';
				}
			}
			echo '</div>' . "\n";
			$i++;
		}
	}
	echo '<br style="clear: both;" />' . "\n";
	rounded_corners_bottom(array('color' => 'white'));
	
	/* Popular and unpopular movies */
	foreach($all_movies AS $movie)
	{
		$movie_popularity[$movie['handle']] = $movie['average_score'];
	}
	
	arsort($movie_popularity);

	rounded_corners_top(array('color' => 'white'));
	echo '<div id="movie_compare_most_popular">' . "\n";
	echo '<h3>Skitbra rullar</h3>' . "\n";
	$i = 0;
	foreach($movie_popularity AS $movie_handle => $average_score)
	{
		if($i < 2 && count($all_movies[$movie_handle]['scores']) >= count($users)- floor(count($users)/3))
		{
			echo '<div class="bars">' . "\n";
			echo '<h4>' . $movie_handle . '</h4>' . "\n";
			foreach($all_movies[$movie_handle]['scores'] AS $user_id => $score)
			{
				echo '<div class="score">' . $score . '</div>' . "\n";
				if(in_array($user_id, array_keys($all_movies[$movie_handle]['scores'])))
				{
					echo '<div class="bar_' . $bar_ids[$user_id] . '" style="width: ' . round($score*18) . 'px; float: left;"></div>' . "\n";
				}
				else
				{
					echo 'Ej sett';
				}
			}
			echo '</div>' . "\n";
			$i++;
		}
	}
	echo '</div>' . "\n";

	echo '<div id="movie_compare_most_unpopular">' . "\n";
	echo '<h3>Filmer som suger</h3>' . "\n";
	asort($movie_popularity);
	$i = 0;
	foreach($movie_popularity AS $movie_handle => $average_score)
	{
		if($i < 2 && count($all_movies[$movie_handle]['scores']) >= count($users)- floor(count($users)/3))
		{
			echo '<div class="bars">' . "\n";
			echo '<h4>' . $movie_handle . '</h4>' . "\n";
			foreach($all_movies[$movie_handle]['scores'] AS $user_id => $score)
			{
				echo '<div class="score">' . $score . '</div>' . "\n";
				if(in_array($user_id, array_keys($all_movies[$movie_handle]['scores'])))
				{
					echo '<div class="bar_' . $bar_ids[$user_id] . '" style="width: ' . round($score*18) . 'px; float: left;"></div>' . "\n";
				}
				else
				{
					echo 'Ej sett';
				}
			}
			echo '</div>' . "\n";
			$i++;
		}
	}	
	echo '</div>' . "\n";
	echo '<br style="clear: both;" />' . "\n";
	rounded_corners_bottom(array('color' => 'white'));
}

?>