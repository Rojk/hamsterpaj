<?php
	/* OPEN_SOURCE */
	require('../include/core/common.php');
	if(!is_privilegied('igotgodmode'))
	{
		die('fast den här söken var ju dum...');
	} 
	
	$ui_options['menu_path'] = array('diskussionsforum', 'soek'); // Doesn't work. I have to get the right keyword.
	$ui_options['title'] = 'Hamsterpajs diskussionsforum';
	$ui_options['stylesheets'][] = 'discussion_forum.css';
	$ui_options['stylesheets'][] = 'forms.css';
	
	$out .= '<h1>Här kan du söka i Hamsterpajs forum</h1>' ."\n";
	
	$out .= '<fieldset>' . "\n";
			$out .= '<legend>Sökalternativ</legend>' . "\n";
			$out .= '<form action="" method="get">';
			$out .= '<table class="form" id="camp_rock_competition">' . "\n";
				$out .= '<tr>' . "\n";
					$out .= '<th><label for="keywords">Sökfras</label></th>' . "\n";
					$out .= '<td><input type="text" name="keywords" value="' . $_GET['keywords'] .'" /></td>' . "\n";
				$out .= '</tr>' . "\n";
			$out .= '</table>' . "\n";
			
			$out .= '<input type="submit" id="submit" value="Sök" />' . "\n";
			$out .= '</form>';
			$out .= '</fieldset>' . "\n";
	
	if(isset($_GET['keywords']))
	{
		$page = (isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0) ? intval($page) : 1;
		
		$post_options['page_offset'] = $page - 1;
		$post_options['min_quality_level'] = 2;
		$post_options['limit'] = 15;
		$post_options['order-direction'] = 'DESC';
		$post_options['threads_only'] = true;
		$post_options['match'] = array(
			'against' => $_GET['keywords'],
			'in_columns' => 'p.content' // Danger: DO ABSOLUTELY NOT change this line without asking Joel first!!!
		);
		
		$posts = discussion_forum_post_fetch($post_options);
		
		$search_keywords = explode(' ', $_GET['discussionforum_search']);

		//$out .= '<h1>Using ' . count($search_keywords) . ' keyword(s), query executed in ' . $query_execution_time . ' secounds</h1>';

		if(empty($posts)) // Same as (count($posts) < 1)
		{
			$out .= '<h2>Tyvärr, inga trådar med den beskrivningen kunde hittas. Kanske skulle ett annat sökord kunna fungera</h2>' ."\n";
		}
		else
		{
			$out .= '<h2>Din sökning genererade ' . count($posts) . ' träffar</h2>' ."\n";
			// List all threads
			$out .= '<h2>Trådar</h2>' . "\n";
			foreach($posts as $post)
			{
				$out .= '<div>' . "\n";
					$out .= '<a href="' . forum_get_url_by_post($post['id']) . '" title="Gå till inlägget"><h3>' . $post['title'] . '</h3></a>' . "\n";
					$out .= '<p>' . ((strlen($post['content']) > 400) ? substr($post['content'], 0, 400) . '...<a href="' . forum_get_url_by_post($post['id']) . '">[Läs mer]</a>' : $post['content']) . '</p>' . "\n";
					$out .= '<span>Skapad av <a href="">' . $post['author'] . '</a>' . "\n";
					$out .= ' i kategorin <a href="">' . $post['forum_id'] . '</a> - ' . "\n";
					$out .= 'den ' . $post['timestamp'] . '</span>' . "\n";
				$out .= '</div>' . "\n";
				//$out .= discussion_forum_post_render($post, array(), array('show_post_controls' => false, 'search_highlight' => $search_keywords));
				//$out .= '<a style="margin-left: 20px;" href="' . forum_get_url_by_post($post['id']) . '">Gå till inlägget »</a>' . "\n";
			}
		}
	}

	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
