<?php
/* Tag library
		Library of functions to handle item tags.
*/

//rensa html-kod...
//htmlspecialchars()
//to lowercase på alla tag labels



/*	Returns an array of all tags associated with the item with id 'item_id'
*/
function tag_get_by_item($object_type, $item_id)
{
  	$query = 'SELECT o.tag_id, o.value, t.label, t.handle';
		$query .= ' FROM object_tags o, tags t';
  	$query .= ' WHERE reference_id = "' . $item_id . '" AND o.object_type = "' . $object_type . '" AND o.tag_id = t.id';
  	log_to_file('tags', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'tag_get_by_item runs sql query: ', $query);
  	$result = mysql_query($query) or die(report_sql_error($query));
	$tags = array();
	while($data = mysql_fetch_assoc($result))
	{
		$tags[$data['handle']] = $data;
	}
	return $tags;
}

function tags_items_get_by_tags($options)
{
	/*
	option		array_support	description
	set on of these tree:
	handle		yes				tag handles
	label		yes				tag(s) as free text
	id			yes				tag ids

	types		yes				the types of objects you want ('discussion', 'post', 'wallpaper', 'article', 'survey', 'game', 'clip', 'a1')
	
	return
	an array with one array for each requested type of objects
	*/

	if(isset($options['handle']))
	{
		$handles = is_array($options['handle']) ? $options['handle'] : array($options['handle']);
	}
	elseif(isset($options['label']))
	{
		$labels = (is_array($options['label']) ? $options['label'] : array($options['label']));
		foreach($labels as $label)
		{
			$handles[] = url_secure_string($label);
		}
	}
	elseif(isset($options['id']))
	{
		$ids = is_array($options['id']) ? $options['id'] : array($options['id']);
	}
	$query = 'SELECT ot.id, ot.reference_id, ot.object_type FROM object_tags AS ot';
	if(isset($handles))
	{
		$query .= ', tags AS t';
	}
	$query .= ' WHERE';
	if(isset($handles))
	{
		$query .= ' ot.tag_id = t.id AND t.handle IN ("' . implode('", "', $handles) . '")';
	}
	else
	{
		$query .= ' ot.id IN ("' . implode('", "', $ids) . '")';
	}
	if(isset($options['types']))
	{
		$query .= ' AND object_type IN ("' . implode('", "', $options['types']) . '")';
	}
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$items[$data['object_type']][] = $data['reference_id'];
	}
	foreach($items as $key => $items_of_one_type)
	{
		$items_new[$key] = array_unique($items_of_one_type);
	}
	return $items_new;
}

/*	Returns an array of all items (item_id and type) matching the conditions 
		set by $options['tags_require'], $options['tags_omit'] and 
		$options['tags_one_of']. The type is an enum value ('discussion' | 'post' 
		| 'wallpaper').
		
		Please note that this function only recieves IDs!
*/
function tag_get_items($options)
{
	$query = 'SELECT ot.reference_id AS id, ot.object_type FROM object_tags as ot';
	$count = 0;
	foreach($options['tags_require'] as $tag)
	{
		$query .= ', object_tags AS t' . $count++;
	}
	$query .=' WHERE ot.object_type IN("' . implode('", "',$options['types']) . '")';
	$count = 0;
	foreach($options['tags_require'] as $tag)
	{
		$query .= ' AND t' . $count . '.tag_id = "' . $tag . '" AND ot.reference_id = t' . $count . '.reference_id';
	}
	$query .= ' AND ot.tag_id IN ("' . implode('", "',$options['tags_one_of']) . '")';
	$query .= ' AND NOT ot.tag_id IN ("' . implode('", "',$options['tags_omit']) . '")';
log_to_file('tags', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
						'tag_get_by_item runs sql query: ', $query);
	$result = mysql_query($query) or die(report_sql_error($query));
	$items = array();
	while($data = mysql_fetch_assoc($result))
	{
		$items[$data['id']] = $data['object_type'];
	}
	return $items;
}

