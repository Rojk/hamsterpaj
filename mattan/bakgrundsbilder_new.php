<?php
/*
	Tables being used in this file:
	WALLPAPERS_TABLE
	WALLPAPERS_CATS
	WALLPAPERS_RES
	WALLPAPERS_RES_RELATION
	WALLPAPERS_TAGS
	
	##### KNOWN BUGS #####
	
	** If there are wallpapers in the subcategories, it won't count them. 
		This is because a recursive function *might* allocate too much memory, 
		i.e. not good. Therefor, only the images in the topcategori (the one being shown) will be counted.

	##### END KNOWN BUGS #####

*/
	require('../include/core/common.php');

$cat = (isset($_GET['cat']) ? intval($_GET['cat']) : 0);
$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);	

//this must be above ui_top(); header()-issues
if(isset($_GET['action'], $_GET['val']) && $_GET['action'] == 'showall')
{
	$val = ($_GET['val'] == 'false' ? 'false' : 'true');
	setcookie('users_resolution_all', $val);
	jscript_alert('Uppdaterat');
	jscript_location('"+document.referrer+"');
}

	require(PATHS_INCLUDE.'libraries/wallpaper.lib.php');
	$javascript_cookie_location = 'bakgrundsbilder_new.php';

	require('../include/core/common.php');
	
	$ui_options['title'] = 'Bakgrundsbilder på Hamsterpaj';
	$ui_options['menu_path'] = array('mattan', 'bakgrundsbilder');
	$ui_options['stylesheets'][] = 'wallpapers.css';

	ui_top($ui_options);




if(isset($_COOKIE["users_resolution"]))
{
	$screen_res = $_COOKIE["users_resolution"];
	$screen_res = split('x', $screen_res);
	$resolution['w'] = $screen_res[0];
	$resolution['h'] = $screen_res[1];
}
else //means cookie is not found set it using Javascript
{
	echo writecookie_users_res($javascript_cookie_location);
}

if(isset($_COOKIE["users_resolution_all"]))
{
	$show_all_res = ($_COOKIE["users_resolution_all"] == "true" ? true : false);
}
else //means cookie is not found set it using Javascript
{
	echo writecookie_all_res($javascript_cookie_location);
}


echo "\n".'<div class="warning_notice">'."\n";
if(!$show_all_res)
{
	echo '<strong>Visa endast bilder som passar din upplösning? Klicka <a href="?action=showall&val=true" title="Klicka här om du vill visa bilder som bara passar din upplösning">här</a>.</strong>'."\n";	
}
else
{
	echo '<img src="'.WALLPAPER_URL.'warning_sign.png" class="left" alt="OBS!" />
	<p class="left warning_text">Kan du inte se några bilder eller bara några få? Klicka <a href="?action=showall&val=false" title="Klicka här om du vill visa alla bilder">här</a> för att visa alla bilder.</p>
	<br class="clear" />'."\n";
}
echo '</div>'."\n";

//	echo '<button onclick="document.location=\'bakgrundsbilder_lagg_till.php\';" class="button_150" style="float:right;">Lägg till din bakgrundsbild</button>'."\n";

switch(isset($_GET['action']) ? $_GET['action'] : 'home')
{
case 'home': 
	$arg['resolution'] = $resolution;
	$arg['show_all_res'] = $show_all_res;
	echo wallpapers_action_home($arg);
	break;
case 'view_cat':
	$arg['resolution'] = $resolution;
	$arg['show_all_res'] = $show_all_res;
	echo wallpapers_action_view_cat($arg);
	break;
case 'preview':
	$arg['resolution'] = $resolution;
	$arr = wallpapers_action_preview($arg);
	
	echo $arr['out'];
	
	$imagetitle = $arr['imagetitle'];
	$tags = $arr['tags'];
	$is_image = $arr['is_image'];
	break;
case 'view_tags':
	$arg['resolution'] = $resolution;
	$arg['show_all_res'] = $show_all_res;
	echo wallpapers_action_tags($arg);
	break;
}
?>
<div id="cats">
<?php

