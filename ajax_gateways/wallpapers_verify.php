<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'wallpaper_admin.lib.php');
	
	if(!is_privilegied('backgrounds_admin'))
	{
		die('Does not compute');
	}
	
	if(!isset($_GET['action']))
	{
		die('Need to know action. '.__FILE__.'#'.__LINE__);
	}
	else
	{
		switch($_GET['action'])
		{
			case 'get_info':
				if(isset($_GET['id']))
				{
					if(is_numeric($_GET['id']))
					{
						echo wallpaper_verify_get_info($_GET['id']);
					}
					else
					{
						echo 'Soet hacker du :P';
					}
				}
				else
				{
					die('Måste veta ID! '.__FILE__.'#'.__LINE__);
				}
				break;
			case 'validate':
				if(isset($_GET['id']))
				{
					if(is_numeric($_GET['id']))
					{
						echo wallpaper_verify_execute($_GET['id'], $_POST);
					}
					else
					{
						echo 'Soet hacker du :P';
					}
				}
				else
				{
					die('Måste veta ID! '.__FILE__.'#'.__LINE__);
				}
				break;
		}
	}

?>