<?php
require_once(PATHS_INCLUDE . 'libraries/distribute.lib.php');
require_once(PATHS_INCLUDE . 'libraries/tags.php');
require_once(PATHS_INCLUDE . 'libraries/schedule.lib.php');
require_once(PATHS_INCLUDE . 'libraries/rank.lib.php');
require_once(PATHS_INCLUDE . 'libraries/comments.lib.php');

define(ENTERTAIN_TEMP_PATH, '/storage/www/www.hamsterpaj.net/data/entertain_temp/');
define(ENTERTAIN_TEMP_URL, '/entertain_temp/');

function entertain_action_get($url)
{
$url = str_replace('entertain/', '', $url);
	global $entertain_types;
	global $entertain_categories;
	global $entertain_category_ids;
	global $entertain_type_categories;
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
	'/klipp/admin/handle.html',
	'/spel/bloons.html',
	'/spel/strategispel/');
	
	$items[1] = 'entertain_type';
	$items[4] = 'view';
	$items[6] = 'initial_letter';
	$items[9] = 'page';
	$items[11] = 'handle';
	$items[13] = 'command';

	unset($return);
	$pattern = '/^\/(';
		foreach($entertain_types as $type)
	{
		$types[] = $type['url_handle'];
	}
	$pattern .= implode('|', $types);
	$pattern .= ')\/(((\w+)(\.([\wåäöÅÄÖ(0-9)]))?\/)?((sida_(\d+)\.html)|((\w+)\.html)|((\w+)\.php))?)?/';
	if(preg_match($pattern, $url, $matches))
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

	foreach($entertain_types as $type)
	{
		if($return['entertain_type'] == $type['url_handle'])
		{
			$return['entertain_type'] = $type['handle'];
			continue;
		}
	}

	if(array_key_exists($return['view'], $entertain_category_ids))
	{
		$return['category_handle'] = $return['view'];
		$return['category_id'] = $entertain_category_ids[$return['view']];
		$return['view'] = 'category_list';
	}

	if($return['view'] != 'admin' && isset($return['handle']))
	{
		$return['view'] = 'item';
	}
	
	global $entertain_list_handles;
	if(array_key_exists($return['view'], $entertain_list_handles))
	{
		$list = $return['view'];
		$return['view'] = 'list';
		$return['list'] = $entertain_list_handles[$list];
	}
    return $return;
}

function entertain_search_field_draw($type)
{
	global $entertain_types;
	$output .= '<div id="entertain_search_field">' . "\n";
	$output .= '<form method="post" action="/' . $entertain_types[$type]['url_handle'] . '/blaeddra/search.php">' . "\n";
	$output .= '<h3><a href="/' . $entertain_types[$type]['url_handle'] . '/blaeddra/">Bläddra</a>, kolla in <a href="/' . $entertain_types[$type]['url_handle'] . '/topplistan/">topplistan</a> eller gör en sökning här &raquo;</h3>' . "\n";
	$output .= '<input id="entertain_search_field_text" type="text" name="search_string" />' . "\n";
	$output .= '<input type="submit" id="games_search_submit" value="Sök"/>' . "\n";
	$output .= '</form>' . "\n";
	$output .= '</div>' . "\n";

	return $output;
}

function entertain_get_handle($title)
{
	$handle_original = url_secure_string($title);
	for($i = 0; $i < 50; $i++)
	{
		$handle = ($i == 0) ? $handle_original : $handle_original . '_' . $i;
		$fetch_result = entertain_fetch(array('handle' => $handle));
		$items = $fetch_result['items'];
		if(count($items) == 0)
		{
			return $handle;
		}
	}
	die('<h1>Fatal error, som man säger. Kunde inte hitta något ledigt handle!</h5>');
}

/* This function returns the config for a given list depending on the content type
 * @return The return value is a options array that can be used with entertain_fetch()
 * and entertain_fetch_and_list();
 */
function entertain_list_by_type_get($type, $list)
{
	global $entertain_types;
	global $entertain_list_styles;
	switch($list)
	{
		case 'search':
			$options_array[0]['fetch']['entertain_type'] = $type;
			$options_array[0]['fetch']['order'] = 'handle';
			$options_array[0]['fetch']['order_direction'] = 'ASC';
			$options_array[0]['list']['headline'] = '';
			switch($type)
			{
				case 'clip':
				case 'flash':
					$options_array[0]['list']['list_style'] = 'thumbnails';
				break;
				case 'software':
					$options_array[0]['list']['list_style'] = 'full';
				break;
				case 'game':
					$options_array[0]['list']['list_style'] = 'thumbnails';
					$options_array[0]['list']['use_searchbar'] = true;
				break;
				case 'image':
					$options_array[0]['list']['list_style'] = 'thumbnails';
				break;
				default:
					$options_array[0]['list']['list_style'] = 'titles';
			}
			$options_array[0]['fetch']['limit'] = $entertain_list_styles[$options_array[0]['list']['list_style']]['items_per_page'];
			$options_array[0]['list']['use_page_navigation'] = true;
		break;
		case 'toplist':
			$options_array[0]['fetch']['entertain_type'] = $type;
			$options_array[0]['fetch']['order'] = 'view_count';
			$options_array[0]['fetch']['limit'] = '12';
			$options_array[0]['list']['headline'] = 'Mest sedda';
			$options_array[0]['list']['list_style'] = $entertain_types[$type]['default_list_style'];
			$options_array[1]['fetch']['entertain_type'] = $type;
			$options_array[1]['fetch']['order'] = 'rank_average';
			$options_array[1]['fetch']['limit'] = '12';
			$options_array[1]['list']['headline'] = 'Andra ' . $entertain_types[$type]['label_plural'] . ' med höga poäng';
			$options_array[1]['list']['list_style'] = $entertain_types[$type]['default_list_style'];
		break;
		case 'favorites':
			$options_array[0]['fetch'] = array('user_id' => $_SESSION['login']['id'], 'entertain_type' => $type, 'order' => 'user_view_date', 'limit' => '4');
			$options_array[0]['list'] = array('headline' => 'Dina senast sedda ' . $entertain_types[$type]['label_plural'] . '');
			$options_array[1]['fetch'] = array('user_id' => $_SESSION['login']['id'], 'entertain_type' => $type, 'order' => 'user_view_count', 'limit' => '4');
			$options_array[1]['list'] = array('headline' => 'Andra ' . $entertain_types[$type]['label_plural'] . ' du tittat mycket på');
			$options_array[2]['fetch'] = array('user_id' => $_SESSION['login']['id'], 'entertain_type' => $type, 'order' => 'user_rank', 'limit' => '4');
			$options_array[2]['list'] = array('headline' => 'Andra ' . $entertain_types[$type]['label_plural'] . ' du tycker är bra');
		break;
	}
	foreach($options_array as $key => $options)
	{
		$options_array[$key]['fetch']['cacheable'] = false;
	}
	return $options_array;
}