/* Updates a tag
*/
function tag_update($options)
{
	if(array_key_exists('tag_id',$options))
	{
		$query = 'UPDATE tags SET label = "' . $options['label'] . '", handle = "' . url_secure_string($options['label']) . '" WHERE id = ' . $options['tag_id'];
	log_to_file('tags', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
							'tag_update runs sql query: ', $query);
		$result = mysql_query($query) or die(report_sql_error($query));
		return true;
	}
	else
	{
		return false;
	}
}

/*	Creates a tag with an optional label
		label		string	the freetext associated with this tag
		return: The new tags identifier number (tag_id)
*/
function tag_create($label)
{
	$label = strtolower(trim($label));
	$query = 'INSERT INTO tags (label, handle) VALUES ("' . $label . '", "' . url_secure_string($label) . '")';
	log_to_file('tags', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
							'tag_create runs sql query: ', $query);
	$result = mysql_query($query) or die(report_sql_error($query));
	return mysql_insert_id();
}
	
/*	Set tags on an $item_id. Set tags and if applicable, values in the 
	$options array, $tags['tag_id'](['value'])
*/
function tag_set($item_id, $object_type, $tags)
{
  	foreach($tags as $tag)
  	{
  		// implementera value!!!!
  		// TODO!
		if(isset($tag['value']))
		{
			$query = 'INSERT INTO object_tags (tag_id, reference_id, object_type, value)';
			$query .= ' VALUES ("' . $tag['tag_id'] . '", "' . $item_id;
			$query .= '", "' . $object_type . '", "' . $tag['value'] . '")';
		}
		else
		{
			$query = 'INSERT INTO object_tags (tag_id, reference_id, object_type)';
			$query .= ' VALUES ("' . $tag['tag_id'] . '", "' . $item_id;
			$query .= '", "' . $object_type . '")';
	  	}
  		log_to_file('tags', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
	  							'tag_get_by_item runs sql query: ', $query);
  		if(mysql_query($query))
  		{
			//if the tag wasn't set before
			$query = 'UPDATE tags SET popularity = IF(popularity IS NULL, 1, popularity + 1) WHERE id = "' .$tag['tag_id'] . '" LIMIT 1';
			mysql_query($query) or die(report_sql_error($query));
		}
  	}
  	return true;
}
  
 /*
	In the $tags variable, please pass one or more tag IDs
 */
