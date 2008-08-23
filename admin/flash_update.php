<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('admin', 'flash_update');
	$ui_options['current_menu'] = 'admin';
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';

	if(!is_privilegied('entertain_add', 'flash'))
	{
		header('location: /');
		die();
	}

	$show = (isset($_GET['show']) && in_array($_GET['show'], array('unactivated', 'activated', 'ideas'))) ? $_GET['show'] : 'unactivated';
	
	if(isset($_GET['activated']) && isset($_GET['id']) && in_array($_GET['activated'], array('yes', 'no')) && is_numeric($_GET['id']) && (int)$_GET['id']>0)
	{
		$query  = 'UPDATE flash_games_update';
		/* Remember: activEted, not activAted. */
		$query .= ' SET activeted = "' . $_GET['activated'] . '"';
		$query .= ' WHERE id=' . $_GET['id'];
		mysql_query($query) or report_sql_error($query);
	}
	
	$rounded_corners_tabs_config = array();
	$rounded_corners_tabs_config['tabs'][] = array('href' => '?show=unactivated', 'label' => 'Nya objekt', 'current' => ($show == 'unactivated'));
	$rounded_corners_tabs_config['tabs'][] = array('href' => '?show=activated', 'label' => 'Papperskorgen', 'current' => ($show == 'activated'));
	$rounded_corners_tabs_config['tabs'][] = array('href' => '?show=ideas', 'label' => 'Kom med förslag till den här sidan', 'current' => ($show == 'ideas'));
	$rounded_corners_tabs_config['return'] = true;
	
	$out .= rounded_corners_tabs_top($rounded_corners_tabs_config);
	
	if($show == 'ideas')
	{
		$out .= 'Om du har förslag till hur den här sidan kan förbättras så släng iväg ett gästboksinlägg till <a href="/traffa/guestbook.php?view=87926">Joel</a>.';
	}
	else
	{
		$query  = 'SELECT name, url, id';
		$query .= ' FROM flash_games_update';
		/* I have realy no clue about why it's called "activeted". But I think changing it, is a bad idea... */
		$query .= ' WHERE activeted = "' . (($show == 'activated') ? 'yes' : 'no') . '"';
		$query .= ' ORDER BY id';
		
		$result = mysql_query($query) or report_sql_error($query);
		
		$out .= '<table>' . "\n";
		
		while ($data = mysql_fetch_array($result))
		{
			$out .= '<tr>';
			$out .= '<td><a href="?show=' . $show . '&activated=' . (($show == 'unactivated') ? 'yes' : 'no') . '&id=' . $data['id'] . '">[' . (($show == 'unactivated') ? 'X' : 'restore') . ']</a> <a href="' . $data['url'] . '" onclick="if(window.open(this.href)){ return false; }">[Länk]</a> ' . $data['name'] . '</td>'; 
			$out .= '<td>' . $data['url'] . '</td>';
			$out .= '</tr>' . "\n";
		}
		$out .= '</table>';
	}
	$out .= rounded_corners_tabs_bottom(array('return' => true));

	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>