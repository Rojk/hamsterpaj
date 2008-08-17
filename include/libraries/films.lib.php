<?php
/*	This is the function library for flash films and film klipps. Functions for 
	listing, playing, adding, editing and fetching films. It uses other libraries
	such as distribute.lib.php, tags.php schedule.lib.php and rank.lib.php.
*/

require_once(PATHS_INCLUDE . 'libraries/distribute.lib.php');
require_once(PATHS_INCLUDE . 'libraries/tags.php');
require_once(PATHS_INCLUDE . 'libraries/schedule.lib.php');
require_once(PATHS_INCLUDE . 'libraries/rank.lib.php');
require_once(PATHS_INCLUDE . 'libraries/comments.lib.php');

define(FILMS_TEMP_PATH, '/storage/www/www.hamsterpaj.net/data/film_temp/');

global $film_category_handles;
foreach($film_categories as $category_id => $film_category)
{
	$film_category_handles[$category_id] = $film_category['handle'];
}

function films_action_get($url)
{
	unset($return);
	// Remove anchor links from url
	$url = preg_replace('/#.*$/', '', $url);
	// Add a '/' if needed
    $url = preg_replace('/([^(php)(html)\/])$/','$0/',$url);									 
	$return['modified_url'] = $url;

	$urls = array(
	'/klipp/',
	'/flash/',
	'/klipp/sida_3.html',
	'/klipp/topplistan/',
	'/flash/favoriter/',
	'/flash/kategori/',
	'/flash/kategori.x/',
	'/flash/kategori.x/sida_2.html',
	'/klipp/kategori/handle.html',
	'/flash/admin/',
	'/flash/admin/kommando.php',
	'/klipp/admin/handle.html');
	
	$items[1] = 'film_type';
	$items[8] = 'action';
	$items[10] = 'initial_letter';
	$items[13] = 'page';
	$items[15] = 'handle';
	$items[17] = 'command';
/*
foreach($urls as $url)
{
*/
	unset($return);

	if(preg_match('/^\/((flash)|(klipp)|(film)|(bilder))\/(((\w+)(\.([\wåäöÅÄÖ]))?\/)?((sida_(\d+)\.html)|((\w+)\.html)|((\w+)\.php))?)?/', $url, $matches))
	{
		foreach($items as $key => $item)
		{
			if(isset($matches[$key]) && $matches[$key] != '')
			{
				$return[$item] = $matches[$key];
			}
		}
		$return['matches'] = $matches;
	}
/*
	$returns[] = $return;
}
	return $returns;
*/

	global $film_category_handles;
//	preint_r($film_category_handles);
	if(in_array($return['action'], $film_category_handles))
	{
		$return['category'] = $return['action'];
		unset($return['action']);
	}
    return $return;
}

function films_fetch_and_list($options_array)
{
	unset($exclude);
	foreach($options_array as $options)
	{
		$options['fetch']['exclude'] = array_merge($options['fetch']['exclude'], $exclude);
		$films = films_fetch($options['fetch']);
		foreach($films as $film)
		{
			$exclude[] = $film['id'];
		}
		films_list($films, $options['list']);
	}
}

/**
 * List films in ordinary list and compact list
 * $films array of films with all necessary data included
 * 		(id, title, handle, rank_avarage, votes)
 */