function tag_remove($item_id, $object_type, $tags)
{
	$tags = (is_array($tags)) ? $tags : array($tags);
	
	foreach($tags AS $tag)
	{
		$query = 'DELETE FROM object_tags WHERE tag_id = "' . $tag . '" AND object_type = "' . $object_type . '" AND reference_id = "' . $item_id . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		$query = 'UPDATE tags SET popularity = popularity - 1 WHERE id = "' . $tag . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
}

/*	Returns tag_id if a tag exists with the label $text, otherwise an array of 
		close matches (if $options['return_array'] is set) or false if there are 
		no matches (or if $options['return_array'] is not set).
*/
function tag_exists($label, $options)
{
  	$query = 'SELECT id, handle FROM tags WHERE label LIKE "' . $label . '"';
		log_to_file('tags', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
								'tag_exists runs sql query: ', $query);
  	$result = mysql_query($query) or die(report_sql_error($query));
  	
  	if(mysql_num_rows($result) == 1)
  	{
  		$data = mysql_fetch_assoc($result);
  		$return['status'] = 'exists';
			$return['id'] = $data['id'];
			$return['handle'] = $data['handle'];
  	}
  	elseif(mysql_num_rows($result) == 0)
  	{
  		if(array_key_exists('return_array',$options))
	  	{
	  		$query = 'SELECT id, label FROM tags WHERE label SOUNDS LIKE "' . $label . '"';
				log_to_file('tags', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
	  								'tag_exists runs sql query: ', $query);
	  		$result = mysql_query($query) or die(report_sql_error($query));
	  		$tags = array();
	  		$return['status'] = 'close_match';
	  		$count = 0;
	  		while($data = mysql_fetch_assoc($query))
	  		{
					$return['matches'][$count]['id'] = $data['id'];
					$return['matches'][$count]['label'] = $data['label'];
	  		}
	  	}
	  	else
	  	{
	  		$return['status'] = 'missing';
	  	}
  	}
  	else
  	{
  		log_to_file('tags', LOGLEVEL_ERROR, __FILE__, __LINE__, 'error: multiple tag labels in database.', $result);
	 		$return['status'] = 'error';
  	}
	return $return;
}

/*	Joins all tags in $tags with $tag. All tagging of items with these tags 
		will be replaced by $tag and the old tags will be removed from the 
		database. $tag and $tags contains tag ids.
*/
function tag_join($tag, $tags)
{
	$query = 'UPDATE object_tags SET tag_id = ' . $tag . ' WHERE tag_id IN ("';
	$query .= implode('", "', $tags) . '")';
	log_to_file('tags', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
							'tag_join runs sql query: ', $query);
	mysql_query($query) or die(report_sql_error($query));
	
	$query = 'REMOVE FROM tags WHERE id IN ("' . implode('", "', $tags) . '")';
	log_to_file('tags', LOGLEVEL_DEBUG, __FILE__, __LINE__, 
							'tag_join runs sql query: ', $query);
	mysql_query($query) or die(report_sql_error($query));
}

function tag_get_by_handle($handles)
{
	$return_array = is_array($handles);
	$handles = is_array($handles) ? $handles : array($handles);
	$query = 'SELECT * FROM tags WHERE handle IN ("' . implode('", "', $handles) . '")';
	$result = mysql_query($query) or die(report_sql_error($query));
	if($return_array)
	{
	  $return = array();
	  while($data = mysql_fetch_assoc($result))
	  {
		  $return[$data['handle']] = $data;
	  }
	  return $return;
	}
	elseif($data = mysql_fetch_assoc($result))
	{
		return $data;
	}
	return false;
}

function tag_set_wrap($options)
{
	/*
	Obs! Removes all old tags
	
	options			array support		possible values
	item_id			no
	object_type		no					'discussion', 'post', 'wallpaper', 'article', 'survey', 'game', 'clip', 'a1'
	tag_handle		yes					handle_type_values
	tag_label		yes					Free text values
	tag_id			yes					1, 2, 3..
	add				no					set to true if tags should be added to old tags
	*/
	if(644314 == $_SESSION['login']['id'])
	{
		preint_r($options);
	}
	$keys = array('tag_handle', 'tag_label', 'tag_id');
	foreach($keys as $key)
	{
		if(isset($options[$key]))
		{
			$options[$key] = is_array($options[$key]) ? $options[$key] : array($options[$key]);
			$keytype = $key;
		}
	}
	if($keytype == 'tag_label')
	{
		foreach($options['tag_label'] as $label)
		{
			if(!($tag = tag_get_by_handle(url_secure_string($label))))
			{
				$tag_id = tag_create($label);
			}
			else
			{
				$tag_id = $tag['id'];
			}
			$tag_ids[] = $tag_id;
		}
	}
	elseif($keytype == 'tag_handle')
	{
		$tags = tag_get_by_handle($options['tag_handle']);
		unset($tag_ids);
		foreach($tags as $tag)
		{
			$tag_ids[] = $tag['id'];
		}
	}
	if(!isset($options['add']))
	{
		$query = 'DELETE FROM object_tags WHERE object_type = "' . $options['object_type'] . '" AND reference_id = "' . $options['item_id'] . '"';
		mysql_query($query);
	}
	foreach($tag_ids as $tag_id)
	{
		$query = 'INSERT INTO object_tags (tag_id, object_type, reference_id)';
		$query .= ' VALUES ("' . $tag_id . '", "' . $options['object_type'] . '", "' . $options['item_id'] . '")';
		mysql_query($query); //todo! annan felhantering här då det kan hända att man försöker sätta redan satta taggar, or die(report_sql_error($query, __FILE__, __LINE__));
	}
}


?>
