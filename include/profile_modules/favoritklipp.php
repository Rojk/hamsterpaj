<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";
	
	$query = 'SELECT ur.rank, uc.comment, f.id, f.handle, f.title, f.category_id';
	$query .= ' FROM user_ranks AS ur, user_comments AS uc, film AS f';
	$query .= ' WHERE ur.user_id = "' . $userid . '" AND uc.user_id = ur.user_id AND uc.item_id = ur.item_id AND f.id = ur.item_id';
	$query .= ' ORDER BY ur.rank DESC, uc.timestamp DESC LIMIT 4';
	
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if(mysql_num_rows($result) > 0)
	{
		while($data = mysql_fetch_assoc($result))
		{
			echo '';
			preint_r($data);
		}
	} 

?>
</div>