function films_list($films, $options)
{
//	echo '<p>list_type: ' . $options['list_type'] . '</p>';
	$options['list_type'] = isset($options['list_type']) ? $options['list_type'] : 'thumbnails';
	$options['list_type'] = $options['list_type'] == 'film_list_compact' ? 'compact' : $options['list_type']; 
	global $film_categories;
	echo '<div class="film_list_' . $options['list_type'] . '">' . "\n";
	if(isset($options['headline']))
	{
		echo '<h2>' . $options['headline'] . '</h2>' . "\n";
	}
	$count = 0;
		switch($options['list_type'])
		{
			case 'compact':
			case 'film_list':
			case 'thumbnails':
				foreach($films as $film)
				{
					films_thumbnail_draw($film);
				}
			break;
			case 'titles':
				$letter = 'A';
				$count = count($films);
				$border[0] = floor($count / 3);
				$border[1] = 2 * $border[0];
				$border[2] = $count;
				$column = 0;
				echo '<ul class="film_title_list">' . "\n";
				echo '<h2>' . $letter . '</h2>' . "\n";
				echo '<div class="list_slot">' . "\n";
				$i = 0;
				$slot = 0;
				foreach($films as $film)
				{
					if($letter != strtoupper(substr($film['title'], 0, 1)))
					{
						echo '</div>' . "\n";
						$letter = strtoupper(substr($film['title'], 0, 1));
						if($i > $border[$column])
						{
							$column++;
	//						echo '<br style="clear: both;">' . "\n";
							echo '</ul>' . "\n";
	//						echo '<h1>Kolumn: ' . $column . '</h1>';
							echo '<ul class="film_title_list">' . "\n";
						}
						echo '<h2>' . $letter . '</h2>' . "\n";
						echo '<div class="list_slot">' . "\n";
						$slot = 2;
					}
					elseif($slot > 6)
					{
						echo '</div>' . "\n";
//						echo '<div class="header_dummy"></div>' . "\n";
						echo '<div class="list_slot">' . "\n";
						$slot = 0;
					}
					echo '<li>' . "\n";
					echo '<a href="/' . $film['film_type'] . '/' . $film_categories[$film['category_id']]['handle'] . '/' . $film['handle'] . '.html">' . "\n";
					echo /*$slot . ' ' .*/ $film['title'] . "\n";
					echo '</a>' . "\n";
					echo '</li>' . "\n";
					$i++;
					$slot++;
				}
				echo '</div' . "\n";
				echo '</ul>' . "\n";
			break;
		}
	echo '<br style="clear: both;">' . "\n";
	echo '</div>' . "\n";
}

function films_thumbnail_draw($film)
{
	global $film_categories;
	echo '<div id="film_compact_' . $film['handle'] . '" class="film_compact">' . "\n";
	if($film['film_type'] == 'spel') /* Så jävla bäst! */
	{
		if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
		{
			echo '<div style="background: url(\'' . IMAGE_URL . 'games/' . $film['handle'] . '.png\') 5px 5px no-repeat;">' . "\n";
			echo '<a href="/' . $film['film_type'] . '/' . $film['handle'] . '.html">' . "\n";
			echo '<img src="http://images.hamsterpaj.net/game_thumb_passepartout.png" />';
			echo '</a>' . "\n";
			echo '</div>';
		}
		else
		{
			echo '<a href="/' . $film['film_type'] . '/' . $film['handle'] . '.html">' . "\n";
			echo '<img alt="' . $film['title'] . '" src="' . IMAGE_URL . 'games/' . $film['handle'] . '.png" />' . "\n";
			echo '</a>' . "\n";
		}
		echo '<a href="/' . $film['film_type'] . '/' . $film['handle'] . '.html">' . "\n";
		echo '<h5>' . $film['title'] . '</h5></a>' . "\n";
	}
	else
	{
		if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
		{
			echo '<div style="background: url(\'' . IMAGE_URL . 'film/' . $film['handle'] . '.png\') 5px 5px no-repeat;">' . "\n";
			echo '<a href="/' . $film['film_type'] . '/' . $film_categories[$film['category_id']]['handle'] . '/' . $film['handle'] . '.html">' . "\n";
			echo '<img src="http://images.hamsterpaj.net/game_thumb_passepartout.png" />';
			echo '</a>' . "\n";
			echo '</div>';
		}
		else
		{
			echo '<a href="/' . $film['film_type'] . '/' . $film_categories[$film['category_id']]['handle'] . '/' . $film['handle'] . '.html">' . "\n";
			echo '<img alt="' . $film['title'] . '" src="' . IMAGE_URL . 'film/' . $film['handle'] . '.png" />' . "\n";
			echo '</a>' . "\n";
		}
		echo '<a href="/' . $film['film_type'] . '/' . $film_categories[$film['category_id']]['handle'] . '/' . $film['handle'] . '.html">' . "\n";
		echo '<h5>' . $film['title'] . '</h5></a>' . "\n";
	}
	echo '</div>' . "\n"; // end game_compact
}

