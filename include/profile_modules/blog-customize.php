<?php
	echo '<h1>Administrera din babablog</h1>';
	switch($_GET['action'])
	{
		case 'new_entry':
			echo '<h2>Skriv ett nytt inlägg</h2>' . "\n";
			echo '<form enctype="multipart/form-data" action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=post_entry" method="post">' . "\n";
			echo '<strong>Rubrik på ditt inlägg:</strong> <input type="text" name="title" /><br />' . "\n";
			echo '<strong>Skriv din text här:</strong><br />';
			echo '<textarea name="text" style="width: 99%; height: 250px;"></textarea>';
			echo '<h2>Vill du ladda upp några foton tillsammans med ditt inlägg?</h2>';
			echo '<input type="file" name="photo_1" />';
			echo '<input type="file" name="photo_2" />';
			echo '<input type="file" name="photo_3" />';
			echo '<input type="file" name="photo_4" /><br />';
			echo '<input type="submit" value="Spara &raquo;" class="button" />';
			echo '</form>' . "\n";
			break;
		case 'post_entry':
			$imagestring = '0000';
			for($i = 1; $i <= 4; $i++)
			{
				if(strlen($_FILES['photo_' . $i]['name']) > 0)
				{
					$extension = strtolower(substr($_FILES['photo_' . $i]['name'], strrpos($_FILES['photo_' . $i]['name'], '.')+1));
					if(in_array($extension, array('jpg', 'jpeg', 'bmp', 'png')))
					{
						$uploaded_images[$i] = $_FILES['photo_' . $i]['tmp_name'];
						$imagestring{$i-1} = '1';
					}
					else
					{
						echo '<p class="error">Filen ' . $_FILES['photo_' . $i]['name'] . ' kunde inte laddas upp, eftersom filformatet inte kändes igen!</p>';
					}
				}
			}
			$query = 'INSERT INTO blog(user, date, title, text, photos) ';
			$query .= 'VALUES("' . $_SESSION['login']['id'] . '", CURDATE(), "' . $_POST['title'] . '", "' . $_POST['text'] . '", "' . $imagestring . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			foreach($uploaded_images AS $position => $filename)
			{
				$save_path = PATHS_IMAGES . 'blog_photos/' . round(mysql_insert_id()/2500) . '/';
				if(!is_dir($save_path))
				{
					if(!mkdir($save_path))
					{
						to_logfile('error', __FILE__, __LINE__, 'Could not create directory',  $save_path);
						die('Ett internt fel har uppstått, dina foton kunde inte laddas upp! Felet har loggats.');
					}
				}
				system('convert ' . $filename . ' -resize 120x90! ' . $save_path . mysql_insert_id() . '_' . $position . '.jpg');
			}
			jscript_location('/traffa/profile.php');
			exit;
			break;
		case 'delete_entry':
			if($_GET['perform'] == 'true' && is_numeric($_GET['delete_entry']))
			{
				$query = 'DELETE FROM blog WHERE id = "' . $_GET['delete_entry'] . '" AND user = "' . $_SESSION['login']['id'] . '" LIMIT 1';
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				if(mysql_affected_rows() == 1)
				{
					$query = 'DELETE FROM comments WHERE type = "blog" AND item_id = "' . $_GET['delete_entry'] . '"';
					mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
					for($i = 1; $i <= 4; $i++)
					{
						unlink(PATHS_IMAGES . 'blog_photos/' . round(mysql_insert_id()/2500) . '/' . $_GET['delete_entry'] . '_' . $i . '.jpg');
					}
				}
				jscript_alert('Inlägget togs bort');
				jscript_location($_SERVER['PHP_SELF'] . '?id=' . $_GET['id']);
				exit;
			}
			else
			{
				if(preg_match('/^[0-9]{4}[-][0-9]{2}$/', $_GET['month']))
				{
					$query = 'SELECT id, date, title FROM blog WHERE date LIKE "' . $_GET['month'] . '%" AND user = "' . $_SESSION['login']['id'] . '" ORDER BY id DESC';
					$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
					echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="get">' . "\n";
					echo '<input type="hidden" name="id" value="' . $_GET['id'] . '" />' . "\n";
					echo '<input type="hidden" name="action" value="delete_entry" />' . "\n";
					echo '<input type="hidden" name="perform" value="true" />' . "\n";
					echo '<select id="delete_entry" name="delete_entry">' . "\n";
					while($data = mysql_fetch_assoc($result))
					{
						echo '<option value="' . $data['id'] . '">' . $data['date'] . ' - ' . $data['title'] . '</option>' . "\n";
					}
					echo '</select>';
//					echo '<input type="submit" onclick="var selected = document.getElementById(\'delete_entry\').value; return confirm(\'Vill verkligen du ta bort \\\'\' + document.getElementById(\'delete_entry\').options[selected].text + \'\\\'?\');" value="Ta bort &raquo;" />';
					echo '<input type="submit" onclick="var trams = document.getElementById(\'delete_entry\').selectedIndex; return confirm(document.getElementById(\'delete_entry\').options[trams].text); return false;" value="Ta bort &raquo;" />';
				}						
			}		
			break;
		case 'update':
			if($_GET['perform'] == 'true')
			{
				$query = 'SELECT id, photos FROM blog WHERE user ="' . $_SESSION['login']['id'] . '" AND date ="' . date('Y-m-d') . '" LIMIT 1';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				$data = mysql_fetch_assoc($result);
				$imagestring = $data['photos'];
				for($i = 1; $i <= 4; $i++)
				{
					if(strlen($_FILES['photo_' . $i]['name']) > 0)
					{
						$extension = strtolower(substr($_FILES['photo_' . $i]['name'], strrpos($_FILES['photo_' . $i]['name'], '.')+1));
						if(in_array($extension, array('jpg', 'jpeg', 'bmp', 'png')))
						{
							$uploaded_images[$i] = $_FILES['photo_' . $i]['tmp_name'];
							$imagestring{$i-1} = '1';
						}
						else
						{
							echo '<p class="error">Filen ' . $_FILES['photo_' . $i]['name'] . ' kunde inte laddas upp, eftersom filformatet inte kändes igen!</p>';
						}
					}
				}
				foreach($uploaded_images AS $position => $filename)
				{
					$save_path = PATHS_IMAGES . 'blog_photos/' . round($data['id']/2500) . '/';
					if(!is_dir($save_path))
					{
						if(!mkdir($save_path))
						{
							to_logfile('error', __FILE__, __LINE__, 'Could not create directory',  $save_path);
							die('Ett internt fel har uppstått, dina foton kunde inte laddas upp! Felet har loggats.');
						}
					}
					system('convert ' . $filename . ' -resize 120x90! ' . $save_path . $data['id'] . '_' . $position . '.jpg');
					echo 'convert ' . $filename . ' -resize 120x90! ' . $save_path . $data['id'] . '_' . $position . '.jpg' . '<br />';
				}
				for($i = 1; $i <= 4; $i++)
				{
					if($_POST['photo_' . $i . '_delete'] == 1)
					{
						$imagestring{$i-1} = 0;
						unlink(PATHS_IMAGES . 'blog_photos/' . round($data['id']/2500) . '/');
					}
				}
				
				$query = 'UPDATE blog SET title = "' . mysql_real_escape_string(stripslashes($_POST['title'])) . '", ';
				$query .= 'text = "' . mysql_real_escape_string(stripslashes($_POST['text'])) . '", photos = "' . $imagestring . '" ';
				$query .= 'WHERE user = "' . $_SESSION['login']['id'] . '" AND date = "' . date('Y-m-d') . '" LIMIT 1';
				mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				jscript_location('/traffa/profile.php');
				exit;
			}
			else
			{
				$query = 'SELECT id, title, text, photos FROM blog WHERE user = "' . $_SESSION['login']['id'] . '" AND date = "' . date('Y-m-d') . '" LIMIT 1';
				$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					echo '<form enctype="multipart/form-data" action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=update&perform=true" method="post">' . "\n";
					echo '<input type="text" name="title" value="' . $data['title'] . '" /><br />' . "\n";
					echo '<textarea name="text" style="width: 99%; height: 250px;">' . $data['text'] . '</textarea>';
					for($i = 1; $i <= 4; $i++)
					{
						echo '<div style="float: left; width: 300px;" class="grey_faded_div">' . "\n";
						echo '<h2>Bild #' . $i . '</h2>';
						if($data['photos']{$i-1} == 1)
						{
							$src = IMAGE_URL . 'images/blog_photos/' . round($data['id']/2500) . '/' . $data['id'] . '_' . $i . '.jpg';
							echo '<img src="' . $src . '" />';
							echo '<br /><strong>Ladda upp annan bild till plats #' . $i . '</strong><br />';
							echo '<input type="file" name="photo_' . $i . '" /><br />' . "\n";
							echo '<input type="checkbox" name="photo_' . $i . '_delete" value="1" /><strong>Ta bort bilden</strong>';
						}
						else
						{
							echo '<br /><strong>Ladda upp en bild till plats #' . $i . '</strong><br />' . "\n";
							echo '<input type="file" name="photo_' . $i . '" />';
						}
						echo '</div>' . "\n";
					}
					echo '<input type="submit" class="button" value="Spara ändringar &raquo;" style="clear: both;" />' . "\n";
					echo '</form>' . "\n";
				}
				else
				{
					echo 'Det tycks inte finnas något inlägg skrivet idag...';
				}
			}
			break;
		default:
			$query = '(SELECT DISTINCT(LEFT(date, 7)) AS date FROM blog WHERE user = "' . $_SESSION['login']['id'] . '" GROUP BY date ORDER BY id DESC)';
			$query .= ' UNION (SELECT date FROM blog WHERE user = "' . $_SESSION['login']['id'] . '" ORDER BY id DESC LIMIT 1)';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			while($data = mysql_fetch_assoc($result))
			{
				if(strlen($data['date']) == 7)
				{
					$months[] = $data['date'];
				}
				else
				{
					$latest_entry = $data['date'];
				}
			}
			echo '<h2>Vad vill du göra?</h2>' . "\n";
			echo '<ul>' . "\n";
			if(date('Y-m-d') == $latest_entry)
			{
				echo '<li><a href="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=update">Ändra dagens inlägg</a></li>';
			}
			else
			{
				echo '<li><a href="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=new_entry">Skriva ett nytt inlägg</a></li>';
			}
			
			echo '<li>Ta bort ett inlägg från ';
			echo '<form action="' . $_SERVER['PHP_SELF'] . '" style="display: inline;" method="get">' . "\n";
			echo '<input type="hidden" name="id" value="' . $_GET['id'] . '" />';
			echo '<input type="hidden" name="action" value="delete_entry" />';
			echo '<select name="month">';
			foreach($months AS $month)
			{
				echo '<option value="' . $month . '">' . $month . '</option>';
			}

			echo '</select>';
			echo '<input type="submit" class="button" value="Fortsätt &raquo;" />';
			echo '</form>';
			echo '</li>' . "\n";

			echo '</ul>' . "\n";
			break;
	}
?>