//Categories, don't show when previewing an image
if(!isset($_GET['action']) || $_GET['action'] != 'preview')
{
$query = 'SELECT a.id, a.title
 FROM '.WALLPAPERS_CATS.' AS a
 LEFT JOIN '.WALLPAPERS_TABLE.' AS b ON b.cid = a.id
 WHERE a.pid = '.$cat.' AND a.is_removed = 0
 AND (
	SELECT COUNT(*) FROM '.WALLPAPERS_TABLE.' AS c WHERE c.cid = a.id AND c.is_verified = 1
 ) > 0 
 GROUP BY a.id';

	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$bg = 'orange';

	if(mysql_num_rows($result) > 0)
	{
		echo '<h2>Kategorier</h2>'."\n";

		echo '<ul class="wallpaper_categories">'."\n";
		while($data = mysql_fetch_assoc($result))
		{
			$query_all_img = 'SELECT NULL FROM '.WALLPAPERS_TABLE.' WHERE cid = '.$data['id'].' AND is_removed = 0 AND is_verified = 1';
			$result_all_img = mysql_query($query_all_img) or report_sql_error($query_all_img, __FILE__, __LINE__);
			$num_all_img = mysql_num_rows($result_all_img);
			
			if($show_all_res)
			{
				$query_img = 'SELECT NULL 
				FROM '.WALLPAPERS_TABLE.' AS a
				LEFT JOIN '.WALLPAPERS_RES_RELATION.' AS b ON a.id = b.pid
				LEFT JOIN '.WALLPAPERS_RES.' AS c ON b.resolution_pid = c.id
				LEFT JOIN '.WALLPAPERS_CATS.' AS d ON a.cid = d.id
				WHERE c.resolution_w = '.intval($resolution['w']).'
				AND c.resolution_h = '.intval($resolution['h']).'
				AND d.id ='.$data['id'].'
				 AND a.is_removed = 0
				 AND a.is_verified = 1';
				$result_img = mysql_query($query_img) or report_sql_error($query_img, __FILE__, __LINE__);
				$num_img = mysql_num_rows($result_img);
				$num_img_text = '('.$num_img.' av '.$num_all_img.' bild'.($num_all_img == 1 ? '' : 'er').')';
			}
			else
			{
				$num_img_text = $num_all_img.' bild'.($num_all_img == 1 ? '' : 'er');
			}
			
			if($num_all_img > 0)
			{
				$bg = ($bg == 'orange' ? '#7a87ff' : 'orange');
				echo '<li style="background-color: '.$bg.'"><a href="?action=view_cat&cat='.$data['id'].'" title="Se bilder i kategorin '.$data['title'].'">'.$data['title'].' - '.$num_img_text.'</a></li>'."\n";
			}
		}
		echo '</ul>';
	}
/*	else
	{
		echo 'Inga kategorier';
	}
*/
}


	if(is_privilegied('backgrounds_admin'))
	{
		switch(isset($_GET['action']) ? $_GET['action'] : 'home')
		{
			case 'preview':
				if(isset($_POST['title'], $_POST['tags'], $_GET['id']))
				{
					$cat = wallpapers_fetch(array('id'=>intval($_GET['id']), 'limit'=>1));
					$cat = $cat[0]['cid'];
					//update
					$tags = explode(',', $_POST['tags']);
					$clean_tags = array();
					foreach($tags as $tag)
					{
						if($tag != '')
							$clean_tags[] = $tag;
					}
					//title
					$query = 'UPDATE '.WALLPAPERS_TABLE.' SET title = "'.$_POST['title'].'", cid = '.intval($_POST['cat']).' WHERE id = '.intval($_GET['id']);
					mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					//tags
					//delete all tags first
					$query = $query = 'UPDATE '.WALLPAPERS_TAGS.' SET is_removed = 1 WHERE pid = '.intval($_GET['id']);
					mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					//then add the new ones
					foreach($clean_tags as $clean_tag)
					{						
						$query = 'INSERT INTO '.WALLPAPERS_TAGS.'(tag, pid) VALUES("'.$clean_tag.'", '.intval($_GET['id']).')';
						jscript_alert($query);
						mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					}

					jscript_alert('Uppdaterat');
					jscript_go_back();

				}
				else
				{
					if($is_image)
					{
						$cat = wallpapers_fetch(array('id'=>intval($_GET['id']), 'limit'=>1));
						$cat = $cat[0]['cid'];

						echo '<h2>Admin</h2>'."\n";
						echo '<form action="'.$_SERVER['PHP_SELF'].'?action=preview&id='.intval($_GET['id']).'" method="post">'."\n";
						echo '<h5>Titel</h5>
						<input type="text" name="title" value="'.$imagetitle.'" />'."\n";
						echo '<br />'."\n";
						echo '<h5>Taggar (kommaseparerade)</h5>'."\n";
						echo '<input type="text" name="tags" value="'.$tags.'" />'."\n<br />\n";
						echo '<h5>Kategori</h5>'."\n";
						echo '<select name="cat">'."\n";
						echo get_cats(0, 0, $cat);
						echo '</select>'."\n";
						echo '<br />'."\n";
						
						echo '<input type="button" onclick="confirm(\'Sure?\') ? document.location.href=\'?action=delete&id='.intval($_GET['id']).'\' : false;" value="Radera" class="button" />'."\n";
						echo '<input type="submit" value="Spara" />';
					}
				}
				break;
			case 'delete':
				
				$query = 'SELECT c.extension, a.resolution_w AS width, a.resolution_h AS height FROM '.WALLPAPERS_RES.' AS a
				 LEFT JOIN '.WALLPAPERS_RES_RELATION.' AS b ON b.resolution_pid = a.id
				  LEFT JOIN '.WALLPAPERS_TABLE.' AS c ON c.id = b.pid
				   WHERE c.id = '.intval($_GET['id']);
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
				if(mysql_num_rows($result) > 0)
				{					
					while($data = mysql_fetch_assoc($result))
					{
						$unlink[] = intval($_GET['id']).'_'.$data['width'].'_'.$data['height'].'.'.$data['extension'];
						$extension = $data['extension'];
					}

					//delete all enteries in db
					$queries[] = 'UPDATE '.WALLPAPERS_TABLE.' SET is_removed = 1 WHERE id='.intval($_GET['id']).' LIMIT 1';
					$queries[] = 'UPDATE '.WALLPAPERS_RES_RELATION.' SET is_removed = 1 WHERE pid = '.intval($_GET['id']);
					$queries[] = 'UPDATE '.WALLPAPERS_TAGS.' SET is_removed = 1 WHERE pid = '.intval($_GET['id']);
					foreach($queries as $query)
					{
						mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					}
				
					//unlink files too!
					$unlink[] = intval($_GET['id']).'_preview.'.$extension;
					$unlink[] = intval($_GET['id']).'_thumb.'.$extension;
					foreach($unlink as $delete)
					{
						if(file_exists(UPLOAD_PATH.$delete))
						{
							(!unlink(UPLOAD_PATH.$delete) ? jscript_alert('Kunde inte radera '.$delete) : '');
						}
						else
						{
							jscript_alert('Filen '.$delete.' finns inte!');
						}
					}
					jscript_alert('Borttaget. Återvänder till start...');
					jscript_location('bakgrundsbilder.php');
				}
				else
				{
					jscript_alert('Bilden finns inte. Återvänder...');
					jscript_go_back();
				}
				break;
		}

	}
	
?>
</div>
<?php
	ui_bottom();
?>