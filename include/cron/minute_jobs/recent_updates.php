<?php
	$query = 'SELECT id, type, label, timestamp, url FROM recent_updates WHERE timestamp > ' . (time()-900) . ' ORDER BY id DESC LIMIT 1';
	$result = mysql_query($query);
	if(mysql_num_rows($result) == 1)
	{
		$data = mysql_fetch_assoc($result);
		cache_save('recent_update', $data);
	}
?>