/**
 * Fetch films based on filter options in $options
	options			array_support	description
	id					yes				only include these ids
	title				yes				only include these titles
	handle				yes				only include these titles
	type				yes				only include films of these types
	category			no				id for category to include
	order				no				column to order by
	order_direction		no				('ASC' or 'DESC')
	release_after		no				only films released after this date (unix timestamp)
	release_before		no				only films released before this date
	limit_offset		no				
	limit				no
 */
function films_fetch($options)
{
//if(
//preint_r($options);
	if(!isset($options['id']) && !isset($options['handle']))
	{
		$options['released'] = true;
	}
	
	$query = 'SELECT f.id, film_type, title, handle, view_count, html, `release`, category_id, use_special_code, extension, trailer_id, r.average as rank_average, r.count as rank_count, r.comment_count as comment_count, f.description' .
				' FROM';
	if(isset($options['order']))
	{
		switch($options['order'])
		{
			case 'user_view_count':
			case 'user_view_date':
				$query .= ' user_visits uv,';
			break;
			case 'user_rank':
				$query .= ' user_ranks ur,';
			break;
		}
	}
	$query .= ' film AS f LEFT OUTER JOIN item_ranks r ON f.id = r.item_id AND r.item_type = "film"';
	$query .= ' WHERE 1';
	if(isset($options['order']))
	{
		switch($options['order'])
		{
			case 'user_view_count':
			case 'user_view_date':
				$query .= ' AND f.id = uv.item_id AND uv.user_id ="' . $options['user_id'] . '"';
			break;
			case 'user_rank':
				$query .= ' AND f.id = ur.item_id AND ur.item_type="film" AND ur.user_id="' . $options['user_id'] . '"';
			break;
		}
	}
	if(isset($options['released']))
	{
		$query .= ' AND `release` < "' . time() . '"';
	}
	if(isset($options['category']))
	{
		$query .= ' AND category_id="' . $options['category'] . '"';
	}
	if(isset($options['film_type']))
	{
		$query .= ' AND film_type="' . $options['film_type'] . '"';
	}
	if(isset($options['handle']))
	{
		$options['handle'] = is_array($options['handle']) ? $options['handle'] : array($options['handle']);
		$query .= ' AND handle IN ("' . implode('", "', $options['handle']) . '")';
	}
	if(isset($options['exclude']))
	{
		$query .= ' AND id NOT IN ("' . implode('", "', $options['exclude']) . '")';
	}
	if(isset($options['order']))
	{
		$query .= ' ORDER BY';
		switch($options['order'])
		{
			case 'user_view_count':
				$query .= ' uv.count';
			break;
			case 'user_view_date':
				$query .= ' uv.timestamp';
			break;
			case 'user_rank':
				$query .= ' ur.rank';
			break;
			case 'comment_count':
				$query .= ' comment_count';
			break;
			case 'random':
				$query .= ' RAND()';
			break;
			default:
				$query .= ' `' . $options['order'] . '`';
		}
		if(isset($options['order_direction']))
		{
			$query .= ' ' . $options['order_direction'];
		}
		else
		{
			$query .= ' DESC';
		}
	}
	if(isset($options['limit']))
	{
		$query .= ' LIMIT ' . (isset($options['limit_offset']) ? $options['limit_offset'] . ', ' : '') . $options['limit'];
	}
	//echo $query;
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$return[$data['handle']] = $data;
	}
	return $return;
}

