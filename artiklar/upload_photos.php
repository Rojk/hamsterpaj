<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'articles.lib.php');
	require(PATHS_LIBRARIES . 'photos.lib.php');
	require(PATHS_LIBRARIES . 'discussion_forum.lib.php');
	require(PATHS_LIBRARIES . 'comments.lib.php');
	require(PATHS_LIBRARIES . 'rank.lib.php');
	

	$ui_options['menu_path'] = array('artiklar', 'start');
	$ui_options['title'] = 'Artiklar på Hamsterpaj.net';
	$ui_options['stylesheets'][] = 'articles.css';
	$ui_options['stylesheets'][] = 'comments.css';
	$ui_options['stylesheets'][] = 'discussion_forum.css';
	$ui_options['stylesheets'][] = 'rank.css';
	//$ui_options['stylesheets'][] = 'codepress.css';
	$ui_options['javascripts'][] = 'comments.js';
	$ui_options['javascripts'][] = 'codepress.js';
	$ui_options['javascripts'][] = 'rank.js';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['javascripts'][] = 'photos.js';
	
	
	
	if(is_privilegied('articles_admin'))
	{
		$display_successful_message = false;
			for($i = 0; $i < PHOTOS_MAX_UPLOADS; $i++)
			{
				if(is_uploaded_file($_FILES['photo_' . $i]['tmp_name']))
				{
					$options['file'] = $_FILES['photo_' . $i]['tmp_name'];
					$options['user'] = 2348;
					$options['description'] = $_POST['description_' . $i];
					$options['category'] = $_POST['category_' . $i];
					
					$category = photos_get_categories(array('user' => $options['user'], 'name' => $options['category'], 'create_if_not_found' => true));

					$category = array_pop($category);
					
					$query = 'UPDATE articles SET photo_category_id = "' . $category['id'] . '" WHERE id = "'. $_GET['article_id'] . '" LIMIT 1';
					jscript_alert($query);
						mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					
					$photo_id = photos_upload($options);
					
						
						
					$display_successful_message = true;
				}
			}
			if($display_successful_message)
			{
				$upload_form .= rounded_corners_top(array('color' => 'blue_deluxe'));
				$upload_form .= 'Bilderna är uppladdade!';
				$upload_form .= rounded_corners_bottom();
			}
			$upload_form .= photos_upload_form(array('user' => 2348));
	}
	
	ui_top($ui_options);
	echo $upload_form;
	echo $_POST['category_' . $i];
	ui_bottom();

?>