function entertain_fetch_and_list($options_array, $request = null)
{
	unset($exclude);
	global $entertain_types;
	global $entertain_lists;
	global $entertain_categories;
//preint_r($options_array);
//exit;
	if(isset($request['page']) && $request['page'] > 1)
	{
		foreach($options_array as $key => $set)
		{
			if(isset($set['fetch']['limit']))
			{
				$options_array[$key]['fetch']['limit_offset'] = $set['fetch']['limit'] * ($request['page'] - 1);
			}
		}
	}
	foreach($options_array as $options)
	{
		$options['fetch']['exclude'] = $exclude;
		$fetch_result = entertain_fetch($options['fetch']);
		$items = $fetch_result['items'];
		foreach($items as $item)
		{
			$exclude[] = $item['id'];
		}
		if($options['list']['use_page_navigation'] && $fetch_result['item_total_count'] > $options['fetch']['limit'])
		{
			$output .= entertain_page_navigation_draw($fetch_result['item_total_count'],
											$request['page'],
											'/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . 
											(isset($request['list']) ? 
												$entertain_lists[$request['list']]['url_handle'] . '/' :
												(isset($request['category_id']) ? 
													$entertain_categories[$request['category_id']]['handle'] . '/' :
														 '')),
											$options['fetch']['limit']);
		}
		$output .= entertain_list($items, $options['list']);
	}
	return $output;
}

function entertain_page_navigation_draw($num_of_items, $page, $url, $items_per_page)
{
	if(!isset($page))
	{
		$page = 1;
	}
	if(is_array($num_of_items))
	{
		$num_of_items = count($num_of_items);
	}
	else
	{
		$pages = ceil($num_of_items/$items_per_page);
	}
	$output .= '<ol class="entertain_page_list">' . "\n";
	for($i = 1; $i <= $pages; $i++)
	{
		$output .= '<li>';
		if($i == $page)
		{
			$output .= '<strong> ' . $i . ' </strong>';
		}
		else
		{
			$output .= '<a href="' . $url . 'sida_' . $i . '.html">' . $i . '</a>';
		}
	 	$output .= '</li>' . "\n";
	}
	$output .= '</ol>' . "\n";
	return $output;
}


/**
 * List entertain items in ordinary list and compact list
 * $items array of entertain items with all necessary data included
 * 		(id, title, handle, rank_avarage, votes, comments)
 */
function entertain_list($items, $options)
{
	// Ok, lets fix some options
	if($options['list_style'] == 'titles' && count($items) < 40)
	{
		$options['list_style'] = 'thumbnails';
	}

//preint_r($options);
//	$output .= '<p>list_style: ' . $options['list_style'] . '</p>';
	$options['list_style'] = isset($options['list_style']) ? $options['list_style'] : 'thumbnails';
	global $entertain_categories;
	global $entertain_list_styles;
	global $entertain_types;
//	if(isset($options['
	$output .= '<div class="entertain_list">' . "\n";
	if(is_string($options['headline']))
	{
		$output .= '<h2>' . $options['headline'] . '</h2>' . "\n";
	}
	$count = 0;
		switch($options['list_style'])
		{
			case 'commented':			
			case 'full':
				foreach($items as $item)
				{
					$output .= rounded_corners_top(array('return' => true));
					$output .= '<div class="entertain_full" id="entertain_full_' . $item['handle'] . '" >' . "\n";
					if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
					{
						$output .= '<div style="background: url(\'' . IMAGE_URL . 'entertain/' . $item['handle'] . '.png\') 5px 5px no-repeat;" class="entertain_full_thumb">' . "\n";
						$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $item['handle'] . '.html">' . "\n";
						$output .= '<img src="http://images.hamsterpaj.net/game_thumb_passepartout.png" />';
						$output .= '</a>' . "\n";
						$output .= '</div>';
					}
					else
					{
						$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $item['handle'] . '.html">' . "\n";
						$output .= '<img alt="' . $item['title'] . '" class="entertain_full_thumb" src="' . IMAGE_URL . 'entertain/' . $item['handle'] . '.png" />' . "\n";
						$output .= '</a>' . "\n";
					}
					$output .= '<div class="title_and_description">' . "\n";
					$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $item['handle'] . '.html"><h1>' . $item['title'] . '</h1></a>' . "\n";
					$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $item['handle'] . '.html">' . "\n";
					$output .= '<p>' . $item['description'] . '</p>' . "\n";
					$output .= '</a>' . "\n";
					$output .= '</div>' . "\n"; // end info

					$output .= '<div class="statistics">' . "\n";
					$output .= '<div class="column_1">' . cute_number($item['played']) . '</div><a href="/spel/topplistan/mest_spelade/">Spelningar</a>' . "\n";
					$output .= '<div class="column_1">Släpptes</div>' . date('Y-m-d', $item['release']) . "\n";
					if(is_privilegied('entertain_admin'))
					{
						$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/admin/' . $item['handle'] . '.php" >Redigera</a>' . "\n";
					}
					$output .= '</div>' . "\n"; // end statistics
					$output .= '</div>' . "\n"; // end entertain_full
					$output .= '<br style="clear: both;">' . "\n";
					$output .= rounded_corners_bottom(array('return' => true));
				}
			break;
			case 'half':
			case 'thumbnails':
				foreach($items as $item)
				{
					$output .= entertain_thumbnail_draw($item);
				}
			break;
			case 'titles':
				$letter = 'A';
				$count = count($items);
				$border[0] = floor($count / 3);
				$border[1] = 2 * $border[0];
				$border[2] = $count;
				$column = 0;
				$output .= '<ul class="entertain_list_titles">' . "\n";
				$output .= '<h2>' . $letter . '</h2>' . "\n";
				$output .= '<div class="list_slot">' . "\n";
				$i = 0;
				$slot = 0;
				foreach($items as $item)
				{
					if($letter != strtoupper(substr($item['title'], 0, 1)))
					{
						$output .= '</div>' . "\n";
						$letter = strtoupper(substr($item['title'], 0, 1));
						if($i > $border[$column])
						{
							$column++;
	//						$output .= '<br style="clear: both;">' . "\n";
							$output .= '</ul>' . "\n";
	//						$output .= '<h1>Kolumn: ' . $column . '</h1>';
							$output .= '<ul class="entertain_list_titles">' . "\n";
						}
						$output .= '<h2>' . $letter . '</h2>' . "\n";
						$output .= '<div class="list_slot">' . "\n";
						$slot = 2;
					}
					elseif($slot > 6)
					{
						$output .= '</div>' . "\n";
//						$output .= '<div class="header_dummy"></div>' . "\n";
						$output .= '<div class="list_slot">' . "\n";
						$slot = 0;
					}
					$output .= '<li>' . "\n";
					$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $entertain_categories[$item['category_id']]['handle'] . '/' . $item['handle'] . '.html">' . "\n";
					$output .= /*$slot . ' ' .*/ $item['title'] . "\n";
					$output .= '</a>' . "\n";
					$output .= '</li>' . "\n";
					$i++;
					$slot++;
				}
				$output .= '</div' . "\n";
				$output .= '</ul>' . "\n";
			break;
		}
	$output .= '<br style="clear: both;">' . "\n";
	$output .= '</div>' . "\n"; // end entertain_list
	return $output;
}

