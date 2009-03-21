<?php
	require('../include/core/common.php');
	
	
	$ui_options['menu_path'] = array('mattan', 'snyggve');
	$ui_options['title'] = 'Snyggve, hur snygg kan du bli?';
	$ui_options['stylesheets'][] = 'snyggve.css';

	ui_top($ui_options);

	function snyggve_intro()
	{
		echo '<h1>Snyggve - hur snygg kan du bli?</h1>' . "\n";		
		echo '<p>Här kan du ladda upp dina bilder och låta Snyggve göra om dom. Om du tycker att någon bild blev riktigt rolig så kan du ';
		echo 'spara den här på Hamsterpaj!</p>' . "\n";
	}


	
	function uploadform()
	{
		echo '<div style="clear: both;">' . "\n";
		echo '<h2>Ladda upp en ny bild till Snyggve</h2>' . "\n";
		echo '<p>Du kan ladda upp vanliga bmp-, jpeg-, png- och gif-bilder till Snyggve. Vi förminskar automatiskt bilderna till rätt storlek.</p>' . "\n";
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?action=upload" method="post" enctype="multipart/form-data">' . "\n";
		echo '<input name="image" type="file" /><br />' . "\n";
		echo '<input type="submit" value="Ladda upp" class="button" onclick="alert(\'OBS! Det tar ganska lång tid att ladda upp bilden, det hjälper inte att trycka flera gånger, det enda du kan göra är att vänta!\');" />' . "\n";
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	}
	
	function saveform($identifier)
	{
		echo '<h1>Välj den snyggaste bilden, fyll i ett namn och spara!</h1>' . "\n";
		echo '<p>För att välja en bild klickar du i den runda ringen under bilden du gillar. Om du vill titta på en bild i större ';
		echo 'version så klickar du bara på bilden och ett nytt fönster öppnas.</p>' . "\n";
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?action=save" method="post" onsubmit="if(this.image_id.value.length < 2){alert(\'Du måste välja en av bilderna innan du kan spara!\'); return false;}">' . "\n";
		echo '<div id="snyggve_album">' . "\n";
		for($i = 1; $i <= 12; $i++)
		{
			echo '<div class="saveform">' . "\n";
			echo '<img onclick="autoSizeWindow(\'' . SNYGGVE_TEMP_URL . 'full/' . $identifier . '_' . $i . '.jpg\', \'Snyggve, stor bild\');" src="' . SNYGGVE_TEMP_URL . 'thumb/' . $identifier . '_' . $i . '.jpg" />' . "\n";
			echo '<input type="radio" name="image_id" value="' . $identifier . '_' . $i . '" />' ."\n";
			echo '</div>' . "\n";
		}
		echo '</div>' . "\n";
		echo '<input type="text" name="title" class="textbox" />' . "\n";
		echo '<input type="submit" value="Spara bild" class="button" />' . "\n";
		echo '</form>' . "\n";
		
		


	}
	
	function saveimage()
	{
	log_to_file('snyggve', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'File saved in snyggve by ' . $_SESSION['login']['id'] . ' and file ' . $_FILES['image']['tmp_name'], '');

		if(!is_numeric(str_replace('_', '', $_POST['image_id'])))
		{
			die('Error (Visst är det skönt med felmeddelanden utan förklaring?) #' . __LINE__);
		}
		
		$query = 'INSERT INTO snyggve(title, owner, timestamp) VALUES("' . htmlspecialchars($_POST['title']) . '", ' . $_SESSION['login']['id'] . ', ' . time() . ')';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$id = mysql_insert_id();
		
		system('cp ' . SNYGGVE_TEMP_PATH . 'thumb/' . $_POST['image_id'] . '.jpg ' . SNYGGVE_PERM_PATH . 'thumb/' . $id . '.jpg');
		system('cp ' . SNYGGVE_TEMP_PATH . 'full/' . $_POST['image_id'] . '.jpg ' . SNYGGVE_PERM_PATH . 'full/' . $id . '.jpg');
		
		return $id;
	}
	
	function displace_image()
	{
		$identifier = rand(0, 999999999);
		$input = $_FILES['image']['tmp_name'];
		system('convert -scale 500x500 ' . $input . ' ' . $input);
		for($i = 1; $i <= 12; $i++)
		{
			$map = PATHS_INCLUDE  . 'snyggve_maps/' . $i . '.jpg';
			$output_full = SNYGGVE_TEMP_PATH . 'full/' . $identifier . '_' . $i . '.jpg';
			$output_thumb = SNYGGVE_TEMP_PATH . 'thumb/' . $identifier . '_' . $i . '.jpg';

			system('composite ' . $map . ' ' . $input . ' -displace 15x10 ' . $output_full);
			system('convert -scale 140x105 ' . $output_full . ' ' . $output_thumb);
			system('convert ' . $output_full . ' -fill "rgba(255,255,255,80)" -draw "rectangle 0,0 800,15" -font Times -fill black -pointsize 12 -draw "text 7,12 \'Bilden har snyggats till med Snyggve på Hamsterpaj - http://www.hamsterpaj.net/snyggve/\'" ' . $output_full);
		}
		return $identifier;
	}
	
	function snyggve_remove($id)
	{
		$query = 'DELETE FROM snyggve WHERE id = "' . $id . '" LIMIT 1';
		mysql_query($query);
		unlink(SNYGGVE_PERM_PATH . 'thumb/' . $id . '.jpg');
		unlink(SNYGGVE_PERM_PATH . 'full/' . $id . '.jpg');
	}
	
	function view_image($id)
	{
		if(!is_numeric($id))
		{
			echo '<h1>En gång såg jag en bil, registreringsnumret började med XSS</h1>';
			exit;
		}
		$query = 'SELECT s.title, s.owner, s.timestamp, l.username FROM snyggve AS s, login AS l ';
		$query .= 'WHERE s.id = "' . $id . '" AND l.id = s.owner LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		$data = mysql_fetch_assoc($result);
		echo '<div class="snyggve_full">' . "\n";
		echo '<h1>' . $data['title'] . '</h1>' . "\n";
		echo '<img src="' . SNYGGVE_PERM_URL . 'full/' . $id . '.jpg" />';
		echo '<div class="snyggve_created_by">' . "\n";
		echo 'Skapad av <a href="/traffa/profile.php?id=' . $data['owner'] . '">' . $data['username'] . '</a> ' . date('Y-m-d', $data['timestamp']) . '<br />';
		echo '</div>' . "\n";
		
		if(is_privilegied('snyggve_admin'))
		{
			echo '<a href="?action=remove&id=' . $id . '">Remove</a>';
		}
		
		echo '</div>' . "\n";
		return $data['owner'];
	}
	
	function view_album($user, $cp_grej = null)
	{
		$query = 'SELECT id FROM snyggve WHERE owner = "' . $user . '"';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) < 2 && $cp_grej != 'fulhack')
		{
			return false;
		}
		echo '<h2>Fler bilder av samma användare</h2>' . "\n";

		while($data = mysql_fetch_assoc($result))
		{
			$items[] = $data;
		}
		snyggve_list_items($items);
		return true;
	}
	
	function snyggve_list_items($items)
	{
		echo '<div id="snyggve_album">' . "\n";
		foreach($items AS $data)
		{
			echo '<div>' . "\n";
			echo '<a href="?action=view_image&image_id=' . $data['id'] . '">';
			echo '<img src="' . SNYGGVE_PERM_URL . 'thumb/' . $data['id'] . '.jpg" />';
			echo '</a>' . "\n";
			echo '</div>' . "\n";
		}
		echo '<br style="clear: both;" />' . "\n";
		echo '</div>' . "\n";
	}
	
	function snyggve_get_latest()
	{
		$query = 'SELECT id, title FROM snyggve ORDER by ID DESC LIMIT 12';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		while($data = mysql_fetch_assoc($result))
		{
			$items[] = $data;
		}
		
		return $items;
	}
	
	if($_GET['action'] == 'upload' && login_checklogin())
	{
		$identifier = displace_image();
		saveform($identifier);
	}
	elseif($_GET['action'] == 'save' && login_checklogin())
	{
		$id = saveimage();
		jscript_location('?action=view_image&image_id=' . $id);
	}
	elseif($_GET['action'] == 'view_image')
	{
		$user = view_image($_GET['image_id']);
		if(!view_album($user))
		{
			echo '<h2>Senast uppladdat till Snyggve</h2>' . "\n";
			snyggve_list_items(snyggve_get_latest());
		}
		if(login_checklogin())
		{
			uploadform();
		}
	}
	elseif($_GET['action'] == 'view_user' && is_numeric($_GET['user_id']))
	{
		snyggve_intro();
		view_album($_GET['user_id'], 'fulhack');
		if(login_checklogin())
		{
			uploadform();
		}
	}
	elseif($_GET['action'] == 'remove' && is_privilegied('snyggve_admin') && is_numeric($_GET['id']))
	{
		snyggve_remove($_GET['id']);
		echo 'done';
	}
	else
	{
		snyggve_intro();
		echo '<h2>Senast uppladdat till Snyggve</h2>' . "\n";
		snyggve_list_items(snyggve_get_latest());
		if(login_checklogin())
		{
			uploadform();
		}
	}

	ui_bottom();

?>


