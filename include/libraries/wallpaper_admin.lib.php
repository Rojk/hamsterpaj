<?php
/*
	Tables being used in this file:
	WALLPAPERS_TABLE
	WALLPAPERS_RES
	WALLPAPERS_RES_RELATION
	WALLPAPERS_CATS
	WALLPAPERS_AUTHORS
	WALLPAPERS_LICENSE
	login
*/

function check_image()
{
	//first we make error control
	$errors = array();

		//check filesize 40MB
		if(filesize($_FILES['uploaded_image']['tmp_name']) > 41943040)
		{
			$errors[] = 'Bilden &auml;r f&ouml;r stor';
		}

		// check image's filetype
		$allowed_image_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);
		$img = exif_imagetype($_FILES['uploaded_image']['tmp_name']);
		if($_FILES['uploaded_image']['error'] == 0 && !in_array($img, $allowed_image_types))
		{
			$errors[] = 'Du har laddat upp en felaktig bild. Bilden f&aring;r endast vara jpg, png, gif eller bmp.';
		}
		
		//no errors
		if(count($errors) == 0)
		{
			return true;
		}
		else
		{
			return array('errors' => $errors);
		}
}

function wallpaper_add_uploaded_image()
{	
	$err = check_image();
	if($err !== false && !is_array($err))
	{
			$filename = check_filename($_FILES['uploaded_image']['name'], 'original_'.time());
			
			if(move_uploaded_file($_FILES['uploaded_image']['tmp_name'], UPLOAD_PATH.$filename))
			{
				//extension
				$extension = exif_imagetype(UPLOAD_PATH.$filename);
				switch($extension)
				{
				case IMAGETYPE_JPEG:
					$extension = 'jpg';
					break;
				case IMAGETYPE_PNG:
					$extension = 'png';
					break;
				case IMAGETYPE_GIF:
					$extension = 'gif';
					break;
				case IMAGETYPE_BMP:
					$extension = 'bmp';
					break;
				}

				//add to database
				$query = 'INSERT INTO '.WALLPAPERS_TABLE.'(timestamp, extension) VALUES(UNIX_TIMESTAMP(), "'.$extension.'")';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				$id = mysql_insert_id();					
				return array('filename' => $filename, 'id' => $id);
			}
			else
			{
				return array('errors' => array('Kunde inte ladda upp bilden. Filename: '.$filename));
			}
						
	}
	else
	{
		return $err;
	}
}


