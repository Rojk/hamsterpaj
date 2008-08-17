<?php
	define(PHOTOS_PER_CATEGORY, 8);

	$query = 'SELECT id, title, position, photos FROM photo_albums WHERE owner = "' . $_SESSION['login']['id'] . '" ORDER BY position ASC LIMIT 8';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if(mysql_num_rows($result) == 0)
	{
		for($i = 1; $i <= 8; $i++)
		{
			$query = 'INSERT INTO photo_albums(owner, position, title) VALUES("' . $_SESSION['login']['id'] . '", ' . $i . ', "Album ' . $i . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			$categories[$i]['title'] = 'Album ' . $i;
			$categories[$i]['photos'] = array();
			$categories[$i]['id'] = mysql_insert_id();
		}
	}
	while($data = mysql_fetch_assoc($result))
	{
		$categories[$data['position']]['title'] = $data['title'];
		$categories[$data['position']]['photos'] = (strlen($data['photos']) > 0) ? explode(',',$data['photos']) : array();
		$categories[$data['position']]['id'] = $data['id'];
	}

	require(PATHS_INCLUDE . 'photoalbum-functions.php');
	$allowed_extensions = array('jpg', 'png', 'gif', 'bmp', 'tiff', 'jpeg');

	switch($_GET['action'])
	{
	case 'upload':
		if($_GET['perform'] == 'true')
		{
			echo '<h2>Uploading...</h2>';
			foreach($_FILES AS $input_name => $file)
			{
				if(strlen($file['name']) < 1)
				{
					continue;
				}
				$extension = strtolower(substr($file['name'], strrpos($file['name'], '.')+1));
				if(!in_array($extension, $allowed_extensions))
				{
					if($extension == 'php')
					{
						to_logfile('notice', __FILE__, __LINE__, 'User tried to upload php file',  $file['name']);
					}
					echo jscript_alert('Filen ' . $file['name'] . ' kunde inte laddas upp, vi tillåter bara dessa filändelser:' .implode(', ', $allowed_extensions));
				}
				else
				{
					if($_POST[$input_name . '_category'] > 0 && $_POST[$input_name . '_category'] < 9 && is_numeric($_POST[$input_name . '_category']))
					{
						$options['category'] = $_POST[$input_name . '_category'];
					}
					if(strlen($_POST[$input_name . '_description']) > 0)
					{
						$options['description'] = $_POST[$input_name . '_description'];
					}
					else
					{
						$options['description'] = null;
					}
					$options['copyright'] = ($_POST[$input_name . '_copyright'] == 'true') ? 'true' : 'false';

					if (is_file($file['tmp_name']))
					{
						$return = photoalbum_upload_photo($file['tmp_name'], $_SESSION['login']['id'], $options);
					}
					else
					{
						$return = array('status' => 'fail', 'reason' => 'Filen laddades inte upp korrekt');
					}
					if($return['status'] != 'success')
					{
						jscript_alert($file['name'] . ': ' . $return['reason']);
					}
				}
			}
			jscript_location('/traffa/profile.php');
		}
		echo '<div class="grey_faded_div">' . "\n";
		echo '<h2>Ladda upp nya bilder</h2>' . "\n";
		echo 'Bilden ska uppfölja följande regler:<br /><br />';

		echo '* Bilden får inte innehålla sexistisk inslag (dock gör någon enkel bh-bild ingen skada)<br />';
		echo '* Inga enorma mängder alkohol (en enstaka ölburk i bakgrunden skadar inte).<br />';
		echo '* Inget som bryter mot svensk lag, är upphovsrättskyddat eller är kränkande för någon person förekommer.<br />';
		echo '* Rasistiska/Nazistiska symboler samt rasismpropaganda är inte tillåtet.<br />';
		echo '* Inte heller något annat som kan uppfattas som stötande.<br /><br />';

		echo 'Tänk på att om en OV/Admin ser bilden och finner den opassande så tar vi bort den.<br /><br />';
		
		echo '<div style="width: 310px; float: left;">' . "\n";
		echo '<form enctype="multipart/form-data" action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=upload&perform=true" method="POST">';
		for($i = 1; $i <= 6; $i++)
		{
			if($i == 4)
			{
				echo '<div style="width: 310px; float: left; margin-left: 5px;">' . "\n";
			}
			echo '<div style="width: 305px; background: white; margin-bottom: 5px; border: 1px solid #cccccc; padding: 2px;">' . "\n";
			echo '<input name="photo_' . $i . '" type="file" id="imagefile" onchange="document.getElementById(\'preview_' . $i . '\').src = \'file://\' + this.value.replace(/\\\\/g,\'/\');"/><br />' . "\n";
			echo '<select name="photo_' . $i . '_category" style="width: 306px;">' . "\n";
			for($j = 1; $j <= 8; $j++)
			{
				echo '<option value="' . $j . '">' . $categories[$j]['title'] . ' (' . (PHOTOS_PER_CATEGORY-count($categories[$j]['photos'])) . ' platser)</option>' . "\n";
			}
			echo '</select><br />' . "\n";
			echo '<textarea name="photo_' . $i . '_description" style="width: 302px; height: 50px;" onclick="if(this.value==\'Skriv din beskrivning här...\'){this.value=\'\'};">Skriv din beskrivning här...</textarea>' . "\n";
			echo '<div style="text-align: right;">Kopieringsskydda bilden: <input type="checkbox" name="photo_' . $i . '_copyright" value="true" /></div>';
			echo '</div>';
			if($i == 3 || $i == 6)
			{
				echo '</div>' . "\n";
			}
		}
		echo '<input type="submit" value="Ladda upp bilder &raquo;" style="float: right;" class="button" />';
		echo 'Ladda inte upp för stora filer - du kan max ladda upp ' . (get_cfg_var('upload_max_filesize')) . 'B stora filer.<br />Format som stöds är ' . implode(', ', $allowed_extensions);
		echo '</div>' . "\n";

		break;
	case 'remove':
		if($_GET['perform'] == 'true')
		{
			foreach($_POST AS $id => $value)
			{
				if(is_numeric($id) && $value == 'remove')
				{
					$remove[] = $id;
				}
			}
			$photoalbum_information['photos'] = $categories[$_GET['category']]['photos'];
			$photoalbum_information['category'] = $_GET['category'];
			if (isset($remove))
			{
				if(photoalbum_remove_photo($remove, $_SESSION['login']['id'], $photoalbum_information))
				{
					$categories[$_GET['category']]['photos'] = array_diff($categories[$_GET['category']]['photos'], $remove);
					echo jscript_alert('Dina bilder togs bort!');
				}
				else
				{
					echo '<p class="error">Dina bilder kunde inte tas bort. Är du säker på att du verkligen försöker ta bort dina egna bilder?</p>';
				}
			}
		}
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=remove&perform=true&category=' . $_GET['category'] . '" method="post">' . "\n";
		foreach($categories[$_GET['category']]['photos'] AS $photo)
		{
			echo '<div style="float: left; margin: 3px;">' . "\n";
			echo '<img src="' . IMAGE_URL . '/images/photoalbums/images_' . round($photo/1000) . '/' . $photo . '_thumb.jpg" style="width: 60px; height: 45px; display: block; border: 1px solid #3f657a;" />' . "\n";
			echo '<div style="text-align: center; width: 100%;"><input type="checkbox" name="' . $photo . '" value="remove" /></div>';
			echo '</div>' . "\n";
		}
		echo '<input type="submit" value="Ta bort markeradebilder &raquo;" class="button" />';
		echo '</form>';
		break;
	case 'move':
		if($_GET['perform'] == 'true')
		{
			foreach($_POST AS $photo => $new_category)
			{
				if($new_category != $_GET['category'])
				{
					if(count($categories[$new_category]['photos']) > PHOTOS_PER_CATEGORY-1)
					{
						echo '<p class="error">Foto #' . $photo . ' kunde inte flyttas till ' . $categories[$new_category]['title'] . ' eftersom det inte finns några ledigaplatser.</p>';
					}
					else
					{
						if(!in_array($photo, $categories[$_GET['category']]['photos']))
						{
							to_logfile('error', __FILE__, __LINE__, 'Tried to move a photo from a category which dont contain the photo', $photo, $new_category);
							die('<p class="error">Ett allvarligt fel har inträffat! Du har försökt att flytta ett foto från en kategori där fotot inte existerar. Den troligaste anledningen till att detta har inträffat är att du har flera fönster med hamsterpaj uppe samtidigt, och i ettannatfönster redan flyttat fotot. <br />En annan tänkbar förklaring är att du sitter och leker med typ curl eller javascript för att försöka hitta säkerhetsluckor.<br />Hursomhelst, inga foton har flyttats och denna sidvisning har stoppats av säkerhetsskäl, det inträffade har även loggats och kommer undersökas av hamsterpajs utvecklare inom kort.</p>');
						}
						else
						{
							$categories[$new_category]['photos'][] = $photo;
							$old_category_key = array_search($photo, $categories[$_GET['category']]['photos']);
							unset($categories[$_GET['category']]['photos'][$old_category_key]); /* Remove the photo from its old cateogry */
							$affected[] = $_GET['category'];
							$affected[] = $new_category;
						}
					}
				}
			}
			$affected = array_unique($affected);
			foreach($affected AS $current)
			{
				$query = 'UPDATE photo_albums SET photos = "' . implode(',', $categories[$current]['photos']) . '" WHERE owner = "' . $_SESSION['login']['id'];
				$query .= '" AND position = "' . $current . '" LIMIT 1';
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			}			
		}
		else
		{
			echo '<div class="grey_faded_div">'  . "\n";
			echo '<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=move&category=' . $_GET['category'] . '&perform=true" method="post">' . "\n";
			echo '<table style="width: 100%;">' . "\n";
			echo '<tr>' . "\n";
			foreach($categories[$_GET['category']]['photos'] AS $row => $photo)
			{
				echo (in_array($row, array(2, 4, 6, 8))) ? '</tr><tr><td>' : '<td>';
				echo '<img src="' . IMAGE_URL . '/images/photoalbums/images_' . round($photo/1000) . '/' . $photo . '_thumb.jpg" style="width: 60px; height: 45px; display: block; border: 1px solid #3f657a;" />' . "\n";
				echo '<select name="' . $photo . '">' . "\n";
				foreach($categories AS $id => $current)
				{
					echo '<option value="' . $id . '"';
					echo ($id == $_GET['category']) ? ' selected="true"' : null;
					echo '>' . $current['title'] . '(' . (PHOTOS_PER_CATEGORY-count($categories[$id]['photos'])) . ' platser)</option>' . "\n";
				}
				echo '</select>' . "\n";
				echo '</td>' . "\n";
			}
			echo '</tr></table>' . "\n";
			echo '<input type="submit" value="Flytta &raquo;" style="clear: left;" />' . "\n";
			echo '</form>' . "\n";
			echo '</div>' . "\n";
		}
		break;
	case 'sort':
	if($_GET['perform'] == 'true')
	{
		$neworder = explode(',', $_POST['order']);
		foreach($neworder AS $current)
		{
			if(!is_numeric($current) || !in_array($current, $categories[$_GET['category']]['photos']))
			{
				die('<p class="error">Ett allvarligt fel har inträffat. Ladda om sidan och försök igen.</p>');
				to_logfile('notice', __FILE__, __LINE__, 'User tried to sort a photo not present in the category!');
			}
		}
		$query = 'UPDATE photo_albums SET photos = "' . $_POST['order'] . '" WHERE owner = "' . $_SESSION['login']['id'] . '" AND position = "' . $_GET['category'] . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		echo jscript_alert('Den nya ordningen har sparats');
		echo jscript_location($_SERVER['PHP_SELF'] . '?id=' . $_GET['id']);
		$categories[$_GET['category']]['photos'] = $neworder;
	}
?>
<script src="/include/jsdragdrop/prototype.js" type="text/javascript"></script>
<script src="/include/jsdragdrop/scriptaculous.js" type="text/javascript"></script>
<style>
  #testlist { 
      list-style-type:none;
      margin:0;
      padding:0;
	  list-style: none;
   }
   #testlist li {
     font:11px Verdana;
     margin:2;
     cursor:move;
	 float: left;
     }
