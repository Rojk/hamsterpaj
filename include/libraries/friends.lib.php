<?php
	function friends_fetch($options)
	{
		$query = 'SELECT f.user_id AS user_id, f.friend_id AS friend_id, l.username, l.lastaction, l.lastrealaction, l.session_id, u.user_status, u.image, u.gender, u.birthday';
		$query .= ' FROM friendslist AS f, login AS l, userinfo AS u';
		$query .= ' WHERE 1';
		$query .=  isset($options['user_id']) ? ' AND f.user_id = "' . $options['user_id'] . '"' : '';
		$query .=  isset($options['friend_id']) ? ' AND f.friend_id = "' . $options['friend_id'] . '"' : '';
		$query .= ' AND l.id = f.friend_id';
		$query .= ' AND is_removed = 0';
		$query .= ' ORDER BY l.username ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		while($data = mysql_fetch_assoc($result))
		{
			$friends[$data['friend_id']] = $data; // Save in array
		}
		
		return $friends;
	}
	
	function friends_fetch_online_smart($options)
	{
		$query = 'SELECT f.friend_id AS user_id, l.username, l.lastaction, l.lastrealaction, u.user_status';
		$query .= ' FROM friendslist AS f, login AS l, userinfo AS u';
		$query .= ' WHERE f.user_id = "' . $options['user_id'] . '" AND l.id = f.friend_id AND u.userid = l.id AND is_removed = 0 AND l.lastaction >= "' . (time()-180) . '" AND l.lastrealaction >= "' . (time()-600) . '"';
		$query .= ' ORDER BY l.username ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		while($data = mysql_fetch_assoc($result))
		{
			$friends[$data['user_id']] = $data; // Save in array
		}
		
		return $friends;
	}
	
	function friends_actions_fetch($options)
	{
		$query = 'SELECT f.friend_id AS user_id, f.url, f.action_id, f.read, f.action, f.label, l.username, f.item_id';
		$query .= ' FROM friends_notices AS f, login AS l, userinfo AS u';
		$query .= ' WHERE 1';
		$query .=  isset($options['user_id']) ? ' AND f.user_id = "' . $options['user_id'] . '"' : '';
		$query .=  isset($options['friend_id']) ? ' AND f.friend_id = "' . $options['friend_id'] . '"' : '';
		$query .= ' AND l.id = f.friend_id';
		$query .= ' AND u.userid = l.id';
		$query .= ' AND l.is_removed = 0';
		$query .= $options['show'] == "new" ? ' AND f.read = "0"' : '';
		$query .= ' ORDER BY l.username ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		while($data = mysql_fetch_assoc($result))
		{
			$friends_notices[$data['user_id']]['actions'][$data['action_id']] = $data; // Save in array
			$friends_notices[$data['user_id']]['username'] = $data['username'];
			$friends_notices[$data['user_id']]['friend_id'] = $data['user_id'];
		}
		return $friends_notices;
	}
	
	function friends_actions_insert($options)
	{
		$query = 'SELECT user_id';
		$query .= ' FROM friendslist';
		$query .= ' WHERE';
		$query .= '	friend_id = "' . $_SESSION['login']['id'] . '"';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);	
		while($data = mysql_fetch_assoc($result))
		{
			$query_insert = 'INSERT INTO friends_notices (user_id, timestamp, friend_id, action, url, label, item_id)';
			$query_insert .= ' VALUES("' . $data['user_id'] . '", "' . time() . '", "' . $_SESSION['login']['id'] . '", "' . $options['action'] . '", "' . $options['url'] . '", "' . $options['label'] . '", "' . $options['item_id'] . '")';
			$result_insert = mysql_query($query_insert) or report_sql_error($query_insert, __FILE__, __LINE__);
		}
	}
	
	function friends_notices_remove($options)
	{
		$query = 'UPDATE friends_notices SET `read` = 1 WHERE 1';
		$query .= ' AND user_id = ' . $_SESSION['login']['id'];
		$query .= (isset($options['friend_id']) && is_numeric($options['friend_id'])) ?  ' AND friend_id = ' . $options['friend_id'] : '';
		$query .= (isset($options['item_id']) && is_numeric($options['item_id'])) ?  ' AND item_id = ' . $options['item_id'] : '';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);echo mysql_affected_rows();
	}
	
	function friends_notices_set_read($options)
	{
		if(!login_checklogin())
		{
			throw new Exception('Men tjockiskod, man måste ju vara inloggad för att kunna uppdatera statusen på vännernotiser!');
		}
		
		if(!isset($options['action']))
		{
			throw new Exception('Ännu mera tjockiskod, grattis! Ingen type sattes för vännernotisen som skulle uppdateras.');
		}
		
		if(!isset($options['item_id']) || !is_numeric($options['item_id']))
		{
			throw new Exception('Vännernotisen saknar helt klart ett item id för att kunna uppdateras...');
		}
		
		if(isset($_SESSION['friends_actions']))
		{
			foreach($_SESSION['friends_actions'] as &$friend)
			{
				foreach($friend['actions'] as &$action)
				{
					if(isset($action['item_id']) && is_numeric($action['item_id']) && $action['action'] == $options['action'] && $action['item_id'] == $options['item_id'])
					{
						friends_notices_remove(array(
							'item_id' => $action['item_id'],
							'action' => $action['action']
						));
						
						// Note: Both &$friend and &$action are reference pointers!
						unset($action);
						
						if(empty($friend['actions']))
						{
							unset($friend);
						}
					}
				}
			}
		}
	}
?>