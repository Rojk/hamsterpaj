<?php
	function knowledge_database_parse_request($options)
	{
		$page = substr($options['url'], strlen($options['url_prefix']));
		
		// NOTE: $page is NOT secure at all!
		switch($page)
		{
			case 'lagg_till.html':
				$add_form = knowledge_database_add_form($options);
				$output .= $add_form['output'];
				$title = $add_form['title'];
			break;
			
			case 'lagg_till_spara.html':
				$add_form = knowledge_database_add_from_save($options);
				$output .= $add_form['output'];
				$title = $add_form['title'];
			break;
				
			default:
				if(preg_match('/^([a-z_\/]+)([a-z_]+\.html)*$/', $page, $matches))
				{
					$options['item_page_matches'] = $matches;
					$item = knowledge_database_show_item($options);
					$output .= $item['output'];
					$title = $item['title'];
				}
				else
				{
					$output .= 'Typ... hej?';//$options['pages']['index']['output'];
					$title = 'Hamstern was here!';//$options['pages']['index']['title'];
				}
		}
		
		return array('response' => $output, 'title' => $title, 'draw_ui' => true);
	}
	
	function knowledge_database_add_form($options)
	{
		$output .= '<form method="post" action="' . $options['url_prefix'] . 'lagg_till.html' . '">' . "\n";
		
		$output .= '<select name="type">' . "\n";
		$output .= ' <option value="question">Fråga/svar</option>' . "\n";
		$output .= ' <option value="category">Kategori</option>' . "\n";
		$output .= '</select><br />' . "\n";
		
		$output .= 'Ettikett (titel på Svenska): <input type="text" name="label" value="" /> (Här anger du titel på kategori eller fråga/svar-post).<br />' . "\n";
		
		$output .= 'Placera i kategori:<br />' . "\n";
		
		$output .= knowledge_database_readable_categories_tree(knowledge_database_fetch_categories_tree($options));
		
		$output .= '</form>' . "\n";
		return array('output' => $output, 'title' => 'Lägg till...');
	}
	
	function knowledge_database_add_form_save($options)
	{
		$source = &$_POST;
		
		if(   isset($source['label'], $source['type'], $source['parent_item'], $source['content'])
		   && is_numeric($source['parent_item'])
		   && in_array($source['type'], array('category', 'question'))
		)
		{
			if(knowledge_database_create_item($create_options) !== false)
			{
				$output .= 'Så, då hade vi lagt till den. Nu kan du gå tillbaka och lägga till mer om du vill.';
			}
			else
			{
				$output .= 'Kunde inte skapa post!';
			}
		}
		else
		{
			$output .= 'Någonting gick lite fel där... Go back, try again och lite sådär!';
		}
		
		return array('output' => $output, 'title' => 'Sparat!');
	}
	
	function knowledge_database_create_item($options)
	{
		$handle = knowledge_database_get_handle(array('action' => 'new', 'from_string' => (empty($options['label']) ? '$EMPTY_LABEL$' : $options['label'])));
		
		$query = 'INSERT INTO knowledge_database (`database`, label, type, parent_item, content, handle)';
		$query .= ' VALUES(';
		$query .= '"' . $options['database'] . '"';
		$query .= ', "' . (empty($options['label']) ? '$EMPTY_LABEL$' : $options['label']) . '",';
		$query .= ', "' . (in_array($options['type'], array('category', 'question')) ? $options['type'] : 'category') . '"';
		$query .= ', ' . (is_numeric($options['parent_item']) ? intval($options['parent_item']) : 0);
		$query .= ', "' . ( (isset($options['content']) && !empty($options['content'])) ? $options['content'] : '') . '"';
		$query .= ', "' . $handle . '"';
		$query .= ')';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		return ($result) ? mysql_insert_id($result) : false;
	}
	
	function knowledge_database_get_handle($options)
	{
		switch($options['action'])
		{
			case 'new':
				$secured_string = url_secure_string($title);
				
				$handle = $secured_string;
				$query = 'SELECT id FROM knowledge_database WHERE handle LIKE "' . $handle . '" LIMIT 1';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
				for($i = 2; mysql_num_rows($result) > 0; $i++)
				{
					$handle = $secured_string . '_' . $i;
					$query = 'SELECT id FROM knowledge_database WHERE handle LIKE "' . $handle . '" LIMIT 1';
					$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);			
				}
				
				return $handle;
			break;
			
			case 'fetch':
				return 'Not completed yet...';
			break;
		}
	}
	
	function knowledge_database_fetch_categories_tree($options)
	{
		$query = 'SELECT id, label, parent_id FROM knowledge_database WHERE `database` = "' . $options['database'] . '" AND type = "category"';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		$categories_by_parent = array();
		while($data = mysql_fetch_assoc($result))
		{
			$categories_by_parent[intval($data['parent_id'])][] = array_merge($data, array('is_data' => true));
		}
		
		// $categories_by_parent: The key for each of the root-keys in the array has the same ID as its childrens "parent_id".
		return knowledge_database_fetch_categories_tree_walk($categories_by_parent);	
	}
	
	function knowledge_database_fetch_categories_tree_walk($categories)
	{
		$return = array();
		foreach($categories as $category)
		{
			if(array_key_exists('is_data', $category))
			{
				$return[] = $category;
			}
			else
			{
				$return[] = knowledge_database_fetch_categories_tree_walk($category);
			}
		}
		
		return $return;
	}
	
	function knowledge_database_readable_categories_tree($categories_tree, $categories_by_parent = false)
	{
		echo 'Fellund...';
		if(!is_array($categories_by_parent))
		{
			// It's the first time we iterate...
			$categories_by_parent = $categories_tree;
		}
		
		preint_r($categories_tree, rand(0, 99999));
		
		$output .= "\n" . '<ul>' . "\n";
		foreach($categories_tree as $category)
		{
			$output .= "\t" . '<li>' . (array_key_exists('is_data', $category) ? 'lolz<a href="#">' . $category['label'] . '</a>' . knowledge_database_readable_categories_tree($category, $categories_by_parent) : 'p3') . '</li>' . "\n";
		}
		$output .= '</ul>' . "\n";
		
		return $output;
	}
	
	function knowledge_database_show_item($options)
	{
		$matches = $options['item_page_matches'];// [1] = category [, [2] = question]
		
		if(strlen($matches[2]) > 0)
		{
			$page = $matches[1] . $matches[2];
			$item = substr( substr($page, strrpos($page, '/') + 1) , 0, -5);// Outer: Remove .html, Inner: Get item
			
			// Just to be sure...
			$item = mysql_real_escape_string($item);
			
			$query = 'SELECT label, content';
			$query .= ' FROM knowledge_database';
			$query .= ' WHERE `database` = "' . $options['database'] . '" AND type = "question"';
			$query .= ' AND handle LIKE "' . $item . '"';
			$query .= ' LIMIT 1';
			
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			if(mysql_num_rows($result) > 0)
			{
				$data = mysql_fetch_assoc($result);
				
				$output .= print_r($data, true);
			}
			else
			{
				$output .= 'Den artikeln kunde vi tyvärr inte hitta i systemet.';
			}
		}
		else
		{
			$page = $matches[1];
			$output .= 'Kategori';
		}
		
		return array('output' => $output, 'title' => 'Kunskapsdatabasen av och med Hamsterpaj');
	}
	
	function knowledge_database_authorizated($options)
	{
		// previlegies...
		return true;
	}
?>