<?php
	$query = 'SELECT * FROM internal_ads ORDER BY handle DESC';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$ads[] = $data;
	}
	
	cache_save('internal_ads', $ads);
?>