</style>
<?php
	echo '<form name="form" action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=sort&category=' . $_GET['category'] . '&perform=true" method="post" onSubmit="inspect(\'testlist\');">';
?>
<div style="background: #7aa0cf; height: 58px;">
<ul id="testlist">
<?php
	foreach($categories[$_GET['category']]['photos'] AS $current)
	{
		echo '<li itemId="' . $current . '"><img src="' . IMAGE_URL . 'images/photoalbums/images_' . round($current/1000) . '/' . $current . '_thumb.jpg" style="margin: 5px; border: 1px solid #40657f;" /></li>';
	}
?>
</ul>
</div>
<input type="hidden" name="order">
<br />
<input type="submit" value="Spara &raquo;" class="button">
<h3>Instruktioner</h3>
Ta tag i en bild och dra den dit där du vill ha den. Bilderna kan fastna eller hoppa lite lustigt ibland, men då är det bara att försöka igen, ny teknik du vet ;)
<script type="text/javascript" language="javascript">
  Sortable.create('testlist',{ghosting:false,constraint:true})
  
	function order(list) {
		var items = list.getElementsByTagName("li")
		var array = new Array()
		for (var i = 0, n = items.length; i < n; i++) {
			array.push(items[i].getAttribute("itemID"))
		}
		return array.join(',')
	}
	
	function inspect(id) {
		document.form.order.value = order(document.getElementById(id));
	}


