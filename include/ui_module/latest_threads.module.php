<?php
	$threads = cache_load('latest_forum_threads');
	$options['output'] .= '<ul>' . "\n";
	foreach($threads AS $thread)
	{
		$thread['title'] = (mb_strlen($thread['title'], 'UTF8') > 22) ? htmlspecialchars(mb_substr(htmlspecialchars_decode($thread['title']), 0, 19, 'UTF8')) . '...' : $thread['title'];
		$info = 'I ' . $thread['category_title'] . ' av ' . $thread['username'] . ': ' . $thread['title'];
		$options['output'] .= '<li>' . date('H:i', $thread['timestamp']) . ' <a title="' . $info . '" href="' . $thread['url'] . '">' . $thread['title'] . '</a></li>' . "\n";
	}
	$options['return'] .= '</ul>' . "\n";
	
	if(!(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0') || $_SESSION['disablesteve'] == 1))
	{
			$options['output'] .= '<img src="' . IMAGE_URL . 'steve/icon_gun.gif" id="steve_gun" />' . "\n";
	}


	
	function recurse_forum_category_specialgrej($categories, $depth)
	{
		foreach($categories AS $category)
		{
			if($category['handle'] == 'hamsterpajs_artiklar' || $category['handle'] == 'forum_error')
			{
				continue;
			}
			$indent = '';
			for($i = 0; $i < $depth; $i++)
			{
				$indent .= '&nbsp;&nbsp;';
			}
			$category['title'] = (strlen($category['title']) > 21) ? substr($category['title'], 0, 19) . '...' : $category['title'];
			$style = ($depth == 0) ? ' style="font-weight: bold;"' : '';
			$output .= '<option value="' . $category['handle'] . '"' . $style . '>' . $indent . $category['title'] . '</option>' . "\n";

			$output .= recurse_forum_category_specialgrej($category['children'], $depth+1);
		}				
		return $output;
	}

	

	$categories = discussion_forum_categories_fetch(array('parent' => 0));
	if($_SESSION['login']['id'] == 3)
	{

	}
	if (login_checklogin())
	{
		$options['output'] .= '<select id="threads_module_create_thread" onchange="threads_module_create_thread()"><option value="false">Starta ny tråd</option>' . "\n";
		$options['output'] .= recurse_forum_category_specialgrej($categories, 0);
		$options['output'] .= '</select>';
	}
	
?>