<?php
require_once(PATHS_INCLUDE . 'libraries/discussions.php');
require_once(PATHS_INCLUDE . 'libraries/posts.php');
require_once(PATHS_INCLUDE . 'libraries/markup.php');
require_once(PATHS_INCLUDE . 'libraries/quality.php');
require_once(PATHS_INCLUDE . 'libraries/flags.php');
require_once(PATHS_INCLUDE . 'libraries/discussions.php');
require_once(PATHS_INCLUDE . 'libraries/forum-antispam.php');
require_once(PATHS_INCLUDE . 'libraries/tips.lib.php');
require_once(PATHS_INCLUDE . 'libraries/rank.lib.php');
require_once(PATHS_INCLUDE . 'libraries/schedule.lib.php');
require_once(PATHS_INCLUDE . 'libraries/comments.lib.php');
require_once(PATHS_INCLUDE . 'guestbook-functions.php');

/*$game_tags = array('two_player', 'multi_player',
					'sport', 'racing', 'skjuta', 'precision', 'strategi', 'klurigt', 'platform', 'rollspel',
					'tidspress', 'action');
*/

define(GAMES_PER_PAGE, 20);

$game_tags = array('turspel', 'simpla_spel', 'skjutspel', 'sportspel', 'strategispel', 'problemloesningsspel', 'tidspress', 'racingspel', 'multiplayerspel', 
					'plattformsspel', 'tajmingspel', 'crews_favoritspel', 'barnfoerbjudna_spel', 'pusselspel', 'klicka', 'klassiker', 'precision', 'fightingspel',
					'labyrintspel', 'flipperspel', 'rollspel', 'highscore');

function games_get_action($url)
{
	unset($return);
	
	$url = preg_replace('/#.*$/', '', $url);
    $url = preg_replace('/([^(php)(html)\/])$/','$0/',$url);									 
	$return['modified_url'] = $url;
/*
/spel/
/spel/start/
/spel/favoriter/
/spel/topplistan/
/spel/bladdra/
/spel/bladdra/c.sport/
/spel/bladdra/b.turspel/sida_2.html
/spel/air_hockey.html
/spel/redigera/air_hockey.php
*/

    $actions = array('start', 'favoriter', 'topplistan', 'bladdra', 'spela', 'redigera', 'redigera_spara', 'nytt_spel', 'nytt_spel_spara', 'vote', 'vote_remove', 'upload_game', 'played', 'crew', 'challenge', 'comment', 'comment_delete');

	//Games index
	// /spel
    // /spel/
	if(preg_match('/^\/spel\/?$/', $url, $matches))
    {
		$return['action'] = 'start';
	}
	elseif(preg_match('/^\/spel\/bladdra\/(((\w|Alla|0-9)\.(\w+)\/)?(sida_(\d+)\.html)?)?/', $url, $matches))
    {
        $return['action'] = 'bladdra';
        $return['initial_letter'] = $matches[3];
        $return['tag'] = $matches[4];
        $return['page_number'] = $matches[6];
    }
	elseif(preg_match('/^\/spel\/admin\/(\w+)\.php/', $url, $matches))
	{
		$return['action'] = 'admin';
		$return['game_handle'] = $matches[1];
	}
	elseif(preg_match('/^\/spel\/admin\/(\w+)\.php/', $url, $matches))
	{
		$return['action'] = 'admin';
		$return['game_handle'] = $matches[1];
	}
	elseif(preg_match('/^\/spel\/vote_remove\.(\w+)\.php/', $url, $matches))
	{
		$return['action'] = 'vote_remove';
		$return['game_handle'] = $matches[1];
	}
	elseif(preg_match('/^\/spel\/vote\.(\w+)\.php/', $url, $matches))
	{
		$return['action'] = 'vote';
		$return['game_handle'] = $matches[1];
	}
	elseif(preg_match('/^\/spel\/played\.(\w+)\.php/', $url, $matches))
	{
		$return['action'] = 'played';
		$return['played'] = $matches[1];
	}
	elseif(preg_match('/^\/spel\/(\w+)\.html/', $url, $matches))
	{
		$return['action'] = 'spela';
		$return['game_handle'] = $matches[1];
	}
	elseif(preg_match('/^\/spel\/(\w+)\/(\w+)\.php/', $url, $matches))
    {
        $return['action'] = $matches[1];
        $return['game_handle'] = $matches[2];
    }
	elseif(preg_match('/^\/spel\/(\w+)(\/(\w+)?)?/', $url, $matches))
    {
        $return['action'] = $matches[1];
        $return['sub_action'] = $matches[3];
    }
    if(!array_search($return['action'], $actions))
    {
        $return['action'] = 'start';
    }
/*     preint_r($return); */
    return $return;
}

function games_list_tags($tags, $options = null)
{
	global $game_tags;
	if($options['size'] == 'large')
	{
/*		echo '<table class="games_tag_table">' . "\n";
		echo '<tr>' . "\n";
		$i = 0;
		foreach($game_tags as $handle)
		{
			if(array_key_exists($handle, $tags))
			{
				//Alla taggar som anvä?nds i spelsystemet har en bild: tag_handle.png
				echo '<td><a href="/spel/bladdra/' . (isset($options['initial_letter']) ? $options['initial_letter'] : 'Alla') . 
						'.' . $handle . '/"><img alt="' .
						$tags[$handle]['label'] . '" class="games_tag" id="games_tag_' . $tags[$handle]['handle'] . 
						'" src="' . IMAGE_URL . 'games/tags/'. $handle . '.png" />';
				echo '<label for ="games_tag_' . $tags[$handle]['handle'] . '">' . $tags[$handle]['label'] . '</label></a></td>' . "\n";
				$i++;
				if($i == 3)
				{
					echo '</tr><tr>' . "\n";
					$i = 0;
				}
			}
		}
		echo '</tr>' . "\n";
		if($options['bladdra'])
		{
			echo '<tr><td><a href="/spel/bladdra/Alla.alla/">Strunta i taggarna</a></td></tr>' . "\n";
		}
		echo '</table>';
		*/
		echo '<div class="games_tag_table" style="padding: 3px; float: left;">';
		echo '<select id="game_category_select">';
		foreach($game_tags as $handle)
		{
			if(array_key_exists($handle, $tags))
			{
				if($handle == $options['current_handle'])
				{
					echo '<option value="' . $handle . '" selected="selected">' . $tags[$handle]['label'] . '</option>' . "\n";
				}
				else
				{
					echo '<option value="' . $handle . '">' . $tags[$handle]['label'] . '</option>' . "\n";					
				}
			}
		}
		echo '</select>';
		echo '<button class="button_100" style="margin-left: 10px;" onclick="window.location = \'/spel/bladdra/Alla.\' + document.getElementById(\'game_category_select\').value + \'/\';">Visa kategori</button>';
		echo '</div>' . "\n";
	}
	else
	{
		echo '<ul class="games_tag_list">' . "\n";
		foreach($game_tags as $handle)
		{
			if(array_key_exists($handle, $tags))
			{
				//Alla taggar som anvä?nds i spelsystemet har en bild: tag_handle.png
				echo '<li><a href="/spel/bladdra/' . (isset($options['initial_letter']) ? $options['initial_letter'] : 'Alla') . 
						'.' . $handle . '/">' .
						'<img alt="' . $tags[$handle]['label'] . '" class="games_tag" id="games_tag_' . $tags[$handle]['handle'] . 
						'" src="' . IMAGE_URL . 'games/tags/'. $handle . '.png" /></a></li>' . "\n";
			}
		}
		if($options['bladdra'])
		{
			echo '<li><a href="/spel/bladdra/Alla.alla/">Strunta i taggarna</a>' . "\n";
		}
		echo '</ul>' . "\n";
	}
}

