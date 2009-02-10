<?php
	require('/home/www/standard.php');
	require_once(PATHS_LIBRARIES . 'open_search.lib.php');

	if(!is_privilegied('open_search'))
	{
		die('Inget för dig...');
	}
	if(count($_POST) > 0)
	{
		if(isset($_GET['action']))
		{
			switch($_GET['action'])
			{
				case 'add':
					echo open_search_add_box_execute($_POST, array('json_encode' => true));
					break;
				case 'edit':
					echo open_search_edit_box_execute($_POST, array('json_encode' => true));
					break;
			}
		}
		else
		{
			echo 'Ingen action!';
		}
	}


?>