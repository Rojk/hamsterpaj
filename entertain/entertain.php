<?php
/* The output you get from executing this file depends mostly on three parameters; view, type and item.
	Both are given by the uri by which this file is called. It is basically /type[/view][/item.html].
	The type is game, clip, flash, image etc and gives the style choices (css) while view can be a number
	of different lists (top list, favorites etc) or, if item is set, a view where you can watch a clip,
	play a game, download a piece of software etc.
*/

	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'movie_compability.lib.php');
	require_once(PATHS_LIBRARIES . 'entertain.lib.php');
	require_once(PATHS_LIBRARIES . 'schedule.lib.php');
	require_once(PATHS_LIBRARIES . 'rank.lib.php');
	require_once(PATHS_LIBRARIES . 'comments.lib.php');

	define(ENTERTAINMENT_ITEMS_PER_PAGE, 24);

	$ui_options['stylesheets'][] = 'entertain.css';
	$ui_options['stylesheets'][] = 'rank.css';
	$ui_options['stylesheets'][] = 'comments.css';
	$ui_options['javascripts'][] = 'entertain.js';
	$ui_options['javascripts'][] = 'comments.js';
	$ui_options['javascripts'][] = 'rank.js';

	
global $entertain_types;
//preint_r($entertain_types);
global $entertain_categories;
//preint_r($entertain_categories);
global $entertain_type_categories;
//preint_r($entertain_type_categories);
global $entertain_lists;

// Parse the URI so we know what to deliver
$request = entertain_action_get($_SERVER['REQUEST_URI']);
if(isset($_GET['search_string']))
{
//search
	$request['view'] = 'search';
	$search_string = $_GET['search_string'];
}
elseif(count($_POST) > 0)
{
	$update_file = false;
	switch($request['command'])
	{
		case 'item_save_new':
			if(!is_privilegied('entertain_add', $request['entertain_type']))
			{
				die('Ej priviligierad för ' . $request['entertain_type']);
			}
			$item = entertain_item_save(array('entertain_type' => $request['entertain_type'], 'new' => true, 'update_file' => false));
			if(!isset($_POST['use_special_code']))
			{
				entertain_item_distribute_new($item);
			}
			header('Location: ' . $item['url']);
			unset($_SESSION['new_entertain_temp']);
			exit;
		break;
		
		case 'item_save_and_update_file':
			$update_file = true;
		case 'item_save':
			preint_r($request);
			
			if(!is_privilegied('entertain_update') && is_privilegied('entertain_delete'))
			{
				die('Ej priviligierad för ändring av det här objektet. Rad ' . __LINE__);
			}
			if($_POST['delete'] == 'delete' && is_privilegied('entertain_delete'))
			{
				entertain_item_delete($_POST['item_id']);
				global $entertain_types;
				header('Location: http://www.hamsterpaj.net/' . $entertain_types[$_POST['entertain_type']]['url_handle'] . '/');
			}
			else
			{
				if(!is_privilegied('entertain_update'))
				{
					die('Inga privilegier, på rad ' . __LINE__);
				}
				
				$item = entertain_item_save(array('entertain_type' => $request['entertain_type'], 'update' => true, 'update_file' => $update_file));
			}
			if($update_file)
			{
				entertain_item_distribute_new($item);
			}
			header('Location: ' . $item['url']);
			unset($_SESSION['new_entertain_temp']);
			exit;
		break;
		case 'search':
			$request['view'] = 'search';
			$search_string = $_POST['search_string'];
		break;
	}
}

$ui_options['menu_path'] = array($request['entertain_type']);
if(isset($page_title))
{
	$ui_options['title'] = $page_title . ' - ' . $entertain_types[$request['entertain_type']]['title'] . ' på Hamsterpaj.net';
}
else
{
	$ui_options['title'] = $entertain_types[$request['entertain_type']]['label_capitol'] . ' på Hamsterpaj.net';
}

switch($request['entertain_type'])
{
	case 'clip':
		$ui_options['adtoma_category'] = 'amuse.movies';
	break;
	
	case 'flash':
		$ui_options['adtoma_category'] = 'amuse.flash';
	break;
	
	case 'game':
		$ui_options['adtoma_category'] = 'amuse.games';
	break;
}
ui_top($ui_options);

switch($request['view'])
{
	case 'list':
		array_push($ui_options['menu_path'], $request['list']);
	break;
	case 'category_list':
		array_push($ui_options['menu_path'], 'search');
		array_push($ui_options['menu_path'], $request['category_handle']);
	break;
}
global $entertain_adtoma_categories;
$ui_options['adtoma_category'] = $entertain_adtoma_categories[$request['entertain_type']]; 

//preint_r($request);

$output .= rounded_corners_top(array('color' => 'red'));
$output .= 'Kan du inte se filmen? Prova att uppdatera Flash!' . "\n";
$output .= '<a href="http://get.adobe.com/flashplayer/">Uppdatera &raquo;</a>';
$output .= rounded_corners_bottom(array('color' => 'red'));