function make_resolution($param)
{
	// Imagetype
	$type = exif_imagetype(UPLOAD_PATH.$param['filename']);
	switch($type)
	{
		case IMAGETYPE_JPEG:
			$type = 'jpg';
			break;
		case IMAGETYPE_PNG:
			$type = 'png';
			break;
		case IMAGETYPE_GIF:
			$type = 'gif';
			break;
		case IMAGETYPE_BMP:
			$type = 'bmp';
			break;
	}
	
	if($param['original'])
	{
	$filename = $param['id'].'_'.$param['width'].'_'.$param['height'].'.'.$type;
		if(copy(UPLOAD_PATH.$param['filename'], UPLOAD_PATH.$param['id'].'_'.$param['width'].'_'.$param['height'].'.'.$type))
		{
			unlink(UPLOAD_PATH.$param['filename']);

			//check if the resolution exists
			$query = 'SELECT NULL FROM '.WALLPAPERS_RES.' WHERE resolution_w = '.$param['width'].' AND resolution_h = '.$param['height'];
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			if(mysql_num_rows($result) == 0)
			{
				//insert the new resolution
				$query = 'INSERT INTO '.WALLPAPERS_RES.'(resolution_w, resolution_h, scale) VALUES('.$param['width'].', '.$param['height'].', '.round($param['width']/$param['height'], 2).')';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			}
		
			$query = 'INSERT INTO '.WALLPAPERS_RES_RELATION.'(pid, resolution_pid)
			SELECT '.$param['id'].', id
			FROM '.WALLPAPERS_RES.'
			WHERE resolution_w = '.$param['width'].' AND resolution_h = '.$param['height'].' LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

			return $filename;
		}
		else
		{
			return 'Kunde inte skapa filen. Det nya filnamnet:'.$filename.' <br />Originalfilen: '.UPLOAD_PATH.$param['filename'];
		}
	}
	else
	{
		// Load
		$new_image = imagecreatetruecolor($param['new_width'], $param['new_height']);

		switch($type)
		{
			case 'jpg':
				$source = imagecreatefromjpeg(UPLOAD_PATH.$param['filename']);
				break;
			case 'png':
				$source = imagecreatefrompng(UPLOAD_PATH.$param['filename']);
				break;
			case 'gif':
				$source = imagecreatefromgif(UPLOAD_PATH.$param['filename']);
				break;
			case 'bmp':
				$source = imagecreatefromwbmp(UPLOAD_PATH.$param['filename']);
				break;
		}

		// Resize
		imagecopyresized($new_image, $source, 0, 0, 0, 0, $param['new_width'], $param['new_height'], $param['width'], $param['height']);
				
		// Filename
		if($param['new_width'] == 120)
		{
			$param['new_width'] = 'thumb';
			$param['new_height'] = '';
		}
		elseif($param['new_width'] == 600)
		{
			$param['new_width'] = 'preview';
			$param['new_height'] = '';
		}
		
		$new_filename = $param['id'].'_'.$param['new_width'].($param['new_height'] != '' ? '_'.$param['new_height'] : '').'.';
		
		if(is_numeric($param['new_width']) && is_numeric($param['new_height']))
		{
			//check if the resolution exists
			$query = 'SELECT NULL FROM '.WALLPAPERS_RES.' WHERE resolution_w = '.$param['new_width'].' AND resolution_h = '.$param['new_height'];
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			if(mysql_num_rows($result) == 0)
			{
				//insert the new resolution
				$query = 'INSERT INTO '.WALLPAPERS_RES.'(resolution_w, resolution_h, scale) VALUES('.$param['new_width'].', '.$param['new_height'].', '.round($param['new_width']/$param['new_height'], 2).')';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			}
		
			$query = 'INSERT INTO '.WALLPAPERS_RES_RELATION.'(pid, resolution_pid)
			SELECT '.$param['id'].', id
			FROM '.WALLPAPERS_RES.'
			WHERE resolution_w = '.$param['new_width'].' AND resolution_h = '.$param['new_height'].' LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}

		//create the picutre!
		if($type == 'jpg')
		{
			$new_filename .= 'jpg';
			$new_filename = check_filename($new_filename);
			imagejpeg($new_image, UPLOAD_PATH.$new_filename);
		}
		elseif($type == 'png')
		{
			$new_filename .= 'png';
			$new_filename = check_filename($new_filename);
			imagepng($new_image, UPLOAD_PATH.$new_filename);
		}
		elseif($type == 'gif')
		{
			$new_filename .= 'gif';
			$new_filename = check_filename($new_filename);
			imagegif($new_image, UPLOAD_PATH.$new_filename);
		}
		elseif($type == 'bmp')
		{
			$new_filename .= 'bmp';
			$new_filename = check_filename($new_filename);
			image2wbmp($new_image, UPLOAD_PATH.$new_filename);
		}
		//destory the image to free up memory
		imagedestroy($new_image);
		
		if($param['last'])
		{
			unlink(UPLOAD_PATH.$param['filename']);
		}
	}

	return $new_filename;
}

function check_filename($filename, $extra='')
{
	$extension_parts = explode(".", $filename);
	$extension = $extension_parts[count($extension_parts)-1];
	if(file_exists(UPLOAD_PATH.$filename))
	{
		$i = 0;
		while(file_exists(UPLOAD_PATH.substr($filename, 0, 0-strlen($extension)-1).($extra != "" ? '_'.$extra : '').'_'.$i.'.'.$extension))
		{
			$i++;
		}
		$filename = substr($filename, 0, 0-strlen($extension)-1).($extra != "" ? '_'.$extra : '').'_'.$i.'.'.$extension;
	}
	else
	{
		$filename = substr($filename, 0, 0-strlen($extension)-1).($extra != "" ? '_'.$extra : '').'.'.$extension;
	}
	return $filename;
}

function url_to_form($form){
	$form = html_entity_decode($form);
	$form = explode('&', urldecode($form));
	foreach($form as $key=>$val)
	{
		$explode = explode('=', $val);
		$new_key = $explode[0];
		$new_val = $explode[1];
		$new_form[$new_key] = $new_val;
	}
	return $new_form;
}

function get_cats($val, $level = 0, $cat)
{ 
	$query = 'SELECT title, id FROM '.WALLPAPERS_CATS.' WHERE pid = '.$val.' AND is_removed = 0';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE);

	$out = '';
	while($row = mysql_fetch_assoc($result))
	{
        if( $row['title'] != '' ){ 
            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) ); 
          	$val = $row['id'];
            $out .= "\t".'<option value="'.$val.'"'.($row['id'] == $cat ? ' selected="selected"' : '').'>'.$spaces.$row['title'].'</option>'."\n"; 
            $out .= get_cats( $val, ($level+1), $cat);
        }
    }

   return $out; 
}