function films_film_play_draw($film, $options)
{
	event_log_log('new_' . $film['film_type'] . '_watch');
	film_view_count($film['id']);
	echo '<div class="film_play" id="film_play">' . "\n";

	rounded_corners_top();

	$related_films = films_fetch(array('released' => true, 'film_type' => $film['film_type'], 'exclude' => array($film['id']), 'limit' => 3, 'order' => 'random'));
	films_list($related_films, array('list_type' => 'film_list_compact', 'headline' => 'Fler ' . $film['film_type']));

	echo '<h1 class="film_header">' . $film['title'] . '</h1>' . "\n";
	echo '<div class="film_statistics">' . "\n";
	echo '<div class="film_view_count">' . "\n";
	echo cute_number($film['view_count'] + 1) . ' visningar' . "\n";
	echo '</div>' . "\n"; // film_view_count
	echo rank_draw($film['rank_average'], array('size' => 'medium'));
//	echo '<div class="film_rank_count">' . "\n";
//	echo cute_number($film['rank_count']) . ' röster' . "\n";
//	echo '</div>' . "\n";
	if(5 == $_SESSION['login']['userlevel'])
	{
		echo '<a class="film_edit" href="/film/admin/' . $film['handle'] . '.html">[Redigera]</a>' . "\n";
	}
	echo '</div>' . "\n"; // film_statistics

	echo '<div class="film_player">' . "\n";
	if($film['trailer_id'] > 0)
	{
		echo '<!-- Play Networks - Embeddable Flash Player -->' . "\n";
		echo '<div id="playnw" class="playnw">' . "\n";
		echo '<script src="http://se.player.playnetworks.net/player.php?mid=' . $film['trailer_id'] . '&channel_user_id=4601100020-1&width=460&height=345"></script><br>' . "\n";
		echo '</div>' . "\n";
		echo '<!-- Play Networks - Embeddable Flash Player -->' . "\n";
	}
	elseif($film['use_special_code'] == 1)
	{
		echo stripslashes($film['html']);
	}
	elseif($film['extension'] == 'swf')
	{
		//todo! Här skall anpassas till distribute-systemet. distribute_server_get skall anropas för att f? en adress
		// adress skall byggas med server . type . handle . '.' . extension
		echo '<object type="application/x-shockwave-flash" data="http://amuse.hamsterpaj.net/distribute/film/' . $film['handle'] . '.swf" >
					<param name="movie" value="http://amuse.hamsterpaj.net/distribute/film/' . $film['handle'] . '.swf" /></object>';
	}
	elseif($film['extension'] == 'flv')
	{
		//todo! Här skall anpassas till distribute-systemet. distribute_server_get skall anropas för att f? en adress
		// adress skall byggas med server . type . handle . '.' . extension
		echo '<p id="player1"><a href="http://www.macromedia.com/go/getflashplayer">Installera Flash Player</a> för att kunna se den här filmen.</p>
				<script type="text/javascript">
				var s1 = new SWFObject("/film/flvplayer.swf","single","460","345","7");
				s1.addParam("allowfullscreen","true");
				s1.addVariable("file","http://amuse.hamsterpaj.net/distribute/film/' . $film['handle'] . '.flv");
				s1.addVariable("image","' . IMAGE_URL . '/film/' . $film['handle'] . '.png");
				s1.addVariable("width","460");
				s1.addVariable("height","345");
				s1.write("player1");
				</script>';

	}
	elseif($film['film_type'] == 'bilder')
	{
		//todo! Här skall anpassas till distribute-systemet. distribute_server_get skall anropas för att f? en adress
		// adress skall byggas med server . type . handle . '.' . extension
		echo '<img src="http://images.hamsterpaj.net/fun_images/' . $film['handle'] . '.jpg" class="fun_images_big" />' . "\n";
	}
	echo '</div>' . "\n"; // film_player
	echo '<br style="clear: both;" />' . "\n";

	echo '<div class="film_description">' . "\n";
	if(strlen($film['description']) > 0)
	{
		echo '<p>' . $film['description'] . '</p>' . "\n";
	}
	if($film['trailer_id'] > 0)
	{
		echo '<p>Filmtrailers visas i samarbete med Play Networks</p>' . "\n";
	}
	echo '</div>' . "\n"; // end film_description

	echo '<div class="film_comments">' . "\n";
	echo '<input type="hidden" id="film_id" value="' . $film['id'] . '" />' . "\n";
	echo '<h2 class="rank_input_header">Din poäng</h2>' . "\n";
	echo '<h2 class="comment_input_header">Din kommentar</h2>' . "\n";
	echo '<br style="clear: both;" />' . "\n";

	$query = 'SELECT rank FROM user_ranks WHERE user_id = "' . $_SESSION['login']['id'] . '" AND item_id = "' . $film['id'] . '" AND item_type = "film"';
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 1)
	{
		$data = mysql_fetch_assoc($result);
	}
	unset($rank_options);
	$rank_options['previous'] = $data['rank'];
	rank_input_draw($film['id'], 'film', $rank_options);
	comments_input_draw($film['id'], 'film');
	echo '<br style="clear: both;" />' . "\n";
	echo '</div>' . "\n"; // film_comments
	rounded_corners_bottom();
	$options['comments'] = isset($options['comments']) ? $options['comments'] : 'yes';
	if($options['comments'] == 'yes')
	{
		rounded_corners_top();
		echo comments_list($film['id'], 'film');
		rounded_corners_bottom();
	}
	echo '</div>' . "\n"; // film_play
}

