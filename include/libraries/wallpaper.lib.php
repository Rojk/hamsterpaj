<?php
/*
	Tables being used in this file:
	(all tables are being used)
	WALLPAPERS_TABLE
	WALLPAPERS_CATS
	WALLPAPERS_RES
	WALLPAPERS_RES_RELATION
	WALLPAPERS_TAGS
	WALLPAPERS_LICENSE
	WALLPAPERS_AUTHORS
*/

function wallpapers_fetch($options)
{
	$query = 'SELECT * FROM '.WALLPAPERS_TABLE;
	$options['id'] = (isset($options['id']) ? intval($options['id']) : false);
	$options['limit'] = (isset($options['limit']) ? intval($options['limit']) : false);
	
	if($options['id'] !== false)
	{
		$query .= ' WHERE id = '.$options['id'];
	}
	
	if($options['limit'] !== false)
	{
		$query .= ' LIMIT '.$options['limit'];
	}
	
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$res[] = $data;
	}
	$res['num_rows'] = mysql_num_rows($result);
	return $res;
}


function wallpapers_instructions()
{
	if(strstr($_SERVER['HTTP_USER_AGENT'], 'Macintosh') !== false)
	{
		$os = 'Mac OS X';
		//instructions, one step / key
		$instructions[] = 'Klicka på den upplösning som passar din skärm (om du inte vet, välj 1280 x 1024)';
		if(strstr($_SERVER['HTTP_USER_AGENT'], 'Safari'))
		{
			$instructions[] = 'Högerklicka på bilden och välj "Använd bild som skrivbordsbild"';
		}
		elseif(strstr($_SERVER['HTTP_USER_AGENT'], 'Firefox'))
		{
			$instructions[] = 'Högerklicka på bilden och välj "Använd som skrivbordsbakgrund..."';
		}	
	}
	elseif(strstr($_SERVER['HTTP_USER_AGENT'], 'Windows') !== false)
	{
		$os = 'Windows';
		//instructions, one step / key
		$instructions[] = 'Högerklicka på den den upplösningen som är i fetstil, finns det ingen i fetstil så finns den här bakgrunden troligtvis inte med rätt mått för din skärm.';
		$instructions[] = 'Spara bilden i en mapp på din dator, kanske "Mina Dokument"';
		$instructions[] = 'Öppna bilden, högerklicka på den och välj "Använd som bakgrundsbild"';
	}
	else
	{
		$os = 'ett okänt OS';
		$instructions[] = 'Eftersom du inte har Windows eller Mac OS X (Apple) så antar vi att du klarar av att byta bakgrundsbild. Om inte, skapa en tråd i forumet ;)';	
	}
	$out .= rounded_corners_top(array('color' => 'blue_deluxe'));
		$out .= '<h3>Så här byter du bakgrundsbild i '.$os.'</h3>'."\n";
		$out .= '<ol class="wallpaper_download_instructions">'."\n";
		foreach($instructions as $instruction)
		{
			$out .= '<li>'.$instruction.'</li>'."\n";
		}
		$out .= '</ol>';
	$out .= rounded_corners_bottom();
	
	return $out;
}

function wallpapers_action_home($arg)
{
	$out = '';
	$out .= '<h1>Blandade bilder</h1>'."\n";
	
	if($arg['show_all_res'])
	{
		$query = 'SELECT a.downloads, a.extension, a.title, a.id, a.cid AS cat
		FROM '.WALLPAPERS_TABLE.' AS a
		LEFT JOIN '.WALLPAPERS_RES_RELATION.' AS b ON a.id = b.pid
		LEFT JOIN '.WALLPAPERS_RES.' AS c ON b.resolution_pid = c.id
		LEFT JOIN '.WALLPAPERS_CATS.' AS d ON a.cid = d.id
		WHERE c.resolution_w = '.intval($arg['resolution']['w']).'
		AND c.resolution_h = '.intval($arg['resolution']['h']).' 
		AND a.is_removed = 0
		AND d.is_removed = 0
		AND a.is_verified = 1
		ORDER BY a.timestamp DESC
		LIMIT 32';
	}
	else
	{
		$query = 'SELECT a.downloads, a.extension, a.title, a.id, a.cid AS cat
		FROM '.WALLPAPERS_TABLE.' AS a
		LEFT JOIN '.WALLPAPERS_CATS.' AS b ON a.cid = b.id
		WHERE b.is_removed = 0 
		AND a.is_removed = 0 
		AND a.is_verified = 1
		ORDER BY a.timestamp DESC
		LIMIT 32';
	}

	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	if(mysql_num_rows($result) > 0)
	{
		while($data = mysql_fetch_assoc($result))
		{
			$out .= '<a href="?action=preview&id='.$data['id'].'" title="' . $data['title'] . '">'."\n";
			$out .= '<img src="'.WALLPAPER_URL.$data['id'].'_thumb.'.$data['extension'].'" alt="'.$data['title'].'" />'."\n";
			$out .= '</a>'."\n";
		}
	}
	else
	{
		$out .= '<p>Inga bilder tillgängliga. Prova att ändra visningsläge (titta lite högre upp).</p>'."\n";
	}
	
	$out .= '<br style="clear: both;" />'."\n";
	
	return $out;
}

