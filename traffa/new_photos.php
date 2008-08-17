<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/photos.lib.php');

	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['menu_path'] = array('traeffa', 'new_photos');
	$ui_options['title'] = 'Nya foton - Hamsterpaj.net';

	$out .= '<h1>Nya foton</h1>';

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

	$offset = (($page - 1) * 32);
	
	$photos = photos_fetch(array('order-direction' => 'DESC', 'offset' => $offset, 'limit' => 32));
	$out .= photos_list($photos);

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

	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
