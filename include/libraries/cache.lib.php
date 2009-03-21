<?php
	function cache_load($handle)
	{
		$serialized = file_get_contents(PATHS_CACHE . $handle . '.phpserialized');
		$data = unserialize($serialized);
		return $data;
	}
	
	function cache_save($handle, $data)
	{
		$serialized = serialize($data);
		$file = fopen(PATHS_CACHE . $handle . '.phpserialized', 'w');
		fwrite($file, $serialized);
		fclose($file);
	}

	function cache_last_update($handle)
	{
		return filemtime(PATHS_CACHE . $handle . '.phpserialized');
	}

	$QUERY_CACHE_CACHE = array();
	function query_cache($options)
	{
		GLOBAL $QUERY_CACHE_CACHE;
		
		if(strpos('u.birthday < 1970', $options['query']) === true)
		{
			die('ERROR!!!');	
		}
		$options['category'] = (isset($options['category'])) ? $options['category'] : 'other';
		$options['max_delay'] = (isset($options['max_delay'])) ? $options['max_delay'] : 300;
		
		if (!is_dir(PATHS_CACHE))
		{
			mkdir(PATHS_CACHE);
		}
		if (!is_dir(PATHS_CACHE . 'query_cache/'))
		{
			mkdir(PATHS_CACHE . 'query_cache/');
		}
		
		$path = PATHS_CACHE . 'query_cache/' . $options['category'] . '/';
		$filename = md5($options['query']) . '.phpserialized';
		
		
		if(isset($QUERY_CACHE_CACHE[$filename]))
		{
			return $QUERY_CACHE_CACHE[$filename];
		}

		if(!is_dir($path))
		{
			mkdir($path);
		}
		
		if(!file_exists($path . $filename))
		{
			trace('new_query_cache_' . $options['category'], $options['query']);
		}
		
		if(filemtime($path . $filename) < time() - $options['max_delay'])
		{
			$result = mysql_query($options['query']) or report_sql_error($query, __FILE__, __LINE__);
			while($row = mysql_fetch_assoc($result))
			{
				$data[] = $row;
			}
			$serialized = serialize($data);
			//trace('query_cache', 'Creating file for query: ' . $options['query']);
			file_put_contents($path . $filename, $serialized);
		}
		else
		{
			$data = unserialize(file_get_contents($path . $filename));
		}
		
		$QUERY_CACHE_CACHE[$filename] = $data;		

		return $data;
	}	
?>