function games_vote($options)
{
	if(!login_checklogin())
	{
		exit;
	}
	$query = 'SELECT * FROM user_favorites WHERE user_id = "' . $_SESSION['login']['id'] . '"' .
			 ' AND item_id = (SELECT id FROM games WHERE handle = "' . $options['game_handle'] . '")' .
			 ' AND type = "game"';

//	echo $query;
//	echo '<br />';
//	preint_r( $options);
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if(!mysql_fetch_assoc($result))
	{
			$query = 'SELECT id FROM games WHERE handle = "' . $options['game_handle'] . '"';
//		echo $query;
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			if($data = mysql_fetch_assoc($result))
			{
				$game_id = $data['id'];
				$query = 'INSERT INTO user_favorites (user_id, item_id, type) VALUES ("' . $_SESSION['login']['id'] . '", "' .
							$game_id . '", "game")';
//		echo $query;
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				$query = 'UPDATE games SET points = points + 1, ratio=points/unique_players WHERE handle = "' . $options['game_handle'] . '"';
//		echo $query;
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
	}
}

//function games_ratio_recalc($

function games_vote_remove($options)
{
	$query = 'SELECT * FROM user_favorites WHERE user_id = "' . $_SESSION['login']['id'] . '"' .
			 ' AND item_id = (SELECT id FROM games WHERE handle = "' . $options['game_handle'] . '")' .
			 ' AND type = "game"';
			 
	echo $query;
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if($data = mysql_fetch_assoc($result))
	{
		if(count($data) > 0)
		{
			$query = 'SELECT id FROM games WHERE handle = "' . $options['game_handle'] . '"';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			if($data = mysql_fetch_assoc($result))
			{
				$game_id = $data['id'];
				$query = 'DELETE FROM user_favorites WHERE user_id = "' . $_SESSION['login']['id'] . '"' .
							' AND item_id = "' . $game_id . '"' .
							' AND type = "game"';
	echo $query;
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$query = 'UPDATE games SET points = points - 1, ratio=points/unique_players WHERE handle = "' . $options['game_handle'] . '"';
	echo $query;
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			}
		}
	}
}

function games_list($options)
{
	$options['list_type'] = isset($options['list_type']) ? $options['list_type'] : 'normal';
	switch($options['list_type'])
	{
		case 'normal':
			foreach($options['games'] as $game)
			{
				echo '<div class="game_full" id="game_full_' . $game['handle'] . '" >' . "\n";
				if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
				{
					echo '<div style="background: url(\'' . IMAGE_URL . 'games/' . $game['handle'] . '.png\') 5px 5px no-repeat;" class="left">' . "\n";
					echo '<a href="/spel/' . $game['handle'] . '.html" >' . "\n";
					echo '<img src="http://images.hamsterpaj.net/game_thumb_passepartout.png" />';
					echo '</a>' . "\n";
					echo '</div>';
				}
				else
				{
					echo '<a href="/spel/' . $game['handle'] . '.html" >' . "\n";
					echo '<img alt="' . $game['title'] . '" class="left" src="' . IMAGE_URL . 'games/' . $game['handle'] . '.png" />' . "\n";
					echo '</a>' . "\n";
				}
				echo '<div class="main">' . "\n";
				echo '<a href="/spel/' . $game['handle'] . '.html" ><h1>' . $game['title'] . '</h1></a>' . "\n";
				echo '<a href="/spel/' . $game['handle'] . '.html" >' . "\n";
				echo '<p>' . $game['description'] . '</p>' . "\n";
				echo '</a>' . "\n";
				echo '</div>' . "\n"; // end main
				echo '<div class="right">' . "\n";
				games_game_popularity_draw($game);
				games_list_tags($game['tags']);
				if(3 <= $_SESSION['login']['userlevel'])
				{
					echo '<a href="/spel/redigera/' . $game['handle'] . '.php" >Redigera spelet</a>' . "\n";
				}
				echo '</div>' . "\n"; // end right
				echo '</div>' . "\n"; // end game_full
				echo '<img class="game_full_footer" src="http://images.hamsterpaj.net/light_blue_div_bottom_line.png" />' . "\n";
			}
		break;
		case 'compact':
			echo '<div class="game_list_compact">' . "\n";
			if(isset($options['headline']))
			{
				echo '<h2>' . $options['headline'] . '</h2>' . "\n";
			}
			foreach($options['games'] as $game)
			{
				echo '<div class="game_compact" id="game_compact_' . $game['handle'] . '" >' . "\n";
				if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
				{
					echo '<div style="background: url(\'' . IMAGE_URL . 'games/' . $game['handle'] . '.png\') 5px 5px no-repeat;" class="left">' . "\n";
					echo '<a href="/spel/' . $game['handle'] . '.html" >' . "\n";
					echo '<img src="http://images.hamsterpaj.net/game_thumb_passepartout.png" />';
					echo '</a>' . "\n";
					echo '</div>';
				}
				else
				{
					echo '<a href="/spel/' . $game['handle'] . '.html" >' . "\n";
					echo '<img alt="' . $game['title'] . '" class="left" src="' . IMAGE_URL . 'games/' . $game['handle'] . '.png" />' . "\n";
					echo '</a>' . "\n";
				}
				echo '<a href="/spel/' . $game['handle'] . '.html" ><h5>' . $game['title'] . '</h5></a>' . "\n";
				echo '</div>' . "\n"; // end game_compact
			}
			echo '<img class="game_full_footer" src="http://images.hamsterpaj.net/light_blue_div_bottom_line.png" />' . "\n";
			echo '</div>' . "\n";
		break;
	}
}

function games_fetch($options)
{
    /*
    $options['initial_letter'] - the initial letter of the title
    $options['tag'] - the handle of the tag to select games by
    $options['handle'] - fetch games with these handles
    $options['user_id'] - the user_id to which voted and played is matched
    $options['period']['start'] - fetch only games released between (now - 'start') and (now - 'end')
    $options['period']['end'] -   start and end are the number of days from now
	played
	voted
	id
	exclude
	count_only			only returns the count of items if set
	*/
/* if(644314 == $_SESSION['login']['id']) */
/* preint_r($options); */
    if(!isset($options['user_id']) && login_checklogin())
    {
    	$options['user_id'] = $_SESSION['login']['id'];
	}
    if(isset($options['played']))
    {
    	$query = 'SELECT item_id FROM user_visits WHERE user_id = "' . $options['user_id'] . '" AND type = "game"';
	    $result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
    	while($data = mysql_fetch_assoc($result))
    	{
    		$played_games[$data['item_id']] = $data;
    	}
    }
    if(isset($options['voted']))
    {
    	$query = 'SELECT item_id FROM user_favorites WHERE user_id = "' . $options['user_id'] . '" AND type = "game"';
	    $result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
    	while($data = mysql_fetch_assoc($result))
    	{
    		$favorite_games[$data['item_id']] = $data;
    	}
    }

	if(isset($options['tag']))
	{
		$options['tag'] = is_array($options['tag']) ? $options['tag'] : array($options['tag']);
	}
	if(isset($options['count_only']))
	{
		$query = 'SELECT COUNT(g.id) AS count';
	}
	else
	{
		$query = 'SELECT g.handle, g.title, g.id, g.controls, g.description, g.played, g.points, g.release, g.unique_players, g.highscore_gname';
	}
	$query .= ' FROM games g' . (isset($options['tag']) && $options['tag'] != 'alla' ? ', object_tags ot, tags t' : '');
	if(isset($options['order']))
	{
		switch($options['order'])
		{
			case 'user_played':
			case 'user_played_date':
				$query .= ', user_visits uv';
			break;
		}
	}
	$query .= ' WHERE 1';
	if(isset($options['order']))
	{
		switch($options['order'])
		{
			case 'user_played':
			case 'user_played_date':
				$query .= ' AND g.id = uv.item_id AND uv.user_id ="' . $options['user_id'] . '"';
			break;
		}
	}
	if(isset($options['search_string_title']))
	{
		$query .= ' AND g.title LIKE "%' . $options['search_string_title'] . '%" OR title SOUNDS LIKE "' . $options['search_string_title'] . '"';
	}
	elseif(isset($options['search_string_description']))
	{
		$query .= ' AND MATCH (g.description) AGAINST ("' . $options['search_string_description'] . '")';
	}
	elseif(isset($options['initial_letter']))
	{
		if($options['initial_letter'] == '0-9')
		{
			$digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
			$query .= ' AND ( title LIKE "' . implode('%" OR title LIKE "', $digits) . '%")';
		}
		elseif($options['initial_letter'] != 'Alla')
		{
			$query .= ' AND title LIKE "' . $options['initial_letter'] . '%"';
		}
	}
	if(isset($options['tag']) && $options['tag'] != 'alla' )
	{
		$query .= ' AND g.id = ot.reference_id AND ot.object_type = "game" AND ot.tag_id = t.id';
		$query .= ' AND t.handle IN ("' . implode('", "', $options['tag']) . '")';
	}
	if(isset($options['handle']))
	{
		$options['handle'] = is_array($options['handle']) ? $options['handle'] : array($options['handle']);
		$query .= ' AND g.handle IN ("' . implode('", "', $options['handle']) . '")';
	}
	if(isset($options['played']))
	{
		$query .= ' AND g.id';
		$query .= $options['played'] == 'yes' ? ' IN' : ' NOT IN';
		$query .= ' ("' . implode('", "', array_keys($played_games)) . '")';
	}
	if(isset($options['voted']))
	{
		$query .= ' AND g.id';
		$query .= $options['voted'] == 'yes' ? ' IN' : ' NOT IN';
		$query .= ' ("' . implode('", "', array_keys($favorite_games)) . '")';
	}
	if(isset($options['period']))
	{
		if(isset($options['period']['start']))
		{
			$query .= ' AND g.release > "' . (time() - $options['period']['start'] * 3600 * 24) . '"';
		}
		if(isset($options['period']['end']))
		{
			$query .= ' AND g.release < "' . (time() - $options['period']['end'] * 3600 * 24) . '"';
		}
	}
	else
	{
		$query .= ' AND g.release < "' . time() . '"';
	}
	if(isset($options['id']) and count($options['id']) > 0)
	{
		$query .= ' AND g.id IN ("' . implode('", "', $options['id']) . '")';
	}
	if(isset($options['exclude']) and count($options['exclude']) > 0)
	{
		$query .= ' AND g.id NOT IN ("' . implode('", "', $options['exclude']) . '")';
	}
	if(!isset($options['count_only']))
	{
		if(isset($options['order']))
		{
			switch ($options['order'])
			{
				case 'ratio':
				case 'popular':
					if(!isset($options['ignore_threshold']))
					{
						$query .= ' AND g.unique_players > 200';
					}
				break;
			}
			$query .= ' ORDER BY ';
			switch ($options['order'])
			{
				case 'played':
					$query .= ' g.played';
				break;
				case 'user_played':
					$query .= ' uv.count';
				break;
				case 'user_played_date':
					$query .= ' uv.timestamp';
				break;
				case 'ratio':
				case 'popular':
					$query .= ' g.points / g.unique_players';
				break;
				case 'points':
					$query .= ' g.points';
				break;
				case 'release':
					$query .= ' g.release';
				break;
				case 'random':
					$query .= ' RAND()'; //todo! slumpa spelen här
				break;
			}
			if(isset($options['direction']))
			{
				$query .= $options['direction'] == 'desc' ? ' DESC' : ' ASC';
			}
			else
			{
				$query .= ' DESC';
			}
		}
		else
		{
			$query .= ' ORDER BY handle ASC';
		}
	}

	if(isset($options['limit']))
	{
		$query .= ' LIMIT ' . $options['limit'];
	}

/*
	if($_SESSION['login']['userlevel'] == 5)
	{
		echo '<p>' . $query . '</p>';
	}

*/
    $result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	unset($games);
	if(isset($options['count_only']))
	{
		$data = mysql_fetch_assoc($result);
		return $data['count'];
	}
    while($data = mysql_fetch_assoc($result))
    {
    	$games[$data['handle']] = $data;
//	   	$games[$data['handle']]['tags'] = tag_get_by_item('game', $data['id']);
	   	$games[$data['handle']]['controls'] = unserialize($data['controls']);
	}
	
/*
	if($_SESSION['login']['userlevel'] == 5)
	{
		echo '<p>count($games) = ' . count($games) . '</p>';
	}
*/
	if(!isset($options['ignore_threshold']) && count($games) < 2 && ($options['order'] == 'ratio' || $options['order'] == 'popular'))
	{
		$options['ignore_threshold'] = true;
		$games = games_fetch($options);
	}
    return $games;
}

function games_game_popularity_draw($game, $options = null)
{
    echo '<div class="game_popularity" >' . "\n";
    echo '<div class="game_played">' . "\n";
	echo '<div class="column_1">' . cute_number($game['played']) . '</div><a href="/spel/topplistan/mest_spelade/">Spelningar</a>' . "\n";
    echo '</div>' . "\n";
    echo '<div class="game_points">' . "\n";
	echo '<div class="column_1">' . cute_number($game['points']) . '</div><a href="/spel/topplistan/mest_hypade/">Hypes</a>' . "\n";
    echo '</div>' . "\n";
    echo '<div class="game_release">' . "\n";
	echo '<div class="column_1">Släpptes</div>' . date('Y-m-d', $game['release']) . "\n";
    echo '</div>' . "\n";
    echo '</div>' . "\n"; //end game_popularity
}

function games_game_controls_draw($game, $options = null)
{
    // $game['controls'] tänks vara en array som här skall loopas igenom och listas
    echo '<table class="game_controls">' . "\n";
    $num_of_rows = ceil(count($game['controls']) / 2);
    for($i = 0; $i < $num_of_rows; $i++)
    {
    	echo '<tr>' . "\n";
        echo '<td class="combination">' . $game['controls'][$i]['combination'] . '</td>' . "\n";
        echo '<td class="description">' . $game['controls'][$i]['description'] . '</td>' . "\n";
		echo '</tr>' . "\n";
    }
    echo '</table>' . "\n";
}

function games_game_draw($game, $options = null)
{
    echo '<input type="hidden" id="game_handle" value="' . $game['handle'] . '"/>' . "\n";
    echo '<div class="game_play">' . "\n";
    echo '<h1>' . $game['title'] . '</h1>' . "\n";
	echo '<div id="game_header">' . "\n";
    rounded_corners_top();
	echo '<div id="game_header_main">' . "\n";
    echo '<p>' . $game['description'] . '</p>' . "\n";
	echo '<div id="game_buttons" >' . "\n";
	echo '<button id="game_fullscreen" onclick="javascript: open_fullscreen_window(\'http://amuse.hamsterpaj.net/distribute/game/' . $game['handle'] . '.swf\');">';
	echo 'Spela i fullskärm';
	echo '</button>' . "\n";
/* 	if(5 == $_SESSION['login']['userlevel']) */
/* 	{ */
		echo '<button id="game_challenge_button" >Utmana en kompis</button>' . "\n";
/* 	} */
    if(login_checklogin())
    {
	    $query = 'SELECT * FROM user_favorites AS uf, games AS g WHERE uf.type = "game" AND uf.item_id = g.id AND g.handle = "' . $game['handle'] . '" AND uf.user_id = "' . $_SESSION['login']['id'] . '"';
    	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
    	$is_favorite = mysql_fetch_assoc($result) ? true : false;
	}
	echo '<div id="game_vote_div" class="vote">' . "\n";
	echo '</div>' . "\n"; //end vote
	echo '</div>' . "\n"; //end game_buttons
	echo '</div>' . "\n"; //end game_header_main

	echo '<div id="game_header_info">' . "\n";
    games_game_popularity_draw($game); //a div with class game_popularity
    games_list_tags(tag_get_by_handle(array_keys($game['tags'])));
	if(3 <= $_SESSION['login']['userlevel'])
	{
		echo '<a href="/spel/redigera/' . $game['handle'] . '.php" >Redigera spelet</a>' . "\n";
	}
	echo '</div>' . "\n"; //end game_header_info
	echo '<br style="clear: both;" />' . "\n";
    rounded_corners_bottom();
	echo '</div>' . "\n"; //end game_header

	//Challenge a friend
	echo '<input type="hidden" id="game_handle" value="' . $game['handle'] . '"/>';
	echo '<input type="hidden" id="game_title" value="' . $game['title'] . '"/>';
	echo '<div id="game_challenge">' . "\n";
    rounded_corners_top();
	echo '<a name="challenge"></a>' . "\n";
	//alternativ A - hamsterpaj
	//todo! kolla att användaren är inloggad annars visa snabbregistrering
	if(login_checklogin())
	{
		echo '<div class="challenge_alternative_a">' . "\n";
		echo '<h3>Skicka utmaning här på Hamsterpaj</h3>' . "\n";
		echo '<div class="challenge_recievers">' . "\n";
		for($i=1; $i < 4; $i++)
		{
			echo '<h5>Kompis #' . $i . '</h5>' . "\n";
			echo '<input type="text" name="game_challenge_username_' . $i . '" id="game_challenge_username_' . $i . '"/>' . "\n";
		}
		echo '</div>' . "\n";
		echo '<div class="challenge_message">' . "\n";
		echo '<h5>Hälsning (skriv gärna hur långt du kom i spelet)</h5>'. "\n";
		echo '<textarea name="game_challenge_hp_message" id="game_challenge_hp_message" ></textarea>' . "\n";
		echo '<button id="game_challenge_hp_submit">Skicka!</button>' . "\n";
		echo '</div>' . "\n"; //end challenge_message;
		echo '<br />' . "\n";
		echo '</div>' . "\n"; //end challenge_alternative
	}
	else
	{
		echo '<div class="challenge_alternative_a_not_member">' . "\n";
		echo '<h3>Skicka utmaning här på Hamsterpaj</h3>' . "\n";
		echo '<div class="register_info">' . "\n";
		echo '<p>Man kan bara skicka meddelanden på hamsterpaj om man är medlem men det blir du på två sekunder tryck bara på knappen.</p>' . "\n";
		echo '<button onclick="javascript: tiny_reg_form_show();" >Bli medlem!</button>' . "\n";
		echo '</div>' . "\n"; //end register_info
		echo '</div>' . "\n"; //end challenge_alternative_a_not_member
	}
	echo '<hr />' . "\n";
	//alternativ B - e-post
	echo '<div class="challenge_alternative_b">' . "\n";
	echo '<h3>Skicka utmaning med e-post</h3>' . "\n";
	echo '<div class="challenge_recievers">' . "\n";
	for($i=1; $i < 4; $i++)
	{
		echo '<h5>e-post till kompis #' . $i . '</h5>' . "\n";
		echo '<input type="text" name="game_challenge_email_' . $i . '" id="game_challenge_email_' . $i . '"/>' . "\n";
	}
	echo '</div>' . "\n";
	echo '<div class="challenge_message">' . "\n";
	echo '<h5>Hälsning (skriv gärna hur långt du kom i spelet)</h5>'. "\n";
	echo '<textarea name="game_challenge_mail_message" id="game_challenge_mail_message" ></textarea>' . "\n";
	if(!isset($_SESSION['tip_security_code']))
	{
		$_SESSION['tip_security_code'] = rand(10000, 9999999);
	}
	echo '<div class="input_a">' . "\n";
	echo '<div class="security_code">' . "\n";
	echo '<img src="/security_code.tip.png.php" id="regfrm_security_code_img" />' . "\n";
	echo '</div>' . "\n"; //end security_code
	echo '<div class="input_b">' . "\n";
	echo '<div class="security_input">' . "\n";
	echo '<h5>Skriv av numret här ovanför</h5>' . "\n";
	echo '<input type="text" name="security_code" id="security_code" />' . "\n";
	echo '</div>' . "\n"; //end security_input
	echo '<div class="challenge_sender_name"><h5>Ditt namn</h5><input type="text" name="challenge_sender_name" id="challenge_sender_name" /></div>' . "\n";
	echo '<button id="game_challenge_mail_submit">Skicka!</button>' . "\n";
	echo '</div>' . "\n"; //end input_b
	echo '</div>' . "\n"; //end input_a
	echo '<p>' . "\n";
	echo 'Vi vill inte få problem med datorprogram som skickar mail via Hamsterpaj, så därför' . "\n";
	echo 'måster du du skriva av siffrorna i bilden, datorprogram är nämnligen inte så bra på att läsa bilder.' . "\n";
	echo '</p>' . "\n";
	echo '</div>' . "\n"; //end challenge_message;
	echo '<br />' . "\n";
	echo '</div>' . "\n"; //end challenge_alternative
	echo '<hr />' . "\n";
	//alternativ C - kopiera länk
	echo '<div class="challenge_alternative_c">' . "\n";
	echo '<h3>Skicka en länk</h3>' . "\n";
	echo '<p>Kopiera länken och skicka till din kompis</p>' . "\n";
	echo '<div class="challenge_link">http://www.hamsterpaj.net/spel/' . $game['handle'] . '.html</div>' . "\n";
	echo '</div>' . "\n"; //end challenge_alternative
    rounded_corners_bottom();
	echo '</div>' . "\n"; //end game_challenge
	
	echo '<div id="game_challenge_result"></div>' . "\n";	

	//The game!
    echo '<div id="game" class="game">' . "\n";
    //todo! Här skall anpassas till distribute-systemet. distribute_server_get skall anropas för att f? en adress
    // adress skall byggas med server . type . handle . '.' . extension
    echo '<object type="application/x-shockwave-flash" data="http://amuse.hamsterpaj.net/distribute/game/' . $game['handle'] . '.swf" >
                <param name="movie" value="http://amuse.hamsterpaj.net/distribute/game/' . $game['handle'] . '.swf" /></object>';
    echo '</div>' . "\n"; //end game
	if(strlen($game['controls'][0]['combination']) > 0)
	{
	    rounded_corners_top();
   		echo '<div class="game_instructions">' . "\n";
	    echo '<h3>Så här spelar du</h3>' . "\n";
	    games_game_controls_draw($game);
		echo '</div>' . "\n"; //end game_instructions
		rounded_corners_bottom();
	}
	if(in_array('highscore', array_keys($game['tags'])))
	{
    rounded_corners_top();
		echo '<h3>Topplista</h3>' . "\n";
		$query = 'SELECT gh.score, gh.user AS user_id, l.username FROM game_highscores AS gh, login AS l WHERE gh.game = "' . $game['highscore_gname'] . '" AND l.id = gh.user ORDER BY gh.score DESC LIMIT 5';
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 0)
		{
			while($data = mysql_fetch_assoc($result))
			{
				$topten[] = $data;
			}

			echo '<table style="float: left; width: 300px;">' . "\n";
			$rank = 1;
			foreach($topten AS $entry)
			{
				$style = ($entry['user_id'] == $_SESSION['login']['id']) ? ' style="font-weight: bold;"' : '';
				echo '<tr' . $style . '>' . "\n";
				echo '<td>' . $rank . '</td>' . "\n";
				echo '<td><a href="/traffa/profile.php?id=' . $entry['user_id'] . '">' . $entry['username'] . '</a></td>' . "\n";
				echo '<td>' . $entry['score'] . 'p</td>' . "\n";
				echo '</tr>' . "\n";
				$rank++;
			}
			echo '</table>' . "\n";

			if(login_checklogin())
			{
				$query = 'SELECT score FROM game_highscores WHERE user = "' . $_SESSION['login']['id'] . '" AND game = "' . $game['highscore_gname'] . '"';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					$user_score = $data['score'];
					
					/* Find out the users position */
					$query = 'SELECT COUNT(*) AS position FROM game_highscores WHERE score > "' . $user_score . '" AND game = "' . $game['highscore_gname'] . '"';
					$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
					$data = mysql_fetch_assoc($result);
					$user_position = $data['position'] + 1;
					
					$i = 1;
					/* Fetch the two users with higher score */
					$query = 'SELECT gh.score, gh.user AS user_id, l.username FROM game_highscores AS gh, login AS l WHERE gh.game = "' . $game['highscore_gname'] . '" AND l.id = gh.user AND gh.score > "' . $user_score . '" ORDER BY gh.score ASC LIMIT 2';
					$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
					while($data = mysql_fetch_assoc($result))
					{
						$data['position'] = $user_position - (mysql_num_rows($result) - $i);
						$score_table[] = $data;
						$i = 0;
					}
					
					$score_table = array_reverse($score_table);

					$score_table[] = array('user_id' => $_SESSION['login']['id'], 'username' => $_SESSION['login']['username'], 'position' => $user_position, 'score' => $user_score);

					$i = 1;
					/* Fetch the two users with lower score */
					$query = 'SELECT gh.score, gh.user AS user_id, l.username FROM game_highscores AS gh, login AS l WHERE gh.game = "' . $game['highscore_gname'] . '" AND gh.user != "' . $_SESSION['login']['id'] . '" AND l.id = gh.user AND gh.score <= "' . $user_score . '" ORDER BY gh.score DESC LIMIT 2';
					$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
					while($data = mysql_fetch_assoc($result))
					{
						$data['position'] = $user_position + $i;
						$score_table[] = $data;
						$i++;
					}

					/* Phew, only the output left */
					echo '<table style="width: 300px;">' . "\n";
					foreach($score_table AS $entry)
					{
						$style = ($entry['user_id'] == $_SESSION['login']['id']) ? ' style="font-weight: bold;"' : '';
						echo '<tr' . $style . '>' . "\n";
						echo '<td>' . $entry['position'] . '</td>' . "\n";
						echo '<td><a href="/traffa/profile.php?id=' . $entry['user_id'] . '">' . $entry['username'] . '</a></td>' . "\n";
						echo '<td>' . $entry['score'] . 'p</td>' . "\n";
						echo '</tr>' . "\n";
					}
					echo '</table>' . "\n";
				}
			}

		}
		rounded_corners_bottom();		
	}
	
    rounded_corners_top();
