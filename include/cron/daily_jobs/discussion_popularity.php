<?php
	$query = 'UPDATE tags SET popularity = IF(popularity > 0.5, popularity - 0.5, popularity)';
	mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
?>