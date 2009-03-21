<?php

	require('../include/core/common.php');
/*
	Tables being used in this file:
	WALLPAPERS_TABLE
	WALLPAPERS_RES
	WALLPAPERS_TAGS
*/
if(!is_privilegied('backgrounds_admin'))
{
	die('Does not compute');
}
	require(PATHS_LIBRARIES . 'wallpaper_admin.lib.php');
	if(isset($_GET['action']))
	{
		if($_GET['action'] == 'resize_wallpapers')
		{
			if(isset($_GET['width'], $_GET['height'], $_GET['original_name'], $_GET['original']))
			{
				list($width, $height) = getimagesize(UPLOAD_PATH.$_GET['original_name']);
	
				if($_GET['width'] == 'thumb')
				{
					$new_width = 120;
					$new_height = 90;
				}
				elseif($_GET['width'] == 'preview')
				{
					$new_width = 600;
					$new_height = 450;
				}
				else
				{
					$new_width = intval($_GET['width']);
					$new_height = intval($_GET['height']);
				}
				$param['filename'] = $_GET['original_name'];
				$param['width'] = $width;
				$param['height'] = $height;
				$param['new_width'] = $new_width;
				$param['new_height'] = $new_height;
				$param['id'] = intval($_GET['id']);
				$param['last'] = ($_GET['last'] == 'true' ? true : false);
				$param['original'] = ($_GET['original'] == 'true' ? true : false);
				echo make_resolution($param);
			}
		}
		elseif($_GET['action'] == 'get_res')
		{
			if(isset($_GET['width'], $_GET['height']))
			{
				if((int)$_GET['height'] != 0) //prevent dividing with 0
				{
				$width = intval($_GET['width']);
				$height = intval($_GET['height']);
				
				$query = 'SELECT resolution_w AS width, resolution_h AS height FROM '.WALLPAPERS_RES.' WHERE scale = '.round($width/$height, 2).' AND resolution_w < '.$width.' AND resolution_h < '.$height.' ORDER BY resolution_w, resolution_h ASC';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	
				while($data = mysql_fetch_assoc($result))
				{
					echo '<li>'."\n";
					echo '<input type="checkbox" name="new_width" checked="checked" value="'.$data['width'].'x'.$data['height'].'" id="make_to_'.$data['width'].'x'.$data['height'].'" />'."\n";
					echo '<label for="make_to_'.$data['width'].'x'.$data['height'].'">'.$data['width'].'x'.$data['height'].'</label>'."\n";
					echo '</li>'."\n";
				}
				echo '<li>'."\n".'<input type="checkbox" disabled="disabled" id="orignal_image_res" name="new_width" value="'.$width.'x'.$height.'" checked="checked" id="make_to_'.$width.'x'.$height.'" />'."\n";
				echo '<label for="make_to_'.$width.'x'.$height.'">'.$width.'x'.$height.' (original)</label>'."\n";

				}
				else
				{
					die('Men nu var du hackig!');
				}

			}
			else
			{
				die('Fel parametrar');
			}
		}
		elseif($_GET['action'] == 'upload_form')
		{
			if(isset($_GET['id']))
			{
				$form = $_GET;
				$query = 'UPDATE '.WALLPAPERS_TABLE.' SET title = "'.$form['title'].'", cid = '.$form['cat'].', user_id = '.$_SESSION['login']['id'].' WHERE id = '.$_GET['id'].' LIMIT 1';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
				$tags = explode(',', $form['tags']);
				foreach($tags as $tag)
				{
					$query = 'INSERT INTO '.WALLPAPERS_TAGS.'(tag, pid) VALUES("'.$tag.'", '.$_GET['id'].')';
					mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				}
			}
			else
			{
				die('Fel parametrar');
			}
		}
		else
		{
			die('Fel parametrar');
		}
	}
	else
	{
		die('Fel parametrar');
	}	
?>