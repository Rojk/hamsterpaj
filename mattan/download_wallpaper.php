<?php
/*
	Tables being used in this file:
	WALLPAPERS_TABLE
	WALLPAPERS_RES
	WALLPAPERS_RES_RESOLUTION

*/

	require('../include/core/common.php');

    $extList = array();
    $extList['gif'] = 'image/gif';
    $extList['jpg'] = 'image/jpeg';
    $extList['png'] = 'image/png';
    $extList['bmp'] = 'image/bmp';

	if(!isset($_GET['w'], $_GET['h'], $_GET['id']))
	{
		echo 'Fel parametrar';
	}
	else
	{
		$w = intval($_GET['w']);
		$h = intval($_GET['h']);
		$id = intval($_GET['id']);
		
		$query = 'SELECT c.extension FROM '.WALLPAPERS_RES.' AS a LEFT JOIN '.WALLPAPERS_RES_RELATION.' AS b ON b.resolution_pid = a.id 
		LEFT JOIN '.WALLPAPERS_TABLE.' AS c ON c.id = b.pid WHERE a.is_removed = 0 AND b.is_removed = 0 AND a.resolution_w = '.$w.' AND a.resolution_h = '.$h.' AND b.pid = '.$id.' LIMIT 1'; 
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(mysql_num_rows($result) == 0)
		{
			echo '<strong>Den uppl&ouml;sningen finns inte</strong>';
		}
		else
		{
			$data = mysql_fetch_assoc($result);
			$query = 'UPDATE '.WALLPAPERS_TABLE.' SET downloads = downloads + 1 WHERE id = '.$id.' LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		    
		    echo '<img src="'.WALLPAPER_URL.$id.'_'.$w.'_'.$h.'.'.$data['extension'].'" alt="" />';

		}
	}
?>