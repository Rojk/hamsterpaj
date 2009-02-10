<?php

/*
	Tables being used in this file:
	WALLPAPERS_TABLE
	WALLPAPERS_CATS
	WALLPAPERS_RES
	WALLPAPERS_RES_RELATION
	WALLPAPERS_AUTHORS
	WALLPAPERS_LICENSE
*/
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'wallpaper_admin.lib.php');

	$ui_options['title'] = 'Bakgrundsbilder | Admin | Hamsterpaj.net';
	$ui_options['menu_path'] = array('admin', 'wallpapers');
	$ui_options['stylesheets'][] = 'rounded_corners_tabs.css';

	//only include if we're adding a wallpaper
	if(isset($_GET['action']) && $_GET['action'] == 'verify_wallpaper')
	{
		$ui_options['stylesheets'][] = 'wallpapers_verify.css';
		$ui_options['javascripts'][] = 'wallpapers_verify.js';
	}

	ui_top($ui_options);

$id = (isset($_GET['id']) ? $_GET['id'] : 0);
$cat = (isset($_GET['cat']) ? $_GET['cat'] : 0);

	if(!is_privilegied('backgrounds_admin'))
	{
		echo 'Du har inga rättigheter hit. Buhu :(';
		exit;
	}

		echo '<h1>Bakgrundsbilder - administration</h1>';
		echo wallpapers_admin_menu_list($_GET['action']);
		echo '<br style="clear:both;" />'."\n";
		switch(isset($_GET['action']) ? $_GET['action'] : 'home')
		{
			case 'home':
				echo '<h2>Välj något att göra där uppe.</h2>'."\n";
				$query = 'SELECT NULL FROM '.WALLPAPERS_TABLE.' WHERE license IS NOT NULL AND is_verified = 0';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				$left_to_validate = mysql_num_rows($result);
				echo 'Det finns <strong>'.$left_to_validate.'</strong> bilder att validera'."\n";
				//end home
				break;
			case 'view_cat':
				if(isset($_POST['submit']))
				{
				$cat = (isset($_GET['cat']) ? $_GET['cat'] : 0);
				$id = (isset($_GET['id']) ? $_GET['id'] : 0);
					switch(isset($_GET['sub_action']) ? $_GET['sub_action'] : 'empty')
					{
						case 'empty':
							die('Ingen sub_action bestämd.');
							break;
						case 'add_cat':
						$query = 'INSERT INTO '.WALLPAPERS_CATS.'(title, pid) VALUES("'.$_POST['title'].'", '.$cat.')';
							mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							jscript_location('?action=view_cat&cat='.$cat);
							break;
						case 'edit_cat':
							$query = 'UPDATE '.WALLPAPERS_CATS.' SET title = "'.$_POST['title'].'", pid = '.intval($_POST['owner']).' WHERE id = '.$cat.' LIMIT 1';
							mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							jscript_location('?action=view_cat&cat='.$cat);
							break;
					}

				}
				else
				{
					switch(isset($_GET['sub_action']) ? $_GET['sub_action'] : 'home')
					{
						case 'home':
						echo '<h2>Alla kategorier</h2>';
						$query = 'SELECT id, title, pid FROM '.WALLPAPERS_CATS.' WHERE pid = '.$cat.' AND is_removed = '.(isset($_GET['dead']) && $_GET['dead'] == 'true' ? 1 : 0).' ORDER BY id ASC';

						if(!isset($_GET['dead']) || $_GET['dead'] == 'false')
						{
							echo '<a href="?action=view_cat&cat='.$cat.'&dead=true" title="Se alla borttagna kategorier">Se alla borttagna kategorier</a>'."\n";
						}
						else
						{
							echo '<a href="?action=view_cat&cat='.$cat.'&dead=false" title="Se alla tillgängliga kategorier">Se alla tillgängliga kategorier</a>'."\n";
						}

						$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
						if(mysql_num_rows($result) > 0)
						{
							echo '<ul>';
							while($data = mysql_fetch_assoc($result))
							{
								echo '<li><a href="?action=view_cat&cat='.$data['id'].'" title="Gå djupare i kategorin">'.$data['title'].'</a> [<a href="?action=view_cat&sub_action=edit_cat&cat='.$data['id'].'" title="Ändra kategorin">Ändra</a>] [<a href="?action=view_cat&sub_action='.(!isset($_GET['dead']) || $_GET['dead'] == 'false' ? '' : 'un').'delete_cat&id='.$data['id'].'" title="'.(!isset($_GET['dead']) || $_GET['dead'] == 'false' ? 'Radera' : 'Återuppliva').' upplösningen" onclick="return confirm(\'Sure?\');">'.(!isset($_GET['dead']) || $_GET['dead'] == 'false' ? 'Radera' : 'Återuppliva').'</a>]</li>'."\n";
							}
							echo '</ul>';
						}
						else
						{
							echo '<p>Inga kategorier här!</p>';
						}
							echo '<form action="?action=view_cat&sub_action=add_cat&cat='.$cat.'" method="post">
							<h3>Lägg till kategori i <em>denna</em> kategori</h3>
							<label for="title">Title</label><input type="text" name="title" id="title" />
							<input type="submit" name="submit" value="Lägg till" />
							</form>';
							break;
						case 'edit_cat':
							echo '<h2>Redigera kategori</h2>';
							$query = 'SELECT id, title, pid FROM '.WALLPAPERS_CATS.' WHERE id = '.$cat.' LIMIT 1';
							$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							$data = mysql_fetch_assoc($result);
							echo '<form action="?action=view_cat&sub_action=edit_cat&cat='.$data['id'].'" method="post">
							<label for="title">Titel:</label> <input type="text" name="title" id="title" value="'.$data['title'].'" />
							<br />
							<label for="owner">Ägarkategor</label> <input type="text" name="owner" id="owner" value="'.$data['pid'].'" /> (0 är högsta nivån)
							<br />
							<input type="submit" name="submit" value="Spara" />
							</form>';
							break;
						case 'delete_cat':
							$query = 'UPDATE '.WALLPAPERS_CATS.' SET is_removed = 1 WHERE id = '.$id;
							mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							jscript_location('?action=view_cat');
							break;
						case 'undelete_cat':
							$query = 'UPDATE '.WALLPAPERS_CATS.' SET is_removed = 0 WHERE id = '.$id;
							mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							jscript_location('?action=view_cat');
							break;
						}
				}
				//end view_cat
				break;
			case 'verify_wallpaper':
					echo wallpapers_admin_wallpapers_verify_list();
				break;
			case 'view_res':
				if(isset($_POST['submit']))
				{
					switch(isset($_GET['sub_action']) ? $_GET['sub_action'] : 'empty')
					{
						case 'empty':
							die('Ingen sub_action bestämd.');
							break;
						case 'edit_res':
							$w = intval($_POST['width']);
							$h = intval($_POST['height']);
							$query = 'UPDATE '.WALLPAPERS_RES.' SET resolution_w = '.$w.', resolution_h = '.$h.', scale = '.round($w/$h, 2).' 							WHERE id = '.$id.' LIMIT 1';
							mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							jscript_alert('Uppdaterat! Går tillbaka till alla upplösningar...');
							jscript_location('?action=view_res');							
							break;
						case 'add_res':
							$w = intval($_POST['width']);
							$h = intval($_POST['height']);
							$query = 'INSERT INTO '.WALLPAPERS_RES.'(resolution_w, resolution_h, scale) VALUES('.$w.', '.$h.', '.round($w/$h, 2).')';
							mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							jscript_location('?action=view_res');
							break;
					}

				}
				else
				{
				$id = (isset($_GET['id']) ? $_GET['id'] : 0);
					switch(isset($_GET['sub_action']) ? $_GET['sub_action'] : 'home')
					{
						case 'home':
						$query = 'SELECT id, resolution_w AS w, resolution_h AS h, scale, is_removed FROM '.WALLPAPERS_RES.' WHERE is_removed =  						'.(isset($_GET['dead']) && $_GET['dead'] == 'true' ? 1 : 0).' ORDER BY scale, resolution_w ASC';
						$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
						if(mysql_num_rows($result) > 0)
						{
						$last_scale = 0;
						echo '<h2>Alla upplösningar</h2>'."\n";
						if(!isset($_GET['dead']) || $_GET['dead'] == 'false')
						{
							echo '<a href="?action=view_res&dead=true" title="Se alla borttagna upplösningar">Se alla borttagna upplösningar</a>'."\n";
						}
						else
						{
							echo '<a href="?action=view_res&dead=false" title="Se alla tillgängliga upplösningar">Se alla tillgängliga upplösningar</a>'."\n";
						}
						echo '<ul>';
							while($data = mysql_fetch_assoc($result))
							{
								if($last_scale != $data['scale'])
								{
									echo '<p style="margin-left:-2.3em;padding-left:0;">Skala '.$data['scale'].'</p>';
								}
								echo '<li>'.$data['w'].' x '.$data['h'].' [<a href="?action=view_res&sub_action=edit_res&id='.$data['id'].'" title="Ändra upplösningen">Ändra</a>] [<a href="?action=view_res&sub_action='.(!isset($_GET['dead']) || $_GET['dead'] == 'false' ? '' : 'un').'delete_res&id='.$data['id'].'" title="'.(!isset($_GET['dead']) || $_GET['dead'] == 'false' ? 'Radera' : 'Återuppliva').' upplösningen" onclick="return confirm(\'Sure?\');">'.(!isset($_GET['dead']) || $_GET['dead'] == 'false' ? 'Radera' : 'Återuppliva').'</a>]</li>'."\n";
								$last_scale = $data['scale'];
							}
						echo '</ul>';
						}
						else
						{
							echo 'Inga upplösningar här!';
						}
							echo '<form action="?action=view_res&sub_action=add_res" method="post">
							<h3>Lägg till upplösning</h3>
							<label for="w">Bredd</label> <input type="text" name="width" id="w" /><br />
							<label for="h">Höjd</label> <input type="text" name="height" id="h" /><br />
							<input type="submit" name="submit" value="Lägg till" />
							</form>';
							break;
						case 'edit_res':
							$query = 'SELECT id, resolution_w AS w, resolution_h AS h, scale, is_removed FROM '.WALLPAPERS_RES.' WHERE id = '.$id.' LIMIT 1';
							$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							$data = mysql_fetch_assoc($result);
							echo '
							<h2>Redigera upplösning</h2>
							<form action="?action=view_res&sub_action=edit_res&id='.$data['id'].'" method="post">
							<label for="width">Bredd:</label> <input type="text" name="width" id="width" value="'.$data['w'].'" />
							<br />
							<label for="height">Höjd:</label> <input type="text" name="height" id="height" value="'.$data['h'].'" />
							<br />';
							
							if($data['is_removed'])
							{
								echo 'Upplösningen är borttagen. <a href="?action=view_res&sub_action=undelete_res&id='.$data['id'].'" title="Återuppliva upplösning" onclick="return confirm(\'Sure?\');">Återuppliva</a>';
							}
							else
							{
								echo '<a href="?action=view_res&sub_action=delete_res&id='.$data['id'].'" title="Radera upplösningen" onclick="return confirm(\'Sure?\');">Radera</a>';
							}
							
							echo '<br />
							<input type="submit" name="submit" value="Spara" />
							</form>';
							break;
						case 'delete_res':
							$query = 'UPDATE '.WALLPAPERS_RES.' SET is_removed = 1 WHERE id = '.$id;
							mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							jscript_alert('Raderat! Går tillbaka till alla upplösningar...');
							jscript_location('?action=view_res');
							break;
						case 'undelete_res':
							$query = 'UPDATE '.WALLPAPERS_RES.' SET is_removed = 0 WHERE id = '.$id;
							mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
							jscript_location('?action=view_res');
							break;
					}
				}
				//end view_res
			break;

			case 'wallpapers':
				//delete the image
				$query = 'SELECT extension FROM '.WALLPAPERS_TABLE.' WHERE id = '.$id;
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				$data = mysql_fetch_assoc($result);
				$w = (isset($_GET['w']) ? intval($_GET['w']) : 0);
				$h = (isset($_GET['h']) ? intval($_GET['h']) : 0);
				$ext = $data['extension'];
				if(file_exists(UPLOAD_PATH.$id.'_'.$w.'_'.$h.'.'.$ext))
				{
					unlink(UPLOAD_PATH.$id.'_'.$w.'_'.$h.'.'.$ext);
				}

				//update db
				$query = 'UPDATE '.WALLPAPERS_RES_RELATION.' SET is_removed = 1 
						WHERE pid = '.$id.' AND resolution_pid = (SELECT id FROM '.WALLPAPERS_RES.' WHERE resolution_w = '.$w.' AND resolution_h = '.$h.')';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

				//if it's the last resolution delete wallpaper
				$query = 'SELECT NULL FROM '.WALLPAPERS_RES_RELATION.' WHERE is_removed = 0 AND pid = '.$id;//is there more resoltions?
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				if(mysql_num_rows($result) == 0)
				{
					//delete wallpaper
					$query = 'UPDATE '.WALLPAPERS_TABLE.' SET is_removed = 1 WHERE id = '.$id;
					mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					
					if(file_exists(UPLOAD_PATH.$id.'_preview.'.$ext))
					unlink(UPLOAD_PATH.$id.'_preview.'.$ext);
					
					if(file_exists(UPLOAD_PATH.$id.'_thumb.'.$ext))
					unlink(UPLOAD_PATH.$id.'_thumb.'.$ext);
				
				}
				jscript_location('?action=home');
				//end wallpapers
				break;
		}
	echo rounded_corners_tabs_bottom();
	ui_bottom();
?>