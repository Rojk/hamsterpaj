<?php function create_photo_album($userid) { $query = null; for($i = 0; $i < 10; 
$i++) { mysql_query('INSERT INTO photoalbums(userid, imgid, status) VALUES(' . 
$userid . ', ' . $i . ', 0)'); } }

function listphotos($userid)
{
	global $hp_url;
	$query = 'SELECT status, imgid FROM photoalbums WHERE userid = ' . $userid . ' ORDER BY imgid ASC LIMIT 10';
	$result = mysql_query($query) or die('MySQL error: ' . mysql_error());
	echo '<div style="width: 100%;">';
	if(mysql_num_rows($result) == 10)
	{
		echo '<p class="title">Fotoalbum</p>';
		echo '<table style="width: 100%"><tr>';
		$blankcounter=0;
		while($data = mysql_fetch_assoc($result))
		{
			if($blankcounter == 5)
			{
				echo '</tr><tr>';
			}
			$blankcounter++;
			echo '<td style="text-align: center;">';
			if($data['status'] == 1)
			{
				echo '<a href="javascript: void(0);" onclick="window.open(\'';
				echo $hp_url . 'traffa/photos.php?action=view&userid=' . $userid . '&imgid=' . $data['imgid'];
				echo "','','status=no, width=700, height=550, resizable = no, scrollbars=yes');";
				echo '"><img src="' . IMAGE_URL . 'images/photoalbum/thumb/' . $userid;
				echo '_' . $data['imgid'] . '.jpg" style="border: 1px solid #737373;" />';
			}
			elseif($_SESSION['login']['id'] == $userid)
			{
				echo '<a href="javascript: void(0);" onclick="window.open(\'';
				echo $hp_url . 'traffa/photos.php?action=add&imgid=' . $data['imgid'];
				echo "','','status=no, width=650, height=510, resizable = no, scrollbars=yes');";
				echo '"><img src="' . IMAGE_URL . 'images/photoalbum/addpic.png" style="border: 1px solid #737373;" />';
			}
			else
			{
				echo '<img src="' . IMAGE_URL . 'images/photoalbum/noimage.png" style="border: 1px solid #737373;" />';
			}
			echo '</td>';
		}
		echo '</tr></table>';
		echo 'Klicka på en bild för att ta bort den eller ladda upp en ny.<br />';
		echo 'Om du nyss laddat upp en bild så kan det hända att den gamla fortfarande syns. Tryck på F5 på tangentbordet för att få fram den nya bilden.';

	}
	elseif($userid == $_SESSION['login']['id'])
	{
			echo '<p class="title">Fotoalbum</p>';
			echo 'Du har inte aktiverat ditt fotoalbum än. Att aktivera fotoalbumet är helt gratis och kräver bara ett litet musklick.<br />';
			echo '<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $userid . '&create_photo_album">Skapa album</a>';
	}
	echo '</div>';
}

function viewPhoto($userid, $imgid)
{
	global $hp_url;
	$query = 'SELECT title, uppladdat, status FROM photoalbums ';
	$query.= 'WHERE userid = "' . $userid . '" AND imgid = "' . $imgid . '" LIMIT 1';
	$result = mysql_query($query) or die(mysql_error());
	$data = mysql_fetch_assoc($result) or die(mysql_error());
	echo '<p class="title" style="margin: 5px;">' . $data['title'];
	if($userid == $_SESSION['login']['id'])
	{
		echo '&nbsp;&nbsp;(<a href="' . $_SERVER['PHP_SELF'] . '?action=delete&imgid=' . $imgid . '">Ta bort</a>)';
	}
	echo '</p>';
	echo '<table style="width:100%"><tr><td>';
	if ($imgid != '0')
	{
		echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=view&userid=' . $userid . '&imgid=' . ($imgid - 1) . '">';
		echo '« Föregående';
		echo '</a>';
	}
	echo '</td><td align="right">';
	if ($imgid != '9')
	{
		echo '<a href="' . $_SERVER['PHP_SELF'] . '?action=view&userid=' . $userid . '&imgid=' . ($imgid + 1) . '">';
		echo 'Nästa »';
		echo '</a>';
	}
	echo '</td></tr></table>';
	if (is_privilegied('remove_photo')) {
		if ($data['uppladdat'] == '0000-00-00 00:00:00') {
			$uppladdat='<i>Innan den 26:e juni 2005</i>';
		}
		else {
			$uppladdat=$data['uppladdat'];
		}
		echo '(admininfo) Denna bild laddades upp: ' . $uppladdat.'<br /><br />';
	}
	echo '<a href="javascript: window.close();">';
	if ($data['status'] == '1')
	{
		echo '<img src="' . IMAGE_URL . 'images/photoalbum/full/' . $userid . '_' . $imgid . '.jpg" style="border: 1px solid #737373;" />';
	}
	else
	{
		echo 'Ingen bild<br />';
	}
	echo '</a>';
}