$output .= '<div class="entertain">' . "\n";
echo rounded_corners_top(array('color' => 'orange'));
$out .= entertain_search_field_draw($request['entertain_type']);
echo $out;
echo rounded_corners_bottom(array('color' => 'orange'));
// Based on the request we shall display different views. We have different lists, different players etc
switch($request['view'])
{
	case 'search':
		if(strlen($search_string) > 2)
		{
			$options_array = entertain_list_by_type_get($request['entertain_type'], 'search');
			$options_array[1] = $options_array[0];
			$options_array[0]['fetch']['search_string_title'] = $search_string;
			$options_array[0]['list']['headline'] = 'Träff på titel';
			$options_array[0]['list']['use_page_navigation'] = true;
			$options_array[1]['fetch']['search_string_description'] = $search_string;
			$options_array[1]['list']['headline'] = 'Träff på beskrivning';
			$options_array[1]['list']['use_page_navigation'] = true;
			$output .= entertain_fetch_and_list($options_array, $request);
		}
	break;
	case 'list':
		if($request['list'] == 'favoriter')
		{
			if(login_checklogin())
			{
				$output .= '<h1>' . $entertain_types[$request['entertain_type']] . '</h1>' . "\n";
				$options_array = entertain_list_by_type_get($request['entertain_type'], 'favorites');
				$output .= entertain_fetch_and_list($options_array);
			}
			else
			{
				$output .= '<h1>Dina favoriter</h1>' . "\n";
				$output .= '<p>Här kan du se de ' . $entertain_types[$request['entertain_type']]['label_plural'] . ' du tittat på senast, de du givit högst betyg med mera, om du är medlem. ' . "\n";
				$output .= '<a href="/register.php">Bli medlem!</a></p>' . "\n";
			}
		}
		else
		{
			$output .= '<h1>' . $entertain_types[$request['entertain_type']]['label_capitol'] . '</h1>' . "\n";
			$options_array = entertain_list_by_type_get($request['entertain_type'], $request['list']);
			if($options_array['use_searchbar'])
			{
				$request['initial_letter'] = isset($request['initial_letter']) ? $request['initial_letter'] : 'a';
				$output .= entertain_searchbar_draw($request);
			}
			$output .= entertain_fetch_and_list($options_array, $request);
		}
	break;
	case 'admin':
		unset($preview);
		if($_POST['action'] == 'upload_new' || $_POST['action'] == 'upload_replace')
		{
			if(strlen($_FILES['entertain_upload']['name']) > 0)
			{
				$success = entertain_item_upload();
			}
			elseif(count($_POST['fetch_link']) > 0)
			{
				$success = entertain_item_fetch($_POST['fetch_link']);
			}
			if($success)
			{
				$preview = entertain_item_preview_draw();
			}
			else
			{
				$preview = '<h5 style="color: red">Denna fil gick inte att hämta</h5>' . "\n";
			}
		}
		unset($item);
		if(isset($request['handle']))
		{
			$fetch_result = entertain_fetch(array('user_id' => $_SESSION['login']['id'], 'handle' => $request['handle']));
			$items = $fetch_result['items'];
			$item = array_pop($items);
		}
		// Both $item and $preview can be unset (== null) here.
		$output .= entertain_admin_form_draw($request['entertain_type'], $item, $preview);
		$options_array = array();
	break;
	case 'item':
		$fetch_result = entertain_fetch(array('user_id' => $_SESSION['login']['id'], 'handle' => $request['handle'], 'limit' => '1', 'entertain_type' => $request['entertain_type']));
		$items = $fetch_result['items'];
		$page_title = $items[$request['handle']]['title'];
		$output .= rounded_corners_top();
		$output .= entertain_item_draw($items[$request['handle']]);
		$output .= rounded_corners_bottom();
	
		$options_array[0]['fetch'] = array('entertain_type' => $request['entertain_type'], 'limit' => 8, 'order' => 'random', 'min_rank' => 3.5);
		$options_array[0]['list'] = array('headline' => 'Fler ' . $entertain_types[$request['entertain_type']]['label_plural']);
		$output .= rounded_corners_top();
		$output .= entertain_fetch_and_list($options_array);
		$output .= rounded_corners_bottom();
	break;
	case 'category_list':
		$output .= '<h1>' . $entertain_types[$request['entertain_type']]['label_capitol'] . ' - ' . $entertain_categories[$request['category_id']]['title'] . '</h1>' . "\n";
		$options_array = entertain_list_by_type_get($request['entertain_type'], 'search');
		$options_array[0]['fetch']['category'] = $request['category_id'];
		$options_array[0]['list']['headline'] = $entertain_types[$request['entertain_type']];
		$options_array[0]['list']['use_page_navigation'] = true;
		$output .= entertain_fetch_and_list($options_array, $request);
	break;
	case 'start':
	default:
		switch($request['entertain_type'])
		{
			case 'game':
				$set[0]['fetch']['entertain_type'] = $request['entertain_type'];
				$set[0]['fetch']['order'] = 'release';
				$set[0]['fetch']['order_direction'] = 'DESC';
				$set[0]['fetch']['limit'] = '8';
				$set[0]['list']['list_style'] = 'thumbnails';
				$set[0]['list']['headline'] = 'Senaste';

				$set[1]['fetch']['entertain_type'] = $request['entertain_type'];
				$set[1]['fetch']['order'] = 'rank_average';
				$set[1]['fetch']['order_direction'] = 'DESC';
				$set[1]['fetch']['limit'] = '8';
				$set[1]['list']['list_style'] = 'thumbnails';
				$set[1]['list']['headline'] = 'Högst poängsatta';

				$set[2]['fetch']['entertain_type'] = $request['entertain_type'];
				$set[2]['fetch']['order'] = 'view_count';
				$set[2]['fetch']['order_direction'] = 'DESC';
				$set[2]['fetch']['limit'] = '8';
				$set[2]['list']['list_style'] = 'thumbnails';
				$set[2]['list']['headline'] = 'Mest spelade';
				$output .= rounded_corners_top();
				$output .= entertain_fetch_and_list($set);
				$output .= rounded_corners_bottom();
			
				break;
			case 'software':
				$output .= '<h1>Program</h1>' . "\n";
				
				$set[0]['fetch']['entertain_type'] = $request['entertain_type'];
				$set[0]['fetch']['order'] = 'release';
				$set[0]['fetch']['order_direction'] = 'DESC';
				$set[0]['fetch']['limit'] = '4';
				$set[0]['list']['list_style'] = 'thumbnails';
				$set[0]['list']['headline'] = 'Senaste';

				$set[1]['fetch']['entertain_type'] = $request['entertain_type'];
				$set[1]['fetch']['order'] = 'rank_average';
				$set[1]['fetch']['order_direction'] = 'DESC';
				$set[1]['fetch']['limit'] = '4';
				$set[1]['list']['list_style'] = 'thumbnails';
				$set[1]['list']['headline'] = 'Högst poängsatta';

				$output .= rounded_corners_top();
				$output .= entertain_fetch_and_list($set);
				$output .= rounded_corners_bottom();
				break;
			default:
		//Todo Här skall vi ha något system för att definiera olika startsidor för olika innehållstyper
				//Topprankade items, 4st
				//slumpade, mest sedda bla bla bla.....
				$set[0]['fetch']['entertain_type'] = $request['entertain_type'];
				$set[0]['fetch']['order'] = 'view_count';
				$set[0]['fetch']['order_direction'] = 'DESC';
				$set[0]['fetch']['limit'] = '4';
				$set[1]['fetch']['entertain_type'] = $request['entertain_type'];
				$set[1]['fetch']['order'] = 'rank_average';
				$set[1]['fetch']['order_direction'] = 'DESC';
				$set[1]['fetch']['limit'] = '4';
				$set[2]['fetch']['entertain_type'] = $request['entertain_type'];
				$set[2]['fetch']['order'] = 'random';
				$set[2]['fetch']['limit'] = '4';
				$set[3]['fetch']['entertain_type'] = $request['entertain_type'];
				$set[3]['fetch']['order'] = 'comment_count';
				$set[3]['fetch']['order_direction'] = 'DESC';
				$set[3]['fetch']['limit'] = '4';
				$options = $set[rand(0, 3)];
				$fetch_result = entertain_fetch($options['fetch']);
				$items = $fetch_result['items'];
		//	preint_r($items);
				$list_options['list_style'] = 'thumbnails';
				$output .= rounded_corners_top();
				$output .= entertain_list($items, $list_options);
				$output .= rounded_corners_bottom();
			
				//En utvalt item med två kommentarer
				$output .= rounded_corners_top();
				$fetch['entertain_type'] = $request['entertain_type'];
				$fetch['order'] = 'random';
				$fetch['limit'] = '1';
				$fetch_result = entertain_fetch($fetch);
				$items = $fetch_result['items'];
				$item = array_pop($items);
				$output .= '<div class="entertain_item_with_comments">' . "\n";
				$output .= entertain_thumbnail_draw($item);
				$comments_options['limit'] = 2;
				$comments_options['style'] = 'no_margin';
				$output .= comments_list($item['id'], 'entertain', $comments_options);
				$output .= '<br style="clear: both;" />' . "\n";
				$output .= '</div>' . "\n";
				$output .= rounded_corners_bottom();
				
				// Visa en slumpvald item
				$fetch['entertain_type'] = $request['entertain_type'];
				$fetch['order'] = 'random';
				$fetch['limit'] = '1';
				$fetch_result = entertain_fetch($fetch);
				$items = $fetch_result['items'];
				$item = array_pop($items);
				$draw_options['comments'] = 'no';
				$output .= entertain_item_draw($item, $draw_options);
	}
}
$output .= '</div>' . "\n";


echo $output;
ui_bottom();

?>