function entertain_thumbnail_draw($item)
{
	global $entertain_categories;
	global $entertain_types;
	$output .= '<div id="entertain_thumbnail_' . $item['handle'] . '" class="entertain_thumbnail">' . "\n";
	if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
	{
		$output .= '<div style="background: url(\'' . IMAGE_URL . 'entertain/' . $item['handle'] . '.png\') 5px 5px no-repeat;">' . "\n";
		$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $entertain_categories[$item['category_id']]['handle'] . '/' . $item['handle'] . '.html">' . "\n";
		$output .= '<img src="http://images.hamsterpaj.net/game_thumb_passepartout.png" />';
		$output .= '</a>' . "\n";
		$output .= '</div>';
	}
	else
	{
		$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $entertain_categories[$item['category_id']]['handle'] . '/' . $item['handle'] . '.html">' . "\n";
		$output .= '<img alt="' . $item['title'] . '" src="' . IMAGE_URL . 'entertain/' . $item['handle'] . '.png" class="ie_thumbnail_120_90" />' . "\n";
		$output .= '</a>' . "\n";
	}
	$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $entertain_categories[$item['category_id']]['handle'] . '/' . $item['handle'] . '.html">' . "\n";
	$output .= '<h5>' . $item['title'] . '</h5></a>' . "\n";
	$output .= '</div>' . "\n"; // end game_compact
	return $output;
}

/**
 * Fetch entertain items based on filter options in $options
	options			array_support	description
	id					yes				only include these ids
	title				yes				only include these titles
	handle				yes				only include these titles
	type				yes				only include items of these types
	category			no				id for category to include
	order				no				column to order by
	order_direction		no				('ASC' or 'DESC')
	release_after		no				only items released after this date (unix timestamp)
	release_before		no				only items released before this date
	limit_offset		no				
	limit				no
 */