function addPhoto($userid, $imgid, $title = 'Namnlös')
{
	$allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff');
	$filext = strtolower(substr($_FILES['photo']['name'], strrpos($_FILES['photo']['name'], '.')+1));
	if(!in_array($filext, $allowed_exts))
	{
		die('Endast filer med ändelserna jpg, jpeg, png, bmp, tiff och gif accepteras!');
	}

	$filename_full = PATHS_IMAGES . 'photoalbum/full/' . $userid . '_' . $imgid . '.jpg';
	$filename_thumb = PATHS_IMAGES . 'photoalbum/thumb/' . $userid . '_' . $imgid . '.jpg';
	
	system('convert -sample 600x450' . ' ' . $_FILES['photo']['tmp_name'] . ' ' . $filename_full, $retval_full);
	system('convert -sample 100x75 ' . $_FILES['photo']['tmp_name'] . ' ' . $filename_thumb, $retval_thumb);
	system('echo "Added image ' . $imgid . ' for user ' . $userid . '\n" >> /home/www/www.hamsterpaj.net/data/images/photoalbum/full/log.txt');

	$query = 'UPDATE photoalbums SET status = 1, title = "' . $title . '",uppladdat = now() WHERE userid = "' . $userid . '" AND imgid = ' . $imgid . ' LIMIT 1';
	mysql_query($query) or die('MySQL error: ' . mysql_error());
}

function deletePhoto($userid, $imgid)
{
	if($userid == 17505)
	{
		echo '<script>alert(\'Vi har gjort ett undantag för tant-erfaren, som får ha sin visningsbild trots att bryter mot reglerna.\');</script>';
		return 0;
	}
	unlink(PATHS_IMAGES . 'photoalbum/full/' . $userid . '_' . $imgid . '.jpg');
	unlink(PATHS_IMAGES . 'photoalbum/thumb/' . $userid . '_' . $imgid . '.jpg');
	$query = 'UPDATE photoalbums SET title = null, status = 0 WHERE userid = "' . $userid . '" AND imgid = "' . $imgid . '" LIMIT 1';
	mysql_query($query) or die('MySQL error when updating photoalbums: ' . mysql_error());
	log_admin_event('photo deleted', '', $_SESSION['login']['id'], $userid, $imgid);
}

function drawAddPhotoForm($imgid)
{
	echo '<p class="title">Ladda upp bild</p>';
	echo '<p>Det är faktiskt rätt lätt att tanka upp en bild, men du behöver tänka på ett par grejor:<br />';
	echo '* Bilden måste vara av typen bmp, jpeg, gif, tiff eller png.<br />';
	echo '* Det kan ta upp till en minut att ladda upp din bild, beroende på hur snabb internetuppkoppling du har.<br />';
	echo '* Vi vill inte se varken porrbilder eller nazibilder här, så snälla. Låt bli att ladda upp sånt.<br />';
	echo '<form action="' . $_SERVER['PHP_SELF'] . '?action=upload&imgid=' . $imgid . '" method="post" name="photoform" enctype="multipart/form-data">';
	echo 'Klicka på "Bläddra" för att leta upp den bild du vill ladda upp.<br />';
	echo '<input type="file" name="photo" class="textbox" /><br />';
	echo 'Namnge din bild:<br />';
	echo '<input type="text" name="title" class="textbox" /><br />';
	echo '<input type="submit" value="Ladda upp" class="button" onclick="this.disabled=true; this.value=\'Var vänlig vänta\'; document.forms.photoform.submit();" />';
	echo '</form>';
}
?>