function wallpapers_action_view_cat($arg)
{
	$out = '';

	$cat = isset($_GET['cat']) ? $_GET['cat'] : 0;
	$query = 'SELECT title FROM '.WALLPAPERS_CATS.' WHERE id = '.$cat.' AND is_removed = 0 LIMIT 1';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	
	if(mysql_num_rows($result) > 0)
	{
		$data = mysql_fetch_assoc($result);
		
		$out .= '<h1>'.$data['title'].'</h1>';
	
		if($arg['show_all_res'])
		{
		$query = 'SELECT a.downloads, a.extension, a.title, a.id 
		FROM '.WALLPAPERS_TABLE.' AS a 
		LEFT JOIN '.WALLPAPERS_CATS.' AS b ON a.cid = b.id
		LEFT JOIN '.WALLPAPERS_RES_RELATION.' AS c ON a.id = c.pid
		LEFT JOIN '.WALLPAPERS_RES.' AS d ON c.resolution_pid = d.id
		WHERE d.resolution_w = '.intval($arg['resolution']['w']).'
		AND d.resolution_h = '.intval($arg['resolution']['h']).' 
		AND b.id = '.intval($_GET['cat']).'
		AND b.is_removed = 0
		AND a.is_removed = 0
		AND a.is_verified = 1';
		}
		else
		{
		$query = 'SELECT a.downloads, a.extension, a.title, a.id 
		FROM '.WALLPAPERS_TABLE.' AS a 
		LEFT JOIN '.WALLPAPERS_CATS.' AS b ON a.cid = b.id
		WHERE b.id = '.intval($_GET['cat']).'
		AND b.is_removed = 0
		AND a.is_removed = 0
		AND a.is_verified = 1';		
		}
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		if(mysql_num_rows($result) > 0)
		{
			while($data = mysql_fetch_assoc($result))
			{
				$out .= '<a href="?action=preview&id='.$data['id'].'" title="' . $data['title'] . '">'."\n";
				$out .= '<img src="'.WALLPAPER_URL.$data['id'].'_thumb.'.$data['extension'].'" alt="'.$data['title'].'" />'."\n";
				$out .= '</a>'."\n";
			}
		}
		else
		{
			$out .= '<p>Inga bilder hittades</p>';
		}
		$out .= '<br style="clear: both;" />'."\n";
	}
	else
	{
		$out .= '<p>Kategorin hittades inte.</p>';
	}

	return $out;
}

