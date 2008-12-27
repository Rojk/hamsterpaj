<?php
	class photoblog_preferences
	{
		function fetch($user_id, $photoblog_preferences_default_values)
		{
			global $photoblog_preferences_default_values;
			$user_id = (!empty($user_id)) ? $user_id : $_SESSION['login']['id'];
			if(!is_numeric($user_id))
			{
				throw new Exception('$user_id must be a numerical value');
			}
			elseif (!login_checklogin())
			{
				throw new Exception('You must be logged in to load your photoblog preferences');
			}
			
			$photoblog_preferences_fetch_sql = 'SELECT * FROM photoblog_preferences WHERE user_id = ' . $_SESSION['login']['id'] . ' LIMIT 1';
			$photoblog_preferences_fetch_result = mysql_query($photoblog_preferences_fetch_sql);
			if (mysql_num_rows($photoblog_preferences_fetch_result) == 0)
			{
				$sql = 'INSERT INTO photoblog_preferences SET';
				$photoblog_preferences_default_values_count = count($photoblog_preferences_default_values);
				$count = 0;
				foreach ($photoblog_preferences_default_values as $default_key => $default_val)
				{
					if (is_numeric($default_val))
					{
						$sql .= ' ' . $default_key . ' = ' . $default_val;
					}
					else
					{
						$sql .= ' ' . $default_key . ' = "' . $default_val . '"';
					}
					$count++;
					if ($count != $photoblog_preferences_default_values_count)
					{
						$sql .= ',';
					}
				}
				mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
				$photoblog_preferences_fetch_data = $photoblog_preferences_default_values;
			}
			else
			{
				$photoblog_preferences_fetch_data = mysql_fetch_assoc($photoblog_preferences_fetch_result);
			}
			
			return $photoblog_preferences_fetch_data;
		}
		
		function save($values)
		{
			$sql = 'UPDATE photoblog_preferences SET';
			$values_count = count($values);
			//UPDATE photoblog_preferences SET color_main = "FFFFFF", color_detail = "FFFFFF", hamster_guard_on = 1 WHERE user_id = 879696 LIMIT 1
			$count = 0;
			foreach ($values as $values_key => $values_val)
			{
				$sql .= ' ' . $values_key . ' = "' . $values_val . '"';
				$count++;
				if ($count != $values_count)
				{
					$sql .= ',';
				}
			}
			$sql .= ' WHERE user_id = ' . $_SESSION['login']['id'] . '';

			if (mysql_query($sql))
			{
				return true;
			}
			else
			{
				report_sql_error($sql, __FILE__, __LINE__);
				return false;
			}
		}
	}
?>