function entertain_fetch($options)
{
	$hash = md5(serialize($options));
	if($options['cacheable'] == true && cache_last_update('entertain_' . $hash) > time() - 60)
	{
		return cache_load('entertain_' . $hash);
	}

	if(!isset($options['id']) && !isset($options['handle']))
	{
		$options['released'] = true;
	}
	
	$query = 'SELECT e.id, entertain_type, title, handle, view_count, html, `release`, category_id, use_special_code, e.uploader' .
				' , extension, trailer_id, link, ir.average as rank_average, ir.count as rank_count, ir.comment_count as comment_count, e.description, controls' .
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
	$query .= ' entertain_items AS e LEFT OUTER JOIN item_ranks AS ir ON (e.id = ir.item_id AND ir.item_type = "entertain")';
	$query .= ' WHERE 1';
//	$query .= ($options['order'] == 'rank_average') ? ' AND ir.count > 10' : '';
	if(isset($options['order']))
	{
		switch($options['order'])
		{
			case 'user_view_count':
			case 'user_view_date':
				$query .= ' AND uv.type = "entertain" AND e.id = uv.item_id AND uv.user_id ="' . $options['user_id'] . '"';
			break;
			case 'user_rank':
				$query .= ' AND e.id = ur.item_id AND ur.item_type="entertain" AND ur.user_id="' . $options['user_id'] . '"';
			break;
		}
	}
	if(isset($options['search_string_title']))
	{
		$query .= ' AND e.title LIKE "%' . $options['search_string_title'] . '%" OR title SOUNDS LIKE "' . $options['search_string_title'] . '"';
	}
	elseif(isset($options['search_string_description']))
	{
		$query .= ' AND MATCH (e.description) AGAINST ("' . $options['search_string_description'] . '")';
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
	if(isset($options['released']))
	{
		if($options['released'])
		{
			$query .= ' AND `release` < "' . time() . '"';
		}
	}
	if(isset($options['category']))
	{
		$query .= ' AND category_id="' . $options['category'] . '"';
	}
	if(isset($options['entertain_type']))
	{
		$query .= ' AND entertain_type="' . $options['entertain_type'] . '"';
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
	$query .= (isset($options['min_rank'])) ? ' AND ir.average > ' . $options['min_rank'] : '';
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
		$query_no_limit = $query;
		$result = mysql_query($query_no_limit) or die(report_sql_error($query, __FILE__, __LINE__));
		$return['item_total_count'] = mysql_num_rows($result);
	}
	if(isset($options['limit']))
	{
		$query .= ' LIMIT ' . (isset($options['limit_offset']) ? $options['limit_offset'] . ', ' : '') . $options['limit'];
	}

	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	global $entertain_types;
	while($data = mysql_fetch_assoc($result))
	{
		$data['description'] = nl2br(html_entity_decode($data['description']));
		$return['items'][$data['handle']] = $data;
		$return['items'][$data['handle']]['controls'] = unserialize($data['controls']);

		$return['items'][$data['handle']]['url'] =' /' . $entertain_types[$data['entertain_type']]['url_handle'] . '/' . $data['handle'] . '.html';
	}
	if($options['cacheable'] == true)
	{
		cache_save('entertain_' . $hash, $return);
	}
	return $return;
}

function entertain_item_draw($item, $options)
{
	global $entertain_types;

	entertain_item_viewed($item['id'], $item['entertain_type'], $item['view_count']);
	
	$output .= '<span class="' . $item['entertain_type'] . '">' . "\n";
	$output .= '<div class="entertain_item">' . "\n";
	
	// Item title
	$output .= '<h1 class="entertain_header">' . $item['title'] . '</h1>' . "\n";

	/* The entertainment items are displayed in different ways for different types.
		Games have full width while movie clips, flash films and images are displayed
		in 3/4 width, whith a few thumbnails on the side.
	*/
	switch($item['entertain_type'])
	{
		case 'software':
		case 'clip':
		case 'flash':
		case 'image':
			$fetch_result = entertain_fetch(array('released' => true,
													'entertain_type' => $item['entertain_type'],
													'exclude' => array($item['id']),
													'limit' => 3,
													'order' => 'random'));
			$related_items = $fetch_result['items'];
			$output .= entertain_list($related_items, array('list_style' => 'thumbnails', 'headline' => 'Fler ' . $entertain_types[$item['entertain_type']]['label_plural']));
		break;
		case 'game':
		break;
	}

	// Item rank, counter and release date
	$output .= '<div class="entertain_statistics">' . "\n";
	$output .= '<div class="entertain_rank">' . "\n";
	$output .= rank_draw($item['rank_average'], array('size' => 'medium'));
	$output .= '</div>' . "\n"; // end entertain_rank
	$output .= '<div class="entertain_view_count">' . "\n";
	$output .= cute_number($item['view_count'] + 1) . ' visningar sedan ' . "\n";
	$output .= date('Y-m-d', $item['release']); //fix_time($item['release']);
	$output .= '</div>' . "\n"; // end entertain_date
	$output .= '</div>' . "\n"; // end entertain_statistics

	$output .= '<div class="entertain_player" id="entertain_player">' . "\n";

	/* Now we shall display the entertainment item itself. This is done in different ways
		for different types and also different depending on special properties like
		use_special_code or trailer_id.
	*/
	
	if($_SESSION['login']['id'] == 87926)
	{
		preint_r($item);
	}
	
	//First, we consider a bunch of special cases, that is film trailers, items with their own special html, images and software links
	if($item['trailer_id'] > 0)
	{
		$output .= '<!-- Play Networks - Embeddable Flash Player -->' . "\n";
		$output .= '<div id="playnw" class="playnw">' . "\n";
		$output .= '<script src="http://se.player.playnetworks.net/player.php?mid=' . $item['trailer_id'] . '&channel_user_id=4601100020-1&width=474&height=355"></script><br>' . "\n";
		$output .= '</div>' . "\n";
		$output .= '<!-- Play Networks - Embeddable Flash Player -->' . "\n";
	}
	elseif($item['use_special_code'] == 1)
	{
		$output .= stripslashes($item['html']);
	}
	elseif(strlen($item['link']) > 2 && $item['entertain_type'] == 'image')
	{
		$server = distribute_server_get(array('item_handle' => $item['handle'], 'type' => $item['entertain_type']));
		$address = 'http://' . $server['address'] . '/distribute/' . $item['entertain_type'] . '/' . $item['handle'] . '.' . $item['extension'];
		$output .= '<a href="' . $item['link'] . '"><img src="' . $address . '" class="entertain_' . $item['entertain_type'] . '" /></a>' . "\n";
	}
	elseif($item['entertain_type'] == 'software')
	{
		$output .= '<button onclick="window.location=\'' . $item['link'] . '\';" class="button_150">Ladda ner</button><br />' . "\n";
		$output .= '<p>Alla filer är givetvis gratis att ladda ner och innehåller inga virus!</p>' . "\n";
	}
	else
	{
		$server = distribute_server_get(array('item_handle' => $item['handle'], 'type' => $item['entertain_type']));
		$address = 'http://' . $server['address'] . '/distribute/' . $item['entertain_type'] . '/' . $item['handle'] . '.' . $item['extension'];
		
		$ads = array(array('source' => '/entertain/ad_1.flv', 'track' => 'http://track.adform.net/adfserve/?bn=121081;1x1inv=1;srctype=3;ord=' . time()), array('source' => '/entertain/ad_2.flv', 'track' => 'http://track.adform.net/adfserve/?bn=121082;1x1inv=1;srctype=3;ord=' . time()), array('source' => '/entertain/ad_3.flv', 'track' => 'http://track.adform.net/adfserve/?bn=121083;1x1inv=1;srctype=3;ord=' . time()), array('source' => '/entertain/ad_4.flv', 'track' => 'http://track.adform.net/adfserve/?bn=121080;1x1inv=1;srctype=3;ord=' . time()));
		shuffle($ads);
		$current_ad = $ads[0];
		
		switch($item['extension'])
		{
			case 'swf':
				$wrapper = ($item['entertain_type'] == 'clip' || $item['entertain_type'] == 'flash') ? '/entertain/jc_flash.swf?ad_source=' . $current_ad['source'] . '&send_me_to=http%3A%2F%2Fwww.jc-online.com%2Fj-store%2Findex.html%23language%3DSV%26pageId%3D280&swf=' : '';
				$output .= '<object type="application/x-shockwave-flash" data="' . $wrapper . $address . '" >
							<param name="movie" value="' . $wrapper . $address . '" /></object>';
				$output .= ($item['entertain_type'] == 'clip' || $item['entertain_type'] == 'flash') ? '<img src="' . $current_ad['track'] . '" border="0" width="1" height="1">' : '';
			break;
			case 'flv':
				$player_url = true ? '/entertain/jc_film.swf' : '/entertain/flvplayer.swf';
				
				// On end of J-store campaign, set width to 456 and height to 355 on both places
				
				$output .= '<div id="player1"><a href="http://www.macromedia.com/go/getflashplayer">Installera Flash Player</a> för att kunna se den här grejen.</div>
						<script type="text/javascript">
						var s1 = new SWFObject("' . $player_url . '","single","466","336","7");
						s1.addParam("allowfullscreen","true");
						s1.addVariable("file","' . $address . '");
						s1.addVariable("image","' . IMAGE_URL . '/entertain/' . $item['handle'] . '.png");
						s1.addVariable("width","466");
						s1.addVariable("height","336");
						s1.addVariable("send_me_to","http%3A%2F%2Fwww.jc-online.com%2Fj-store%2Findex.html%23language%3DSV%26pageId%3D280");
						s1.addVariable("ad_source","' . $current_ad['source'] . '");
						s1.write("player1");
						</script>';
				$output .= '<img src="' . $current_ad['track'] . '" border="0" width="1" height="1">';
			break;
			case 'jpg':
			case 'png':
			case 'gif':
				$server = distribute_server_get(array('item_handle' => $item['handle'], 'type' => $item['entertain_type']));
				$address = 'http://' . $server['address'] . '/distribute/' . $item['entertain_type'] . '/' . $item['handle'] . '.' . $item['extension'];
				$output .= '<img src="' . $address . '" class="entertain_' . $item['entertain_type'] . '" />' . "\n";
		}
	}

	$output .= '<div class="entertain_item_buttons">' . "\n";
	if($item['entertain_type'] == 'image')
	{
		$query = 'SELECT id, handle FROM entertain_items WHERE id < "' . $item['id'] . '"';
		$query .= ' AND `release` < ' . time();
		$query .= ' AND entertain_type = "' . $item['entertain_type'] . '"';
		$query .= ' ORDER BY id DESC';
		$query .= ' LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if($data = mysql_fetch_assoc($result))
		{
			$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $data['handle'] . '.html">';
			$output .= '<button id="entertain_previous" class="button_90">';
			$output .= 'Föregående';
			$output .= '</button></a>' . "\n";
		}
		$query = 'SELECT id, handle FROM entertain_items WHERE id > "' . $item['id'] . '"';
		$query .= ' AND `release` < ' . time();
		$query .= ' AND entertain_type = "' . $item['entertain_type'] . '"';
		$query .= ' ORDER BY id ASC';
		$query .= ' LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if($data = mysql_fetch_assoc($result))
		{
			$output .= '<a href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $data['handle'] . '.html">';
			$output .= '<button id="entertain_next" class="button_90">';
			$output .= 'Nästa';
			$output .= '</button></a>' . "\n";
		}
	}

	if($item['extension'] == 'swf')
	{
		$output .= '<button id="entertain_fullscreen" onclick="javascript: open_fullscreen_window(\'' . $address . '\');">';
		$output .= 'Spela i fullskärm';
		$output .= '</button>' . "\n";
	}
	$output .= '</div>' . "\n"; // end entertain_item_buttons
	$output .= '</div>' . "\n"; // end entertain_player

	// Description and game controls
	$output .= '<div class="entertain_description">' . "\n";
	if(strlen($item['description']) > 0)
	{
		$output .= $item['description'] . "\n";
	}
	if($item['trailer_id'] > 0)
	{
		$output .= '<p>Filmtrailers visas i samarbete med Play Networks</p>' . "\n";
	}
	$output .= '</div>' . "\n"; // end entertain_description

	if(!is_array($item['controls']))
	{
		$item['controls'] = unserialize(utf8_decode($item['controls']));
		$utf8_mupp = true;
	}

	if(is_array($item['controls']) && count($item['controls']) > 0)
	{
		$output .= '<h2>Kontroller</h2>' . "\n";
		$output .= '<table class="entertain_controls">' . "\n";
		$num_of_rows = ceil(count($item['controls']) / 2);
		for($i = 0; $i < $num_of_rows; $i++)
		{
			$output .= '<tr>' . "\n";
			if(!$utf8_mupp)
			{
				$output .= '<td class="combination">' . $item['controls'][$i]['combination'] . '</td>' . "\n";
				$output .= '<td class="description">' . $item['controls'][$i]['description'] . '</td>' . "\n";
			}
			else
			{
				$output .= '<td class="combination">' . utf8_encode($item['controls'][$i]['combination']) . '</td>' . "\n";
				$output .= '<td class="description">' . utf8_encode($item['controls'][$i]['description']) . '</td>' . "\n";
			}
			$output .= '</tr>' . "\n";
		}
		$output .= '</table>' . "\n";
	}
	if(is_privilegied('entertain_admin'))
	{
		$output .= '<a class="entertain_edit" href="/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/admin/' . $item['handle'] . '.html">[Redigera]</a>' . "\n";
	}
	if($_SESSION['login']['id'] == 57100)
	{
		$output .= 'Eftersom att du är en sån där Ace får du se vem som laddat upp objektet: <a href="/traffa/profile.php?id=' . $item['uploader'] . '">' . $item['uploader'] . '</a>';
	}

//todo! connect with javascript
	// Comments - Users can rank and leave a comment. These are handled by separate libraries but are connected
	// by javascript so that users submit rank and comment together.
	$output .= '<div class="entertain_comments">' . "\n";
	$output .= '<input type="hidden" id="entertain_item_id" value="' . $item['id'] . '" />' . "\n";
	$output .= '<h2 class="rank_input_header">Din poäng</h2>' . "\n";
	$output .= '<h2 class="comment_input_header">Din kommentar</h2>' . "\n";
	$output .= '<br style="clear: both;" />' . "\n";

	if(login_checklogin())
	{
		$query = 'SELECT rank FROM user_ranks WHERE user_id = "' . $_SESSION['login']['id'] . '" AND item_id = "' . $item['id'] . '" AND item_type = "entertain"';
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
		}
	}
	unset($rank_options);
	$rank_options['previous'] = $data['rank'];
	$output .= rank_input_draw($item['id'], 'entertain', $rank_options);
	$output .= comments_input_draw($item['id'], 'entertain');
	$output .= '<br style="clear: both;" />' . "\n";
	$output .= '</div>' . "\n"; // end entertain_comments

	$options['comments'] = isset($options['comments']) ? $options['comments'] : 'yes';
	if($options['comments'] == 'yes')
	{
		$output .= comments_list($item['id'], 'entertain');
	}
	$output .= '</div>' . "\n"; // end entertainment_item
	$output .= '</span>' . "\n"; // end $entertain_type
	return $output;
}