function films_film_viewed($film_id)
{
	if(login_checklogin())
	{
		$query_update = 'UPDATE user_visits SET count = count + 1, timestamp = "' . time() . '" WHERE' .
					' user_id = ' . $_SESSION['login']['id'] . 
					' AND item_id = ' . $film_id .
					' AND type = "film"';
		$query_insert = 'INSERT INTO user_visits (user_id, item_id, type, timestamp) VALUES' . 
							' (' . $_SESSION['login']['id'] . ', ' . $film_id . ', "film", "' . time() . '")';
		mysql_query($query_insert) or mysql_query($query_update) or die(report_sql_error($query_update, __FILE__, __LINE__));
	}
}

function film_view_count($id)
{
	$query = 'UPDATE film SET view_count = view_count + 1 WHERE id = "' . $id . '"';
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
}

function films_searchbar_draw($options)
{
}

function films_category_selection_draw($options)
{
}

/**
 * Draws the form where you can add or edit a film.
 	$film	an array with all film data
 */
function films_admin_form_draw($film_type, $film = null, $options)
{
	rounded_corners_top();
	if(isset($film))
	{
//		preint_r($film);
		echo '<h1>Redigera film</h1>' . "\n";
	}
	else
	{
		echo '<h1>Lägg in en ny film</h1>' . "\n";
	}
/*
	echo '<h2>Ladda upp en filmfil:</h2>' . "\n";
	echo '<form id="film_upload_form" method="post" target="film_upload_frame" action="/film/admin/film_upload_user.php">' . "\n";
	echo '<input type="file" name="film_upload" />' . "\n";
	echo '<input type="submit" class="button_80" value="Skicka" />' . "\n";
	echo '</form>' . "\n";
	echo '<iframe name="film_upload_frame" style="display: none;" ></iframe>' . "\n";
*/
	echo '<h2>eller klistra in länk till filmfilen:</h2>' . "\n";
	echo '<input type="text" name="fetch_link" id="film_fetch_link_input"/>' . "\n";
	echo '<button class="button_50" id="film_fetch_link_button">Skicka</button>' . "\n";
	if(isset($film))
	{
		echo '<input type="checkbox" name="upload_new_file" id="upload_new_file" />' . "\n";
		echo '<label for="upload_new_file" >Uppdatera filmfilen</label>' . "\n";
	}
	echo '<div id="film_preview">' . "\n";
	echo '</div>' . "\n";

	echo '<form id="film_edit_form" method="post" action="/' . 
			$film_type . '/admin/' . (isset($film) ? 'film_save.php' : 'film_new.php') . '" enctype="multipart/form-data">' . "\n";
	if(isset($film))
	{
		echo '<input name="film_id" type="hidden" value="' . $film['id'] . '" />' . "\n";
	}
	echo '<h2>Titel</h2>' . "\n";
	echo '<input type="text" name="title" value="' . (isset($film) ? $film['title'] : '') . '" />' . "\n";
	echo '<h2>Filmtyp <strong>Byt absolut inte här, gå istället in på rätt sida!</strong></h2>' . "\n";
	$film_type = isset($film) ? $film['film_type'] : $film_type;
	echo '<input type="radio" name="film_type" value="flash" id="film_type_radio_flash" ' . ($film_type == 'flash' ? 'checked="on"' : '') . '/>' . "\n";
	echo '<label for="film_type_radio_flash">Flash</label>' . "\n";
	echo '<input type="radio" name="film_type" value="klipp" id="film_type_radio_klipp" ' . ($film_type == 'klipp' ? 'checked="on"' : '') . '/>' . "\n";
	echo '<label for="film_type_radio_klipp">Klipp</label>' . "\n";
	echo '<input type="radio" name="film_type" value="bilder" id="film_type_radio_bilder" ' . ($film_type == 'bilder' ? 'checked="on"' : '') . '/>' . "\n";
	echo '<label for="film_type_radio_bilder">Bild</label>' . "\n";
	echo '<h2>Kategori</h2>' . "\n";
	global $film_categories;
	global $film_type_categories;
//	preint_r($film_categories);
//	preint_r($film_type_categories[$film_type]);
	foreach($film_type_categories[$film_type] as $category_id)
	{
		$categories[$category_id] = $film_categories[$category_id];
	}
//	preint_r($categories);
	echo '<select name="film_category">' . "\n";
	foreach($categories as $category_id => $category)
	{
		echo '<option value="' . $category_id . '"';
		if(isset($film) && $film['category_id'] == $category_id)
		{
			echo ' selected="selected"';
		}
		echo ' >' . $category['title'] . '</option>' . "\n";
	}
	echo '</select>' . "\n";
	echo '<h2>Övriga nyckelord (separerade med mellanslag)</h2>' . "\n";
	if(isset($film))
	{
		unset($keywords);
		$tags = tag_get_by_item('film', $film['id']);
		foreach($tags as $tag)
		{
			$keywords[] = $tag['label'];
		}
	}
	preint_r($keywords);
	echo '<textarea name="tags" rows="3" cols="60" >' . (isset($film) ? implode(' ', $keywords) : '') . '</textarea>' . "\n";
	echo '<h2>Bild (skalas och konverteras automagiskt)</h2>' . "\n";
	if(isset($film))
	{
		echo '<img src="' . IMAGE_URL . '/film/' . $film['handle'] . '.png" />' . "\n";
	}
	echo '<input name="thumbnail" type="file" />' . "\n";
	echo '<h2>Release</h2>' . "\n";
	echo '<input type="text" name="release" value="' . date('Y-m-d H:i', (isset($film) ? $film['release'] : schedule_release_get(array('type' => 'new_' . $film_type)))) . '" />' . "\n";
	echo '<input type="checkbox" name="release_now" value="true" id="release_now_check" />' . "\n";
	echo '<label for="release_now_check">Släpp filmen direkt</label>' . "\n";
	echo '<h2>Specialkod</h2>' . "\n";
	echo '<input type="checkbox", value="true", id="chk_use_special_code" name="use_special_code" ' . (isset($film) && $film['use_special_code'] == 1 ? 'checked=checked' : '') . '/>' . "\n";
	echo '<label for="chk_use_special_code">Använd specialkod</label>' . "\n";
	echo '<textarea name="special_code" rows="5" cols="60" >' . (isset($film) ? stripslashes($film['html']) : '') . '</textarea>' . "\n";
	echo '<br />' . "\n";
	echo '<h2>Filmtrailer.se ID (rör ej om du inte vet vad detta är!)</h2>' . "\n";
	echo '<input type="text" name="trailer_id" value="' . $film['trailer_id'] . '" />' . "\n";
	echo '<input type="checkbox", value="delete" id="chk_film_delete" name="delete" />' . "\n";
	echo '<label for="chk_film_delete">Ta bort film</label>' . "\n";
	echo '<input class="button_50" type="submit" value="Spara" />' . "\n";
	echo '</form>' . "\n";
	rounded_corners_bottom();
}