function wallpapers_admin_wallpapers_verify_list()
{
	$query = 'SELECT l.username, w.id, w.title, w.timestamp FROM login AS l, '.WALLPAPERS_TABLE.' AS w WHERE w.is_verified = 0 AND l.id = w.user_id AND w.license IS NOT NULL';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	if(mysql_num_rows($result) > 0)
	{	
		$out = '<ul class="wallpapers_list">'."\n";

		while($data = mysql_fetch_assoc($result))
		{
			$out .= "\t".'<li id="li_'.$data['id'].'">';
			$out .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
			$out .= '<br style="clear:both" />';
			$out .= '<h3 id="head_'.$data['id'].'" style="font-weight: bold;"><img src="'.IMAGE_URL.'/loading_icons/ajax-loader1.gif" alt="Laddar..." id="ajax_icon_'.$data['id'].'" style="display:none;" />'."\n";
			$out .= '<a href="#" class="box_link" id="link_'.$data['id'].'"><img src="'.IMAGE_URL.'plus.gif" alt="Expandera/Kollapsa" id="image_'.$data['id'].'" /> '.$data['title'].'</a><span style="color:#aaa"> - '.fix_time($data['timestamp']).' - '.$data['username'].'</span>'."\n";
			$out .= '</h3>'."\n";
		
			$out .= '<div class="wallpaper_boxes" id="box_'.$data['id'].'">'."\n";
			$out .= '</div>';

			$out .= '<br style="clear:both" />';
			$out .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
			$out .= '</li>'."\n";
		}
		$out .= '</ul>'."\n";
	}
	else
	{
		$out .= 'Det finns inga bakgrundsbilder att validera!'."\n";
	}

	return $out;
}

function wallpaper_verify_fetch($options)
{
	$query = 'SELECT l.username, w.user_id, w.id, w.title, w.extension, w_l.title AS license_title, w_a.title AS author_title, w.timestamp, w.cid, w_c.title AS cat_title FROM login AS l, '.WALLPAPERS_TABLE.' AS w, '.WALLPAPERS_AUTHORS.' AS w_a, '.WALLPAPERS_LICENSE.' AS w_l, '.WALLPAPERS_CATS.' AS w_c WHERE w.is_verified = 0 AND l.id = w.user_id AND w.license = w_l.id AND w.author = w_a.id AND w.title <> "" AND w_c.id = w.cid';
	if(isset($options['id']))
	{
		$query .= ' AND w.id = '.intval($options['id']).' LIMIT 1';
	}
	
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	
	while($data = mysql_fetch_assoc($result))
	{
		$return[] = $data;
	}
	$return['num_rows'] = mysql_num_rows($result);
	return $return;
}
function wallpaper_verify_get_info($id)
{
	$out = '';
	if(is_numeric($id))
		$wallpaper = wallpaper_verify_fetch(array('id'=>$id));
	else
		die('Soet hacker du :P');
	
	if($wallpaper['num_rows'] == 1)
	{
		$data = $wallpaper[0];
		$out .= '<div class="box_left">'."\n";
		$out .= '<img src="'.WALLPAPER_URL.$id.'_thumb.'.$data['extension'].'" style="border: 4px solid #fff" />'."\n";
		$out .= '<br />'."\n";
		$out .= '<div style="text-align: center;font-weight: bold;color:#aaa">'."\n";
		$out .=	'<a href="'.WALLPAPER_URL.$id.'_preview.'.$data['extension'].'" target="_blank" title="Se bilden i större skala">'.$id.'_preview.'.$data['extension'].'</a>'."\n";
		$out .= '</div>'."\n";
		$out .= '</div>'."\n";
		$out .= '<div class="box_right">'."\n";
		$out .= '<ul class="no_bullets">'."\n";
		$out .= "\t".'<li>Tillagd: '.fix_time($data['timestamp'])."</li>\n";
		$out .= "\t".'<li>Uppladdad av: <a href="/traffa/profile.php?id='.$data['user_id'].'">'.$data['username'].'</a>'."</li>\n";
		$out .= "\t".'<li>License: '.$data['license_title']."</li>\n";
		$out .= "\t".'<li>Av: '.$data['author_title']."</li>\n";
		$out .= "\t".'<li>Kategori: '.$data['cat_title'].'</li>'."\n";
		$out .= '<hr />'."\n";
		$out .= '<form action="wallpapers_admin.php" method="post">'."\n";
		$out .= "\t".'<li>Status: <input type="radio" name="approved" value="1" /> <span style="color:#008102;">Y</span> <input type="radio" name="approved" value="0" /> <span style="color:red;">N</span> <input type="radio" name="approved" value="-1" /> X</li>'."\n";
		$out .= "\t".'<li><textarea id="verify_comment_'.$data['id'].'" name="verify_comment" rows="3" cols="40">Skriv en kommentar här</textarea></li>'."\n";
		$out .= "\t".'<li><input type="submit" value="Ok" /></li>'."\n";
		$out .= '</form>'."\n";
		$out .= '</ul>'."\n";
		$out .= '</div>'."\n";
	}
	else
	{
		$out .= 'Hittade inte bakgrundsbilden.'."\n";
	}
	return $out;
}

