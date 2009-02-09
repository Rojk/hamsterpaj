<?php
	require('include/core/common.php');
	require_once(PATHS_LIBRARIES . 'entertain.lib.php');
	require_once(PATHS_LIBRARIES . 'rank.lib.php');
	require_once(PATHS_LIBRARIES . 'photos.lib.php');
	require_once(PATHS_LIBRARIES . 'fp_modules.lib.php');

	$ui_options['javascripts'][] = 'fp_common_modules.js';

	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'fp_modules.css';
	$ui_options['stylesheets'][] = 'fp_common_modules.css';
	

	$ui_options['title'] = 'Startsidan på Hamsterpaj';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';

	$fp_modules = fp_modules_fetch(array('removal_min' => time(), 'launch_max' => time()));

	$out .= '<ol id="fp_module_list">' . "\n";

	$puff_no = -1;
	foreach($fp_modules AS $module)
	{
		$o = '';
		$module_hide = false;
		if($module['code_mode'] == 'php')
		{
			include(PATHS_DYNAMIC_CONTENT . 'fp_modules/' . $module['id'] . '.php');
			if($module_hide == true)
			{
				continue;
			}
		}
		else
		{
			$o = file_get_contents(PATHS_DYNAMIC_CONTENT . 'fp_modules/' . $module['id'] . '.php');
		}

		$class = ($module['commenting'] == 'true' || $module['published'] == 'true' || $module['grading'] == 'true') ? 'module' : 'module_noframe';
		if($module['format'] == '2_3')
		{
			$puff_no++;
			$out .= '<li class="module_2_3">' . "\n";			
			$out .= '<div class="' . $class . '">' . "\n";
		}
		else
		{
			$out .= '<li class="' . $class . '">' . "\n";
		}
		
		
		$regexp = '#(href="?)([a-zA-Z0-9\:\.\-\_������&\(\)~\/=?]{4,})"#eis';
		$o = preg_replace($regexp, "'href=\"/fp_module_click.php?id=" . $module['id'] . "&url=' . base64_encode(stripcslashes('$2')) . '\"'", $o);
		$out .= $o;

			if($module['commenting'] == 'true')
			{
				if($module['thread_id'] == 0)
				{
					$thread_options['forum_id'] = 114;
					$thread_options['title'] = $module['name'];
					$thread_options['content'] = 'Det här är en kommenteringstråd för modulen \\"' . $module['name'] . '\\" på förstasidan. Egentligen skall själva modulen visas här i forumet, typ ovanför tråden. Men det är vi inte klara med än, så tillsvidare får man kommentera utan att se modulen :)';
					$thread_options['mode'] = 'new_thread';
					$thread_options['author'] = 57100;
					$module['thread_id'] = discussion_forum_post_create($thread_options);		
					
					$query = 'UPDATE fp_modules SET thread_id = "' . $module['thread_id'] . '" WHERE id = "' . $module['id'] . '"';
					mysql_query($query);		
				}
				$out .= '<p style="margin-top: 2px;"><a style="color: #565656; text-decoration: underline;" href="' . forum_get_url_by_post($module['thread_id']) . '" class="fp_moudle_commenting">Kommentera i forumet</a></p>' . "\n";
			}
			if($module['piraja'] == 'true')
			{
				$out .= '<p style="margin-top: 2px;"><a style="color: #565656; text-decoration: underline;" href="/piraja/prylar.php">Flera prylar</a></p>' . "\n";
			}

		$out .= '<br style="font-size: 0px; height: 0px; clear: both; line-height: 0px;" />' . "\n";
		
		if($module['commenting'] == 'true' || $module['published'] == 'true' || $module['grading'] == 'true')
		{
			$out .= '<div class="controls">' . "\n";
			if($module['published'] == 'true')
			{
				$out .= '<p class="fp_module_published">Upplagd ' . date('Y-m-d', $module['launch']) . '</p>' . "\n";
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
		$out .= '</li>' . "\n";		
	}
	$out .= '</ol>' . "\n";
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
	?>
