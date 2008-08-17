<?php

	function event_log_log($event)
	{
		$insertquery = 'INSERT INTO event_log (`date`, event, count, hour) VALUES("' . date('Y-m-d') . '", "' . $event . '", 1, "' . date('H') . '")';
		$updatequery = 'UPDATE event_log SET count = count + 1 WHERE date = "' . date('Y-m-d') . '" AND event = "' . $event . '" AND hour = "' . date('H') . '" LIMIT 1';
		
		mysql_query($updatequery);
		if(mysql_affected_rows() != 1)
		{
			mysql_query($insertquery);
		}
	}

?>