//    rounded_corners_top(array('color' => 'white'));
/*	echo '<div class="game_comments">' . "\n";
    echo '<h3>Snacka om spelet</h3>' . "\n";
	if(login_checklogin())
	{
		echo '<textarea id="game_comment_textarea"></textarea>' . "\n";
		echo '<button id="comment_submit_button">Skicka</button>' . "\n";
	}
	else
	{
		echo '<p>Man kan bara skriva kommentarer om man är medlem. Men det blir du jättelätt, tryck bara på knappen och välj användarnamn och lösenord, inget mer!</p>';
		echo '<button onclick="javascript: tiny_reg_form_show();" >Bli medlem!</button>' . "\n";
	}

	$comments_fetch['type'] = 'game_comments';
	$comments_fetch['handle'] = $game['handle'];
	$discussions = discussions_fetch($comments_fetch);
	if(count($discussions) > 0)
	{
		$discussion = $discussions[0];
	}
	else
	{
		$create['handle'] = $game['handle'];
		$create['title'] = $game['title'];
		$create['author'] = 2348;
		$create['discussion_type'] = 'game_comments';
		$discussion	= discussion_create($create);
		$post_create['content'] = 'Skriv här vad du tycker om spelet!';
		$post_create['discussion_id'] = $discussion['id'];
		$post_create['author'] = 2348;
		$post_id = posts_create($post_create);
	}
	$posts = posts_fetch(array('discussion_id' => $discussion['id'], 'limit' => '10', 'order' => array(array('field' => 'p.id', 'direction' => 'desc'))));
	echo '<div id="game_comments_list">' . "\n";
	echo games_comments_list($posts);
	echo '</div>' . "\n";
	echo '</div>' . "\n";
//	rounded_corners_bottom(array('color' => 'white'));
*/
	echo '<h2 class="rank_input_header">Din poäng</h2>' . "\n";
	echo '<h2 class="comment_input_header">Din kommentar</h2>' . "\n";
	echo '<br style="clear: both;" />' . "\n";
	echo '<div class="game_comments">' . "\n";
	$query = 'SELECT rank FROM user_ranks WHERE user_id = "' . $_SESSION['login']['id'] . '" AND item_id = "' . $game['id'] . '" AND item_type = "game"';
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 1)
	{
		$data = mysql_fetch_assoc($result);
	}
	unset($rank_options);
	$rank_options['previous'] = $data['rank'];
	rank_input_draw($game['id'], 'game', $rank_options);
	comments_input_draw($game['id'], 'game');
	echo '<br style="clear: both;" />' . "\n";
	echo '</div>' . "\n"; // game_comments
	rounded_corners_bottom();
	rounded_corners_top();
	echo comments_list($game['id'], 'game');
	rounded_corners_bottom();

    echo '</div>' . "\n"; //end game_play
    echo '<h1>Andra spel</h1>' . "\n";
}

