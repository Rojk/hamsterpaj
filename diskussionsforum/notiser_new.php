<?
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('forum');
	$ui_options['title'] = 'Nytt forum, igen...';
	$ui_options['stylesheets'][] = 'discussion_forum.css';
	$ui_options['stylesheets'][] = 'abuse.css';
	$ui_options['javascripts'][] = 'discussion_forum.js';	
	$ui_options['javascripts'][] = 'forum_help_texts.js';	

	
	function discussion_forum_thread_list_edit_by_joar($threads)
	{
		$output .= '<table class="forum_thread_list">' . "\n";
		$output .= '<tr class="headings"><th>Rubrik</th><th>Skapare</th><th>Inlägg</th><th>Olästa</th><th>Poäng</th></tr>' . "\n";
		$zebra = 'odd';
		foreach($threads AS $thread)
		{
			$flags = ($thread['sticky'] == 1) ? ' <img src="' . IMAGE_URL . 'discussion_forum/thread_sticky_icon.png" alt="Klistrad" />' : '';
			$flags .= ($thread['locked'] == 1) ? ' L' : '';
			$href = (isset($thread['url'])) ? $thread['url'] : $thread['handle'] . '/sida_1.php';
			$thread['unread_posts'] = ($thread['unread_posts'] > 0) ? '<strong>' . $thread['unread_posts'] . '</strong>' : '';
			
			$output .= '<tr class="' . $zebra . '">' . "\n";
			$output .= '	<td class="main_info">' . "\n";
			$output .= '		' . (empty($flags) ? '' : '&laquo;' . $flags . ' &raquo;') . ' <a href="' . $href . '">' . $thread['title'] . '</a>' . "\n";
			$output .= '	</td>' . "\n";
			$output .= '	<td class="author"><a href="/traffa/profile.php?id=' . $thread['author'] . '">' . $thread['username'] . '</a></td>' . "\n";			
			$output .= '	<td class="post_count">' . $thread['child_count'] . '</td>' . "\n";
			$output .= '	<td class="unread_posts">' . $thread['unread_posts'] . '</td>' . "\n";
			$output .= '	<td class="score">' . $thread['score'] . '</td>' . "\n";
			$output .= '</tr>' . "\n";

			$zebra = ($zebra == 'odd') ? 'even' : 'odd';
		}
		$output .= '</table>' . "\n";

		return $output;
	}
	
	discussion_forum_reload_all();
	$ui_options['menu_path'][] = 'notices';
	$ui_options['title'] = 'Bevakade trådar - Hamsterpaj.net';
	$output .= discussion_forum_locator(array('page' => 'notices'));
	$output .= discussion_forum_list_notices();

	$output .= '<h1>Bevakade trådar</h1>' . "\n";
	
	$output .= '<h1>Bajs?</h1>' . "\n";
	$output .= discussion_forum_thread_list_edit_by_joar($_SESSION['forum']['subscriptions']);
	

	foreach($_SESSION['forum']['categories'] AS $category)
	{
		if($category['subscribing'] == 1)
		{
			$subscribing_categories[] = $category['category_id'];
		}
	}
	if(count($subscribing_categories) > 0)
	{
		$output .= '<h1 style="margin-top: 20px;">Kategorier du prenumererar på</h1>' . "\n";
		
		$viewers_userlevel = login_checklogin() ? $_SESSION['login']['userlevel'] : 0;
		$categories = discussion_forum_categories_fetch(array('id' => $subscribing_categories, 'max_levels' => 0, 'viewers_userlevel' => $viewers_userlevel));
		
		$output .= discussion_forum_categories_list($categories);
	}
	echo ui_top($ui_options);
	echo $output;
	echo ui_bottom();
?>