function entertain_item_viewed($item_id, $type, $view_count)
{
	if(login_checklogin())
	{
		$query_update = 'UPDATE user_visits SET count = count + 1, timestamp = "' . time() . '" WHERE' .
					' user_id = ' . $_SESSION['login']['id'] . 
					' AND item_id = ' . $item_id .
					' AND type = "' . $type . '"';
		$query_insert = 'INSERT INTO user_visits (user_id, item_id, type, timestamp) VALUES' . 
							' (' . $_SESSION['login']['id'] . ', ' . $item_id . ', "entertain", "' . time() . '")';
		mysql_query($query_insert) or mysql_query($query_update) or die(report_sql_error($query_update, __FILE__, __LINE__));
	}
	if($view_count < 1000)
	{
		$query = 'UPDATE entertain_items SET view_count = view_count + 1 WHERE id = "' . $item_id . '"';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
	else
	{
		if(rand(1, 10) == 7)
		{
			$query = 'UPDATE entertain_items SET view_count = view_count + "' . rand(1, 20) . '" WHERE id = "' . $item_id . '"';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
	}
}


function entertain_searchbar_draw($request)
{
	global $entertain_types;
    $output .= '<div class="entertain_searchbar">' . "\n";
    $letters = array('0-9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Å', 'Ä', 'Ö', 'Alla');
    $output .= '<ul>' . "\n";
    foreach($letters as $letter)
    {
        $output .= '<li><a';
        if($letter == $request['initial_letter'] || ($letter >= '0' && $letter <= '9' && $request['initial_letter'] >= '0' && $request['initial_letter'] <= '9'))
        {
        	$output .= ' class="current_selection"';
        }
        $output .= ' href="/' . $entertain_types[$request['entertain_type']]['url_handle'] . '/bladdra/' . $letter . '.' . 
        		(isset($request['tag']) ? $request['tag'] : 'alla') . '/' .'" >';
        if($letter == $request['initial_letter'] || ($letter >= '0' && $letter <= '9' && $request['initial_letter'] >= '0' && $request['initial_letter'] <= '9'))
        {
	        $output .= '<strong>';
	    }
        $output .= $letter; 
        if($letter == $request['initial_letter'] || ($letter >= '0' && $letter <= '9' && $request['initial_letter'] >= '0' && $request['initial_letter'] <= '9'))
        {
	        $output .= '</strong>';
	    }
        $output .= '</a>';
        $output .= '</li>' . "\n";
    }
    $output .= '</ul>' . "\n";
    $output .= '</div>' . "\n"; //end entertain_searchbar
	return $output;
}

/**
 * Draws the form where you can add or edit an entertainment item.
 	$item	an array with all item data
 */
function entertain_admin_form_draw($entertain_type, $item = null, $preview = null, $options)
{
	global $entertain_types;
	global $entertain_categories;
	global $entertain_type_categories;
	// create a new item, start by upload och post fetch link
	if(!isset($item) && !isset($preview))
	{
		$mode = 'upload_new';
	}
	// user has uploaded or posted link by which a file has been fetched
	// now we shall display a preview
	elseif(!isset($item) && isset($preview))
	{
		$mode = 'preview_new';
	}
	// a new file has been posted (or fetched from posted link) to an existing item
	elseif(isset($item) && isset($preview))
	{
		$mode = 'preview_replace';
	}
	// An item i opened for editing (there will be a button for uploading a new file)
	else
	{
		$mode = 'edit';
	}
//$output .= '<p>mode: ' . $mode . '</p>';
	$entertain_type = isset($item) ? $item['entertain_type'] : $entertain_type;
	
	$output .= '<span class="admin">' . "\n";
	if(isset($item))
	{
		$output .= '<h1>Redigera ' . $entertain_types[$entertain_type]['label'] . '</h1>' . "\n";
	}
	else
	{
		$output .= '<h1>Lägg in ' . $entertain_types[$entertain_type]['label'] . '</h1>' . "\n";
	}
	
	$output .= '<p>Eftersom Henrik har fulkodat den här skiten är det <strong>jätteviktigt</strong> att du fyller i både namn och laddar upp en bild!</p>' . "\n";

	if($mode == 'edit')
	{
		$output .= '<button id="entertain_upload_form_show_button" class="button_150">Ladda upp ny fil</button>' . "\n";
	}
//	preint_r($_SESSION['new_entertain_temp']);
	// Upload form
	$output .= '<div id="entertain_upload_form" ' . ($mode != 'upload_new' ? ' style="display: none;"' : '') . '>' . "\n";
	$output .= '<h2>Ladda upp en fil:</h2>' . "\n";
	$output .= '<form id="entertain_upload_form" enctype="multipart/form-data" method="post" action="/' . $entertain_types[$entertain_type]['url_handle'] . '/admin/' . (isset($item) ? $item['handle'] . '.html' : '') . '">' . "\n";
	$output .= '<input type="hidden" name="action"';
	if($mode == 'upload_new')
	{
		$output .= ' value="upload_new"';
	}
	else
	{
		$output .= ' value="upload_replace"';
	}
	$output .= '>' . "\n";
	$output .= '<input type="file" name="entertain_upload" />' . "\n";
	$output .= '<h2>eller klistra in länk till filen:</h2>' . "\n";
	$output .= '<input type="text" name="fetch_link" id="entertain_fetch_link_input"/>' . "\n";
	$output .= '<input type="submit" class="button_80" value="Skicka" />' . "\n";
	$output .= '</form>' . "\n";
	$output .= '</div>' . "\n";

	if($mode == 'preview_new' || $mode == 'preview_replace')
	{
		$output .= '<div id="entertain_preview" >' . "\n";
		$output .= $preview;
		$output .= '</div>' . "\n";
		$output .= '<div id="entertain_preview_buttons">' . "\n";
		$output .= '<h2>Kan du se filmen/spelet/bilden?</h2>' . "\n";
		$output .= '<button class="button_150" id="entertain_preview_ok_button">Ja, gå vidare</button>' . "\n";
		$output .= '<button class="button_150" id="entertain_preview_retry_button">Nej, försök igen</button>' . "\n";
		$output .= '</div>' . "\n";
	}

	// Edit form
	$output .= '<form id="entertain_edit_form" ' . ($mode != 'edit' ? ' style="display: none;"' : '') . ' method="post" action="/' . 
			$entertain_types[$entertain_type]['url_handle'] . '/admin/';
	switch($mode)
	{
		case 'preview_replace':
			$output .= 'item_save_and_update_file';
		break;
		case 'preview_new':
			$output .= 'item_save_new';
		break;
		case 'edit':
			$output .= 'item_save';
		break;
	}
	$output .= '.php" enctype="multipart/form-data">' . "\n";
	if(isset($item))
	{
		$output .= '<input name="item_id" type="hidden" value="' . $item['id'] . '" />' . "\n";
	}
	$output .= '<dl>' . "\n";
	// Title
	$output .= '<dt>Titel</dt>' . "\n";
	$output .= '<dd>' . "\n";
	$output .= '<input type="text" name="title" value="' . (isset($item) ? $item['title'] : '') . '" />' . "\n";
	$output .= '</dd>' . "\n";
	// Category
	
	foreach($entertain_type_categories[$entertain_type] as $id )
	{
		$temp_category_ids[$entertain_categories[$id]['handle']] = $id;
	}
	ksort($temp_category_ids);

	$output .= '<dt>Kategori</dt>' . "\n";
	$output .= '<dd>' . "\n";
	$output .= '<select name="entertain_category">' . "\n";

	foreach($temp_category_ids as $category_id)
	{
		$output .= '<option value="' . $category_id . '"';
		if(isset($item) && $item['category_id'] == $category_id)
		{
			$output .= ' selected="selected"';
		}
		$output .= ' >' . $entertain_categories[$category_id]['title'] . '</option>' . "\n";
	}
	$output .= '</select>' . "\n";
	$output .= '</dd>' . "\n";
	// Keywords (tags)
/*	These are hidden for now, waiting for a decision on using tags (or not)
	$output .= '<dt>Övriga nyckelord (separerade med mellanslag)</dt>' . "\n";
	$output .= '<dd>' . "\n";
	if(isset($item))
	{
		unset($keywords);
		$tags = tag_get_by_item('entertain', $item['id']);
		foreach($tags as $tag)
		{
			$keywords[] = $tag['label'];
		}
	}
	$output .= '<textarea name="tags" rows="3" cols="60" >' . (isset($item) ? implode(' ', $keywords) : '') . '</textarea>' . "\n";
	$output .= '</dd>' . "\n";
*/
	// Image - used for thumbnail image, image types create thumbnails from the object itself
	if(!in_array($entertain_type, array('image', 'background', 'software')))
	{
		$output .= '<dt>Bild (skalas och konverteras automatiskt) <strong>OBS! Du måste ladda upp en bild!</strong></dt>' . "\n";
		$output .= '<dd>' . "\n";
		if(isset($item))
		{
			$output .= '<img src="' . IMAGE_URL . 'entertain/' . $item['handle'] . '.png" />' . "\n";
		}
		$output .= '<input name="thumbnail" type="file" />' . "\n";
		$output .= '</dd>' . "\n";
	}
	if(in_array($entertain_typ, array('software')))
	{
		$output .= '<dt>Länk</dt>' . "\n";
		$output .= '<dd>' . "\n";
		$value = isset($item['link']) ? '' : '';
		$output .= '<input type="text" name="link" value="' . $value . '"/>' . "\n";
		$output .= '</dd>' . "\n";
	}
	// Release
	$output .= '<dt>Release</dt>' . "\n";
	$output .= '<dd>' . "\n";
	$output .= '<input type="text" name="release" value="' . date('Y-m-d H:i', (isset($item) ? $item['release'] : schedule_release_get(array('type' => 'new_' . $entertain_type)))) . '" />' . "\n";
	$output .= '<input type="checkbox" name="release_now" value="true" id="release_now_check" />' . "\n";
	$output .= '<label for="release_now_check">Släpp direkt</label>' . "\n";
	$output .= '</dd>' . "\n";
	// Special code
	$output .= '<dt>Specialkod</dt>' . "\n";
	$output .= '<dd>' . "\n";
	$output .= '<input type="checkbox", value="true", id="chk_use_special_code" name="use_special_code" ' . (isset($item) && $item['use_special_code'] == 1 ? 'checked=checked' : '') . '/>' . "\n";
	$output .= '<label for="chk_use_special_code">Använd specialkod</label>' . "\n";
	$output .= '<br />' . "\n";
	$output .= '<textarea name="special_code" rows="5" cols="60" >' . (isset($item) ? stripslashes($item['html']) : '') . '</textarea>' . "\n";
	$output .= '</dd>' . "\n";
	// Trailer id
	if($entertain_type == 'clip')
	{
		$output .= '<dt>Filmtrailer.se ID (rör ej om du inte vet vad detta är!)</dt>' . "\n";
		$output .= '<dd>' . "\n";
		$output .= '<input type="text" name="trailer_id" value="' . (isset($item['trailer_id']) ? $item['trailer_id'] : '') . '" />' . "\n";
		$output .= '</dd>' . "\n";
	}
	if($entertain_type == 'software')
	{
		$output .= '<dt>Länk till webbsida</dt>' . "\n";
		$output .= '<dd>' . "\n";
		$output .= '<input type="text" name="link" value="' . $item['link'] . '" />' . "\n";
		$output .= '</dd>' . "\n";		
	}
	// Description
	$output .= '<dt>Beskrivning</dt>' . "\n";
	$output .= '<dd>' . "\n";
	$output .= '<textarea name="description" rows="6" cols="60" >' . (isset($item) ? $item['description'] : '') . '</textarea>' . "\n";
	$output .= '</dd>' . "\n";
	// Game controls
	if($entertain_type == 'game')
	{
		$output .= '<dt>Kontroller</dt>' . "\n";
		$output .= '<dd>' . "\n";
		$output .= '<table>' . "\n";
		for($i = 0; $i < 8; $i++)
		{
			$output .= '<tr>' . "\n";
			$output .= '<td><input type="text" name="key_' . $i . '" value="' . (is_array($item['controls']) ? $item['controls'][$i]['combination'] : '') . '"></td>';
			$output .= '<td><input type="text" name="action_' . $i . '" value="' . (is_array($item['controls']) ? $item['controls'][$i]['description'] : '') . '"></td>';
			$output .= '</tr>' . "\n";
		}
		$output .= '</table>' . "\n";
		$output .= '</dd>' . "\n";
		$output .= '<dt>Highscore gname (låt bli om du inte vet vad detta är!)</dt>' . "\n";
		$output .= '<dd>' . "\n";
		$output .= '<input type="text" name="highscore_gname" value="' . $item['highscore_gname'] . '" /><br />' . "\n";
		$output .= '</dd>' . "\n";
	}
	// Delete item
	if(isset($item))
	{
		$output .= '<dt>Redera</dt>' . "\n";
		$output .= '<dd>' . "\n";
		$output .= '<input type="checkbox", value="delete" id="chk_entertain_delete" name="delete" />' . "\n";
		$output .= '<label for="chk_item_delete">Ta bort ' . $entertain_types[$entertain_type]['label'] . '</label>' . "\n";
		$output .= '</dd>' . "\n";
	}
	$output .= '</dl>' . "\n";
	$output .= '<input class="button_50" type="submit" value="Spara" />' . "\n";

	$output .= '</form>' . "\n";
	$output .= '</span>' . "\n";
	return $output;
}

function entertain_item_delete($item_id)
{
	$query = 'DELETE FROM entertain_items WHERE id="' . $item_id . '"';
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
}

/**
	This functions takes a web-link from the user (administrator of the entertain system) and
	fetches the file from where ever it is located.
	The function returns html code for displaying the temporary file so that it can be previed.
	The temporary file is stored in ENTERTAIN_TEMP_PATH and it can be viewed at ENTERTAIN_TEMP_URL
 */
function entertain_item_fetch($fetch_link)
{
	$hash = md5(rand());
	preg_match('/\.(\w+)$/', $fetch_link, $matches);
	$extension = strtolower($matches[1]);
	$_SESSION['new_entertain_temp']['hash'] = $hash;
	$_SESSION['new_entertain_temp']['extension'] = $extension;
	$command = 'wget -O ' . ENTERTAIN_TEMP_PATH . $hash . '.' . $extension . ' ' . $fetch_link;
	exec($command, $output, $return_value);
	system('convert ' .  ENTERTAIN_TEMP_PATH . $hash . '.' . $extension . ' -resize 474x355 ' .  ENTERTAIN_TEMP_PATH . $hash . '.' . $extension);	
	return($return_value == 0);
}


/**
	This function is used when an administrator (or who ever is allowed to add items to the entertain system)
	wants to upload a local file from his/hers computer.
	@return The return data is a preview of the item in html
 */
function entertain_item_upload()
{
	/* Resize, convert and save the uploaded thumbnail */
	// This is probably not a thumbnail, but any type of file. Which means that swf-files are put thru convert...
	if(strlen($_FILES['entertain_upload']['tmp_name']) > 1)
	{
		$hash = md5(rand());
		preg_match('/\.(\w+)$/', $_FILES['entertain_upload']['name'], $matches);
		$extension = $matches[1];
		$_SESSION['new_entertain_temp']['hash'] = $hash;
		$_SESSION['new_entertain_temp']['extension'] = $extension;
		move_uploaded_file($_FILES['entertain_upload']['tmp_name'], ENTERTAIN_TEMP_PATH . $hash . '.' . strtolower($extension));
		system('convert ' .  ENTERTAIN_TEMP_PATH . $hash . '.' . strtolower($extension) . 
				' -resize 474x355 ' .  ENTERTAIN_TEMP_PATH . $hash . '.' . strtolower($extension));
		return true;
	}
	else
	{
		return false;
	}
}

function entertain_item_preview_draw()
{
	$hash = $_SESSION['new_entertain_temp']['hash'];
	$extension = strtolower($_SESSION['new_entertain_temp']['extension']);
	switch($extension)
	{
		case 'swf':
			$output = '<object style="width: 460px; height: 345px;" type="application/x-shockwave-flash" data="/entertain_temp/' . $hash . '.' . $extension . '" >
					<param name="movie" value="' . ENTERTAIN_TEMP_URL . $hash . '.' . $extension . '" />
					<img src="http://images.hamsterpaj.net/logo.png" alt="Hamsterpaj logo" />
				</object>';
		break;
		case 'flv':
			$output = '<p id="player1"><a href="http://www.macromedia.com/go/getflashplayer">Installera Flash Player</a> för att kunna se den här grejen.</p>
					<script type="text/javascript">
					var s1 = new SWFObject("/entertain/flvplayer.swf","single","460","345","7");
					s1.addParam("allowfullscreen","true");
					s1.addVariable("file","' . ENTERTAIN_TEMP_URL . $hash . '.flv");
					s1.addVariable("width","460");
					s1.addVariable("height","345");
					s1.write("player1");
					</script>';
		break;
		case 'jpg':
		case 'png':
		case 'gif':
			$output = '<img src="' . ENTERTAIN_TEMP_URL . $hash . '.' . $extension . '" class="entertain_preview_image" />' . "\n";
		break;
		case 'wma':
			$output = '<p>nån slags ljudfil här....</p>';
		break;
		case 'wmv':
			$output = '<p>nån slags videofil här...</p>';
		break;
	}
	return $output;
}
/**
	Call the distribute library to distribute the item to servers. An original i stored in /distribute on the web server.
 */
function entertain_item_distribute_new($item)
{
	$command = 'mv ' . ENTERTAIN_TEMP_PATH . $_SESSION['new_entertain_temp']['hash'] . '.' . $_SESSION['new_entertain_temp']['extension'] . 
				' /storage/www/www.hamsterpaj.net/data/distribute/' . $item['entertain_type'] . '/' . $item['handle'] . '.' . $item['extension'];
	exec($command, $output, $return_value);
	distribute_item(array('type' => $item['entertain_type'], 'handle' => $item['handle'], 'extension' => $item['extension']));
}

/**
 * Saves an entertain item from POST form to database
 * options
 	new		a new item
 	update	update an existing item
 * @return handle, the items handle
 */
function entertain_item_save($options)
{
	$_SESSION['new_entertain_temp']['extension'] = strtolower($_SESSION['new_entertain_temp']['extension']);
	global $entertain_categories;
	global $entertain_types;
	global $entertain_type_categories;
	// Make handle from title
	if($options['new'])
	{
		$handle = entertain_get_handle($_POST['title']);
	}
	else
	{
		$item_id = $_POST['item_id'];
	}
	$release = isset($_POST['release_now']) ? time() : strtotime($_POST['release']);
	$entertain_type = $options['entertain_type'];
	// Make array of controls
	$controls = array();
	for($i = 0; $i < 8; $i++)
	{
		if(isset($_POST['key_' . $i]))
		{
			$controls[$i]['combination'] = $_POST['key_' . $i];
			$controls[$i]['description'] = $_POST['action_' . $i];
		}
	}
	if($options['new'])
	{
		$mode = 'new';
		$query = 'INSERT INTO entertain_items (handle, title, entertain_type, category_id, `release`, extension, use_special_code, html, trailer_id, description, controls, link, uploader)';
		$query .= ' VALUES ("' . $handle . '", "' . 
									$_POST['title'] . '", "' . 
									$entertain_type . '", "' . 
									$_POST['entertain_category'] . '", "' . 
									$release . '", "' . 
									(isset($_SESSION['new_entertain_temp']['extension']) ? $_SESSION['new_entertain_temp']['extension'] : '') . '", "' . 
									(isset($_POST['use_special_code']) ? '1' : '0') . '", "' . 
									addslashes(html_entity_decode($_POST['special_code'])) . '", "' .
									$_POST['trailer_id'] . '", "' .
									$_POST['description'] . '", "' . 
									mysql_real_escape_string(serialize($controls)) . '", "' .	
									(isset($_POST['link']) ? $_POST['link'] : '') . '", ' .
									(isset($_SESSION['login']['id']) ? $_SESSION['login']['id'] : 0) . ')'; //error (one line up?)
	}
	elseif($options['update'])
	{
		$mode = 'update';
		$query = 'UPDATE entertain_items SET title = "' . $_POST['title'] . '"';
		$query .= ', entertain_type = "' . $entertain_type . '"';
		$query .= ', `release` = "' . $release . '"';
		$query .= ', trailer_id = "' . $_POST['trailer_id'] . '"';
		$query .= ', category_id = "' . $_POST['entertain_category'] . '"';
		$query .= $options['update_file'] ? ', extension = "' .  $_SESSION['new_entertain_temp']['extension'] . '"' : '';
		$query .= ', use_special_code = "' . (isset($_POST['use_special_code']) ? '1' : '0') . '"';
		$query .= isset($_POST['use_special_code']) ? ', html="' . addslashes(html_entity_decode($_POST['special_code'])) . '"' : '';
		$query .= ', description = "' . $_POST['description'] . '"';
		$query .= count($controls) > 0 ? ', controls = "' . mysql_real_escape_string(serialize($controls)) . '"' : '';
		$query .= isset($_POST['link']) ? ', link = "' . $_POST['link'] . '"' : '';
		$query .= ' WHERE id = "' . $item_id . '"';
	}
		
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if($mode == 'new')
	{
		$item_id = mysql_insert_id();
	}
	$query = 'SELECT * FROM entertain_items WHERE id = "' . $item_id . '"';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if($data = mysql_fetch_assoc($result))
	{
		$item = $data;
		$handle = $data['handle'];
	}
	else
	{
		return false;
	}

	if($mode == 'new')
	{
		$schedule['item_id'] = $item_id;
		$schedule['type'] = 'new_' . $entertain_type;
		$_POST['url'] = '/' . $entertain_types[$entertain_type]['url_handle'] . '/' . $handle . '.html';
		$schedule['data'] = serialize($_POST);
		$schedule['release'] = $release;
		schedule_event_add($schedule);
	}
	unset($save);
	$save['item_id'] = $item_id;
	$save['object_type'] = $entertain_type;
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
		$command = 'convert ' . $_FILES['thumbnail']['tmp_name'] . ' -resize 120!x90! ' . IMAGE_PATH . 'entertain/' . $handle . '.png';
		system($command, $return_var);
	}
	//This is done instead of calling the distribute library as is done for clips, flash films and games.
	if($entertain_type == 'image' || $entertain_type == 'software')
	{
		if(!isset($options['update']) || ($options['update'] && $options['update_file']))
		{
			$command = 'convert ' . ENTERTAIN_TEMP_PATH . $_SESSION['new_entertain_temp']['hash'] . '.' . $_SESSION['new_entertain_temp']['extension'] . 
					' -resize 120!x90! ' . IMAGE_PATH . 'entertain/' . $handle . '.png';
			system('convert ' . ENTERTAIN_TEMP_PATH . $_SESSION['new_entertain_temp']['hash'] . '.' . $_SESSION['new_entertain_temp']['extension'] . 
					' -resize 120!x90! ' . IMAGE_PATH . 'entertain/' . $handle . '.png');
//		system('convert ' . ENTERTAIN_TEMP_PATH . $_SESSION['new_entertain_temp']['hash'] . '.' . $_SESSION['new_entertain_temp']['extension'] .
//				' -resize 460x345 ' . IMAGE_PATH . 'fun_images/' . $handle . '.jpg');
		}
	}
/*
	elseif($entertain_type == 'background')
	{
		//todo! lägg till alla önskade storlekar
		system('convert ' . ENTERTAIN_TEMP_PATH . $_SESSION['new_entertain_temp']['hash'] . '.' . $_SESSION['new_entertain_temp']['extension']
				 . ' -resize 1024x768 ' . IMAGE_PATH . 'fun_images/' . $handle . '.jpg');
	}
*/
	// This is a safety output that the user will see if the redirect (done in entertain.php) does not happen.
	echo '<p>Nu är din/ditt ' . $entertain_types[$entertain_type]['label'] . ' sparad/sparat och dess handle är: ' . $handle . '</p>' . "\n";
	$item['url'] = '/' . $entertain_types[$item['entertain_type']]['url_handle'] . '/' . $item['handle'] . '.html';
	echo '<p>Direktlänken blir då <a href="' . $item['url'] . '">' . $item['url'] . '</a>' . '</p>';
	return $item;
}
