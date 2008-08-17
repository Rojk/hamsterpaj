<?php
	/* OPEN_SOURCE */
	require('../include/core/common.php');
	
	$ui_options['menu_path'] = array('diskussionsforum', 'soek'); // Doesn't work. I have to get the right keyword.
	$ui_options['title'] = 'Hamsterpajs diskussionsforum';
	$ui_options['stylesheets'][] = 'discussion_forum.css';
	
	$out .= '<h1>Här kan du söka i Hamsterpajs forum</h1>' ."\n";
	$out .= '<div class="discussionforum_searchbox">' ."\n";
	$out .= '<form action="" method="get">' ."\n";
	$out .= '<input type="text" id="discussionforum_search" class="discussionforum_search" name="discussionforum_search" value="' . $_GET['discussionforum_search'] .'" />' ."\n";
	$out .= '<input type="submit" value="Sök" class="search_button" />' ."\n";
	$out .= '</form>' ."\n";
	$out .= '</div><br style="clear: both;" />' ."\n";
	
	if(isset($_GET['discussionforum_search']))
	{
		$page = (isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0) ? intval($page) : 1;
		
		$post_options['page_offset'] = $page - 1;
		$post_options['min_quality_level'] = 2;
		$post_options['limit'] = 15;
		$post_options['order-direction'] = 'DESC';
		$post_options['threads_only'] = true;
		$post_options['match'] = array(
			'against' => $_GET['discussionforum_search'],
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

			foreach($posts as $post)
			{
				$out .= discussion_forum_post_render($post, array(), array('show_post_controls' => false, 'search_highlight' => $search_keywords));
				$out .= '<a style="margin-left: 20px;" href="' . forum_get_url_by_post($post['id']) . '">Gå till inlägget »</a>' . "\n";
			}
		}
	}

	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
