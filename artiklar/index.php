<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/articles.lib.php');
	require(PATHS_INCLUDE . 'libraries/photos.lib.php');
	require(PATHS_INCLUDE . 'libraries/discussion_forum.lib.php');
	require(PATHS_INCLUDE . 'libraries/comments.lib.php');
	require(PATHS_INCLUDE . 'libraries/rank.lib.php');
	

	$ui_options['menu_path'] = array('artiklar', 'start');
	$ui_options['title'] = 'Artiklar på Hamsterpaj.net';
	$ui_options['stylesheets'][] = 'articles.css';
	$ui_options['stylesheets'][] = 'comments.css';
	$ui_options['stylesheets'][] = 'discussion_forum.css';
	$ui_options['stylesheets'][] = 'rank.css';
	//$ui_options['stylesheets'][] = 'codepress.css';
	$ui_options['javascripts'][] = 'comments.js';
	$ui_options['javascripts'][] = 'rank.js';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['javascripts'][] = 'photos.js';
	
	if($_GET['category'] == 'create' && isset($_POST["category_name"]) && is_privilegied('articles_admin')) // I can probably make this in a better way. I will have to ask Joel about that...
	{
		create_category($_POST["category_name"]);
	}
	if($_GET['category'] == 'remove' && isset($_GET['id']) && is_privilegied('articles_admin')) // I can probably make this in a better way. I will have to ask Joel about that...
	{
		remove_category($_GET['id']);
	}
	if($_GET['article'] == 'submit' && isset($_POST) && is_privilegied('articles_admin')) // I can probably make this in a better way. I will have to ask Joel about that...
	{
		if(isset($_GET['id']))
		{
			update_article($_POST, $_GET['id']);
		} 
		else
		{
			create_article($_POST);
		}
	}
	if($_GET['article'] == 'remove' && isset($_GET['id']) && is_privilegied('articles_admin')) // I can probably make this in a better way. I will have to ask Joel about that...
	{
		remove_article($_GET['id']);
	}

	switch($_GET['action'])
	{
		case 'admin': // If an admin would like to do anything
			$ui_options['menu_path'] = array('artiklar', 'admin');
				// Use of privilegies. Which I don't know anything about. ---------------------------------------------------------------
				if(!is_privilegied('articles_admin'))
				{
					$out .= rounded_corners_top(array('color' => 'red'), true);
					$out .= '<h1>Den här delen är endast till för de med privilegier till artikelsystemet</h1>' . "\n";
					$out .= '<a href="?action=list"><< Gå till listan över artiklar</a>' . "\n";
					$out .= rounded_corners_bottom(array('color' => 'red'), true);
					break;
				}
				if($_GET['article'] == 'create')
				{
					$out .= '<h1>Skapa artikel</h1>' . "\n";
					$out .= rounded_corners_top(array('color' => 'blue'), true);
					$out .= article_form($_POST);
					$out .= rounded_corners_bottom(array('color' => 'blue'), true);
					$out .= '<a href="?action=admin"><< Gå till administrationsindex</a>' . "\n";
					break;
				}
				if($_GET['article'] == 'edit' && isset($_GET['id']))
				{
					$out .= '<h1>Ändra artikel</h1>' . "\n";
					$out .= rounded_corners_top(array('color' => 'blue'), true);
					$article = articles_fetch(array('id' => $_GET['id'], 'show' => 'all'));
					$out .= article_form($article);
					$out .= rounded_corners_bottom(array('color' => 'blue'), true);
					$out .= '<a href="?action=admin"><< Gå till administrationsindex</a>' . "\n";
					break;
				}
				
				$out .= render_article_admin($_SESSION['login']['id']);
				
				$out .= rounded_corners_top(array(), true);
				$out .= '<strong>Om du vill att kategorierna ska dyka upp i menyn så måste det fixas i config filen</strong>' . "\n";
				$out .= '<form action="?action=admin&category=create" method="post">' . "\n";
				$out .= '<input type="text" name="category_name" maxlength="20">' . "\n";
				$out .= '<input type="submit" class="button_100" value="Skapa kategori" />' . "\n";
				$out .= '</form>' . "\n";
				$out .= rounded_corners_bottom(array(), true);
				
				$out .= rounded_corners_top(array(), true);
				$out .= '<form action="?action=admin&article=create" method="post">' . "\n";
				$out .= '<input type="text" name="title" maxlength="100">' . "\n";
				$out .= '<input type="submit" class="button_100" value="Skapa artikel" />' . "\n";
				$out .= '</form>' . "\n";
				$out .= rounded_corners_bottom(array(), true);
			break;
		case 'show': //If the user wish to see only one article
			$article = articles_fetch(array('id' => $_GET['id']));
			$out .= render_full_article($article);
			//$out .= '<a href="?action=list&category=' . $article['category_id'] . '">&laquo; Se alla artiklar i kategorin</a>' . "\n";
		break;
		
		case 'list':
			$ui_options['menu_path'] = array('artiklar', 'all');
			if(isset($_GET['category'])) // I wish to get the category name.
			{
				$ui_options['menu_path'] = array('artiklar', $_GET['category']);
				$category = categories_fetch(array('id' => $_GET['category']));
				$out .= '<h1>' . $category['name'] . '</h1>' . "\n";
			}
			
			$options['category'] = $_GET['category']; // If no category is set, itt will return null.
			$articles = articles_fetch($options);
			$out .= articles_list($articles);
		break;
		
		default:
			$ui_options['menu_path'] = array('artiklar', 'start');
			$out .= rounded_corners_top(array('color' => 'orange'), true);
			$out .= '<h1>I artikelarkivet kan man hitta en massa roliga artiklar</h1>' . "\n";
			$out .= rounded_corners_bottom(array('color' => 'orange'), true);
			
			$articles = articles_fetch();
			$out .= articles_list($articles);
			
	}
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();

?>


