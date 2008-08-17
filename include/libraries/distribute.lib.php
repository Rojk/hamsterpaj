<?php
/* The library of the Hamsterpaj Distribute System
	A part of the entertain system
*/

function distribute_item($options)
{
	/* 
	Observe! Before you call this function, make sure the file exists: master_url/distribute_master/type/handle.extension
	
	option		explanation
	type		entertain type
	handle		the url encoded handle for the item
	extension	file extension
	*/
	//retrieve servers serving the right type
	unset($fetch);
	$fetch['types'][] = $options['type'];
	$servers = distribute_server_get($fetch);
	//loop servers, send a retrieve order to each one
	foreach($servers as $server)
	{
		$command = 'wget "http://' . $server['address'] . '/distribute/distribute.slave.php?action=retrieve&handle=' . $options['handle'] . 
																			'&extension=' . $options['extension'] . 
																			'&type=' . $options['type'] . 
																			'" -o /dev/null -O /dev/null';
		exec($command, $output, $return_value);
		log_to_file('distribute', LOGLEVEL_DEBUG, __FILE__, __LINE__, ' command: '  . $command . ' return_value: ' . $return_value, $output);
	}
}

function distribute_item_delete($options)
{
	/* 
	option		explanation
	type		the entertain type
	item_id		the numeric item id
	handle		the url encoded handle for the item
	*/
	$query = 'DELETE FROM distributed_items WHERE type = "' . $options['type'] . '" AND item_id';
	if(isset($options['item_id']))
	{
		$query .= ' = "' . $options['item_id'] . '"';
	}
	else
	{
		$query .= ' IN (SELECT id FROM entertain_items WHERE handle = "' . $options['handle'] . '" LIMIT 1)';
	}
	$result = mysql_query($query) or die(report_sql_error($query));
	log_to_file('distribute', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'item deleted, type: ' . $options['type'] . ' id: ' . $options['id'] . ' handle: ' . $options['handle'], $result);
	//todo! Delete item on distribute servers
}

function distribute_item_status_set($options)
{
	/* 
	option		explanation
	type		array of entertain types
	item_id		the numeric item id
	handle		the url encoded handle for the item
				one of item_id and handle should be specified
	server_id	the numeric id of the server
	status		the status of this item on this server
	*/
	$query_insert = 'INSERT INTO distributed_items (item_id, type, server_id, status) VALUES (';
	if(isset($options['item_id']))
	{
		$query_insert .= '"' . $options['item_id'] . '"';
	}
	else
	{
		$query_insert .= '(SELECT id FROM entertain_items WHERE handle = "' . $options['handle'] . '")';
	}
	$query_insert .= ', "' . $options['type'] . '", "' . $options['server_id'] . '", "' . $options['status'] . '")';
	
	$query_update = 'UPDATE distributed_items SET status = "' . $options['status'] . '"';
	$query_update .= ' WHERE type = "' . $options['type'] . '" AND server_id = "' . $options['server_id'] . '" AND item_id';
	if(isset($options['item_id']))
	{
		$query_update .= ' = "' . $options['item_id'] . '"';
	}
	else
	{
		$query_update .= ' IN (SELECT id FROM entertain_items WHERE handle = "' . $options['handle'] . '")';
	}
	if(mysql_query($query_insert))
	{
		log_to_file('distribute', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'item status set: ' . $options['status'], $query_insert);
	}
	else
	{
		mysql_query($query_update) or die(report_sql_error($query));
		log_to_file('distribute', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'item status set: ' . $options['status'], $query_update);
	}
}

function distribute_server_mod($options)
{
	/* 
	option		explanation
	types		array of entertain types supported by this server
	id			the numeric id of the server
	address		the url of the server
	status		status of this server
	*/
	
	$query = 'UPDATE distribute_servers SET';
	foreach($options['types'] as $type)
	{
		$query .= ' supports_' . $type .' = 1';
	}
	if(isset($options['address']))
	{
		$query .= ', address = "' . $options['address'] . '"';
	}
	if(isset($options['status']))
	{
		$qeury .= ', status = "' . $options['status'] . '"';
	}
	$query .= ' WHERE id = "' . $options['id'] . '"';
	$result = mysql_query($query) or die(report_sql_error($query));
	log_to_file('distribute', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'server modified', $query);
}

function distribute_server_get($options)
{
	/* Use this function to retrieve a list of servers to distribute items to 
		or to retrieve a server that hosts an item
	options		explenation
	-------------------------------------------------------
	types		array of required types
	status		required status (not implemented, defaults to 'active')
	item_handle		only servers holding a copy of this item
	item_type	item type
	
	return array
	key			value
	-------------------------------------------------------
	server_id	server id
	address		valid internet host name
	*/
	global $entertain_types;
	if(isset($options['item_handle']))
	{
		//find servers hosting this item
		$query = 'SELECT ds.server_id server_id, ds.address address FROM distributed_items di, distribute_servers ds, entertain_items items' .
					' WHERE di.server_id = ds.server_id ' .
					' AND items.id = di.item_id ' .
					' AND items.handle = "' . $options['item_handle'] . '"' . 
					' AND di.type = "' . $options['type'] . '"' .
					' AND di.status = "ok"' .
					' AND ds.status = "active"' .
					' ORDER BY RAND() LIMIT 1';
		log_to_file('distribute', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'fetching server', $query);
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if($data = mysql_fetch_assoc($result))
		{
			$server['server_id'] = $data['server_id'];
			$server['address'] = $data['address'];
		}
		log_to_file('distribute', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'server fetched', print_r($server, true));
		return $server;
	}
	// This case can be used when you need a list of servers that supports the types you specify.
	// Each server will be listed once for every type that it supports.
	elseif(isset($options['types']))
	{
		$query = 'SELECT ds.server_id as server_id, ds.address as address'
					. ' FROM distribute_servers ds, distribute_servers_types dst'
					. ' WHERE ds.server_id = dst.server_id AND ds.status = "active"'
					. ' AND type IN ("' . implode('", "', $options['types']) . '")';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		log_to_file('distribute', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'servers fetched', $query);
		while($data = mysql_fetch_assoc($result))
		{
			$servers[] = $data;
		}
		return $servers;
	}
}

?>