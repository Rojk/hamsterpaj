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
		$query .= (!$options['show_sent']) ? ' AND NOW() < rs.starttime ' : ''; // Show programs that already been sent?
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
		$radio_djs_add_sql = 'INSERT INTO radio_djs SET information = "' . $dj_information . '", user_id = ' . $dj_user_id . '';
		if (mysql_query($radio_djs_add_sql))
		{
			return true;
		}
		else
		{
			report_sql_error($radio_djs_add_sql, __FILE__, __LINE__);
			throw new Exception('Något gick fel i ett MySQL-query.<br />' . mysql_error() . '');
		}
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
	
	function radio_sending_fetch()
	{
		return true;
	}
?>