function games_comments_list($posts)
{
	$output = '';
	foreach($posts as $post)
	{
		if(in_array('removed', $post['flags']))
		{
			continue;
		}
//		$output .= print_r($post, true);
		$output .= '<div class="post" id="game_comment_' . $post['post_id'] . '">' . "\n";
		$output .= '<div class="head">' . "\n";
		$output .= '<div class="date_time">' . fix_time($post['timestamp']) . '</div>' . "\n";
		$output .= '<img class="author_icon" onmouseover="javascript: makeTrue(domTT_activate(this, event, \'content\', \'<img src=' . IMAGE_URL . 'images/users/thumb/' . $post['author'] . '.jpg />\', \'trail\', true));" src="' . IMAGE_URL . 'images/icons/user.png" />' . "\n";
		$output .= '<div class="author"><a href="/traffa/profile.php?id=' . $post['author'] . '">' . $post['username'] . '</a>' . "\n";
		$output .= $post['gender'];
		$output .= (date_get_age($post['birthday']) > 0) ? ' ' . date_get_age($post['birthday']) . ' ' : '';
		$output .= '</div>' . "\n"; //end author
		$output .= '</div>' . "\n"; //end head
		$output .= '<p>' . $post['content'] . '</p>' . "\n";
		if($_SESSION['login']['userlevel'] >= 3)
		{
			$output .= '<div class="controls">' . "\n";
			$output .= '<button onclick="javascript: games_comment_delete(\'' . $post['post_id'] . '\');" class="comment_delete_button" id="comment_delete_button_' . $post['post_id'] . '">Ta bort</button>' . "\n";
			$output .= '</div>' . "\n"; //end controls
		}
		$output .= '</div>' . "\n"; //end post
	}
	return $output;
}

