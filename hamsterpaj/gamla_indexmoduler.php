<?php
	require('../include/core/common.php');
	require_once(PATHS_LIBRARIES . 'entertain.lib.php');
	require_once(PATHS_LIBRARIES . 'rank.lib.php');
	require_once(PATHS_LIBRARIES . 'photos.lib.php');
	require_once(PATHS_LIBRARIES . 'fp_modules.lib.php');

	$ui_options['javascripts'][] = 'fp_common_modules.js';
	$ui_options['javascripts'][] = 'fp_modules_old_ones.js';

	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'fp_modules_old_ones.css';
	$ui_options['stylesheets'][] = 'fp_common_modules.css';
	

	$ui_options['title'] = 'Gamla startside-moduler på Hamsterpaj';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';
	
	//Get pagenumber
	$page = 1;
	if(isset($_GET['page']) && is_numeric($_GET['page']))
	{
		$page = intval($_GET['page']);
		if($page < 1 || $page > 999)
		{
			$page = 1;
		}
	}
	$offset = (($page - 1) * 13);

	$fp_modules = fp_modules_fetch(array('launch_max' => time(), 'order-by' => 'launch', 'limit' => 13, 'offset' => $offset));

	$out .= '<h1>Här kan du kika på gamla saker som varit på startsidan</h1>' . "\n";

	$out .= '<ol id="fp_module_list">' . "\n";

	$puff_no = -1;
	foreach($fp_modules AS $module)
	{
		$o = '';
		$module_hide = false;
		if($module['code_mode'] == 'php')
		{
			include(PATHS_INCLUDE . 'fp_modules/' . $module['id'] . '.php');
			if($module_hide == true)
			{
				continue;
			}
		}
		else
		{
			$o = file_get_contents(PATHS_INCLUDE . 'fp_modules/' . $module['id'] . '.php');
		}

		$class = ($module['commenting'] == 'true' || $module['published'] == 'true' || $module['grading'] == 'true') ? 'module' : 'module_noframe';
		if($module['format'] == '2_3')
		{
			$puff_no++;
			$out .= '<li class="module_2_3" id="module_' . $module['id'] . '">' . "\n";		
			$out .= '<div class="module_header" id="' . $module['id'] . '">' . "\n";
			$out .= '<strong>' . $module['name'] . '</strong>' . "\n";
			$out .= '<span>' . cute_number($module['clicks']) . ' klick</span>' . "\n";
			$out .= '<span class="module_toggle_guide">(Klicka för att visa modulen)</span>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div class="module_content" style="display:none;">' . "\n";
			$out .= '<div class="' . $class . '">' . "\n";
		}
		else
		{
			$out .= '<li class="module" id="module_' . $module['id'] . '">' . "\n";
			$out .= '<div class="module_header" id="' . $module['id'] . '">' . "\n";
			$out .= '<strong>' . $module['name'] . '</strong>' . "\n";
			$out .= '<span>' . cute_number($module['clicks']) . ' klick</span>' . "\n";
			$out .= '<span class="module_toggle_guide">(Klicka för att visa modulen)</span>' . "\n";
			$out .= '</div>' . "\n";
			$out .= '<div class="module_content" style="display:none;">' . "\n";
		}
		
		$out .= $o;

		$out .= '<br style="font-size: 0px; height: 0px; clear: both; line-height: 0px;" />' . "\n";
		
		if($module['commenting'] == 'true' || $module['published'] == 'true' || $module['grading'] == 'true')
		{
			$out .= '<div class="controls">' . "\n";
			if($module['published'] == 'true')
			{
				$out .= '<p class="fp_module_published">Upplagd ' . date('Y-m-d', $module['launch']) . '</p>' . "\n";
			}
			if($module['commenting'] == 'true')
			{
				if($module['thread_id'] == 0 && (strlen($module['name']) > 0))
				{
					$thread_options['forum_id'] = 114;
					$thread_options['title'] = $module['name'];
					$thread_options['content'] = 'Det hÃ¤r Ã¤r en kommenteringstrÃ¥d fÃ¶r modulen \\"' . $module['name'] . '\\" pÃ¥ fÃ¶rstasidan. Egentligen skall sjÃ¤lva modulen visas hÃ¤r i forumet, typ ovanfÃ¶r trÃ¥den. Men det Ã¤r vi inte klara med Ã¤n, sÃ¥ tillsvidare fÃ¥r man kommentera utan att se modulen :)';
					$thread_options['mode'] = 'new_thread';
					$thread_options['author'] = 57100;
					$module['thread_id'] = discussion_forum_post_create($thread_options);		
					
					$query = 'UPDATE fp_modules SET thread_id = "' . $module['thread_id'] . '" WHERE id = "' . $module['id'] . '"';
					mysql_query($query);		
				}
				$out .= '<a href="' . forum_get_url_by_post($module['thread_id']) . '" class="fp_moudle_commenting">Kommentera i forumet</a>' . "\n";
			
			}
			if($module['grading'] == 'true')
			{
				if(login_checklogin() && !in_array($module['id'], $_SESSION['fp_module_votes']))
				{
					$out .= '<img src="http://images.hamsterpaj.net/discussion_forum/thread_voting_plus.png" class="fp_vote" id="fp_vote_plus_' .$module['id'] . '" />' . "\n";
					$out .= '<img src="http://images.hamsterpaj.net/discussion_forum/thread_voting_minus.png" class="fp_vote" id="fp_vote_minu_' .$module['id'] . '" />' . "\n";
				}
				$out .= '<p class="fp_module_score"><span id="fp_module_score_' . $module['id'] . '">' . $module['score'] . '</span>p</p>' . "\n";
			}
			if($module['clicks'] > 0)
			{
				$out .= '<p>' . cute_number($module['clicks']) . ' klick</p>' . "\n";
			}
			$out .= '</div>' . "\n";
		}
		if($module['format'] == '2_3')
		{
			$out .= '</div>' . "\n";
			$out .= '<div class="puff">' . "\n";
			
			$puff_query = 'SELECT * FROM fp_puffs ORDER BY id ASC';
			$puffs = query_cache(array('category' => 'fp_puffs', 'max_limit' => 600, 'query' => $puff_query));

			$puff_key = (date('z') + $puff_no) % count($puffs);

			$out .= $puffs[$puff_key]['content'];
			$out .= '</div>' . "\n";
		}
		$out .= '</div>' . "\n";
		$out .= '</li>' . "\n";		
	}
	$out .= '</ol>' . "\n";
	
	$out .= '<div id="module_pagination">' . "\n";
	//Create Pagination links
	if(isset($_GET['page']) && is_numeric($_GET['page']))
	{
		$page = intval($_GET['page']);
		if($page > 1)
		{
			$out .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($page - 1) . '">&laquo; Föregående</a> |';
		}
		
		if($page > 0)
		{
			$out .= ' ' . $page . ' | <a href="' . $_SERVER['PHP_SELF'] . '?page=' . ($page + 1) . '">Nästa &raquo;</a>';
		}
	}
	else
	{
		$out .= ' <a href="' . $_SERVER['PHP_SELF'] . '?page=2">Nästa &raquo;</a>';
	}
	$out .= '</div>' . "\n";
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
	?>