function films_film_delete($film_id, $film_type)
{
	$query = 'DELETE FROM film WHERE id="' . $film_id . '" AND film_type="' . $film_type . '"';
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
}

function films_film_upload_fetch($options)
{
	log_to_file('film', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'start of films_film_upload_reference()');
	$hash = md5(rand());
	preg_match('/\.(\w+)$/', $options['fetch_link'], $matches);
	$extension = $matches[1];
	$_SESSION['new_film_temp']['hash'] = $hash;
	$_SESSION['new_film_temp']['extension'] = $extension;
	$command = 'wget -O ' . FILMS_TEMP_PATH . $hash . '.' . $extension . ' ' . $options['fetch_link'];
	log_to_file('film', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'executing command: ' . $command);
	exec($command, $output, $return_value);
	log_to_file('film', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'command: ' . $command . ' returned ' . $return_value, serialize($output));
	if($_SESSION['new_film_temp']['extension'] == 'swf')
	{
	    echo '<object style="width: 460px; height: 345px;" type="application/x-shockwave-flash" data="http://www.hamsterpaj.net/film_temp/' . $hash . '.' . $extension . '" >
                <param name="movie" value="http://www.hamsterpaj.net/film_temp/' . $hash . '.' . $extension . '" />
                <img src="http://images.hamsterpaj.net/logo.png" alt="Hamsterpaj logo" />
            </object>';
    }
	elseif($_SESSION['new_film_temp']['extension'] == 'flv')
	{
		//todo! Här skall anpassas till distribute-systemet. distribute_server_get skall anropas för att f? en adress
		// adress skall byggas med server . type . handle . '.' . extension
		echo '<p id="player1"><a href="http://www.macromedia.com/go/getflashplayer">Installera Flash Player</a> för att kunna se den här filmen.</p>
				<script type="text/javascript">
				var s1 = new SWFObject("/film/flvplayer.swf","single","460","345","7");
				s1.addParam("allowfullscreen","true");
				s1.addVariable("file","http://www.hamsterpaj.net/film_temp/' . $_SESSION['new_film_temp']['hash'] . '.flv");
				s1.addVariable("width","460");
				s1.addVariable("height","345");
				s1.write("player1");
				</script>';
	}

	echo '<p>command: ' . $command . ' returned ' . $return_value . '</p>' . "\n";
	echo '<p> output: ' . print_r($output, true) . '</p>' . "\n";
	echo '<p> extension: ' . $extension . "\n";

	preint_r($_SESSION['new_film_temp']);
}