function games_played($game)
{
	$query_extra = '';
	if(login_checklogin())
	{
		$query_update = 'UPDATE user_visits SET count = count + 1, timestamp = "' . time() . '" WHERE' .
					' user_id = ' . $_SESSION['login']['id'] . 
					' AND item_id = ' . $game['id'] .
					' AND type = "game"';
		$query_insert = 'INSERT INTO user_visits (user_id, item_id, type, timestamp) VALUES' . 
							' (' . $_SESSION['login']['id'] . ', ' . $game['id'] . ', "game", "' . time() . '")';
		if(mysql_query($query_insert))
		{
			$query_extra = ', unique_players = unique_players + 1';
		}
		else
		{
			mysql_query($query_update) or die(report_sql_error($query_insert, __FILE__, __LINE__));
		}
	}
	$query = 'UPDATE games SET played = played + 1' . $query_extra . ', ratio=points/unique_players WHERE handle = "' . $game['handle'] . '"';
	mysql_query($query) or die(report_sql_error($query, __LINE__, __FILE__));
}

function games_search_bar_draw($options)
{
    echo '<div id="game_search_bar">' . "\n";
    echo 'Bläddra: ' . "\n";
    $letters = array('0-9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '?', 'Ä', 'Ö', 'Alla');
    echo '<ul>' . "\n";
    foreach($letters as $letter)
    {
        echo '<li><a';
        if($letter == $options['initial_letter'] || ($letter >= '0' && $letter <= '9' && $options['initial_letter'] >= '0' && $options['initial_letter'] <= '9'))
        {
        	echo ' class="current_selection"';
        }
        echo ' href="/spel/bladdra/' . $letter . '.' . 
        		(isset($options['tag']) ? $options['tag'] : 'alla') . '/' .'" >';
        if($letter == $options['initial_letter'] || ($letter >= '0' && $letter <= '9' && $options['initial_letter'] >= '0' && $options['initial_letter'] <= '9'))
        {
	        echo '<strong>';
	    }
        echo $letter; 
        if($letter == $options['initial_letter'] || ($letter >= '0' && $letter <= '9' && $options['initial_letter'] >= '0' && $options['initial_letter'] <= '9'))
        {
	        echo '</strong>';
	    }
        echo '</a>';
        echo '</li>' . "\n";
    }
    echo '</ul>' . "\n";
    echo '</div>' . "\n"; //end games_search_bar
}

function games_search_options_draw($request)
{
    echo '<div id="game_search_options">' . "\n";
    echo '<form name="game_search_options_form" method="post" action="' . $_SERVER['request_uri'] . '">' . "\n";
    echo '<ul>' . "\n";
    echo '<li><input id="played_yes" name="played" type="radio" value="played_yes" ' . ($_SESSION['spel']['played'] == 'yes' ? 'checked="checked"' : '') . '/>' . "\n";
    echo '<label for="played_yes" >Spel jag spelat</label></li>' . "\n";
    echo '<li><input id="played_no" name="played" type="radio" value="played_no" ' . ($_SESSION['spel']['played'] == 'no' ? 'checked="checked"' : '') . ' />' . "\n";
    echo '<label for="played_no" >Spel jag <strong>inte</strong> spelat</label><li>' . "\n";
    echo '<li><input id="played_ignore" name="played" type="radio" value="played_ignore" ' .
    		 ((!isset($_SESSION['spel']['played']) || $_SESSION['spel']['played'] == 'ignore') ? 'checked="checked"' : '') . ' />' . "\n";
    echo '<label for="played_ignore" >Alla</label></li>' . "\n";
    echo '</ul>' . "\n";
	echo '</form>' ."\n";
    echo '</div>' . "\n"; //end games_search_options
}

function games_search_field_draw()
{
	echo '<div id="game_search_field">' . "\n";
	echo '<form method="post" action="/spel/bladdra/">' . "\n";
	echo '<input id="game_search_field_text" type="text" name="search_string" />' . "\n";
	echo '<input type="submit" id="games_search_submit" value="Sök"/>' . "\n";
	echo '</form>' . "\n";
	echo '</div>' . "\n";
}

function games_page_navigation_draw($num_of_games, $page, $url)
{
	echo 'Sida: ';
	if(!isset($page))
	{
		$page = 1;
	}
	if(is_array($num_of_games))
	{
		$num_of_games = count($num_of_games);
	}
	else
	{
		$pages = ceil($num_of_games/GAMES_PER_PAGE);
	}
	echo '<ol class="games_page_list">' . "\n";
	for($i = 1; $i <= $pages; $i++)
	{
		echo '<li>';
		if($i == $page)
		{
			echo '<strong> ' . $i . ' </strong>';
		}
		else
		{
			echo '<a href="' . $url . 'sida_' . $i . '.html">' . $i . '</a>';
		}
	 	echo '</li>' . "\n";
	}
	echo '</ol>' . "\n";
}

function games_game_new($options)
{
	echo '<h1>Nytt spel</h1>' . "\n";
	echo '<div id="game_fetch_link">' . "\n";
	echo '<h5>Klistra in länk till spelfilen:</h5>' . "\n";
	echo '<input type="text" name="fetch_link" id="game_fetch_link_input"/>'. "\n";
	echo '<button id="game_fetch_link_button">Skicka</button>' . "\n";
	echo '</div>' . "\n";
	
	echo '<p>bry dig inte om det nedanför förrän du tryckt p? skicka och sett att spelet fungerar i förhandsgranskningen nedan...</p>' . "\n";
	
	echo '<div id="game_preview"></div>' . "\n";

	games_admin_draw(array('game_new' => true));
}

function games_game_upload_reference($options)
{
	define(GAMES_TEMP_PATH, '/storage/www/www.hamsterpaj.net/data/spel_temp/');
	$hash = md5(rand());
	preg_match('/\.(\w+)$/', $options['fetch_link'], $matches);
	$extension = $matches[1];
	$_SESSION['new_game_temp']['hash'] = $hash;
	$_SESSION['new_game_temp']['extension'] = $extension;
	$command = 'wget -O ' . GAMES_TEMP_PATH . $hash . '.' . $extension . ' ' . $options['fetch_link'];
	log_to_file('games', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'executing command: ' . $command);
	exec($command, $output, $return_value);
	log_to_file('games', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'command: ' . $command . ' returned ' . $return_value, serialize($output));
    echo '<object style="width: 635px; height: 560px;" type="application/x-shockwave-flash" data="http://www.hamsterpaj.net/spel_temp/' . $hash . '.' . $extension . '" >
                <param name="movie" value="http://www.hamsterpaj.net/spel_temp/' . $hash . '.' . $extension . '" />
                <img src="http://images.hamsterpaj.net/logo.png" alt="Hamsterpaj logo" />
            </object>';
	echo '<p>' . 'command: $command returned ' . $return_value . '</p>' . "\n";
	echo '<p> output: ' . $output . '</p>' . "\n";
	echo '<p> extension: ' . $extension . "\n";
}

function games_game_new_save()
{
	define(GAMES_TEMP_PATH, '/storage/www/www.hamsterpaj.net/data/spel_temp/');
	$handle = games_game_save();
	$command = 'mv ' . GAMES_TEMP_PATH . $_SESSION['new_game_temp']['hash'] . '.' . $_SESSION['new_game_temp']['extension'] . 
				' /storage/www/www.hamsterpaj.net/data/distribute/game/' . $handle . '.' . $_SESSION['new_game_temp']['extension'];
	log_to_file('games', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'executing command: ' . $command);
	exec($command, $output, $return_value);
	log_to_file('games', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'command: ' . $command . ' returned ' . $return_value, serialize($output));
	return $handle;
}

function games_game_distribute($options)
{
	distribute_item(array('type' => 'game', 'handle' => $options['handle'], 'extension' => $_SESSION['new_game_temp']['extension']));
}

function games_admin_draw($options)
{
	$slot_1_start = strtotime('10:30');
	$slot_1_end   = strtotime('11:30');
	$slot_2_start = strtotime('17:30');
	$slot_2_end   = strtotime('18:30');
	
//	echo date('Y-m-d H:i', $slot_1_start) . '<br />';
//	echo date('Y-m-d H:i', $slot_1_end) . '<br />';
//	echo date('Y-m-d H:i', $slot_2_start) . '<br />';
//	echo date('Y-m-d H:i', $slot_2_end) . '<br />';
	
//	unset($game);
	if(isset($options['game']))
	{
		$game = $options['game'];
	}
	else
	{

/*------------------------------------------------------------------------------------*/
		
	$config['new_game'][0]['start']				= strtotime('07:00');
	$config['new_game'][0]['end']				= strtotime('09:15');
	$config['new_game'][1]['start']				= strtotime('16:00');
	$config['new_game'][1]['end']				= strtotime('21:30');
		
		$type = 'new_game';
		$slots = $config[$type];
		$num_of_slots = count($slots);
		$slot = 0;
		$day = 0; /* Offset, days counting from today */
		$midnight = strtotime(date('Y-m-d'));
		$time = time();
		unset($free_slot);
		
		echo 'time: ' . $time . ' ' . date('Y-m-d H:i', $time) . '<br />';
		echo 'after: ' . $options['after'] . ' ' . date('Y-m-d H:i', $options['after']) . '<br />';
		echo 'type: ' . $type . '<br />';
		echo date('Y-m-d H:i', $slots[$slot]['end'] + $day * 86400) . '<br />';
		
		/* Find the next slot, regardless if it's occupied or not */
		while(($slots[$slot]['start'] + ($day * 86400)) <= $time)
		{
			echo 'slot before time <br />';
			$slot++;
			if($slot >= $num_of_slots)
			{
				$day++;
				$slot = 0;
			}
		}
		echo 'Find nearest slot after day #' . $day . ', slot #' . $slot . '<br />';
		
		$query = 'SELECT `release` FROM games WHERE `release` > "' . $time . '" ORDER BY `release` ASC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		echo 'Items in qeue: ' . mysql_num_rows($result) . '<br />';
		if($data = mysql_fetch_assoc($result))
		{
			$release = $data['release'];
			echo 'before loop, release: ' . $release . ' ' . date('Y-m-d H:i', $release) . '<br />';
			while(!isset($free_slot))
			{
				if($release < $slots[$slot]['start'] + ($day * 86400)) /* Released before current slot */
				{
					echo 'Released before current slot' . "\n";
					if($data = mysql_fetch_assoc($result))
					{
						$release = $data['release'];
					}
					else
					{
						$free_slot = $slot;
					}
				}
				elseif($release < $slots[$slot]['end'] + $day * 86400) /* Released during current slot */
				{
					$slot++;
					if($slot >= $num_of_slots)
					{
						$day++;
						$slot = 0;
					}
					if($data = mysql_fetch_assoc($result))
					{
						$release = $data['release'];
					}
					else
					{
						$free_slot = $slot;
					}
				}
				else /* Released after current slot */
				{
					$free_slot = $slot;
				}
			}
		}
	//	preint_r($slots);
		echo 'slot: ' . $slot . '<br />';
		echo 'day: ' . $day . '<br />';
		$next_release = $slots[$slot]['start'] + $day * 86400 + rand(0, $slots[$slot]['end'] - $slots[$slot]['start']);
	}

/*--------------------------------------------------------------------------------------------------------------*/


/* 	preint_r($game); */
	global $game_tags;
	echo '<form id="game_edit_form" method="post" action="/spel/' . (isset($options['game_new']) ? 'nytt_spel' : 'redigera') . '_spara.php" enctype="multipart/form-data">' . "\n";
	echo '<h5>Titel</h5>' . "\n";
	echo '<input type="text" name="title" value="' . (isset($game) ? $game['title'] : '') . '" />' . "\n";
	if(isset($game))
	{
		echo '<input type="hidden" name="handle" value="' . $game['handle'] . '" />' . "\n";
	}
	echo '<h5>Taggar</h5>' . "\n";
	$tags_all = tag_get_by_handle($game_tags);
	echo '<ul>' . "\n";
	foreach($tags_all as $tag)
	{
		echo '<li>' . "\n";
		echo '<img alt="' . $tag['label'] . '" class="games_tag" id="games_tag_' . $tag['handle'] . 
			 '" src="' . IMAGE_URL . 'games/tags/'. $tag['handle'] . '.png" />' . "\n";
		echo '<br />';
		echo '<input type="checkbox" name="chk_tag_' . $tag['handle'] . '" id="chk_tag_' . $tag['handle'] . '" name="tags"';
		if(isset($game))
		{
			echo array_key_exists($tag['handle'], $game['tags']) ? ' checked="checked" ' : '';
		}
		echo '>' . '' . '</input>' . "\n";
//		echo '<label for="chk_tag_' . $tag['handle'] . '">' . "\n";
//		echo '</label>' . "\n";
		echo '<li>' . "\n";
	}
	echo '</ul>' . "\n";
	foreach($game['tags'] as $tag)
	{
		if(!array_key_exists($tag['handle'], $tags_all))
		{
			$extra_tags_labels[] = $tag['label'];
		}
	}
	echo '<h5>Nyckelord för övriga taggar (separerade med mellanslag)</h5>' . "\n";
	echo '<textarea name="tags" rows="3" cols="60" >' . (isset($game) ? implode(', ', $extra_tags_labels) : '') . '</textarea>' . "\n";
	echo '<h5>Beskrivning</h5>' . "\n";
	echo '<textarea name="description" rows="6" cols="60" >' . (isset($game) ? $game['description'] : '') . '</textarea>' . "\n";
	/* Added by Johan at 2007-09-23 10:00 */
	echo '<h5>Bild (skalas och konverteras automagiskt)</h5>' . "\n";
	echo '<input name="thumbnail" type="file" />' . "\n";		

	echo '<h5>Release</h5>' . "\n";
	echo '<p>ex 2008-12-24, ' . $next_release . '</p>';
	echo '<input type="text" name="release" value="' . (isset($game['release']) ? date('Y-m-d h:i',$game['release']) : date('Y-m-d H:i', $next_release)) . '" />' . "\n";
	echo '<input type="checkbox" name="release_now" value="true" id="release_now_check" />' . "\n";
	echo '<label for="release_now_check">Släpp spelet direkt</label>' . "\n";
	
	echo '<h5>Kontroller</h5>' . "\n";
	echo '<table>' . "\n";
	for($i = 0; $i < 8; $i++)
	{
		echo '<tr>' . "\n";
		echo '<td><input type="text" name="key_' . $i . '" value="' . (isset($game) ? $game['controls'][$i]['combination'] : '') . '"></td>';
		echo '<td><input type="text" name="action_' . $i . '" value="' . (isset($game) ? $game['controls'][$i]['description'] : '') . '"></td>';
		echo '</tr>' . "\n";
	}
	echo '</table>' . "\n";
	echo '<input type="checkbox", value="delete" id="chk_game_delete" name="delete" />';
	echo '<label for="chk_game_delete">Ta bort spelet</label>';
	echo '<h5>Highscore gname (låt bli om du inte vet vad detta är!)</h5>' . "\n";
	echo '<input type="text" name="highscore_gname" value="' . $game['highscore_gname'] . '" /><br />' . "\n";
	echo '<input type="submit" value="Spara" class="button_60" style="border: none;" />' . "\n";
	echo '</form>' . "\n";
}

function games_game_save($options)
{
	// Make handle from title
	$handle = isset($_POST['handle']) ? $_POST['handle'] : url_secure_string($_POST['title']);
	// Make array of controls
	$controls = array();
	for($i = 0; $i < 8; $i++)
	{
		$controls[$i]['combination'] = $_POST['key_' . $i];
		$controls[$i]['description'] = $_POST['action_' . $i];
	}
	
	$release = (isset($_POST['release_now'])) ? time() : strtotime($_POST['release']);
	
	$query_insert = 'INSERT INTO games (handle, title, description, controls, `release`, highscore_gname)';
	$query_insert .= ' VALUES ("' . $handle . '", "' . $_POST['title'] . '", "' . $_POST['description'] . '", "' . mysql_real_escape_string(serialize($controls)) . '", ' . $release . ', "' . $_POST['highscore_gname'] . '")';

	$query_update = 'UPDATE games SET title = "' . $_POST['title'] . '"';
	$query_update .= ', description = "' . $_POST['description'] . '", controls = "' . mysql_real_escape_string(serialize($controls)) . '"';
	$query_update .= ', `release` = "' . $release . '"';
	$query_update .= ', highscore_gname = "' . $_POST['highscore_gname'] . '"';
	$query_update .= ' WHERE handle = "' . $handle . '"';

	log_to_file('games', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'query_insert: ' . $query_insert);
	if(!mysql_query($query_insert))
	{
		log_to_file('games', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'query_update: ' . $query_update);
		mysql_query($query_update) or die(report_sql_error($query_update));
	}
	else
	{
		jscript_alert('Scheduling release');
		$schedule['item_id'] = mysql_insert_id();
		$schedule['type'] = 'new_game';
		$schedule['data'] = serialize($_POST);
		$schedule['release']= $release;
		schedule_event_add($schedule);
	}
	$query = 'SELECT id, handle FROM games WHERE handle = "' . $handle . '"';
	$result = mysql_query($query) or die(report_sql_error($query));
	if($data = mysql_fetch_assoc($result))
	{
		$game_id = $data['id'];
		$game_handle = $data['handle'];
	}

	//save tags
	global $game_tags;
	foreach($game_tags as $handle)
	{
		if(isset($_POST['chk_tag_' . $handle]))
		{
			$save['tag_handle'][] = $handle;
		}
	}
	$save['item_id'] = $game_id;
	$save['object_type'] = 'game';
	tag_set_wrap($save);
	
	unset($save);
	$save['item_id'] = $game_id;
	$save['object_type'] = 'game';
	$save['add'] = true;
	foreach(explode(',', $_POST['tags']) as $keyword)
	{
		$keyword = trim($keyword);
		$save['tag_label'][] = $keyword;
	}
	tag_set_wrap($save);
	
	/* Resize, convert and save the uploaded thumbnail */
	if(strlen($_FILES['thumbnail']['tmp_name']) > 1)
	{
		system('convert ' . $_FILES['thumbnail']['tmp_name'] . ' -resize 120x90! /mnt/images/games/' . $game_handle . '.png');
		echo 'Running: convert ' . $_FILES['thumbnail']['tmp_name'] . ' -resize 120x90! /mnt/images/games/' . $game_handle . '.png';
	}

	echo '<p>Nu är spelet sparat och spelets handle är: ' . $game_handle . '</p>' . "\n";
	echo 'game_id = ' . $game_id . '<br />' . "\n";
	
	return $game_handle;
}

function games_challenge_send($options)
{
	log_to_file('games', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
				$options['reciever_name'] . ' challenged by ' . $options['sender_id'] . ' in a game of ' . 
				$options['game_handle'] . ' with the message ' . $options['message']);

	$message = '<div class="challenge">' .
				'<div class="challenge_message" >Jag utmanar dig i <a href="/spel/' . $options['game_handle'] . '.html">' . 
				$options['game_title'] . '</a></p>' .
				'<p>' . $options['message'] . '</p>' .
                '</div>' .
				'<a href="/spel/' . $options['game_handle'] . '.html"><img alt="' . $options['game_title'] . '" class="left" ' . 
                ' src="' . IMAGE_URL . 'games/' . $options['game_handle'] . '.png" /></a>' .
                '</div><br style="clear: both;" />';
	foreach($options['reciever'] as $reciever)
	{
		if(strlen($reciever) > 2)
		{
			if($options['method'] == 'guestbook')
			{
				$query = 'SELECT id FROM login WHERE username="' . $reciever . '"';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				if($data = mysql_fetch_assoc($result))
				{
					$recipient = $data['id'];
					new_entry($recipient, $options['sender_id'], $message);
					$output = '<p>Din utmaning och en länk till spelet finns nu i gästboken hos ' . $reciever . '.</p>' . "\n";
				}
				else
				{
					$output = '<p>Den användaren (' . $reciever . ') finns inte. Kolla namnet och försök igen.</p>';
				}
			}
			elseif($options['method'] == 'mail')
			{
				$result = tips_send(array('sender_name' => $options['sender_name'],
											'reciever' => $reciever,
											'message' => $options['message'],
											'link' => '/spel/' . $options['game_handle'] . '.html',
											'subject' => 'Tips från Hamsterpaj.net'));
				switch($result)
				{
					case 'ok':
						$output .= '<p>Din utmaning har nu skickats med e-post.</p>';
					break;
					case 'denies':
						$output .= '<p class="challenge_error">Inget meddelande skickades eftersom ' . $reciever . ' vill inte ha några tips från Hamsterpaj.</p>';
					break;
					case 'false_sender':
						$output .= '<p class="challenge_error">Ett fel uppstod då ett meddelande skulle skickas från en användare som inte finns.<p>';
					break;
				}
			}
		}
	}
	echo utf8_encode($output);
}
?>
