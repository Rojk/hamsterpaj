<?php
	function radio_shoutcast_fetch()
	{
		$scs = &new ShoutcastInfo(RADIO_SERVER);
		if($scs->connect())
		{
			$scs->send();
			$data = $scs->parse();
			$scs->close();
			
			return $data;
		}
		else
		{
			return false;
		}
	}
	
	function radio_schedule_fetch($options)
	{
		$options['order-by'] = (in_array($options['order-by'], array('rs.starttime'))) ? $options['order-by'] : 'rs.starttime';
		$options['order-direction'] = (in_array($options['order-direction'], array('ASC', 'DESC'))) ? $options['order-direction'] : 'DESC';
		$options['offset'] = (isset($options['offset']) && is_numeric($options['offset'])) ? $options['offset'] : 0;
		$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 9999;
		
		$query = 'SELECT rs.*, l.username, rp.*';
		$query .= ' FROM radio_schedule AS rs, login AS l, radio_programs AS rp';
		$query .= ' WHERE l.id = rp.user_id';
		$query .= ' AND rp.id = rs.program_id';
		$query .= (isset($options['id'])) ? ' AND rs.id IN("' . implode('", "', $options['id']) . '")' : '';
		$query .= (isset($options['user'])) ? ' AND rp.user_id  = "' . $options['user'] . '"' : '';
		$query .= ($options['broadcasting']) ? ' AND NOW() BETWEEN rs.starttime AND rs.endtime' : ' AND NOW() NOT BETWEEN rs.starttime AND rs.endtime';
		$query .= (!$options['show_sent'] && !$options['broadcasting']) ? ' AND NOW() < rs.starttime ' : ''; // Show programs that already been sent?
		$query .= ' ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$schedule = array();
		while($data = mysql_fetch_assoc($result))
		{
			$schedule[] = $data;
			$found_something = true;
		}
		
		if ($found_something)
		{
			return $schedule;
		}
		else
		{
			return false;
		}
	}
	
	function radio_djs_fetch($options)
	{
		$options['order-by'] = (in_array($options['order-by'], array('l.username'))) ? $options['order-by'] : 'l.username';
		$options['order-direction'] = (in_array($options['order-direction'], array('ASC', 'DESC'))) ? $options['order-direction'] : 'DESC';
		$options['offset'] = (isset($options['offset']) && is_numeric($options['offset'])) ? $options['offset'] : 0;
		$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 9999;
		
		$query = 'SELECT rd.*, l.username';
		$query .= ' FROM radio_djs AS rd, login AS l';
		$query .= ' WHERE l.id = rd.user_id';
		$query .= (isset($options['id'])) ? ' AND rd.id IN("' . implode('", "', $options['id']) . '")' : '';
		$query .= (isset($options['user'])) ? ' AND rd.user_id  = "' . $options['user'] . '"' : '';
		$query .= ' ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
			
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$schedule = array();
		while($data = mysql_fetch_assoc($result))
		{
			$schedule[] = $data;
			$found_something = true;
		}
		
		if ($found_something)
		{
			return $schedule;
		}
		else
		{
			return false;
		}
	}
	
	function radio_dj_add($dj_user_id, $dj_information)
	{
		if(!is_numeric($dj_user_id))
		{
			throw new Exception('User ID not numeric');
		}

		$query = 'INSERT INTO radio_djs (information, user_id) VALUES("' . $dj_information . '", ' . $dj_user_id . ')';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		$query = 'INSERT INTO privilegies (privilegie, value, user) VALUES ("radio_sender", 0, ' . $dj_user_id . ')';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		return true;
	}
	
	function radio_dj_remove($options)
	{
		if(!is_numeric($options['user_id']))
		{
			throw new Exception('User ID not numeric');
		}

		$query = 'DELETE FROM radio_djs WHERE user_id = ' . $options['user_id'] . '';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		$query = 'DELETE FROM privilegies WHERE user = ' . $options['user_id'] . ' AND privilegie = "radio_sender"';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		return true;
	}
	
	function radio_programs_fetch($options)
	{
		$options['order-by'] = (in_array($options['order-by'], array('rp.name'))) ? $options['order-by'] : 'rp.name';
		$options['order-direction'] = (in_array($options['order-direction'], array('ASC', 'DESC'))) ? $options['order-direction'] : 'DESC';
		$options['offset'] = (isset($options['offset']) && is_numeric($options['offset'])) ? $options['offset'] : 0;
		$options['limit'] = (isset($options['limit']) && is_numeric($options['limit'])) ? $options['limit'] : 9999;
		
		$query = 'SELECT rp.*, l.username';
		$query .= ' FROM radio_programs AS rp, login AS l';
		$query .= ' WHERE l.id = rp.user_id';
		$query .= (isset($options['id'])) ? ' AND rp.id IN("' . implode('", "', $options['id']) . '")' : '';
		$query .= (isset($options['user'])) ? ' AND rp.user_id  = "' . $options['user'] . '"' : '';
		$query .= ' ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
			
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$schedule = array();
		while($data = mysql_fetch_assoc($result))
		{
			$schedule[] = $data;
			$found_something = true;
		}
		
		if ($found_something)
		{
			return $schedule;
		}
		else
		{
			return false;
		}
	}
	
	function radio_program_add($options)
	{
		if(!isset($options['user_id']) && isset($options['dj']))
		{
			$dj = array_pop(radio_djs_fetch(array('id' => array($options['dj']))));
			$options['user_id'] = $dj['user_id'];
		}
		
		$query = 'INSERT INTO radio_programs (user_id, name, information, sendtime) VALUES("' . implode('", "', array($options['user_id'], $options['name'], $options['information'], $options['sendtime'])) . '")';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
	function radio_program_remove($options)
	{
		if(!is_numeric($options['id']))
		{
			throw new Exception('Id is not numeric');
		}
		
		$query = 'DELETE FROM radio_programs WHERE id = ' . $options['id'] . '';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
	function radio_schedule_add($options)
	{		
		$query = 'INSERT INTO radio_schedule (program_id, starttime, endtime) VALUES("' . implode('", "', array($options['program_id'], $options['starttime'], $options['endtime'])) . '")';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
	function radio_sending_fetch()
	{
		return true;
	}
?>