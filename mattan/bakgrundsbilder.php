<?php
	/* OPEN_SOURCE */
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('mattan', 'bakgrundsbilder');
	$ui_options['title'] = 'Bakgrundsbilder på Hamsterpaj.net';
	$ui_options['stylesheets'][] = 'wallpapers.css';

	ui_top($ui_options);

	define('WALLPAPER_URL', IMAGE_URL . 'wallpapers/');

	function list_wallpapers($tag = null)
	{
		if($tag != null)
		{
			echo '<h1>Visar bara: ' . $tag . '</h1>';
		}
		$query = 'SELECT DISTINCT w.id, w.title, w.timestamp, w.downloads FROM wallpapers AS w';
		$query .= ($tag != null) ? ', wallpaper_tags AS wt WHERE wt.tag = "' . $tag. '" AND w.id = wt.wallpaper' : '';
		$query .= ' ORDER BY id DESC';
		$query .= ($tag != null) ? '' : ' LIMIT 50';
		
		$result = mysql_query($query) or die(report_sql_error($query));
		while($data = mysql_fetch_assoc($result))
		{
			echo '<div class="wallpaper_container">' . "\n";
			echo '<h3>' . $data['title'] . '</h3>';
			echo '<a href="?action=preview&id=' . $data['id'] . '">' . "\n";
			echo '<img src="' . WALLPAPER_URL . $data['id'] . '_thumb.jpg" />' . "\n";
			echo '</a>' . "\n";
			echo '<span class="downloads">' . $data['downloads'] . ' nedladdningar</span>' . "\n";
			echo '</div>' . "\n";
		}
		echo '<br style="clear: both;" />' . "\n";
		// treasure_item borttaget, renderade fatal error.
		//treasure_item(17);
	}
	
	function preview_wallpaper($id)
	{
		$query = 'SELECT w.*, GROUP_CONCAT(wt.tag) AS tags, wl.license AS license_text, wl.title AS license_title, wa.title AS author_title, ';
		$query .= 'wa.author AS author_text ';
		$query .= 'FROM wallpapers AS w, wallpaper_licenses AS wl, wallpaper_authors AS wa, wallpaper_tags AS wt ';
		$query .= 'WHERE w.id = "' . $id . '" AND wl.id = w.license AND wa.id = w.author AND wt.wallpaper = w.id ';
		$query .= 'GROUP BY w.id';
		
		$result = mysql_query($query) or die(report_sql_error($query));
		$data = mysql_fetch_assoc($result);
		
		echo '<input type="button" value="&laquo; Tillbaks" class="button" onclick="history.go(-1);" style="float: right;" />' . "\n";
		echo '<h1>' . $data['title'] . '</h1>' . "\n";
		echo '<img src="' . WALLPAPER_URL . $id . '_preview.jpg" id="wallpaper_preview" />' . "\n";
		
		echo '<h4>Taggad som</h4>' . "\n";
		echo '<ul id="wallpaper_tags">' . "\n";
		$tags = explode(',', $data['tags']);
		foreach($tags AS $tag)
		{
			echo '<li><a href="?action=view_category&tag=' . $tag . '">' . $tag . '</a></li>'. "\n";
		}
		echo '</ul>' . "\n";
			
		echo '<div id="wallpaper_resolutions">' . "\n";
		echo '<h2>Ladda hem bakgrundsbilden</h2>' . "\n";
		echo '<ul>' . "\n";
		
		$resolutions[2000] = '2000 x 1600';
		$resolutions[1600] = '1600 x 1200';
		$resolutions[1280] = '1280 x 1024';
		$resolutions[1024] = '1024 x 768';
		$resolutions[800] = '800 x 600';
		
		foreach($resolutions AS $resolution => $label)
		{
			if($data[$resolution] == 1)
			{
				echo '<li><a href="wallpaper_download.php?id=' . $id . '&resolution=' . $resolution . '">' . $label . '</a></li>' . "\n";
			}
		}
		echo '</ul>' . "\n";
		echo '<h3>Så här byter du bakgrundsbild i Windows</h3>' . "\n";
		echo '<ol class="wallpaper_download_instructions">' . "\n";
		echo '<li>Högerklicka på den upplösning som passar din skärm (om du inte vet, välj 1280 x 1024)</li>' . "\n";
		echo '<li>Spara bilden i en mapp på din dator, kanske "Mina Dokument"</li>' . "\n";
		echo '<li>Öppna bilden, högerklicka på den och välj "Använd som bakgrundsbild"</li>' . "\n";
		echo '</ol>' . "\n";	
		echo '</div>' . "\n";
		
		echo '<div class="wallpaper_formalia">' . "\n";
		echo '<h3>Licens och upphovsrätt</h3>' . "\n";
		echo '<div class="license">' . "\n";
		echo '<h4>' . $data['license_title'] . '</h4>' . "\n";
		echo '<p>' . $data['license_text'] . '</h4>' . "\n";
		echo '</div>' . "\n";
		
		echo '<div class="author">' . "\n";
		echo '<h4>' . $data['author_title'] . '</h4>' . "\n";
		echo '<p>' . $data['author_text'] . '</p>' . "\n";
		echo '</div>' . "\n";
		
		echo '</div>' . "\n";
		
	}
	
	function wallpaper_add_form()
	{
		echo '<div class="wallpaper_admin">' . "\n";
		echo '<h2>Lägg till ny bakgrundsbild</h2>' . "\n";
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?action=new_wallpaper" method="post">' . "\n";
		echo '<h5>Namn</h5>' . "\n";
		echo '<input type="text" class="textbox" name="title" />' . "\n";
		echo '<h5>Licens</h5>' . "\n";
		echo '<select name="license">' . "\n";
		$query = 'SELECT * FROM wallpaper_licenses ORDER BY id ASC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		while($data = mysql_fetch_assoc($result))
		{
			echo '<option value="' . $data['id'] . '">' . $data['title'] . '</option>' . "\n";
		}
		echo '</select>' . "\n";
		echo '<h5>Upphovsrättsinnehavare</h5>' . "\n";
		echo '<select name="author">' . "\n";
		$query = 'SELECT * FROM wallpaper_authors ORDER BY id ASC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		while($data = mysql_fetch_assoc($result))
		{
			echo '<option value="' . $data['id'] . '">' . $data['title'] . '</option>' . "\n";
		}		
		echo '</select>' . "\n";
		echo '<fieldset>' . "\n";
		echo '<legend>Tillgängliga upplösningar</legend>' . "\n";
		foreach(array(1600, 1280, 1024, 800) AS $resolution)
		{
			echo '<input type="checkbox" name="' . $resolution . '" value="true" id="wallpaper_resolution_' . $resolution . '" />' . "\n";
			echo '<label for="wallpaper_resolution_' . $resolution . '" />' . $resolution . '</label>' . "\n";
		}
		echo '</fieldset>' . "\n";
		echo '<h5>Tags (kommaseparerade)</h5>' . "\n";
		echo '<input type="text" name="tags"  class="textbox" />' . "\n";
		echo '<input type="submit" value="Lägg till" class="button" />' . "\n";
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}
	
	function wallpaper_add($data)
	{
		$query = 'INSERT INTO wallpapers (title, license, author, timestamp, `1600`, `1280`, `1024`, `800`) ';
		$query .= 'VALUES("' . $data['title'] . '", "' . $data['license'] . '", "' . $data['author'] . '", UNIX_TIMESTAMP(), ';
		$query .= ($data['1600'] == true) ? '1, ' : '0, ';
		$query .= ($data['1280'] == true) ? '1, ' : '0, ';
		$query .= ($data['1024'] == true) ? '1, ' : '0, ';
		$query .= ($data['800'] == true)  ? '1)' : '0)';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$id = mysql_insert_id();
		
		$tags = explode(',', $data['tags']);
		foreach($tags AS $tag)
		{
			$query = 'INSERT INTO wallpaper_tags (wallpaper, tag) VALUES("' . $id . '", "' . trim($tag) . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
		echo '<h1>BIldens ID-nummer: ' . $id . '</h1>';
		list_wallpapers();
	}
	
	
	switch($_GET['action'])
	{
		case 'download':
		
			break;
		case 'preview':
			preview_wallpaper($_GET['id']);
			break;
		case 'view_category':
			list_wallpapers($_GET['tag']);
			break;
		case 'new_wallpaper':
			if(is_privilegied('backgrounds_admin'))
			{
				wallpaper_add($_POST);
			}
			break;
		default:
			list_wallpapers();
			break;
	}

	if(is_privilegied('backgrounds_admin'))
	{
		wallpaper_add_form();
	}

	ui_bottom();
?>


