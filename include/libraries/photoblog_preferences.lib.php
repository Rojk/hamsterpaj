<?php
	class photoblog_preferences 
	{
		function fetch($user_id)
		{
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
			$photoblog_preferences_fetch_data = mysql_fetch_assoc($photoblog_preferences_fetch_result);
			
			return $photoblog_preferences_fetch_data;
		}
	}
?>