</script></form>
<?php
		break;
	case 'organize':
	if($_GET['perform'] == 'true')
	{
		$explosion = explode(',', $_POST['order']);
		for($i = 0; $i < count($explosion); $i++)
		{
			$positions[$explosion[$i]] = $i+1;
		}
		foreach($_POST AS $input => $value)
		{
			print_r($_POST);
			if(is_numeric($input))
			{
				if(strlen(trim($value)) < 2)
				{
					echo jscript_alert('Kunde inte byta namn på kategori ' . $categories[$input]['title'] . ', det nya namnet är för kort.');
				}
				else
				{
					$query = 'UPDATE photo_albums SET title = "' . $value . '", position = "' . $positions[$input] . '" WHERE owner = "' . $_SESSION['login']['id'] . '" AND id ="' . $input . '" LIMIT 1';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				}
			}
		}
		jscript_location('/traffa/profile.php?id=' . $_SESSION['login']['id']);
	}
	echo '<h2>Flytta och byt namn på dina fotoalbum</h2>';
	echo '<form name="form" action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=organize&perform=true" method="post"';
	echo ' onSubmit="inspect(\'testlist\');">';
?>
<script src="/include/jsdragdrop/prototype.js" type="text/javascript"></script>
<script src="/include/jsdragdrop/scriptaculous.js" type="text/javascript"></script>
<style>
	#testlist
	{ 
		list-style-type:none;
		margin: 10px;;
		padding: 0px;
		list-style: none;
	}
	#testlist li
	{
		font: 11px Verdana;
		margin: 2;
		float: left;
		margin-left: 20px;
	}
	#testlist img
	{
		cursor: move;
	}
