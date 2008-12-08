<?php
echo 'fuck';
		$photoblog_preferences_default_values['color_main'] = 'FFFF00';
		$photoblog_preferences_default_values['color_detail'] = 'FF00FF';
		$photoblog_preferences_default_values['hamster_guard'] = 0;
		function fetch($user_id)
		{
			global $photoblog_preferences_default;
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
			}
			$photoblog_preferences_fetch_data = mysql_fetch_assoc($photoblog_preferences_fetch_result);
			
			return $photoblog_preferences_fetch_data;
		}
		
		function save($values)
		{
			$photoblog_preferences_save_sql = 'UPDATE ';
		}
	
?>