// Den här funktionen verkar inte användas...
function films_film_upload_user()
{
	log_to_file('film', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'start of films_film_upload_user()');
	/* Resize, convert and save the uploaded thumbnail */
	if(strlen($_FILES['film_upload']['tmp_name']) > 1)
	{
		$hash = md5(rand());
		preg_match('/\.(\w+)$/', $_FILES['film_upload']['tmp_name'], $matches);
	//todo! DET ÄR HÄR DET BLIR FEL!!!!!!!!!!!!!!!!!!!!!
if(644314 == $_SESSION['login']['id'])
{
	log_to_file('film', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'name match', print_r($matches, true));
}
		$extension = $matches[1];
		$_SESSION['new_film_temp']['hash'] = $hash;
		$_SESSION['new_film_temp']['extension'] = $extension;
		move_uploaded_file($_FILES['film_upload']['tmp_name'], FILMS_TEMP_PATH . $hash . '.' . $extension);
		if($_SESSION['new_film_temp']['extension'] == 'swf')
		{
			echo '<object style="width: 460px; height: 345px;" type="application/x-shockwave-flash" data="http://www.hamsterpaj.net/film_temp/' . $hash . '.' . $extension . '" >
					<param name="movie" value="http://www.hamsterpaj.net/film_temp/' . $hash . '.' . $extension . '" />
					<img src="http://images.hamsterpaj.net/logo.png" alt="Hamsterpaj logo" />
				</object>';
		}
		elseif($_SESSION['new_film_temp']['extension'] == 'flv')
		{
			echo '<p id="player1"><a href="http://www.macromedia.com/go/getflashplayer">Installera Flash Player</a> för att kunna se den här filmen.</p>
					<script type="text/javascript">
					var s1 = new SWFObject("/film/flvplayer.swf","single","460","345","7");
					s1.addParam("allowfullscreen","true");
					s1.addVariable("file","http://www.hamsterpaj.net/film_temp/' . $_SESSION['new_film_temp']['hash'] . '.flv");
					s1.addVariable("width","460");
					s1.addVariable("height","345");
					s1.write("player1");
					</script>';
		}
	
		echo '<p>command: ' . $command . ' returned ' . $return_value . '</p>' . "\n";
		echo '<p> output: ' . preint_r($output, true) . '</p>' . "\n";
		echo '<p> extension: ' . $extension . "\n";
	}
}

function films_film_distribute_new($film)
{
	preint_r($_SESSION);
	$command = 'mv ' . FILMS_TEMP_PATH . $_SESSION['new_film_temp']['hash'] . '.' . $_SESSION['new_film_temp']['extension'] . 
				' /storage/www/www.hamsterpaj.net/data/distribute/film/' . $film['handle'] . '.' . $film['extension'];
	log_to_file('films', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'executing command: ' . $command);

	exec($command, $output, $return_value);
	log_to_file('films', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'command: ' . $command . ' returned ' . $return_value, serialize($output));
	distribute_item(array('type' => 'film', 'handle' => $film['handle'], 'extension' => $film['extension']));
}

