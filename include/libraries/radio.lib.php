<?php
function radio_shoutcast_fetch() {
	$scs = &new ShoutcastInfo('kaizoku.se:8000');
	if($scs->connect())
	{
		$scs->send();
		$data = $scs->parse();
		$scs->close();
	}
	return $data;
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
	$query .= ($options['broadcasting'] == 'yes') ? ' AND NOW() BETWEEN rs.starttime AND rs.endtime' : '';
	$query .= ($options['broadcasting'] == 'no') ? ' AND NOW() NOT BETWEEN rs.starttime AND rs.endtime' : '';
	$query .= ($options['show_sent'] == no) ? ' AND NOW() < rs.starttime ' : ''; // Show programs that already been sent?
	$query .= ' ORDER BY ' . $options['order-by'] . ' ' . $options['order-direction'] . ' LIMIT ' . $options['offset'] . ', ' . $options['limit'];
		
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$schedule = array();
	while($data = mysql_fetch_assoc($result))
	{
		$schedule[] = $data;
		$found_something = true;
	}
	
	return $schedule;
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
	
	return $schedule;
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
	
	return $schedule;
}
?>