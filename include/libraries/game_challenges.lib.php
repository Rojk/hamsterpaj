<?php

function game_challenges_view($challenge)
{
	$profile['henrik'] = '<a href="/traffa/profile.php?id=644314">Henrik</a>';
	$profile['johan'] = '<a href="/traffa/profile.php?id=3">Johan</a>';
	$profile['maja'] = '<a href="/traffa/profile.php?id=644314">Maja</a>';
	$profile['alexander'] = '<a href="/traffa/profile.php?id=644314">Alexander</a>';
	$profile['ace'] = '<a href="/traffa/profile.php?id=644314">Ace</a>';
	$profile['soode'] = '<a href="/traffa/profile.php?id=644314">Soode</a>';

	$scoring_types['points'] = 'poäng';
	$scara

	$search['handle'] = $challenge['game_handle'];
	$game = games_fetch($search);
	$game = $game[0];

	echo '<h1>' . $profile[$challenge['challenger']] . ' utmanar i <a href="/spel/' . $game['handle'] . '.html">' . $game['title'] . '</a></h1>' . "\n";
	echo '<p>' . $challenge['bread_text'] . '</p>' . "\n";
	games_list(array($game));
	
	echo '<h2>Spöar du inte ' . $chalenger[$challenge['challenger']] . '? Du kanske slår någon annan av oss i Hamsterpaj Crew?</h2>' . "\n";
	echo '<table>' . "\n";
	
	echo '</table>' . "\n";
}

?>