<?php
	function articles_fetch($options)
	{
		if(isset($options['id']))
		{
			$options['id'] = (is_array($options['id'])) ? $options['id'] : array($options['id']);
		}
		else
		{
				if($options['show'] <> 'all')
				{
					$published = 1;
					$listed = 1; // Default when we get a category or a bunch of articles we only want to show those who are listed. (A database setting)
				}	
		}
		if(isset($options['category']))
		{
			$options['category'] = (is_array($options['category'])) ? $options['category'] : array($options['category']);
		}
		if(isset($options['date']))
		{
			$options['date'] = (is_array($options['date'])) ? $options['date'] : array($options['date']);
		}
		if($options['show'] <> 'all')
		{
			$published = 1;
		}
		
		$options['order-by'] = (in_array($options['order-by'], array('id'))) ? $options['order-by'] : 'id';
		$options['order-direction'] = (in_array($options['order-direction'], array('ASC', 'DESC'))) ? $options['order-direction'] : 'ASC';
		$options['offset'] = (isset($options['offset']) && is_numeric($options['offset'])) ? $options['offset'] : 0;
		$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 9999;

		$query = 'SELECT id, title, date, author, summary, thumb, rankable, forum_category_id, commentable, published, category_id, showauthor, listed, breaklayout, photo_category_id';
		$query .= ' FROM articles';
		$query .= ' WHERE 1 AND removed <> 1';
		$query .= (isset($published)) ? ' AND published = "' . $published . '"' : '';
		$query .= (isset($options['id'])) ? ' AND id IN("' . implode('", "', $options['id']) . '")' : '';
		$query .= (isset($listed)) ? ' AND listed = "' . $listed . '"' : '';
		$query .= (isset($options['date'])) ? ' AND date IN("' . implode('", "', $options['date']) . '")' : '';
		$query .= (isset($options['category'])) ? ' AND category_id IN("' . implode('", "', $options['category']) . '")' : '';
		$query .= ' ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			$data['description'] = (strlen($data['description']) > 0) ? $data['description'] : 'namnlös';
			$articles[] = $data; // Save in array
			$article = $data; // Save as Variable
		}
		if(isset($options['id'])) // If we only get one article by id we don't want to set a foreach. So then we save it in a Variabel.
		{
			return $article;
		}
		else // But if there are more, we returns it in an array.
		{
			return $articles;
		}
	}
	
	function articles_list($articles)
	{
		foreach($articles AS $article)
		{
			$output .= render_article_list_item($article);
		}
		return $output;
	}
	
	function get_thumbnail($id)
	{
		if(file_exists('/mnt/images/article_thumbs/' . $id . '.jpg'))
		{
			return '<img src="http://images.hamsterpaj.net/article_thumbs/' . $id . '.jpg" />' . "\n";
		}
		else
		{
			return '<img src="http://images.hamsterpaj.net/article_thumbs/no_image.png" />' . "\n";
		}
	}
	
	function render_author($name)
	{
		$author['name'] = 'Anonym';
		$author['description'] = 'I ett fåtal fall har artikelförfattaren valt att vara anonym, det kan handla om ovilja att ta en hetsig debatt som kan följa av en kontroversiell artikel eller om att vilja bevara sitt privatliv. Hamsterpajs ansvariga vet dock alltid vem författaren är.';
		$author['image'] = 'unknown.png';
		
		if(isset($name))
		{
			if(file_exists(PATHS_INCLUDE . 'article_authors/' . $name . '.php'))
			{
				include(PATHS_INCLUDE . 'article_authors/' . $name . '.php');
			}
		}

		$output .=  rounded_corners_top(array('color' => 'orange'));
		$output .=  '<div id="author">' . "\n";
		$output .=  '<img class="author" src="http://images.hamsterpaj.net/article_authors/' . $author['image'] . '" />' . "\n";
		$output .=  '<h3>Upphovsman: ' . $author['name'] . '</h3>' . "\n";
		$output .=  '<p>' . $author['description']. '</p>' . "\n";
		$output .=  '<div style="clear: both;"></div>' . "\n";
		$output .=  '</div>' . "\n";
		$output .=  rounded_corners_bottom();
		return $output;
	}
	
	function render_article_list_item($article)
	{
		$output .= rounded_corners_top();
		$output .= '<div class="article_list_item">' . "\n";
		$output .= '<a href="?action=show&id=' . $article['id'] . '">' . "\n";
		$output .= get_thumbnail($article['id']);
		$output .= '<h2>' . $article['title'] . '</h2>' . "\n";
		$output .= '<p>' . $article['summary'] .'</p>' . "\n";
		$output .= '</a>' . "\n";
		$output .= '</div>' . "\n";
		$output .= rounded_corners_bottom();
		return $output;
	}
	
	function render_article($article)
	{
		$output .= '<h1>' . $article['title'] . '</h1>' . "\n";
		$output .= '<div id="article">' . "\n";
		$output .= include_article($article['id']);
		$output .= '</div>' . "\n";
		$output .= '<div style="clear: both; height: 1px;"></div>' . "\n";
		return $output;
	}
	
	function create_category($name)
	{
		$query = 'INSERT INTO articles_categories (name) VALUES ("';
		$query .= $name . '")';
		mysql_query($query) or die(report_sql_error($query));
	}
	
	function create_article($content)
	{
		if($content['create_forum_category'] == 1)
		{
			$category['title'] = $content['title'];
			$category['parent'] = 112;
			$category['quality_level'] = 4;
			$category['title'] = $content['title'];
			$category['description'] = 'För diskussioner länkade till artikeln: ' . $content['title'];
			$forum_category_id = discussion_forum_category_create($category);
			
			$post['author'] = '2348';
			$post['title'] = 'Vad tycker du om artikeln?';
			$post['mode'] = 'new_thread';
			$post['forum_id'] = $forum_category_id;
			$post['content'] = 'Vad tycker du om artikeln: ' . $content['title'] . '?';
			discussion_forum_post_create($post);
		}
		
		$query = 'INSERT INTO articles (title, summary, date, author, category_id, published, commentable, rankable, showauthor, listed, breaklayout, forum_category_id) VALUES("';
		$query .= $content['title'] . '", "' . $content['summary'] . '", "' . date('Y-m-d') . '", "' . $content['author'] . '", "';
		$query .= $content['category'] . '", "' . $content['published'] . '", "' . $content['commentable'] . '", "' . $content['rankable'] . '", "' . $content['showauthor'] . '", "' . $content['listed'] . '", "' . $content['breaklayout'] . '","' .  $forum_category_id . '")';

		mysql_query($query) or die(report_sql_error($query));
		$id = mysql_insert_id();

		$content = html_entity_decode(stripslashes($content['content']));
		$file = fopen(PATHS_DYNAMIC_CONTENT . 'articles/' . $id . '.php', 'w');
		fwrite($file, $content);
		fclose($file);
		jscript_location('/artiklar/?action=show&id=' . $id);
	}
	
	function update_article($content, $id)
	{
		if(!isset($content['forum_category_id']) && $content['create_forum_category'] == 1 || $content['forum_category_id'] == 0 && $content['create_forum_category'] == 1)
		{
			$category['title'] = $content['title'];
			$category['parent'] = 90;
			$category['quality_level'] = 4;
			$category['title'] = $content['title'];
			$category['description'] = 'För diskussioner länkade till artikeln: ' . $content['title'];
			$forum_category_id = discussion_forum_category_create($category);
			
			$post['author'] = '2348';
			$post['title'] = 'Vad tycker du om artikeln?';
			$post['mode'] = 'new_thread';
			$post['forum_id'] = $forum_category_id;
			$post['content'] = 'Vad tycker du om artikeln: ' . $content['title'] . '?';
			discussion_forum_post_create($post);
		}
		else
		{
			$forum_category_id = $content['forum_category_id'];
		}
		
		$query = 'UPDATE articles SET ';
		$query .= 'title = "' . $content['title'] . '", ';
		$query .= 'summary = "' . $content['summary'] . '", ';
		$query .= 'author = "' . $content['author'] . '", ';
		$query .= 'category_id = "' . $content['category'] . '", ';
		$query .= 'published = "' . $content['published'] . '", ';
		$query .= 'commentable = "' . $content['commentable'] . '", ';
		$query .= 'rankable = "' . $content['rankable'] . '", ';
		$query .= 'showauthor = "' . $content['showauthor'] . '", ';
		$query .= 'listed = "' . $content['listed'] . '", ';
		$query .= 'breaklayout = "' . $content['breaklayout'] . '", ';
		$query .= 'forum_category_id = "' . $forum_category_id . '" ';
		$query .= 'WHERE id = "' . $id . '"';
		
		mysql_query($query) or die(report_sql_error($query));
		
		$content = html_entity_decode(stripslashes($content['content']));
		$file = fopen(PATHS_DYNAMIC_CONTENT . 'articles/' . $id . '.php', 'w');
		fwrite($file, $content);
		fclose($file);
		jscript_location('/artiklar/?action=show&id=' . $id);
	}
	
	function remove_article($id)
	{
		$query = 'UPDATE articles SET removed = "1" WHERE id = "' . $id . '"';
		mysql_query($query) or die(report_sql_error($query));
		jscript_location('/artiklar/?action=admin');
	}
	
	function remove_category($id)
	{
		$query = 'UPDATE articles_categories SET removed = "1" WHERE id = "' . $id . '"';
		mysql_query($query) or die(report_sql_error($query));
		jscript_location('/artiklar/?action=admin');
	}
	
	function categories_fetch($options)
	{
		if(isset($options['id']))
		{
			$options['id'] = (is_array($options['id'])) ? $options['id'] : array($options['id']);
		}
		
		$options['order-by'] = (in_array($options['order-by'], array('id'))) ? $options['order-by'] : 'id';
		$options['order-direction'] = (in_array($options['order-direction'], array('ASC', 'DESC'))) ? $options['order-direction'] : 'ASC';
		$options['offset'] = (isset($options['offset']) && is_numeric($options['offset'])) ? $options['offset'] : 0;
		$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 9999;
		
		$query = 'SELECT id, name';
		$query .= ' FROM articles_categories';
		$query .= ' WHERE removed <> 1';
		$query .= (isset($options['id'])) ? ' AND id IN("' . implode('", "', $options['id']) . '")' : '';
		$query .= ' ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($data = mysql_fetch_assoc($result))
		{
			$data['description'] = (strlen($data['description']) > 0) ? $data['description'] : 'namnlös';
			$categories[] = $data; // Save in array
			$category = $data; // Save invariabel
		}
		
		if(isset($options['id'])) // If we only get one article by id we don't want to set a foreach. So then we save it in a Variabel.
		{
			return $category;
		}
		else // But if there are more, we returns it in an array.
		{
			return $categories;
		}
	}
	
	function render_article_admin()
	{
		$output .= rounded_corners_top();
		$categories = categories_fetch();
		
		$output .= '<ul>' . "\n";
		foreach($categories AS $category)
		{
			$output .= '<li>' . "\n";
			$output .= $category['name'] . "\n";
			if(is_privilegied('articles_admin'))
			{
				$output .= '<a href="?action=admin&category=remove&id='. $category['id'] . '" onclick="return confirm(\'Ar du säker på att du vill ta bort den här kategorin? Dess innehåll kommer fortsatt att vara kvar.\')">[remove]</a>' . "\n";
			}
			
			$output .= '<ul>' . "\n"; // Categorized and listed
				$articles = articles_fetch(array('category' => $category['id'], 'show' => 'all'));
				foreach($articles AS $article)
				{
					$output .= ($article['published'] == 0) ? '<li style="color: red;">' : '<li>' .  "\n";
					$output .= $article['title'] . "\n";
					$output .= '<a href="?action=admin&article=edit&id='. $article['id'] . '">[edit]</a>' . "\n";
					if(is_privilegied('articles_admin'))
					{
						$output .= '<a href="?action=admin&article=remove&id='. $article['id'] . '" onclick="return confirm(\'Ar du säker på att du vill ta bort artikeln?\')">[remove]</a>' . "\n";
					}
				}
			$output .= '</ul>' . "\n";
			$output .= '</li>' . "\n";
		}
		$output .= '<li>Icke listade och icke kategoriserade' . "\n";
		$output .= '<ul>' . "\n"; // Non listed and non categorized
			$articles = articles_fetch(array('category' => "", 'show' => 'all'));
			foreach($articles AS $article)
			{
				$output .= ($article['published'] == 0) ? '<li style="color: red;">' : '<li>' .  "\n";
				$output .= $article['title'] . "\n";
				$output .= '<a href="?action=admin&article=edit&id='. $article['id'] . '">[edit]</a>' . "\n";
				if(is_privilegied('articles_admin'))
					{
						$output .= '<a href="?action=admin&article=remove&id='. $article['id'] . '" onclick="return confirm(\'Ar du säker på att du vill ta bort artikeln?\')">[remove]</a>' . "\n";
					}
			}
		$output .= '</ul>' . "\n";
		$output .= '</li>' . "\n";
		$output .= '</ul>' . "\n";
		$output .= '<span>* De röda är icke publicerade artiklar</span>' . "\n";
		$output .= rounded_corners_bottom();
		return $output;
	}
	
	function include_article($id)
	{
		if (is_file(PATHS_DYNAMIC_CONTENT . 'articles/' . $id . '.php')) // If the file exists.
		{ 
			ob_start();
			include PATHS_DYNAMIC_CONTENT . 'articles/' . $id . '.php';
			$content .= ob_get_contents();
			ob_end_clean();
		}
		else //if the file can't be found
		{
			$output .= '<h1>Här har någon bugg skett, räkmacka!</h1>' . "\n";
		}
		return $content;
	}
	
	function article_form($article)
	{
		if(isset($article['id'])) // If edit
		{
			$edit = '&id=' . $article['id'];
		}
		
		$output .= '<form action="?article=submit' . $edit . '" method="post">' . "\n";
		$output .= '<input style="width: 99%; font-size: 20px;" name="title" value="' . $article['title'] . '" />' . "\n";
	
		if(isset($article['id']))
		{
			$output .= '<textarea name="content" style="width: 99%; height: 400px;">' . file_get_contents(PATHS_DYNAMIC_CONTENT . 'articles/' . $article['id'] . '.php') . '</textarea>' . "\n";
		}
		else
		{
			$output .= '<textarea name="content" style="width: 99%; height: 400px;"></textarea>' . "\n";
		}
		$output .= '<label for="summary">Sammanfattning</label>' . "\n";
		$output .= '<textarea name="summary" style="width: 99%; height: 100px;">' . $article['summary'] . '</textarea>' . "\n";
		
		$output .= '<label for="category">Kategori</label>' . "\n";
		$output .= '<select name="category">' . "\n";
			$output .= '<option value="">Ingen kategori</option>' . "\n";
			
			$categories = categories_fetch();
			foreach($categories AS $category)
			{
				if($article['category_id'] ==  $category['id'])
				{
					$selected = ' selected="selected"';
				}
				$output .= '<option' . $selected . ' value="' . $category['id'] . '">' . $category['name'] . '</option>' . "\n";
				$selected = ""; //Empties the variabel for next time
			}

		$output .= '</select>' . "\n";
		
		$output .= '<label for="author">Författare</label>' . "\n";
		$output .= '<select name="author">' . "\n";
		$output .= '<option value="">Anonym</option>' . "\n";
		$handle = opendir(PATHS_INCLUDE . 'article_authors/');
		while (false !== ($file = readdir($handle))) 
		{
			if ($file != "." && $file != "..") 
			{
				if($article['author'] ==  substr($file, 0, -4))
				{
					$selected = ' selected="selected"';
				}
				$output .= '<option ' . $selected . ' value="' . substr($file, 0, -4) . '">' . substr($file, 0, -4) . '</option>' . "\n";
				$selected = ""; //Empties the variabel for next time
			}
		}
		$output .= '</select><br />' . "\n";

		$output .= '<label for="Published">Publicerad:</label>' . "\n";
		$checked .= ($article['published'] == 1) ? ' checked="checked"' : '';
		$output .= '<input type="checkbox" name="published" value="1"' . $checked . '>' . "\n";
		$checked = "";
		
		$output .= '<label for="commentable">Möjlighet att kommentera:</label>' . "\n";
		$checked .= ($article['commentable'] == 1) ? ' checked="checked"' : '';
		$output .= '<input type="checkbox" name="commentable" value="1"' . $checked . '>' . "\n";
		$checked = "";
		
		$output .= '<label for="ranktable">Möjlighet att betygssätta:</label>' . "\n";
		$checked .= ($article['rankable'] == 1) ? ' checked="checked"' : '';
		$output .= '<input type="checkbox" name="rankable" value="1"' . $checked . '><br />' . "\n";
		$checked = "";
		
		$output .= '<label for="showauthor">Visa författare:</label>' . "\n";
		$checked .= ($article['showauthor'] == 1) ? ' checked="checked"' : '';
		$output .= '<input type="checkbox" name="showauthor" value="1"' . $checked . '>' . "\n";
		$checked = "";
		
		$output .= '<label for="listed">Visa listad i artikelarkivet:</label>' . "\n";
		$checked .= ($article['listed'] == 1) ? ' checked="checked"' : '';
		$output .= '<input type="checkbox" name="listed" value="1"' . $checked . '>' . "\n";
		$checked = "";
		
		$output .= '<label for="breaklayout">Bryt standardlayouten:</label>' . "\n";
		$checked .= ($article['breaklayout'] == 1) ? ' checked="checked"' : '';
		$output .= '<input type="checkbox" name="breaklayout" value="1"' . $checked . '><br />' . "\n";
		$checked = "";
		
		$output .= '<label for="create_forum_category">Skapa en forumkategori(har du kryssat i knappen så kommer kategorin att skapas när du klickar på spara ändringar, sedan kan du inte ta bort forumkategorin):</label>' . "\n";
		$checked .= (isset($article['forum_category_id']) && $article['forum_category_id'] != 0) ? ' checked="checked" disabled="disabled"' : '';
		$output .= '<input type="checkbox" name="create_forum_category" value="1"' . $checked . '><br />' . "\n";
		$checked = "";
		
		$output .= '<input type="text" name="forum_category_id" value="' . $article['forum_category_id'] . '" style="display: none;""><br />' . "\n";
		
		$output .= '<input type="submit" class="button" value="Spara ändringar" />' . "\n";
		if(isset($article['id']))
		{
			$output .= '<a target="_blank" href="upload_photos.php?article_id=' . $article['id'] . '">Ladda upp bilder</a>';
		}
		else
		{
			$output .= 'Fulkodat skräp. Du måste spara artikeln innan du kan ladda upp bilder' . "\n";
		}
		$output .= '</form>' . "\n";
		return $output;
	}
	
	function render_full_article($article)
	{
		if(empty($article)) // Checks if any article was found.
		{
			$out .= rounded_corners_top(array('color' => 'red'));
			$out .= '<h1>Den här artikeln kunde tyvärr inte hittas</h1>' . "\n";
			$out .= '<a href="?action=list"><< Gå till listan över artiklar</a>' . "\n";
			$out .= rounded_corners_bottom(array('color' => 'red'));
		}
		else
		{
			
			$out .= render_article($article);
			
			if($article['photo_category_id'] > 0)
			{
				$options['category'] = $article['photo_category_id'];
				$photos = photos_fetch($options);
				$out .= '<h2>Tillhörande bilder</h2>' . "\n";
				$out .= photos_list($photos);
			}
			
			if($article['showauthor'] == 1)
			{
				$out .= render_author($article['author']);
			}
			
			if(isset($article['forum_category_id']) && $article['forum_category_id'] != 0)
			{
				$all_categories_list = discussion_forum_categories_fetch(array('id' => $article['forum_category_id']));
				$category = array_pop($all_categories_list);
	
				$forum_security = forum_security(array('action' => 'view_category', 'category' => $category));
				if($forum_security == true)
				{
					$path_to_category = discussion_forum_path_to_category(array('id' => $category['id']));
					$locator_options['categories'] = $path_to_category;
		
					unset($options);
					$options['max_levels'] = 0;
					$options['parent'] = $category['id'];
					$categories = discussion_forum_categories_fetch($options);
					$out .= discussion_forum_categories_list($categories);
				
					$out .= '<h2>Trådar</h2>' . "\n";
					$post_options['forum_id'] = $category['id'];
					$post_options['threads_only'] = true;
					$post_options['order_by_sticky'] = true;
					$post_options['page_offset'] = $request['page_offset'];
					$post_options['url_lookup'] = true;
					$threads = discussion_forum_post_fetch($post_options);
					//$threads['url'] = $path_to_trailing_category = array_pop($path_to_category) . '/' . $thread['handle'] . '/sida_1.php';
					$out .= discussion_forum_thread_list($threads);
					
					$path_to_trailing_category = array_pop($path_to_category);
					$out .= '<a href="' . $path_to_trailing_category['url'] . '">Skapa en egen tråd länkad till artikeln</a>' . '<br style="clear: both;" />';
					
					forum_update_category_session(array('category' => $category, 'threads' => $threads));
				}
			}
			
			if($article['commentable'] == 1)
			{
					$out .= rounded_corners_top(array('color' => 'blue_deluxe'));
				if($article['rankable'] == 1)
				{
					$out .= rank_input_draw($article['id'], 'articles');
				}
			$out .= comments_input_draw($article['id'], 'articles');
			$out .= '<div style="clear: both;"></div>' . "\n";
			$out .= rounded_corners_bottom();
			$out .= comments_list($article['id'], 'articles');
			}
		}
		
			
					
		
		if(is_privilegied('articles_admin'))
		{
			$out .= '<a href="/artiklar/index.php?action=admin&article=edit&id=' . $article['id'] . '">Ändra i artikeln</a>' . "\n";
		}
		return $out;
	}

?>