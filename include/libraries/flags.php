<?php

	function flags_clear_category($user, $category)
	{
	
	}


	function flags_get_category($flag_id)
	{
	
	}
	
	

	function flags_set($id, $type, $flag)
	{
		$query = 'INSERT INTO flags (object_id, object_type, flag) VALUES("' . $id . '", "' . $type . '", "' . $flag . '")';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));		
	}

?>