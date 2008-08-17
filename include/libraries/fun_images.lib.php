<?php

function fun_images_list($images)
{
	echo '<div class="fun_images_list">' . "\n";
	foreach($images AS $image)
	{
		if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
        {
        	echo '<div style="background: url(\'' . IMAGE_URL . 'fun_images/thumb/' . $image['handle'] . '.png\') center no-repeat;" class="fun_images_thumbnail">' . "\n";
        	echo '<a href="/kul/roliga_bilder/' . $image['category'] . '/' . $image['handle'] . '.html">' . "\n";
        	echo '<img src="http://images.hamsterpaj.net/game_thumb_passepartout.png" /></a>';
        	echo '</div>';
        }
        else
        {
        	echo '<div class="fun_images_thumbnail_ie">' . "\n";
        	echo '<a href="/kul/roliga_bilder/' . $image['category'] . '/' . $image['handle'] . '.html"><img alt="' . $image['title'] . '" src="' . IMAGE_URL . 'fun_images/thumb/' . $image['handle'] . '.png" /></a>' . "\n";
			echo '</div>' . "\n";
        }
	}
	echo '<br style="clear: both;" />' . "\n";
	echo '</div>' . "\n";	
}

function fun_images_fetch($search)
{
	$search['order-by'] = isset($search['order-by']) ? $search['order-by'] : 'fi.id';
	$search['order-direction'] = isset($search['order-direction']) ? $search['order-direction'] : 'DESC';
	
	$search['offset'] = (isset($search['offset']) && is_numeric($search['offset'])) ? $search['offset'] : '0';
	$search['limit'] = (isset($search['limit']) && is_numeric($search['limit'])) ? $search['limit'] : '100';
	
	$query = 'SELECT fi.* FROM fun_images AS fi';
	$query .= ' WHERE 1';
	$query .= (isset($search['exclude'])) ? ' AND fi.id NOT IN("' . implode($search['exclude'], '", "') . '")' : '';
	$query .= (isset($search['category'])) ? ' AND fi.category = "' . $search['category'] . '"' : '';
	$query .= (isset($search['handle'])) ? ' AND fi.handle IN("' . implode($search['handle'], '", "') . '")' : '';
	
	$query .= ' ORDER BY ' . $search['order-by'] . ' ' . $search['order-direction'];
	$query .= ' LIMIT ' . $search['offset'] . ', ' . $search['limit'];
	
	$result = mysql_query($query) or die(report_sql_error($query));
	while($data = mysql_fetch_assoc($result))
	{
		$return[$data['id']] = $data;	
	}
	return $return;
}

function fun_images_display($images)
{
	foreach($images AS $id => $image)
	{
		echo '<div class="fun_images_display">' . "\n";
		echo '<h1>' . $image['title'] . '</h1>' . "\n";
		if(strlen($image['description']) > 0)
		{
			echo '<p>' . $image['description'] . '</p>' . "\n";	
		}
		echo '<img src="' . IMAGE_URL . 'fun_images/full/' . $image['handle'] . '.png" alt="Bild på ' . $image['handle'] . '" />' . "\n";
		echo '<div class="footer">' . "\n";
		echo '<span class="views">' . $image['views'] . ' visningar</span>' . "\n";
		echo '<span class="timestamp">Pubilcerad ' . date('Y-m-d H:i', $image['timestamp']) . '</span>' . "\n";
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		echo '<img src="http://images.hamsterpaj.net/fun_images/fun_images_bottom.png" />' . "\n";
	}
	$query = 'UPDATE fun_images SET views = views + 1 WHERE id IN ("' . implode(array_keys($images), '", "') . '")';
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
}

function fun_images_create($image)
{
	$query = 'INSERT INTO fun_images (timestamp, handle, title, description, category)';
	$query .= ' VALUES("' . time() . '", "' . $image['handle'] . '", "' . $image['title'] . '", "' . $image['description'] . '", "' . $image['category'] . '")';
	
	mysql_query($query) or report_sql_error($query);
	echo 'Running query: ' . $query;
}

function fun_images_upload($handle)
{
	system('convert -resize 510x510 ' . $_FILES['file']['tmp_name'] . ' /mnt/images/fun_images/full/' . $handle . '.png');
	system('convert -resize 120x90 ' . $_FILES['file']['tmp_name'] . ' /mnt/images/fun_images/thumb/' . $handle . '.png');
}

function fun_images_admin_form()
{
	echo '<h2>Ladda upp en ny bild</h2>' . "\n";
	echo '<form action="/kul/roliga_bilder/upload.php" method="post" enctype="multipart/form-data">' . "\n";
	echo '<h3>Namn</h3>' . "\n";
	echo '<input type="text" name="title" />' . "\n";
	echo '<h3>Beskrivning/kommentar/förklaring (bara om det behövs)</h3>' . "\n";
	echo '<textarea name="description"></textarea>' . "\n";
	echo '<h3>Kategori</h3>' . "\n";
	echo '<select name="category">' . "\n";
	$result = mysql_query('SHOW COLUMNS FROM fun_images LIKE "category"');
	$types = mysql_result($result, 0, "Type");
	$types = substr($types, 5, strlen($types)-6);
	$i = 1;
	foreach (explode(',', $types) as $value) {
		$severity[$value] = $i++;		
	}
	
	foreach ($severity as $key => $value)
	{
		echo '<option value="' . substr($key, 1, -1) . '">' . substr($key, 1, -1) . '</option>' . "\n";
	}
	echo '</select>' . "\n";
	
	echo '<h3>Ladda upp fil</h3>' . "\n";
	echo '<input type="file" name="file" />' . "\n";
	echo '<h3>Release</h3>' . "\n";
	$release = time();
	echo '<input type="text" name="release" value="' . date('Y-m-d H:i', schedule_release_get(array('type' => 'fun_images'))) . '" />' . "\n";
	echo '<input type="checkbox" name="release_now" value="true" id="fun_images_release_now"/>' . "\n";
	echo '<label for="fun_images_release_now">Släpp omedelbart</label><br />' . "\n";
	echo '<input type="submit" value="Ladda upp" />' . "\n";
	echo '</form>' . "\n";
}

function fun_images_parse_request()
{
	$url = $_SERVER['REQUEST_URI'];
	if($_SESSION['login']['userlevel'] == 5 && $url == '/kul/roliga_bilder/upload.php')
	{
		$action = 'upload';
	}
	elseif($url == '/kul/roliga_bilder/topplistan/')
	{
		$action = 'list';
		$category = 'topplistan';
	}
	elseif(preg_match('/\/kul\/roliga_bilder\/(\w+)\/(\w+)\.html/', $url, $matches))
	{
		$action = 'view';
		$category = $matches[1];
		$handle = $matches[2];
	}
	elseif(preg_match('/\/kul\/roliga_bilder\/(\w+)\//', $url, $matches))
	{
		$action = 'list';
		$category = $matches[1];
	}
	else
	{
		$action = 'list';
		$category = 'nya_bilder';	
	}
	
	$request['action'] = $action;
	$request['category'] = $category;
	$request['handle'] = $handle;
	
	return $request;
}

?>