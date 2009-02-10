<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'movie_compability.lib.php');
	$ui_options['menu_path'] = array('mattan', 'ladda_ner_program');
	$ui_options['stylesheets'][] = 'downloads.css';

	event_log_log('downloads_section_load');

	$query = 'SELECT * FROM downloads ORDER BY category ASC, title ASC';
	$result = mysql_query($query);
	$program_count = mysql_num_rows($result);
	while($data = mysql_fetch_assoc($result))
	{
		$download_categories[url_secure_string($data['category'])] = $data['category'];
		$download_items[] = $data;
	}
	
	foreach($download_categories AS $handle => $category)
	{
		$ui_options['menu_addition']['mattan']['children']['ladda_ner_program']['children'][$handle] = array('label' => $category, 'url' => '#' . $handle);
	}
	
	ui_top($ui_options);
	
	echo '<div id="downloads">' . "\n";

	echo '<h1>Hamsterpaj tipsar om bra program att ladda hem</h1>' . "\n";
	echo '<p>Vi har valt ut våra favoritprogram från nätet, samlat länkar och beskrivningar här. Just nu finns det <strong>' . $program_count . '&nbsp;program</strong> att ladda ner <strong>gratis</strong>.<br />Har du ett tips på ett program som skulle passa här? Skicka namn och länk till <a href="/traffa/profile.php?id=85514">ehrw</a> som ett privat meddelande!</p>' . "\n";
	
	foreach($download_items AS $data)
	{
		if($current_category != $data['category'])
		{
			foreach($download_categories AS $handle => $category)
			{
				if($category == $data['category'])
				{
					echo '<a name="' . $handle . '"></a>';
				}
			}
			echo '<h2>' . $data['category'] . '</h2>' . "\n";
			$current_category = $data['category'];
		}
		echo '<a name="' . $data['handle'] . '"></a>';
		echo '<div class="download_item">' . "\n";
		
		if(!strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
		{
			echo '<div style="background: url(\'' . IMAGE_URL . 'downloads/icons/' . $data['handle'] . '.png\') 5px 5px no-repeat;" class="image">' . "\n";
			echo '<img src="http://images.hamsterpaj.net/game_thumb_passepartout.png" />';
			echo '</div>';
		}
		else
		{
			echo '<img src="' . IMAGE_URL . 'downloads/icons/' . $data['handle'] . '.png" class="image" />' . "\n";
		}

		echo '<h3>' . $data['title'] . '</h3>' . "\n";
		echo '<p>' . html_entity_decode($data['description']) . '</p>' . "\n";
		echo '<div class="download_foot">' . "\n";
		echo '<span class="locense">Licens: ' . $DOWNLOAD_LICENSE[$data['license']] . '</span>' . "\n";
		if(strlen($data['website']) > 0)
		{
			echo '<span class="website"><a href="' . $data['website'] . '">Webbsajt</a></span>' . "\n";
		}
		if(strlen($data['direct_link']) > 0)
		{
			echo '<span class="direct_link"><a href="' . $data['direct_link'] . '">Direktlänk</a></span>' . "\n";
		}
		echo '</div>' . "\n";
		echo '</div>' . "\n";
	}
	
	if(is_privilegied('programlist_admin'))
	{
		echo '<form class="downloads_form" enctype="multipart/form-data" method="post">' . "\n";
		echo '<h5>Rubrik</h5>' . "\n";
		echo '<input type="text" name="title" class="textbox" />' . "\n";
		
		echo '<h5>Beskrivning</h5>' . "\n";
		echo '<textarea name="description"></textarea>' . "\n";

		echo '<h5>Kategori</h5>' . "\n";
		echo '<input type="text" name="category" class="textbox" />' . "\n";
		foreach($download_categories AS $category)
		{
			echo '<input type="radio" name="category" value="' . $category . '" />' . $category . '<br />';
		}
		
		echo '<h5>Bild</h5>' . "\n";
		echo '<input type="file" name="image" />' . "\n";
		
		echo '<h5>Webbsajt</h5>' . "\n";
		echo '<input type="text" name="website" class="textbox" />' . "\n";
		
		echo '<h5>Direktlänk</h5>' . "\n";
		echo '<input type="text" name="direct_link" class="textbox" />' . "\n";
		
		echo '<h5>Licens</h5>' . "\n";
		echo '<select name="license">' . "\n";
		echo '<option value="shareware">Shareware</option>' . "\n";
		echo '<option value="open_source">Open Source</option>' . "\n";
		echo '<option value="trial">Testversion</option>' . "\n";
		echo '</select>' . "\n";
		
		echo '<input type="submit" value="spara" class="button_50" />' . "\n";
		echo '</form>' . "\n";

		if(isset($_POST['title']))
		{
			$handle = url_secure_string($_POST['title']);
			/* Scale and move the image */
			system('convert ' . $_FILES['image']['tmp_name'] . ' -resize 120!x90! ' . IMAGE_PATH . 'downloads/icons/' . $handle . '.png');
			
			/* Database */
			$query = 'INSERT INTO downloads(handle, title, category, description, website, direct_link, license)';
			$query .= ' VALUES("' . $handle . '", "' . $_POST['title'] . '" , "' . $_POST['category'] . '", "' . $_POST['description'] . '", "' . $_POST['website'] . '", "' . $_POST['direct_link'] . '", "' . $_POST['license'] . '")';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

			$query = 'INSERT INTO recent_updates(type, label, timestamp, url) VALUES("new_software", "' . $_POST['title'] . '", "' . time() . '", "/mattan/ladda_ner_program.php#' . $handle . '")';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}

	}
	
	echo '</div>' . "\n";
	
	ui_bottom();
?>