function wallpapers_action_preview($arg)
{
	$out = '';
	
	$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);
	$wallpapers = wallpapers_fetch(array('id'=>$id, 'limit'=>1));
	$cat = $wallpapers[0]['cid'];
	
	$query = 'SELECT a.title, a.extension
	FROM '.WALLPAPERS_TABLE.' AS a 
	LEFT JOIN '.WALLPAPERS_CATS.' AS d ON a.cid = d.id
	WHERE a.id = '.$id.' 
	AND a.is_removed = 0 
	AND d.is_removed = 0
	AND a.is_verified = 1
	LIMIT 1';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	if(mysql_num_rows($result) > 0)
	{
	$data = mysql_fetch_assoc($result);
	
	//next wallpaper
	$query = 'SELECT id FROM `'.WALLPAPERS_TABLE.'` WHERE id > '.$id.' AND cid = '.$cat.' AND is_removed = 0 AND is_verified = 1 LIMIT 1';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$next = (mysql_num_rows($result) > 0 ? mysql_fetch_assoc($result) : false);
	$next = $next['id'];

	//previous wallpaper
	$query = 'SELECT id FROM `'.WALLPAPERS_TABLE.'` WHERE id < '.$id.' AND cid = '.$cat.' AND is_removed = 0 AND is_verified = 1 ORDER BY id DESC LIMIT 1';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$previous = (mysql_num_rows($result) > 0 ? mysql_fetch_assoc($result) : false);
	$previous = $previous['id'];
	echo '<br />'."\n";

	//for the admins later on
	$imagetitle = $data['title'];
	
	$out .= '<h1 id="wallpaper_header">'.$data['title'].'</h1>'."\n";
	$out .= '<img src="'.WALLPAPER_URL.$id.'_preview.'.$data['extension'].'" id="wallpaper_preview" />'."\n";
	
	$out .= '<div>'."\n";
	if($previous)
	{
		$out .= '<input type="button" value="&laquo; Föregående" class="button" id="wallpaper_prev" onclick="document.location.href = \'?action=preview&id='.$previous.'\';" />'."\n";
	}
	
	if($next)
	{
		$out .= '<input type="button" value="Nästa &raquo;" class="button" onclick="document.location.href = \'?action=preview&id='.$next.'\';" style="float:right;" />'."\n";
	}
	$out .= '<br style="clear:both;" />'."\n";
	$out .= '<br />'."\n";
	$out .= '</div>'."\n";

	$out .= '<div id="wallpaper_resolutions">'."\n";
	$out .= rounded_corners_top(array('color' => 'orange'));
	$out .= '<h2>Ladda hem bakgrundsbilden</h2>'."\n";
	$out .= '<ul>'."\n";

	$query = 'SELECT a.id, a.resolution_w AS width, a.resolution_h AS height 
	FROM '.WALLPAPERS_RES.' AS a 
	LEFT JOIN '.WALLPAPERS_RES_RELATION.' AS b ON b.resolution_pid = a.id 
	LEFT JOIN '.WALLPAPERS_TABLE.' AS c ON c.id = b.pid 
	LEFT JOIN '.WALLPAPERS_CATS.' AS d ON c.cid = d.id
	WHERE c.id = '.$id.' 
	AND a.is_removed = 0
	AND b.is_removed = 0
	AND c.is_removed = 0
	AND d.is_removed = 0
	AND c.is_verified = 1
	ORDER BY resolution_w, resolution_h ASC
	';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$num_res = mysql_num_rows($result);
	if($num_res > 0)
	{
		while($data = mysql_fetch_assoc($result))
		{
			if($data['width'] == $arg['resolution']['w'] && $data['height'] == $arg['resolution']['h'])
				$same_res = true;
			else
				$same_res = false;
				
			$out .= '<li><a href="download_wallpaper.php?action=download&w='.$data['width'].'&h='.$data['height'].'&id='.$id.'" title="Ladda ner bilden med upplösningen '.$data['width'].' x '.$data['height'].'">'.($same_res ? '<strong>' : '').$data['width'].' x '.$data['height'].($same_res ? '</strong>' : '').'</a>';
			
			if(login_checklogin() && $_SESSION['login']['userlevel'] >= 5)
			{
				$out .= ' [<a href="/admin/wallpapers_admin.php?action=wallpapers&sub_action=delete_res&id='.$id.'&w='.$data['width'].'&h='.$data['height'].'" onclick="return confirm(\'Sure?'.($num_res == 1 ? '\n\n***** OBS! LÄS DETTA *****\n\nEftersom den här upplösningen är den sista kommer även bilden tas bort.\n\nStill sure?' : '').'\')" title="Radera upplösningen">X</a>]';
			}
			
			$out .= '</li>'."\n";
		}
	}
	else
	{
		$out .= 'Inga upplösningar tillgängliga';
	}
	
	$out .= '</ul>'."\n";
	$out .= rounded_corners_bottom();
	$out .= wallpapers_instructions();
	$out .= '</div>
<div class="wallpaper_formalia">

	</div>'."\n";
	$is_image = true;
	$tags = implode(',', $tags);
	}
	else
	{
		$out .= '<h1>Bilden hittades inte</h1>';
		$tags = '';
		$imagetitle = '';
		$is_image = false;
	}

	return array('out'=>$out, 'tags'=>$tags, 'imagetitle'=>$imagetitle, 'is_image'=>$is_image);
}