</style>
<?php
	echo '<ul id="testlist">' . "\n";
		foreach($categories AS $id => $category)
		{
			echo '<li itemId="' . $category['id'] . '">';
			echo '<img src="/images/handtag.jpg" /><br />';
			echo '<input name="' . $category['id'] . '" value="' . $category['title'] . '" type="text" style="width: 100px;" />';
			echo '</li>';
		}
		echo '</ul>';
?>
<input type="hidden" name="order">
<script type="text/javascript" language="javascript">
  Sortable.create('testlist',{ghosting:false,constraint:true})
  
	function order(list) {
		var items = list.getElementsByTagName("li")
		var array = new Array()
		for (var i = 0, n = items.length; i < n; i++) {
			array.push(items[i].getAttribute("itemID"))
		}
		return array.join(',')
	}
	
	function inspect(id) {
		document.form.order.value = order(document.getElementById(id));
	}
</script>
<br /><br /><br /><br />
<input type="submit" value="Spara&raquo;" class="button" />
<p style="clear: both; margin: 10px;">
	Ta tag i handtaget och dra det till höger eller vänster för att flytta fotoalbumet.
</p>
<?php

		break;
	default:
		echo '<h2>Vad vill du göra?</h2>';
		echo '<ul>';

		echo '<li><a href="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=upload">Ladda upp nya bilder</a></li>';

		echo '<li>Ta bort några bilder från ';
		echo '<select onchange="window.location=\'' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=remove&category=\' + this.value;">' . "\n";
		echo '<option>--Välj en kategori--</option>' . "\n";
		foreach($categories AS $category_id => $category)
		{
			echo '<option value="' . $category_id . '">' . $category['title'] . '</option>' . "\n";
		}
		echo '</select></li>' . "\n";
	
		echo '<li>Flytta några bilder från ';
		echo '<select onchange="window.location=\'' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=move&category=\' + this.value;">' . "\n";
		echo '<option>--Välj en kategori--</option>' . "\n";
		foreach($categories AS $category_id => $category)
		{
			echo '<option value="' . $category_id . '">' . $category['title'] . '</option>' . "\n";
		}
		echo '</select></li>' . "\n";

		echo '<li>Sortera bilderna i ';
		echo '<select onchange="window.location=\'' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=sort&category=\' + this.value;">' . "\n";
		echo '<option>--Välj en kategori--</option>' . "\n";
		foreach($categories AS $category_id => $category)
		{
			echo '<option value="' . $category_id . '">' . $category['title'] . '</option>' . "\n";
		}
		echo '</select></li>' . "\n";
	
		echo '<li><a href="' .  $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=organize">Byta namn och flytta kategorier</a></li>';

		echo '</ul>';
		break;
	}
?>
