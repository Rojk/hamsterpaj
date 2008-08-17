#!/usr/bin/php -q
<?php
	require('/storage/www/standard.php');
	
	$query = 'SELECT COUNT(DISTINCT(id)) AS bills FROM bills';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$data = mysql_fetch_assoc($result);
	
	$count = date('Y-m-d;H:i') . ';' . $data['bills'] . ';' . "\n";
	
	$handle = fopen($hp_includepath . 'bills/count.txt', 'a');
	fwrite($handle, $count);
	fclose($handle);
	
	
	$query = 'SELECT SUM(collisions) AS collisions FROM bill_collisions WHERE collisions > 1';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$data = mysql_fetch_assoc($result);
	if (mysql_num_rows() == 0)
	{
		$data['collisions'] = 0;
	}
	$collisions = date('Y-m-d;H:i') . ';' . $data['collisions'] . ';' . "\n";
	
	$handle = fopen($hp_includepath . 'bills/collisions.txt', 'a');
	
	fwrite($handle, $collisions);
	fclose($handle);

	include(PATHS_INCLUDE . 'samsung_stat.php');

?>