function wallpapers_action_tags($arg)
{
$out = '';
	echo '<h1>Visar bara bilder med taggen: '.urldecode($_GET['tag']).'</h1>'."\n";
	$query = 'SELECT DISTINCT pid FROM '.WALLPAPERS_TAGS.' WHERE tag  = "'.$_GET['tag'].'" AND is_removed = 0';
	$result = mysql_query($query) or report_sql_report($query, __FILE__, __LINE__);
	if(mysql_num_rows($result) > 0)
	{
		$i = 0;
		while($data = mysql_fetch_assoc($result))
		{
			if($arg['show_all_res'])
			{
			$query = 'SELECT a.downloads, a.extension, a.title, a.id, a.cid 
			FROM '.WALLPAPERS_TABLE.' AS a 
			LEFT JOIN '.WALLPAPERS_CATS.' AS b ON a.cid = b.id
			LEFT JOIN '.WALLPAPERS_RES_RELATION.' AS c ON a.id = c.pid
			LEFT JOIN '.WALLPAPERS_RES.' AS d ON c.resolution_pid = d.id
			WHERE a.id = '.$data['pid'].' 
			AND d.resolution_w = '.intval($arg['resolution']['w']).'
			AND d.resolution_h = '.intval($arg['resolution']['h']).' 
			AND a.is_removed = 0
			AND b.is_removed = 0
			AND c.is_removed = 0
			AND d.is_removed = 0
			AND a.is_verified = 1';
			}
			else
			{
			$query = 'SELECT a.downloads, a.extension, a.title, a.id, a.cid 
			FROM '.WALLPAPERS_TABLE.' AS a 
			LEFT JOIN '.WALLPAPERS_CATS.' AS b ON a.cid = b.id
			WHERE a.id = '.$data['pid'].' 
			AND a.is_removed = 0
			AND b.is_removed = 0
			AND a.is_verified = 1';
			}
			
			$result_imgs = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			if(mysql_num_rows($result_imgs) > 0)
			{
				while($data_imgs = mysql_fetch_assoc($result_imgs))
				{
					$out .= '<a href="?action=preview&id='.$data_imgs['id'].'" title="' . $data_imgs['title'] . '">'."\n";
					$out .= '<img src="'.WALLPAPER_URL.$data_imgs['id'].'_thumb.'.$data_imgs['extension'].'" alt="'.$data_imgs['title'].'" />'."\n";
					$out .= '</a>'."\n";
				}
			}
			else
			{
				if($i == 0)
				{
					$out .= '<p>Inga bilder hittades</p>';
				}
			}
			$i++;		
		}
		$out .= '<br style="clear:both;" />';
		
	}
	else
	{
		$out .= '<p>Inga bilder hittades!</p>';
	}

	return $out;	
}

function get_cats($val, $level = 0, $id = false)
{ 
   $query = 'SELECT title, id FROM '.WALLPAPERS_CATS.' WHERE pid = '.$val.' AND is_removed = 0';
   $result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE);

	$out = '';
	while($row = mysql_fetch_assoc($result))
	{
        if( $row['title'] != '' ){ 
            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) ); 
          	$val = $row['id'];
            $out .= "\t".'<option value="'.$val.'"'.(is_numeric($id) && $id == $val ? ' selected="selected"' : '').'>'.$spaces.$row['title'].'</option>'."\n"; 
            $out .= get_cats( $val, ($level+1), $id);
        }
    }

   return $out; 
}

function writecookie_users_res($location)
{
	$out = '<script type="text/javascript">'."\n";
	$out .= 'writeCookie_users_res();'."\n";
	$out .= 'function writeCookie_users_res()'."\n";
	$out .= '{'."\n";
	$out .= 'var today = new Date();'."\n";
	$out .= 'var the_date = new Date("December 31, 2023");'."\n";
	$out .= 'var the_cookie_date = the_date.toGMTString();'."\n";
	$out .= 'var the_cookie = "users_resolution="+ screen.width +"x"+ screen.height;'."\n";
	$out .= 'var the_cookie = the_cookie + ";expires=" + the_cookie_date;'."\n";
	$out .= 'document.cookie=the_cookie;'."\n";
	$out .= 'document.location.href = \''.$location.'\';'."\n";
	$out .= '}'."\n";
	$out .= '</script>'."\n";

	return $out;
}

function writecookie_all_res($location)
{
	$out = '<script type="text/javascript">'."\n";
	$out .= 'writeCookie_all_res();'."\n";
	$out .= 'function writeCookie_all_res()'."\n";
	$out .= '{'."\n";
	$out .= 'var today = new Date();'."\n";
	$out .= 'var the_date = new Date("December 31, 2023");'."\n";
	$out .= 'var the_cookie_date = the_date.toGMTString();'."\n";
	$out .= 'var the_cookie = "users_resolution_all=true";'."\n";
	$out .= 'var the_cookie = the_cookie + ";expires=" + the_cookie_date;'."\n";
	$out .= 'document.cookie=the_cookie'."\n";
	$out .= 'document.location.href = \''.$location.'\';'."\n";
	$out .= '}'."\n";
	$out .= '</script>'."\n";
	return $out;
}
?>