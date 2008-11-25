<?php
	function msnbot_is_valid_msn($msn)
	{
		return true;
	}
	
	function msnbot_is_valid_salt($options)
	{
		if(!defined('MSNBOT_SALT'))
		{
			die('MSNBOT_SALT constant is not defined. This script can only be run from dynamic01.');
		}
		return $options['salt'] == sha1(MSNBOT_SALT . md5(strrev(MSNBOT_SALT) . $options['msn']));
	}
	
	function msnbot_queue_add($options)
	{
		if(!is_numeric($options['user_id']))
		{
			throw new Exception('user_id not numeric');
		}
		if(!isset($options['msn']))
		{
			$query = 'SELECT msnbot_msn FROM userinfo WHERE userid = ' . $options['user_id'];
			$result = mysql_query($query) or report_sql_error($query);
			$data = mysql_fetch_assoc($result);
			$options['msn'] = $data['msnbot_msn'];
		}
		
		if($options['msn'] != '')
		{
			$query = mysql_query('INSERT INTO msnbot (user_id, msn, message) VALUES (' . $options['user_id'] . ', "' . $options['msn'] . '", "' . $options['message'] . '")');
		}
	}
	
	// The quick 'n dirty solution this time... It's called "deadlines", and it's really; really bad...
	function msnbot_queue_add_everyone($options)
	{
		$query = 'INSERT msnbot (user_id, msn, message) SELECT userid AS user_id, msnbot_msn AS msn, "' . $options['message'] . '" AS message FROM userinfo WHERE msnbot_msn IS NOT NULL';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
?>