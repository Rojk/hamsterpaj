<?php
	require('../include/core/common.php');
	require_once(PATHS_INCLUDE  . 'libraries/entertain.lib.php');
	require_once(PATHS_INCLUDE  . 'libraries/rank.lib.php');
	require_once(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	require_once(PATHS_INCLUDE . 'libraries/fp_modules.lib.php');

	$ui_options['javascripts'][] = 'fp_common_modules.js';

	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'fp_modules.css';
	$ui_options['stylesheets'][] = 'fp_common_modules.css';
	

	$ui_options['custom_logo'] = 'http://images.hamsterpaj.net/piraja/hp_piraja_logo.png';
	$ui_options['title'] = 'Piraja';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';
	
	event_log_log('piraja');

	$fp_modules = fp_modules_fetch(array('piraja' => 1));

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
			$out .= '<li class="module_2_3">' . "\n";			
			$out .= '<div class="' . $class . '">' . "\n";
		}
		else
		{
			$out .= '<li class="' . $class . '">' . "\n";
		}
		
		
		$regexp = '#(href="?)([a-zA-Z0-9\:\.\-\_Ã¥Ã¤Ã¶Ã…Ã„Ã–&\(\)~\/=?]{4,})"#eis';
		$o = preg_replace($regexp, "'href=\"/fp_module_click.php?id=" . $module['id'] . "&url=' . base64_encode(stripcslashes('$2')) . '\"'", $o);
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
				if($module['thread_id'] == 0)
				{
					$thread_options['forum_id'] = 114;
					$thread_options['title'] = $module['name'];
					$thread_options['content'] = 'Det hÃƒÂ¤r ÃƒÂ¤r en kommenteringstrÃƒÂ¥d fÃƒÂ¶r modulen \\"' . $module['name'] . '\\" pÃƒÂ¥ fÃƒÂ¶rstasidan. Egentligen skall sjÃƒÂ¤lva modulen visas hÃƒÂ¤r i forumet, typ ovanfÃƒÂ¶r trÃƒÂ¥den. Men det ÃƒÂ¤r vi inte klara med ÃƒÂ¤n, sÃƒÂ¥ tillsvidare fÃƒÂ¥r man kommentera utan att se modulen :)';
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
		$out .= '</li>' . "\n";		
	}
	$out .= '</ol>' . "\n";
	
	ui_top($ui_options);
	echo '<h1>Hamsterpaj & Piraja sitting in a tree K-I-S-S-I-N-G</h1>' . "\n";
	echo '<p>Vi har ett litet test nu när vi stjäl innehåll ur tidningen <a href="http://www.piraja.se/">Piraja</a> och lägger på Hamsterpaj. Vi tror att det är okej, killen som chefar pratade gotländska, så vi begrep inte riktigt allt, men det blir säkert bra :)</p>' . "\n";
	echo '<h3>Här finns allt Pirajamaterial vi publicerat</h3>' . "\n"; 


	echo $out;
	ui_bottom();
	?>
