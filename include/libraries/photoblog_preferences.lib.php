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
				$photoblog_preferences_init_sql = 'INSERT INTO photoblog_preferences SET';
				$photoblog_preferences_default_values_count = count($photoblog_preferences_default_values);
				$count = 0;
				foreach ($photoblog_preferences_default_values as $default_key => $default_val)
				{
					if (is_numeric($default_val))
					{
						$photoblog_preferences_init_sql .= ' ' . $default_key . ' = ' . $default_val;
					}
					else
					{
						$photoblog_preferences_init_sql .= ' ' . $default_key . ' = "' . $default_val . '"';
					}
					$count++;
					if ($count != $photoblog_preferences_default_values_count)
					{
						$photoblog_preferences_init_sql .= ',';
					}
				}
				mysql_query($photoblog_preferences_init_sql) or report_sql_error($photoblog_preferences_init_sql, __FILE__, __LINE__);
				$photoblog_preferences_fetch_data = $photoblog_preferences_default_values;
			}
			else
			{
				$photoblog_preferences_fetch_data = mysql_fetch_assoc($photoblog_preferences_fetch_result);
			}
			
			return $photoblog_preferences_fetch_data;
		}
		
		function save($photoblog_preferences_save_values)
		{
			$photoblog_preferences_save_sql = 'UPDATE photoblog_preferences WHERE user_id = ' . $_SESSION['login']['id'] . ' SET';
			$photoblog_preferences_save_values_count = count($photoblog_preferences_save_values);
			$count = 0;
			foreach ($photoblog_preferences_save_values as $values_key => $values_val)
			{
				if (is_numeric($values_val))
				{
					$photoblog_preferences_save_sql .= ' ' . $values_key . ' = ' . $values_val;
				}
				else
				{
					$photoblog_preferences_save_sql .= ' ' . $values_key . ' = "' . $values_val . '"';
				}
				$count++;
				if ($count != $photoblog_preferences_save_values_count)
				{
					$photoblog_preferences_save_sql .= ',';
				}
			}
			if (mysql_query($photoblog_preferences_save_sql))
			{
				return true;
			}
			else
			{
				report_sql_error($photoblog_preferences_save_sql, __FILE__, __LINE__);
				return false;
			}
		}
	}
?>