function wallpaper_verify_execute($id, $form)
{
	if(!isset($id, $form))
		die('Wrong parameters');
	if(!is_numeric($id))
		die('Soet hacker du :P');
	require(PATHS_INCLUDE . 'guestbook-functions.php');	
	
	$wallpapers = wallpaper_verify_fetch(array('id'=>intval($id)));
	$wallpaper_recipient = $wallpapers[0]['user_id'];

	$form['verify_comment'] = ($form['verify_comment'] == 'Skriv en kommentar här' ? '' : $form['verify_comment']);
	if($form['approved'] == 1)
	{
		$query = 'UPDATE '.WALLPAPERS_TABLE.' SET is_verified = 1, verify_comment = "'.$form['verify_comment'].'", verifier_user_id = '.$_SESSION['login']['id'].' WHERE id = '.intval($id).' LIMIT 1';
		$wallpaper_status = 'tillagd!';
	}
	elseif($form['approved'] == 0)
	{
		$query = 'UPDATE '.WALLPAPERS_TABLE.' SET is_verified = 1, is_removed = 1, verify_comment = "'.$form['verify_comment'].'", verifier_user_id = '.$_SESSION['login']['id'].' WHERE id = '.intval($id).' LIMIT 1';
		$wallpaper_status = 'nekad!';
	}
	elseif($form['approved'] == -1)
	{
		$query = 'UPDATE '.WALLPAPERS_TABLE.' SET is_verified = 1, is_removed = 1, verify_comment = "'.$form['verify_comment'].'", verifier_user_id = '.$_SESSION['login']['id'].' WHERE id = '.intval($id).' LIMIT 1';
		
		$query_ban = 'UPDATE userinfo SET wallpapers_ban = '.(time()+60*60*24*7).' WHERE userid = '.$wallpaper_recipient;
		mysql_query($query_ban) or report_sql_error($query_ban, __FILE__, __LINE__);
		$wallpaper_status = 'nekad! Du har även blivit bannad en vecka, se kommentaren nedan varför.';
	}
	mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	
	//send gb-entry
	$message .= 'Hej!' . "\n" . 'Din bakgrundsbild som du laddade upp till oss tidigare är nu granskad av ' . $_SESSION['login']['username'] . '.'."\n";
	$message .= 'Den blev '.$wallpaper_status.'\n';
	if($form['verify_comment'] != '')
	{
		$message .= 'Hon eller han även har skivit en kommentar till dig:' . "\n";
		$message .= $form['verify_comment']."\n";
	}
	$message .= "\n" . 'Tack för att du hjälper oss att göra Hamsterpaj till ett bättre och mer trivsamt ställe. Keep on rocking!';
	$message .= "\n\n" . '/Webmaster (referensnummret till bakgrundsbilden är '. intval($id) . ')';
	new_entry($wallpaper_recipient, 2348, htmlentities(utf8_decode($message), ENT_QUOTES, UTF-8));

	return 'Fixat';
}

function wallpapers_admin_menu_list($action='home')
{
	$menu = array();
	$menu[0]['href'] = '?action=view_cat';
	$menu[0]['label'] = 'Kategorier';
	$menu[1]['href'] = '?action=view_res';
	$menu[1]['label'] = 'Upplösning';
	$menu[2]['href'] = '?action=view_license';
	$menu[2]['label'] = 'Licenser';
	$menu[3]['href'] = '?action=view_authors';
	$menu[3]['label'] = 'Upphovsrättsinnehavare';
	$menu[5]['href'] = '?action=verify_wallpaper';
	$menu[5]['label'] = '<span style="color:#ff691d;">Godkänn bakgrundsbild</span>';
	
	foreach($menu as $key=>$menu_item)
	{
		if($menu_item['href'] == '?action='.$action)
			$menu[$key]['current'] = TRUE;
	}
	$rounded_corners_tabs_options['tabs'] = $menu;
	$out = rounded_corners_tabs_top($rounded_corners_tabs_options); 
	return $out;
}

?>