/**
 * Saves a film from POST form to database
 * options
 	new		a new film
 	update	update an existing film
 * @return handle, the films handle
 */
function films_film_save($options)
{
	if($_POST['film_type'] == 'bilder')
	{
		unset($_SESSION['new_film_temp']);
	}
	global $film_categories;
	// Make handle from title
	$handle = isset($_POST['handle']) ? $_POST['handle'] : url_secure_string($_POST['title']);
	
	$release = (isset($_POST['release_now'])) ? time() : strtotime($_POST['release']);
	$film_type = $_POST['film_type'];
	if(isset($options['new']))
	{
		$query = 'INSERT INTO film (handle, title, film_type, category_id, `release`, extension, use_special_code, html, trailer_id)';
		$query .= ' VALUES ("' . $handle . '", "' . 
									$_POST['title'] . '", "' . 
									$film_type . '", "' . 
									$_POST['film_category'] . '", "' . 
									$release . '", "' . 
									(isset($_SESSION['new_film_temp']['extension']) ? $_SESSION['new_film_temp']['extension'] : '') . '", "' . 
									(isset($_POST['use_special_code']) ? '1' : '0') . '", "' . 
									addslashes(html_entity_decode($_POST['special_code'])) . '", "' .
									$_POST['trailer_id'] . '")';

		$schedule['type'] = 'new_' . $film_type;
		$_POST['url'] = '/' . $film_type . '/' . $film_categories[$_POST['film_category']]['handle'] . '/' . $handle . '.html';
		$schedule['data'] = serialize($_POST);
		$schedule['release'] = $release;

		schedule_event_add($schedule);

	}
	elseif(isset($options['update']))
	{
		$query = 'UPDATE film SET title = "' . $_POST['title'] . '"';
		$query .= ', film_type = "' . $film_type . '"';
		$query .= ', `release` = "' . $release . '"';
		$query .= ', trailer_id = "' . $_POST['trailer_id'] . '"';
		$query .= ', category_id = "' . $_POST['film_category'] . '"';
		$query .= (isset($_SESSION['new_film_temp']['extension']) ? ', extension = "' .  $_SESSION['new_film_temp']['extension'] . '"' : '');
		$query .= ', use_special_code = "' . (isset($_POST['use_special_code']) ? '1' : '0') . '"';
		$query .= isset($_POST['use_special_code']) ? ', html="' . addslashes(html_entity_decode($_POST['special_code'])) . '"' : '';
		$query .= ' WHERE handle = "' . $handle . '"';
	}
//	echo '<p>' . $query . '</p>';
	log_to_file('films', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'query: ' . $query);
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

	$query = 'SELECT id, handle FROM film WHERE handle = "' . $handle . '"';
	$result = mysql_query($query) or die(report_sql_error($query));
	if($data = mysql_fetch_assoc($result))
	{
		$film_id = $data['id'];
		$film_handle = $data['handle'];
	}

	unset($save);
	$save['item_id'] = $game_id;
	$save['object_type'] = 'film';
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
		system('convert ' . $_FILES['thumbnail']['tmp_name'] . ' -resize 120!x90! ' . IMAGE_PATH . 'film/' . $film_handle . '.png');
	}
	if($film_type == 'bilder')
	{
		system('convert ' . $_FILES['thumbnail']['tmp_name'] . ' -resize 460x345 ' . IMAGE_PATH . 'fun_images/' . $film_handle . '.jpg');
	}

//	echo '<p>Nu är filmen sparad och filmens handle är: ' . $film_handle . '</p>' . "\n";
//	echo '<p>Direktlänken blir då <a href="http://www.hamsterpaj.net/' . $film_type . '/' . $film_categories[$_POST['film_category']]['handle'] . '/' . $film_handle . '.html">' .
//			'http://www.hamsterpaj.net/' . $film_type . '/' . $film_categories[$_POST['film_category']]['handle'] . '/' . $film_handle . '.html</a' . "\n";
	$film['handle'] = $handle;
	$film['extension'] = $_SESSION['new_film_temp']['extension'];
	$film['url'] = 'http://www.hamsterpaj.net/' . $film_type . '/' . $film_categories[$_POST['film_category']]['handle'] . '/' . $film_handle . '.html';
	return $film;
}
?>